<?php

namespace App\Http\Controllers\Admin\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use App\DataTables\Admin\Management\SubjectDataTable;
use App\Repositories\Interfaces\Admin\Management\SubjectRepositoryInterface;
use App\Enums\Status;

class SubjectController extends Controller
{
    protected SubjectRepositoryInterface $repository;
    protected $parentViewPath = 'admin.pages.management.subjects.';
    protected $parentRoutePath = 'admin.management.subjects.';

    public function __construct(SubjectRepositoryInterface $repository)
    {
        $this->middleware('auth');
        $this->repository = $repository;
    }

    public function index(SubjectDataTable $datatable)
    {
        checkPermissionAndRedirect('admin.management.subjects.index');
        Session::put('title', 'Subject Management');
        return $datatable->render($this->parentViewPath . 'index');
    }

    public function form($id = null)
    {
        checkPermissionAndRedirect('admin.management.subjects.' . ($id ? 'edit' : 'form'));
        Session::put('title', ($id ? 'Update' : 'Create') . ' Subject');

        if ($id) {
            $subject = $this->repository->getWithRelations($id);
            if (!$subject) {
                flashResponse('Subject not found.', 'danger');
                return Redirect::route($this->parentRoutePath . 'index');
            }
            return view($this->parentViewPath . 'form', compact('subject', 'id'));
        }

        $subject = null;
        return view($this->parentViewPath . 'form', compact('id'));
    }

    public function enroll(Request $request)
    {
        $id = $request->input('id');
        checkPermissionAndRedirect('admin.management.subjects.' . ($id ? 'edit' : 'form'));

        if ($request->has('id') && $request->filled('id')) {
            return $this->update($request);
        }

        $rules = [
            'subject_name' => 'required|min:2|max:100',
            'grade_level' => 'required|integer|min:1|max:13',
            'description' => 'nullable|max:1000',
            'credits' => 'required|integer|min:1|max:10',
            'type' => 'required|in:Core,Elective,Optional',
            'status' => 'required|in:1,2,3',
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $subject = $this->repository->create($request->all());

            DB::commit();

            flashResponse('Subject created successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            flashResponse('Failed to create Subject. Please try again.', 'danger');
        }

        return redirect()->route($this->parentRoutePath . 'index');
    }

    public function show(string $id)
    {
        checkPermissionAndRedirect('admin.management.subjects.show');
        $subject = $this->repository->getWithRelations($id);

        if (!$subject) {
            flashResponse('Subject not found.', 'danger');
            return Redirect::back();
        }

        return view($this->parentViewPath . 'view', compact('subject'));
    }

    public function update(Request $request)
    {
        $id = $request->input('id');

        $rules = [
            'subject_name' => 'required|min:2|max:100',
            'grade_level' => 'required|integer|min:1|max:13',
            'description' => 'nullable|max:1000',
            'credits' => 'required|integer|min:1|max:10',
            'type' => 'required|in:Core,Elective,Optional',
            'status' => 'required|in:1,2,3',
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $subject = $this->repository->getById($id);
            if (!$subject) {
                flashResponse('Subject not found.', 'danger');
                return Redirect::route($this->parentRoutePath . 'index');
            }

            $this->repository->update($id, $request->all());

            DB::commit();

            flashResponse('Subject updated successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            flashResponse('Failed to update Subject. Please try again.', 'danger');
        }

        return redirect()->route($this->parentRoutePath . 'index');
    }

    public function delete(string $id)
    {
        checkPermissionAndRedirect('admin.management.subjects.delete');

        try {
            DB::beginTransaction();

            $subject = $this->repository->getById($id);
            if (!$subject) {
                flashResponse('Subject not found.', 'danger');
                return Redirect::back();
            }

            $this->repository->delete($id);

            DB::commit();

            flashResponse('Subject deleted successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            flashResponse('Failed to delete Subject. Please try again.', 'danger');
        }

        return redirect()->route($this->parentRoutePath . 'index');
    }
}
