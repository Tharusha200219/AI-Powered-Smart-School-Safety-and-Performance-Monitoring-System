<?php

namespace App\Http\Controllers\Admin\Management;

use App\DataTables\Admin\Management\TeacherDataTable;
use App\Enums\Status;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\Interfaces\Admin\Management\SubjectRepositoryInterface;
use App\Repositories\Interfaces\Admin\Management\TeacherRepositoryInterface;
use App\Traits\CreatesNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class TeacherController extends Controller
{
    use CreatesNotifications;

    protected TeacherRepositoryInterface $repository;

    protected SubjectRepositoryInterface $subjectRepository;

    protected $parentViewPath = 'admin.pages.management.teachers.';

    protected $parentRoutePath = 'admin.management.teachers.';

    public function __construct(
        TeacherRepositoryInterface $repository,
        SubjectRepositoryInterface $subjectRepository
    ) {
        $this->middleware('auth');
        $this->repository = $repository;
        $this->subjectRepository = $subjectRepository;
    }

    public function index(TeacherDataTable $datatable)
    {
        checkPermissionAndRedirect('admin.management.teachers.index');
        Session::put('title', 'Teacher Management');

        return $datatable->render($this->parentViewPath.'index');
    }

    public function form($id = null)
    {
        checkPermissionAndRedirect('admin.management.teachers.'.($id ? 'edit' : 'form'));
        Session::put('title', ($id ? 'Update' : 'Create').' Teacher');

        $subjects = $this->subjectRepository->getAll();
        $roles = Role::where('name', 'teacher')->get();

        if ($id) {
            $teacher = $this->repository->getWithRelations($id);
            if (! $teacher) {
                flashResponse('Teacher not found.', 'danger');

                return Redirect::route($this->parentRoutePath.'index');
            }

            return view($this->parentViewPath.'form', compact('teacher', 'id', 'subjects', 'roles'));
        }

        $teacher = null;

        return view($this->parentViewPath.'form', compact('id', 'subjects', 'roles'));
    }

    public function enroll(Request $request)
    {
        $id = $request->input('id');
        checkPermissionAndRedirect('admin.management.teachers.'.($id ? 'edit' : 'form'));

        if ($request->has('id') && $request->filled('id')) {
            return $this->update($request);
        }

        $rules = [
            'first_name' => 'required|min:2|max:50',
            'last_name' => 'required|min:2|max:50',
            'middle_name' => 'nullable|max:50',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:M,F,Other',
            'qualification' => 'required|max:255',
            'specialization' => 'nullable|max:255',
            'experience_years' => 'nullable|numeric|min:0',
            'joining_date' => 'required|date',
            'employee_id' => 'nullable|max:50',
            'is_class_teacher' => 'boolean',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'name' => trim($request->first_name.' '.$request->last_name),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'usertype' => UserType::TEACHER->value,
                'status' => Status::ACTIVE->value,
            ]);

            // Assign roles to user
            if ($request->has('roles')) {
                $user->assignRole($request->roles);
            }

            // Create teacher
            $teacherData = $request->except(['password', 'password_confirmation', 'roles', 'subjects', 'profile_image']);
            $teacherData['user_id'] = $user->id;
            $teacherData['is_active'] = true;
            $teacherData['is_class_teacher'] = $request->boolean('is_class_teacher');

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = 'teacher_'.time().'_'.$user->id.'.'.$image->getClientOriginalExtension();
                $imagePath = $image->storeAs('teachers/profiles', $imageName, 'public');
                $teacherData['photo_path'] = $imagePath;
            }

            // Generate teacher code if not provided
            if (empty($teacherData['teacher_code'])) {
                $teacherData['teacher_code'] = \App\Models\Teacher::generateTeacherCode();
            }

            $teacher = $this->repository->create($teacherData);

            // Create notification for teacher creation
            $this->notifyCreated('Teacher', $teacher);

            // Assign subjects if provided
            if ($request->has('subjects') && ! empty($request->subjects)) {
                $this->repository->assignSubjects($teacher->teacher_id, $request->subjects);
            }

            DB::commit();

            flashResponse('Teacher created successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            flashResponse('Failed to create Teacher. Please try again.', 'danger');
        }

        return redirect()->route($this->parentRoutePath.'index');
    }

    public function show(string $id)
    {
        checkPermissionAndRedirect('admin.management.teachers.show');
        $teacher = $this->repository->getWithRelations($id);

        if (! $teacher) {
            flashResponse('Teacher not found.', 'danger');

            return Redirect::back();
        }

        return view($this->parentViewPath.'view', compact('teacher'));
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
            'qualification' => 'required|max:255',
            'specialization' => 'nullable|max:255',
            'experience_years' => 'nullable|numeric|min:0',
            'joining_date' => 'required|date',
            'employee_id' => 'nullable|max:50',
            'is_class_teacher' => 'boolean',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id, 'id')],
            'password' => 'nullable|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $teacher = $this->repository->getById($id);
            if (! $teacher) {
                flashResponse('Teacher not found.', 'danger');

                return Redirect::route($this->parentRoutePath.'index');
            }

            // Update user account
            $userData = [
                'name' => trim($request->first_name.' '.$request->last_name),
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $teacher->user->update($userData);

            // Update roles
            if ($request->has('roles')) {
                $teacher->user->syncRoles($request->roles);
            }

            // Update teacher
            $teacherData = $request->except(['password', 'password_confirmation', 'roles', 'subjects', 'profile_image']);
            $teacherData['is_class_teacher'] = $request->boolean('is_class_teacher');

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($teacher->photo_path && Storage::disk('public')->exists($teacher->photo_path)) {
                    Storage::disk('public')->delete($teacher->photo_path);
                }

                $image = $request->file('profile_image');
                $imageName = 'teacher_'.time().'_'.$teacher->user_id.'.'.$image->getClientOriginalExtension();
                $imagePath = $image->storeAs('teachers/profiles', $imageName, 'public');
                $teacherData['photo_path'] = $imagePath;
            }

            $this->repository->update($id, $teacherData);

            // Create notification for teacher update
            $this->notifyUpdated('Teacher', $teacher);

            // Update subjects
            if ($request->has('subjects')) {
                $this->repository->assignSubjects($teacher->teacher_id, $request->subjects ?? []);
            }

            DB::commit();

            flashResponse('Teacher updated successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            flashResponse('Failed to update Teacher. Please try again.', 'danger');
        }

        return redirect()->route($this->parentRoutePath.'index');
    }

    public function delete(string $id)
    {
        checkPermissionAndRedirect('admin.management.teachers.delete');

        try {
            DB::beginTransaction();

            $teacher = $this->repository->getById($id);
            if (! $teacher) {
                flashResponse('Teacher not found.', 'danger');

                return Redirect::back();
            }

            // Create notification for teacher deletion (before deletion)
            $this->notifyDeleted('Teacher', $teacher);

            // Delete user account
            $teacher->user->delete();

            // Delete teacher
            $this->repository->delete($id);

            DB::commit();

            flashResponse('Teacher deleted successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            flashResponse('Failed to delete Teacher. Please try again.', 'danger');
        }

        return redirect()->route($this->parentRoutePath.'index');
    }

    public function generateCode()
    {
        return response()->json([
            'code' => \App\Models\Teacher::generateTeacherCode(),
        ]);
    }
}
