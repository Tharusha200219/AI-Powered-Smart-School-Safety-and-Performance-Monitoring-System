<?php

namespace App\Http\Controllers\Admin\Management;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\DataTables\Admin\Management\ParentDataTable;
use App\Repositories\Interfaces\Admin\Management\ParentRepositoryInterface;

class ParentController extends Controller
{
    protected ParentRepositoryInterface $repository;
    protected $parentViewPath = 'admin.pages.management.parents.';
    protected $parentRoutePath = 'admin.management.parents.';

    public function __construct(ParentRepositoryInterface $repository)
    {
        $this->middleware('auth');
        $this->repository = $repository;
    }

    public function index(ParentDataTable $datatable)
    {
        checkPermissionAndRedirect('admin.management.parents.index');
        Session::put('title', 'Parent Management');
        return $datatable->render($this->parentViewPath . 'index');
    }

    public function show(string $id)
    {
        checkPermissionAndRedirect('admin.management.parents.show');
        $parent = $this->repository->getWithRelations($id);

        if (!$parent) {
            flashResponse('Parent not found.', 'danger');
            return Redirect::back();
        }

        return view($this->parentViewPath . 'view', compact('parent'));
    }
}
