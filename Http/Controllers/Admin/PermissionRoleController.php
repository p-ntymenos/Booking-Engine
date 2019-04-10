<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\PermissionRoleDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreatePermissionRoleRequest;
use App\Http\Requests\Admin\UpdatePermissionRoleRequest;
use App\Repositories\Admin\PermissionRoleRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class PermissionRoleController extends AppBaseController
{
    /** @var  PermissionRoleRepository */
    private $permissionRoleRepository;

    public function __construct(PermissionRoleRepository $permissionRoleRepo)
    {
        $this->permissionRoleRepository = $permissionRoleRepo;
    }

    /**
     * Display a listing of the PermissionRole.
     *
     * @param PermissionRoleDataTable $permissionRoleDataTable
     * @return Response
     */
    public function index(PermissionRoleDataTable $permissionRoleDataTable)
    {
        return $permissionRoleDataTable->render('admin.permission_roles.index');
    }

    /**
     * Show the form for creating a new PermissionRole.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.permission_roles.create');
    }

    /**
     * Store a newly created PermissionRole in storage.
     *
     * @param CreatePermissionRoleRequest $request
     *
     * @return Response
     */
    public function store(CreatePermissionRoleRequest $request)
    {
        $input = $request->all();

        $permissionRole = $this->permissionRoleRepository->create($input);

        Flash::success('Permission Role saved successfully.');

        return redirect(route('admin.permissionRoles.index'));
    }

    /**
     * Display the specified PermissionRole.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $permissionRole = $this->permissionRoleRepository->findWithoutFail($id);

        if (empty($permissionRole)) {
            Flash::error('Permission Role not found');

            return redirect(route('admin.permissionRoles.index'));
        }

        return view('admin.permission_roles.show')->with('permissionRole', $permissionRole);
    }

    /**
     * Show the form for editing the specified PermissionRole.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $permissionRole = $this->permissionRoleRepository->findWithoutFail($id);

        if (empty($permissionRole)) {
            Flash::error('Permission Role not found');

            return redirect(route('admin.permissionRoles.index'));
        }

        return view('admin.permission_roles.edit')->with('permissionRole', $permissionRole);
    }

    /**
     * Update the specified PermissionRole in storage.
     *
     * @param  int              $id
     * @param UpdatePermissionRoleRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePermissionRoleRequest $request)
    {
        $permissionRole = $this->permissionRoleRepository->findWithoutFail($id);

        if (empty($permissionRole)) {
            Flash::error('Permission Role not found');

            return redirect(route('admin.permissionRoles.index'));
        }

        $permissionRole = $this->permissionRoleRepository->update($request->all(), $id);

        Flash::success('Permission Role updated successfully.');

        return redirect(route('admin.permissionRoles.index'));
    }

    /**
     * Remove the specified PermissionRole from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $permissionRole = $this->permissionRoleRepository->findWithoutFail($id);

        if (empty($permissionRole)) {
            Flash::error('Permission Role not found');

            return redirect(route('admin.permissionRoles.index'));
        }

        $this->permissionRoleRepository->delete($id);

        Flash::success('Permission Role deleted successfully.');

        return redirect(route('admin.permissionRoles.index'));
    }
}
