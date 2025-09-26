<?php

namespace App\Http\Controllers\Admin\Management;

use App\DataTables\Admin\Management\SchoolClassDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\Admin\Management\SchoolClassRepositoryInterface;
use App\Repositories\Interfaces\Admin\Management\SubjectRepositoryInterface;
use App\Repositories\Interfaces\Admin\Management\TeacherRepositoryInterface;
use App\Services\DatabaseTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class SchoolClassController extends Controller
{
    protected SchoolClassRepositoryInterface $repository;

    protected TeacherRepositoryInterface $teacherRepository;

    protected SubjectRepositoryInterface $subjectRepository;

    protected $parentViewPath = 'admin.pages.management.classes.';

    protected $parentRoutePath = 'admin.management.classes.';

    protected DatabaseTransactionService $transactionService;

    public function __construct(
        SchoolClassRepositoryInterface $repository,
        TeacherRepositoryInterface $teacherRepository,
        SubjectRepositoryInterface $subjectRepository,
        DatabaseTransactionService $transactionService

    ) {
        $this->middleware('auth');
        $this->repository = $repository;
        $this->teacherRepository = $teacherRepository;
        $this->subjectRepository = $subjectRepository;
        $this->transactionService = $transactionService;
    }

    public function index(SchoolClassDataTable $datatable)
    {
        checkPermissionAndRedirect('admin.management.classes.index');
        Session::put('title', 'Class Management');

        return $datatable->render($this->parentViewPath.'index');
    }

    public function form($id = null)
    {
        checkPermissionAndRedirect('admin.management.classes.'.($id ? 'edit' : 'form'));
        Session::put('title', ($id ? 'Update' : 'Create').' Class');

        $teachers = $this->teacherRepository->getClassTeachers();
        $subjects = $this->subjectRepository->getAll();

        if ($id) {
            $class = $this->repository->getWithRelations($id);
            if (! $class) {
                flashResponse('Class not found.', 'danger');

                return Redirect::route($this->parentRoutePath.'index');
            }

            return view($this->parentViewPath.'form', compact('class', 'id', 'teachers', 'subjects'));
        }

        $class = null;

        return view($this->parentViewPath.'form', compact('id', 'teachers', 'subjects'));
    }

    public function enroll(Request $request)
    {
        $id = $request->input('id');
        checkPermissionAndRedirect('admin.management.classes.'.($id ? 'edit' : 'form'));

        if ($request->has('id') && $request->filled('id')) {
            return $this->update($request);
        }

        $rules = [
            'class_name' => 'required|min:2|max:100',
            'grade_level' => 'required|integer|min:1|max:13',
            'academic_year' => 'required|max:10',
            'section' => 'nullable|max:10',
            'class_teacher_id' => 'nullable|exists:teachers,teacher_id',
            'room_number' => 'nullable|max:20',
            'capacity' => 'required|integer|min:1|max:200',
            'description' => 'nullable|max:1000',
            'status' => 'required|in:active,inactive',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $classData = $request->except(['subjects']);
            $class = $this->repository->create($classData);

            // Assign subjects if provided
            if ($request->has('subjects') && ! empty($request->subjects)) {
                $this->repository->assignSubjects($class->id, $request->subjects);
            }

            DB::commit();

            flashResponse('Class created successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            flashResponse('Failed to create Class. Please try again.', 'danger');
        }

        return redirect()->route($this->parentRoutePath.'index');
    }

    public function show(string $id)
    {
        checkPermissionAndRedirect('admin.management.classes.show');
        $class = $this->repository->getWithRelations($id);

        if (! $class) {
            flashResponse('Class not found.', 'danger');

            return Redirect::back();
        }

        return view($this->parentViewPath.'view', compact('class'));
    }

    public function update(Request $request)
    {
        $id = $request->input('id');

        $rules = [
            'class_name' => 'required|min:2|max:100',
            'grade_level' => 'required|integer|min:1|max:13',
            'academic_year' => 'required|max:10',
            'section' => 'nullable|max:10',
            'class_teacher_id' => 'nullable|exists:teachers,teacher_id',
            'room_number' => 'nullable|max:20',
            'capacity' => 'required|integer|min:1|max:200',
            'description' => 'nullable|max:1000',
            'status' => 'required|in:active,inactive',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $class = $this->repository->getById($id);
            if (! $class) {
                flashResponse('Class not found.', 'danger');

                return Redirect::route($this->parentRoutePath.'index');
            }

            // Update class
            $classData = $request->except(['subjects']);
            $this->repository->update($id, $classData);

            // Update subjects
            if ($request->has('subjects')) {
                $this->repository->assignSubjects($class->id, $request->subjects ?? []);
            }

            DB::commit();

            flashResponse('Class updated successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            flashResponse('Failed to update Class. Please try again.', 'danger');
        }

        return redirect()->route($this->parentRoutePath.'index');
    }

    public function delete(string $id)
    {
        checkPermissionAndRedirect('admin.management.classes.delete');

        try {
            DB::beginTransaction();

            $class = $this->repository->getById($id);
            if (! $class) {
                flashResponse('Class not found.', 'danger');

                return Redirect::back();
            }

            // Delete class
            $this->repository->delete($id);

            DB::commit();

            flashResponse('Class deleted successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            flashResponse('Failed to delete Class. Please try again.', 'danger');
        }

        return redirect()->route($this->parentRoutePath.'index');
    }
}



