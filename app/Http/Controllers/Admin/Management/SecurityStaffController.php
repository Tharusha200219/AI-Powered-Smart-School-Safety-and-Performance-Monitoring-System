<?php

namespace App\Http\Controllers\Admin\Management;

use App\DataTables\Admin\Management\SecurityStaffDataTable;
use App\Enums\UserType;
use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\Interfaces\Admin\Management\SecurityStaffRepositoryInterface;
use App\Services\DatabaseTransactionService;
use App\Services\ImageUploadService;
use App\Services\UserService;
use App\Traits\CreatesNotifications;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class SecurityStaffController extends Controller
{
    use CreatesNotifications;

    protected SecurityStaffRepositoryInterface $repository;

    protected UserService $userService;

    protected DatabaseTransactionService $transactionService;

    protected ImageUploadService $imageService;

    protected string $parentViewPath = 'admin.pages.management.security.';

    protected string $parentRoutePath = 'admin.management.security.';

    public function __construct(
        SecurityStaffRepositoryInterface $repository,
        UserService $userService,
        DatabaseTransactionService $transactionService,
        ImageUploadService $imageService
    ) {
        $this->middleware('auth');
        $this->repository = $repository;
        $this->userService = $userService;
        $this->transactionService = $transactionService;
        $this->imageService = $imageService;
    }

    public function index(SecurityStaffDataTable $datatable): View
    {
        checkPermissionAndRedirect('admin.management.security.index');
        Session::put('title', 'Security Staff Management');

        return $datatable->render($this->parentViewPath . 'index');
    }

    public function form(?string $id = null): View|RedirectResponse
    {
        checkPermissionAndRedirect('admin.management.security.' . ($id ? 'edit' : 'form'));
        Session::put('title', ($id ? 'Update' : 'Create') . ' Security Staff');

        $roles = Role::where('name', 'security')->get();

        if ($id) {
            $security = $this->repository->getWithRelations($id);
            if (! $security) {
                flashResponse('Security Staff not found.', 'danger');

                return Redirect::route($this->parentRoutePath . 'index');
            }

            return view($this->parentViewPath . 'form', compact('security', 'id', 'roles'));
        }

        $security = null;

        return view($this->parentViewPath . 'form', compact('id', 'roles'));
    }

    public function enroll(Request $request): RedirectResponse
    {
        $id = $request->input('id');
        checkPermissionAndRedirect('admin.management.security.' . ($id ? 'edit' : 'form'));

        if ($request->has('id') && $request->filled('id')) {
            return $this->update($request);
        }

        $rules = $this->getSecurityValidationRules();
        $request->validate($rules);

        $result = $this->transactionService->executeCreate(
            function () use ($request) {
                // Create user account
                $user = $this->userService->createUserWithRole(
                    $request->all(),
                    UserType::SECURITY,
                    $request->input('roles', [])
                );

                // Prepare security data
                $securityData = $request->except(['password', 'password_confirmation', 'roles', 'profile_image']);
                $securityData['user_id'] = $user->id;
                $securityData['is_active'] = true;

                // Handle profile image upload
                if ($request->hasFile('profile_image')) {
                    $securityData['photo_path'] = $this->imageService->uploadProfileImage(
                        $request->file('profile_image'),
                        'security',
                        $user->id
                    );
                }

                return $this->repository->create($securityData);
            },
            'Security Staff'
        );

        if ($result['success']) {
            flashResponse($result['message'], 'success');
        } else {
            flashResponse($result['message'], 'danger');
        }

        return redirect()->route($this->parentRoutePath . 'index');
    }

    public function show(string $id): View|RedirectResponse
    {
        checkPermissionAndRedirect('admin.management.security.show');
        $security = $this->repository->getWithRelations($id);

        if (! $security) {
            flashResponse('Security Staff not found.', 'danger');

            return Redirect::back();
        }

        return view($this->parentViewPath . 'view', compact('security'));
    }

    public function update(Request $request): RedirectResponse
    {
        $id = $request->input('id');
        $rules = $this->getSecurityValidationRules(true, $id);
        $request->validate($rules);

        $result = $this->transactionService->executeUpdate(
            function () use ($request, $id) {
                $security = $this->repository->getById($id);
                if (! $security) {
                    throw new \Exception('Security Staff not found.');
                }

                // Update user account
                $userData = [
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'middle_name' => $request->input('middle_name'),
                    'email' => $request->input('email'),
                ];

                if ($request->filled('password')) {
                    $userData['password'] = $request->input('password');
                }

                $updatedUser = $this->userService->updateUser($security->user, $userData);

                // Update roles
                if ($request->has('roles')) {
                    $this->userService->updateUserRoles($updatedUser, $request->input('roles'));
                }

                // Prepare security staff data
                $securityData = $request->except(['password', 'password_confirmation', 'roles', 'profile_image']);

                // Handle profile image upload
                if ($request->hasFile('profile_image')) {
                    $securityData['photo_path'] = $this->imageService->uploadProfileImage(
                        $request->file('profile_image'),
                        'security',
                        $security->user_id,
                        $security->photo_path
                    );
                }

                return $this->repository->update($id, $securityData);
            },
            'Security Staff'
        );

        if ($result['success']) {
            flashResponse($result['message'], 'success');
        } else {
            flashResponse($result['message'], 'danger');
        }

        return redirect()->route($this->parentRoutePath . 'index');
    }

    public function delete(string $id): RedirectResponse
    {
        checkPermissionAndRedirect('admin.management.security.delete');

        $result = $this->transactionService->executeDelete(
            function () use ($id) {
                $security = $this->repository->getById($id);
                if (! $security) {
                    throw new \Exception('Security Staff not found.');
                }

                // Delete profile image
                if ($security->photo_path) {
                    $this->imageService->deleteProfileImage($security->photo_path);
                }

                // Delete user account
                $security->user->delete();

                // Delete security staff
                return $this->repository->delete($id);
            },
            'Security Staff'
        );

        if ($result['success']) {
            flashResponse($result['message'], 'success');
        } else {
            flashResponse($result['message'], 'danger');
        }

        return redirect()->route($this->parentRoutePath . 'index');
    }

    /**
     * Get validation rules for security staff
     */
    private function getSecurityValidationRules(bool $isUpdate = false, ?string $userId = null): array
    {
        $rules = [
            'first_name' => ValidationRules::PERSONAL_NAME_RULES,
            'last_name' => ValidationRules::PERSONAL_NAME_RULES,
            'middle_name' => ValidationRules::OPTIONAL_NAME_RULES,
            'date_of_birth' => ValidationRules::DATE_RULES,
            'gender' => 'required|in:M,F,Other',
            'joining_date' => 'required|date',
            'employee_id' => 'nullable|max:50',
            'shift' => 'required|in:Morning,Afternoon,Night',
            'position' => 'required|max:100',
            'profile_image' => ValidationRules::PROFILE_IMAGE_RULES,
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ];

        if ($isUpdate && $userId) {
            $rules['email'] = [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId, 'id'),
            ];
            $rules['password'] = ValidationRules::OPTIONAL_PASSWORD_RULES;
        } else {
            $rules['email'] = ValidationRules::EMAIL_RULES . '|unique:users,email';
            $rules['password'] = ValidationRules::PASSWORD_RULES;
        }

        return $rules;
    }
}
