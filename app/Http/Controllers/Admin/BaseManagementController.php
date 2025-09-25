<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constants;
use App\Http\Controllers\Controller;
use App\Services\DatabaseTransactionService;
use App\Services\ImageUploadService;
use App\Services\UserService;
use App\Traits\CreatesNotifications;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

abstract class BaseManagementController extends Controller
{
    use CreatesNotifications;

    protected $repository;

    protected UserService $userService;

    protected ImageUploadService $imageService;

    protected DatabaseTransactionService $transactionService;

    protected string $parentViewPath;

    protected string $parentRoutePath;

    protected string $entityName;

    protected string $entityType;

    public function __construct(
        $repository,
        UserService $userService,
        ImageUploadService $imageService,
        DatabaseTransactionService $transactionService
    ) {
        $this->middleware('auth');
        $this->repository = $repository;
        $this->userService = $userService;
        $this->imageService = $imageService;
        $this->transactionService = $transactionService;
    }

    /**
     * Display entity index page
     */
    public function index($datatable)
    {
        checkPermissionAndRedirect($this->getPermissionKey('index'));
        Session::put('title', $this->getPageTitle('Management'));

        return $datatable->render($this->parentViewPath.'index');
    }

    /**
     * Show entity details
     */
    public function show(string $id)
    {
        checkPermissionAndRedirect($this->getPermissionKey('show'));

        $entity = $this->repository->getWithRelations($id);

        if (! $entity) {
            flashResponse(Constants::getErrorMessage('not_found', $this->entityName), Constants::FLASH_ERROR);

            return Redirect::back();
        }

        return view($this->parentViewPath.'view', [$this->getEntityVariableName() => $entity]);
    }

    /**
     * Display form for creating/editing entity
     */
    public function form($id = null)
    {
        $action = $id ? 'edit' : 'form';
        checkPermissionAndRedirect($this->getPermissionKey($action));

        $pageTitle = ($id ? 'Update' : 'Create').' '.$this->entityName;
        Session::put('title', $pageTitle);

        $data = $this->getFormData($id);

        if ($id) {
            $entity = $this->repository->getWithRelations($id);
            if (! $entity) {
                flashResponse(Constants::getErrorMessage('not_found', $this->entityName), Constants::FLASH_ERROR);

                return Redirect::route($this->parentRoutePath.'index');
            }
            $data[$this->getEntityVariableName()] = $entity;
            $data['id'] = $id;
        } else {
            $data['id'] = $id;
        }

        return view($this->parentViewPath.'form', $data);
    }

    /**
     * Delete entity
     */
    public function delete(string $id)
    {
        checkPermissionAndRedirect($this->getPermissionKey('delete'));

        $entity = $this->repository->getById($id);
        if (! $entity) {
            flashResponse(Constants::getErrorMessage('not_found', $this->entityName), Constants::FLASH_ERROR);

            return Redirect::back();
        }

        $result = $this->transactionService->executeDelete(
            function () use ($entity, $id) {
                // Delete user account if exists
                if (isset($entity->user)) {
                    $entity->user->delete();
                }

                // Delete entity
                $this->repository->delete($id);

                return $entity;
            },
            $this->entityName,
            $entity
        );

        flashResponse($result['message'], $result['success'] ? Constants::FLASH_SUCCESS : Constants::FLASH_ERROR);

        return redirect()->route($this->parentRoutePath.'index');
    }

    /**
     * Handle profile image upload
     */
    protected function handleProfileImageUpload($request, $entity = null): ?string
    {
        if (! $request->hasFile('profile_image')) {
            return null;
        }

        $oldImagePath = $entity?->photo_path ?? null;
        $userId = $entity?->user_id ?? time();

        return $this->imageService->uploadProfileImage(
            $request->file('profile_image'),
            $this->entityType,
            $userId,
            $oldImagePath
        );
    }

    /**
     * Get permission key for action
     */
    protected function getPermissionKey(string $action): string
    {
        return str_replace('/', '.', $this->parentRoutePath).$action;
    }

    /**
     * Get page title
     */
    protected function getPageTitle(string $suffix = ''): string
    {
        return $this->entityName.($suffix ? " $suffix" : '');
    }

    /**
     * Get entity variable name for views
     */
    protected function getEntityVariableName(): string
    {
        return strtolower($this->entityType);
    }

    /**
     * Get form data - to be implemented by child classes
     */
    abstract protected function getFormData($id = null): array;
}
