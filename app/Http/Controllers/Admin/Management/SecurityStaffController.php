<?php

namespace App\Http\Controllers\Admin\Management;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
=======
use App\Traits\CreatesNotifications;
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\DataTables\Admin\Management\SecurityStaffDataTable;
use App\Repositories\Interfaces\Admin\Management\SecurityStaffRepositoryInterface;
use App\Enums\Status;
use App\Enums\UserType;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class SecurityStaffController extends Controller
{
<<<<<<< HEAD
=======
    use CreatesNotifications;
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
    protected SecurityStaffRepositoryInterface $repository;
    protected $parentViewPath = 'admin.pages.management.security.';
    protected $parentRoutePath = 'admin.management.security.';

    public function __construct(SecurityStaffRepositoryInterface $repository)
    {
        $this->middleware('auth');
        $this->repository = $repository;
    }

    public function index(SecurityStaffDataTable $datatable)
    {
        checkPermissionAndRedirect('admin.management.security.index');
        Session::put('title', 'Security Staff Management');
        return $datatable->render($this->parentViewPath . 'index');
    }

    public function form($id = null)
    {
        checkPermissionAndRedirect('admin.management.security.' . ($id ? 'edit' : 'form'));
        Session::put('title', ($id ? 'Update' : 'Create') . ' Security Staff');

        $roles = Role::where('name', 'security')->get();

        if ($id) {
            $security = $this->repository->getWithRelations($id);
            if (!$security) {
                flashResponse('Security Staff not found.', 'danger');
                return Redirect::route($this->parentRoutePath . 'index');
            }
            return view($this->parentViewPath . 'form', compact('security', 'id', 'roles'));
        }

        $security = null;
        return view($this->parentViewPath . 'form', compact('id', 'roles'));
    }

    public function enroll(Request $request)
    {
        $id = $request->input('id');
        checkPermissionAndRedirect('admin.management.security.' . ($id ? 'edit' : 'form'));

        if ($request->has('id') && $request->filled('id')) {
            return $this->update($request);
        }

        $rules = [
            'first_name' => 'required|min:2|max:50',
            'last_name' => 'required|min:2|max:50',
            'middle_name' => 'nullable|max:50',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:M,F,Other',
            'joining_date' => 'required|date',
            'employee_id' => 'nullable|max:50',
            'shift' => 'required|in:Morning,Afternoon,Night',
            'position' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'name' => trim($request->first_name . ' ' . $request->last_name),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'usertype' => UserType::SECURITY->value,
                'status' => Status::ACTIVE->value,
            ]);

            // Assign roles to user
            if ($request->has('roles')) {
                $user->assignRole($request->roles);
            }

            // Create security staff
            $securityData = $request->except(['password', 'password_confirmation', 'roles', 'profile_image']);
            $securityData['user_id'] = $user->id;
            $securityData['is_active'] = true;

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = 'security_' . time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('security/profiles', $imageName, 'public');
                $securityData['photo_path'] = $imagePath;
            }

            $security = $this->repository->create($securityData);

<<<<<<< HEAD
=======
            // Create notification for security staff creation
            $this->notifyCreated('SecurityStaff', $security);

>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
            DB::commit();

            flashResponse('Security Staff created successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            flashResponse('Failed to create Security Staff. Please try again.', 'danger');
        }

        return redirect()->route($this->parentRoutePath . 'index');
    }

    public function show(string $id)
    {
        checkPermissionAndRedirect('admin.management.security.show');
        $security = $this->repository->getWithRelations($id);

        if (!$security) {
            flashResponse('Security Staff not found.', 'danger');
            return Redirect::back();
        }

        return view($this->parentViewPath . 'view', compact('security'));
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
            'joining_date' => 'required|date',
            'employee_id' => 'nullable|max:50',
            'shift' => 'required|in:Morning,Afternoon,Night',
            'position' => 'required|max:100',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id, 'id')],
            'password' => 'nullable|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $security = $this->repository->getById($id);
            if (!$security) {
                flashResponse('Security Staff not found.', 'danger');
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

            $security->user->update($userData);

            // Update roles
            if ($request->has('roles')) {
                $security->user->syncRoles($request->roles);
            }

            // Update security staff
            $securityData = $request->except(['password', 'password_confirmation', 'roles', 'profile_image']);

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($security->photo_path && Storage::disk('public')->exists($security->photo_path)) {
                    Storage::disk('public')->delete($security->photo_path);
                }

                $image = $request->file('profile_image');
                $imageName = 'security_' . time() . '_' . $security->user_id . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('security/profiles', $imageName, 'public');
                $securityData['photo_path'] = $imagePath;
            }

            $this->repository->update($id, $securityData);

            DB::commit();

            flashResponse('Security Staff updated successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            flashResponse('Failed to update Security Staff. Please try again.', 'danger');
        }

        return redirect()->route($this->parentRoutePath . 'index');
    }

    public function delete(string $id)
    {
        checkPermissionAndRedirect('admin.management.security.delete');

        try {
            DB::beginTransaction();

            $security = $this->repository->getById($id);
            if (!$security) {
                flashResponse('Security Staff not found.', 'danger');
                return Redirect::back();
            }

            // Delete user account
            $security->user->delete();

            // Delete security staff
            $this->repository->delete($id);

            DB::commit();

            flashResponse('Security Staff deleted successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            flashResponse('Failed to delete Security Staff. Please try again.', 'danger');
        }

        return redirect()->route($this->parentRoutePath . 'index');
    }
}
