<?php

namespace App\Http\Controllers\Admin\Management;

use App\Enums\UserType;
use App\Helpers\Constants;
use App\Helpers\ValidationRules;
use App\Http\Controllers\Admin\BaseManagementController;
use App\Repositories\Interfaces\Admin\Management\ParentRepositoryInterface;
use App\Repositories\Interfaces\Admin\Management\SchoolClassRepositoryInterface;
use App\Repositories\Interfaces\Admin\Management\StudentRepositoryInterface;
use App\Repositories\Interfaces\Admin\Management\SubjectRepositoryInterface;
use App\Services\DatabaseTransactionService;
use App\Services\ImageUploadService;
use App\Services\ParentCreationService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;

class ImprovedStudentController extends BaseManagementController
{
    protected SchoolClassRepositoryInterface $classRepository;

    protected SubjectRepositoryInterface $subjectRepository;

    protected ParentRepositoryInterface $parentRepository;

    protected ParentCreationService $parentCreationService;

    public function __construct(
        StudentRepositoryInterface $repository,
        UserService $userService,
        ImageUploadService $imageService,
        DatabaseTransactionService $transactionService,
        SchoolClassRepositoryInterface $classRepository,
        SubjectRepositoryInterface $subjectRepository,
        ParentRepositoryInterface $parentRepository,
        ParentCreationService $parentCreationService
    ) {
        parent::__construct($repository, $userService, $imageService, $transactionService);

        $this->classRepository = $classRepository;
        $this->subjectRepository = $subjectRepository;
        $this->parentRepository = $parentRepository;
        $this->parentCreationService = $parentCreationService;

        // Set entity properties
        $this->parentViewPath = 'admin.pages.management.students.';
        $this->parentRoutePath = 'admin.management.students.';
        $this->entityName = 'Student';
        $this->entityType = 'student';
    }

    /**
     * Process student enrollment (create or update)
     */
    public function enroll(Request $request)
    {
        $id = $request->input('id');
        $isUpdate = $id && $request->filled('id');

        // Validate request
        $this->validateStudentRequest($request, $isUpdate, $id);

        if ($isUpdate) {
            return $this->updateStudent($request, $id);
        }

        return $this->createStudent($request);
    }

    /**
     * Create new student
     */
    private function createStudent(Request $request)
    {
        $result = $this->transactionService->executeCreate(
            function () use ($request) {
                // Create user account
                $user = $this->userService->createUserWithRole(
                    $request->all(),
                    UserType::STUDENT,
                    $request->input('roles', [])
                );

                // Create student record
                $studentData = $this->buildStudentData($request, $user->id);
                $student = $this->repository->create($studentData);

                // Handle profile image
                if ($imagePath = $this->handleProfileImageUpload($request)) {
                    $this->repository->update($student->student_id, ['photo_path' => $imagePath]);
                }

                // Create and link parents
                $this->handleParentCreation($request, $student);

                // Assign subjects
                $this->handleSubjectAssignment($request, $student);

                return $student;
            },
            $this->entityName,
            Constants::getSuccessMessage('created', $this->entityName.' and parents'),
        );

        flashResponse($result['message'], $result['success'] ? Constants::FLASH_SUCCESS : Constants::FLASH_ERROR);

        return redirect()->route($this->parentRoutePath.'index');
    }

    /**
     * Update existing student
     */
    private function updateStudent(Request $request, int $id)
    {
        $student = $this->repository->getById($id);
        if (! $student) {
            flashResponse(Constants::getErrorMessage('not_found', $this->entityName), Constants::FLASH_ERROR);

            return Redirect::route($this->parentRoutePath.'index');
        }

        $result = $this->transactionService->executeUpdate(
            function () use ($request, $student, $id) {
                // Update user account
                $this->userService->updateUser($student->user, $request->all());
                $this->userService->updateUserRoles($student->user, $request->input('roles', []));

                // Update student record
                $studentData = $this->buildStudentData($request);

                // Handle profile image update
                if ($imagePath = $this->handleProfileImageUpload($request, $student)) {
                    $studentData['photo_path'] = $imagePath;
                }

                $this->repository->update($id, $studentData);

                // Update parent relationships
                $this->handleParentUpdate($request, $student);

                // Update subject assignments
                $this->handleSubjectAssignment($request, $student);

                return $student;
            },
            $this->entityName,
            $student
        );

        flashResponse($result['message'], $result['success'] ? Constants::FLASH_SUCCESS : Constants::FLASH_ERROR);

        return redirect()->route($this->parentRoutePath.'index');
    }

    /**
     * Generate student code
     */
    public function generateCode()
    {
        return response()->json([
            'code' => $this->repository->generateStudentCode(),
        ]);
    }

    /**
     * Get form data for views
     */
    protected function getFormData($id = null): array
    {
        return [
            'classes' => $this->classRepository->getAll(),
            'subjects' => $this->subjectRepository->getAll(),
            'parents' => $this->parentRepository->getActive(),
            'roles' => Role::where('name', 'student')->get(),
        ];
    }

    /**
     * Validate student request
     */
    private function validateStudentRequest(Request $request, bool $isUpdate, ?int $id): void
    {
        $rules = ValidationRules::getStudentRules($isUpdate, $id);
        $parentRules = ValidationRules::getParentArrayRules();

        $request->validate(array_merge($rules, $parentRules));
    }

    /**
     * Build student data array
     */
    private function buildStudentData(Request $request, ?int $userId = null): array
    {
        $data = $request->except([
            'password',
            'password_confirmation',
            'roles',
            'parents',
            'subjects',
            'profile_image',
        ]);

        // Remove parent fields
        $parentFields = array_filter(
            array_keys($request->all()),
            fn ($key) => str_starts_with($key, 'parent_')
        );
        $data = array_diff_key($data, array_flip($parentFields));

        if ($userId) {
            $data['user_id'] = $userId;
        }

        $data['is_active'] = $request->boolean('is_active');

        // Generate student code if not provided
        if (empty($data['student_code'])) {
            $data['student_code'] = $this->repository->generateStudentCode();
        }

        return $data;
    }

    /**
     * Handle parent creation for new student
     */
    private function handleParentCreation(Request $request, $student): void
    {
        $parentIds = [];

        // Create new parents from form data
        $newParentIds = $this->parentCreationService->createParentsFromArray($request->all());
        $parentIds = array_merge($parentIds, $newParentIds);

        // Add existing parent selections
        if ($request->has('parents') && ! empty($request->parents)) {
            $parentIds = array_merge($parentIds, $request->parents);
        }

        // Link all parents to student
        if (! empty($parentIds)) {
            $student->parents()->sync(array_unique($parentIds));
        }
    }

    /**
     * Handle parent updates for existing student
     */
    private function handleParentUpdate(Request $request, $student): void
    {
        $existingParentIds = $request->input('existing_parents', []);
        $linkedParentIds = $request->input('parents', []);
        $newParentIds = $this->parentCreationService->createParentsFromArray($request->all());

        // Combine all parent IDs and sync
        $allParentIds = array_unique(array_merge($existingParentIds, $linkedParentIds, $newParentIds));
        $student->parents()->sync($allParentIds);
    }

    /**
     * Handle subject assignment
     */
    private function handleSubjectAssignment(Request $request, $student): void
    {
        if ($request->has('subjects')) {
            $this->repository->assignSubjects(
                $student->student_id,
                $request->subjects ?? [],
                $request->grade_level
            );
        }
    }
}
