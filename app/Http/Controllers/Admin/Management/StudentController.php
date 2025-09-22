<?php

namespace App\Http\Controllers\Admin\Management;

use App\Http\Controllers\Controller;
use App\Traits\CreatesNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\DataTables\Admin\Management\StudentDataTable;
use App\Repositories\Interfaces\Admin\Management\StudentRepositoryInterface;
use App\Repositories\Interfaces\Admin\Management\SchoolClassRepositoryInterface;
use App\Repositories\Interfaces\Admin\Management\SubjectRepositoryInterface;
use App\Repositories\Interfaces\Admin\Management\ParentRepositoryInterface;
use App\Enums\Status;
use App\Enums\UserType;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    use CreatesNotifications;
    protected StudentRepositoryInterface $repository;
    protected SchoolClassRepositoryInterface $classRepository;
    protected SubjectRepositoryInterface $subjectRepository;
    protected ParentRepositoryInterface $parentRepository;
    protected $parentViewPath = 'admin.pages.management.students.';
    protected $parentRoutePath = 'admin.management.students.';

    public function __construct(
        StudentRepositoryInterface $repository,
        SchoolClassRepositoryInterface $classRepository,
        SubjectRepositoryInterface $subjectRepository,
        ParentRepositoryInterface $parentRepository
    ) {
        $this->middleware('auth');
        $this->repository = $repository;
        $this->classRepository = $classRepository;
        $this->subjectRepository = $subjectRepository;
        $this->parentRepository = $parentRepository;
    }

    public function index(StudentDataTable $datatable)
    {
        checkPermissionAndRedirect('admin.management.students.index');
        Session::put('title', 'Student Management');
        return $datatable->render($this->parentViewPath . 'index');
    }

    public function form($id = null)
    {
        checkPermissionAndRedirect('admin.management.students.' . ($id ? 'edit' : 'form'));
        Session::put('title', ($id ? 'Update' : 'Create') . ' Student');

        $classes = $this->classRepository->getAll();
        $subjects = $this->subjectRepository->getAll();
        $parents = $this->parentRepository->getActive();
        $roles = Role::where('name', 'student')->get();

        if ($id) {
            $student = $this->repository->getWithRelations($id);
            if (!$student) {
                flashResponse('Student not found.', 'danger');
                return Redirect::route($this->parentRoutePath . 'index');
            }
            return view($this->parentViewPath . 'form', compact('student', 'id', 'classes', 'subjects', 'parents', 'roles'));
        }

        $student = null;
        return view($this->parentViewPath . 'form', compact('id', 'classes', 'subjects', 'parents', 'roles'));
    }

    public function enroll(Request $request)
    {
        $id = $request->input('id');
        checkPermissionAndRedirect('admin.management.students.' . ($id ? 'edit' : 'form'));

        if ($request->has('id') && $request->filled('id')) {
            return $this->update($request);
        }

        $rules = [
            'first_name' => 'required|min:2|max:50',
            'last_name' => 'required|min:2|max:50',
            'middle_name' => 'nullable|max:50',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:M,F,Other',
            'nationality' => 'nullable|max:50',
            'religion' => 'nullable|max:50',
            'home_language' => 'nullable|max:50',
            'grade_level' => 'required|integer|min:1|max:13',
            'class_id' => 'nullable|exists:school_classes,id',
            'section' => 'nullable|max:10',
            'enrollment_date' => 'required|date',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parents' => 'nullable|array',
            'parents.*' => 'exists:parents,parent_id',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            // Parent validation rules
            'parent_first_name' => 'nullable|array',
            'parent_first_name.*' => 'required_with:parent_last_name.*|max:50',
            'parent_last_name' => 'nullable|array',
            'parent_last_name.*' => 'required_with:parent_first_name.*|max:50',
            'parent_middle_name' => 'nullable|array',
            'parent_middle_name.*' => 'nullable|max:50',
            'parent_gender' => 'nullable|array',
            'parent_gender.*' => 'required_with:parent_first_name.*|in:M,F,Other',
            'parent_relationship_type' => 'nullable|array',
            'parent_relationship_type.*' => 'required_with:parent_first_name.*|in:Father,Mother,Guardian,Stepfather,Stepmother,Grandfather,Grandmother,Uncle,Aunt,Other',
            'parent_mobile_phone' => 'nullable|array',
            'parent_mobile_phone.*' => 'required_with:parent_first_name.*|max:15',
            'parent_email' => 'nullable|array',
            'parent_email.*' => 'nullable|email|max:100',
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'name' => trim($request->first_name . ' ' . $request->last_name),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'usertype' => UserType::STUDENT->value,
                'status' => Status::ACTIVE->value,
            ]);

            // Assign roles to user
            if ($request->has('roles')) {
                $user->assignRole($request->roles);
            }

            // Create student
            $studentData = $request->except([
                'password',
                'password_confirmation',
                'roles',
                'parents',
                'subjects',
                'profile_image',
                'parent_first_name',
                'parent_last_name',
                'parent_middle_name',
                'parent_gender',
                'parent_date_of_birth',
                'parent_relationship_type',
                'parent_mobile_phone',
                'parent_email',
                'parent_occupation',
                'parent_workplace',
                'parent_work_phone',
                'parent_is_emergency_contact',
                'parent_address_line1'
            ]);
            $studentData['user_id'] = $user->id;
            $studentData['is_active'] = $request->has('is_active') ? true : false;

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = 'student_' . time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('students/profiles', $imageName, 'public');
                $studentData['photo_path'] = $imagePath;
            }

            // Generate student code if not provided
            if (empty($studentData['student_code'])) {
                $studentData['student_code'] = \App\Models\Student::generateStudentCode();
            }

            $student = $this->repository->create($studentData);

            // Create notification for student creation
            $this->notifyCreated('Student', $student);

            // Create parents if provided
            $parentIds = [];
            if ($request->has('parent_first_name') && is_array($request->parent_first_name)) {
                foreach ($request->parent_first_name as $index => $firstName) {
                    if (!empty($firstName) && !empty($request->parent_last_name[$index])) {
                        // Create user account for parent
                        $parentEmail = $request->parent_email[$index] ?? null;
                        $parentUser = null;

                        if ($parentEmail) {
                            // Check if user with this email already exists
                            $existingUser = User::where('email', $parentEmail)->first();
                            if (!$existingUser) {
                                $parentUser = User::create([
                                    'name' => trim($firstName . ' ' . $request->parent_last_name[$index]),
                                    'email' => $parentEmail,
                                    'password' => Hash::make('password123'), // Default password
                                    'usertype' => UserType::PARENT->value,
                                    'status' => Status::ACTIVE->value,
                                ]);
                                $parentUser->assignRole('parent');
                            } else {
                                $parentUser = $existingUser;
                            }
                        }

                        // Create parent record
                        $parentData = [
                            'user_id' => $parentUser ? $parentUser->id : null,
                            'parent_code' => \App\Models\ParentModel::generateParentCode(),
                            'first_name' => $firstName,
                            'middle_name' => $request->parent_middle_name[$index] ?? null,
                            'last_name' => $request->parent_last_name[$index],
                            'date_of_birth' => $request->parent_date_of_birth[$index] ?? null,
                            'gender' => $request->parent_gender[$index],
                            'relationship_type' => $request->parent_relationship_type[$index],
                            'mobile_phone' => $request->parent_mobile_phone[$index],
                            'email' => $parentEmail,
                            'occupation' => $request->parent_occupation[$index] ?? null,
                            'workplace' => $request->parent_workplace[$index] ?? null,
                            'work_phone' => $request->parent_work_phone[$index] ?? null,
                            'is_emergency_contact' => isset($request->parent_is_emergency_contact) &&
                                in_array($index + 1, (array)$request->parent_is_emergency_contact),
                            'address_line1' => $request->parent_address_line1[$index] ?? null,
                            'is_active' => true,
                        ];

                        $parent = $this->parentRepository->create($parentData);
                        $parentIds[] = $parent->parent_id;
                    }
                }
            }

            // Assign existing parents if provided
            if ($request->has('parents') && !empty($request->parents)) {
                $parentIds = array_merge($parentIds, $request->parents);
            }

            // Link parents to student
            if (!empty($parentIds)) {
                $student->parents()->sync($parentIds);
            }

            // Assign subjects if provided
            if ($request->has('subjects') && !empty($request->subjects)) {
                $this->repository->assignSubjects($student->student_id, $request->subjects, $request->grade_level);
            }

            DB::commit();

            flashResponse('Student and parents created successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            flashResponse('Failed to create Student. Please try again. Error: ' . $e->getMessage(), 'danger');
        }

        return redirect()->route($this->parentRoutePath . 'index');
    }

    public function show(string $id)
    {
        checkPermissionAndRedirect('admin.management.students.show');
        $student = $this->repository->getWithRelations($id);

        if (!$student) {
            flashResponse('Student not found.', 'danger');
            return Redirect::back();
        }

        return view($this->parentViewPath . 'view', compact('student'));
    }

    public function update(Request $request)
    {
        $id = $request->input('id');

        $rules = [
            'first_name' => 'required|min:2|max:50',
            'last_name' => 'required|min:2|max:50',
            'middle_name' => 'nullable|max:50',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:M,F,Other',
            'grade_level' => 'required|integer|min:1|max:13',
            'class_id' => 'nullable|exists:school_classes,id',
            'enrollment_date' => 'required|date',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id, 'id')],
            'password' => 'nullable|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parents' => 'nullable|array',
            'parents.*' => 'exists:parents,parent_id',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $student = $this->repository->getById($id);
            if (!$student) {
                flashResponse('Student not found.', 'danger');
                return Redirect::route($this->parentRoutePath . 'index');
            }

            // Update user account
            $userData = [
                'name' => trim($request->first_name . ' ' . $request->last_name),
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $student->user->update($userData);

            // Update roles
            if ($request->has('roles')) {
                $student->user->syncRoles($request->roles);
            }

            // Update student
            $studentData = $request->except(['password', 'password_confirmation', 'roles', 'parents', 'subjects', 'profile_image']);

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($student->photo_path && Storage::disk('public')->exists($student->photo_path)) {
                    Storage::disk('public')->delete($student->photo_path);
                }

                $image = $request->file('profile_image');
                $imageName = 'student_' . time() . '_' . $student->user_id . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('students/profiles', $imageName, 'public');
                $studentData['photo_path'] = $imagePath;
            }

            $this->repository->update($id, $studentData);

            // Create notification for student update
            $this->notifyUpdated('Student', $student);

            // Update parents
            $existingParentIds = $request->input('existing_parents', []);
            $linkedParentIds = $request->input('parents', []);

            // Create new parents if provided
            $newParentIds = [];
            if ($request->has('parent_first_name') && is_array($request->parent_first_name)) {
                foreach ($request->parent_first_name as $index => $firstName) {
                    if (!empty($firstName) && !empty($request->parent_last_name[$index])) {
                        // Create user account for parent
                        $parentEmail = $request->parent_email[$index] ?? null;
                        $parentUser = null;

                        if ($parentEmail) {
                            // Check if user with this email already exists
                            $existingUser = User::where('email', $parentEmail)->first();
                            if (!$existingUser) {
                                $parentUser = User::create([
                                    'name' => trim($firstName . ' ' . $request->parent_last_name[$index]),
                                    'email' => $parentEmail,
                                    'password' => Hash::make('password123'), // Default password
                                    'usertype' => UserType::PARENT->value,
                                    'status' => Status::ACTIVE->value,
                                ]);
                                $parentUser->assignRole('parent');
                            } else {
                                $parentUser = $existingUser;
                            }
                        }

                        // Create parent record
                        $parentData = [
                            'user_id' => $parentUser ? $parentUser->id : null,
                            'parent_code' => \App\Models\ParentModel::generateParentCode(),
                            'first_name' => $firstName,
                            'middle_name' => $request->parent_middle_name[$index] ?? null,
                            'last_name' => $request->parent_last_name[$index],
                            'date_of_birth' => $request->parent_date_of_birth[$index] ?? null,
                            'gender' => $request->parent_gender[$index],
                            'relationship_type' => $request->parent_relationship_type[$index],
                            'mobile_phone' => $request->parent_mobile_phone[$index],
                            'email' => $parentEmail,
                            'occupation' => $request->parent_occupation[$index] ?? null,
                            'workplace' => $request->parent_workplace[$index] ?? null,
                            'work_phone' => $request->parent_work_phone[$index] ?? null,
                            'is_emergency_contact' => isset($request->parent_is_emergency_contact) &&
                                in_array($index + 1, (array)$request->parent_is_emergency_contact),
                            'address_line1' => $request->parent_address_line1[$index] ?? null,
                            'is_active' => true,
                        ];

                        $parent = $this->parentRepository->create($parentData);
                        $newParentIds[] = $parent->parent_id;
                    }
                }
            }

            // Combine all parent IDs (existing, linked, and newly created)
            $allParentIds = array_merge($existingParentIds, $linkedParentIds, $newParentIds);
            $allParentIds = array_unique($allParentIds); // Remove duplicates

            // Sync all parents
            $student->parents()->sync($allParentIds);

            // Update subjects
            if ($request->has('subjects')) {
                $this->repository->assignSubjects($student->student_id, $request->subjects ?? [], $request->grade_level);
            }

            DB::commit();

            flashResponse('Student updated successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            flashResponse('Failed to update Student. Please try again.', 'danger');
        }

        return redirect()->route($this->parentRoutePath . 'index');
    }

    public function delete(string $id)
    {
        checkPermissionAndRedirect('admin.management.students.delete');

        try {
            DB::beginTransaction();

            $student = $this->repository->getById($id);
            if (!$student) {
                flashResponse('Student not found.', 'danger');
                return Redirect::back();
            }

            // Create notification for student deletion (before deletion)
            $this->notifyDeleted('Student', $student);

            // Delete user account
            $student->user->delete();

            // Delete student
            $this->repository->delete($id);

            DB::commit();

            flashResponse('Student deleted successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            flashResponse('Failed to delete Student. Please try again.', 'danger');
        }

        return redirect()->route($this->parentRoutePath . 'index');
    }

    public function generateCode()
    {
        return response()->json([
            'code' => \App\Models\Student::generateStudentCode()
        ]);
    }
}
