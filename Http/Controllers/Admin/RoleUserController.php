<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\RoleUserDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateRoleUserRequest;
use App\Http\Requests\Admin\UpdateRoleUserRequest;
use App\Repositories\Admin\RoleUserRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class RoleUserController extends AppBaseController
{
    /** @var  RoleUserRepository */
    private $roleUserRepository;

    public function __construct(RoleUserRepository $roleUserRepo)
    {
        $this->roleUserRepository = $roleUserRepo;
    }

    /**
     * Display a listing of the RoleUser.
     *
     * @param RoleUserDataTable $roleUserDataTable
     * @return Response
     */
    public function index(RoleUserDataTable $roleUserDataTable)
    {
        return $roleUserDataTable->render('admin.role_users.index');
    }

    /**
     * Show the form for creating a new RoleUser.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.role_users.create');
    }

    /**
     * Store a newly created RoleUser in storage.
     *
     * @param CreateRoleUserRequest $request
     *
     * @return Response
     */
    public function store(CreateRoleUserRequest $request)
    {
        $input = $request->all();

        $roleUser = $this->roleUserRepository->create($input);

        Flash::success('Role User saved successfully.');

        return redirect(route('admin.roleUsers.index'));
    }

    /**
     * Display the specified RoleUser.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $roleUser = $this->roleUserRepository->findWithoutFail($id);

        if (empty($roleUser)) {
            Flash::error('Role User not found');

            return redirect(route('admin.roleUsers.index'));
        }

        return view('admin.role_users.show')->with('roleUser', $roleUser);
    }

    /**
     * Show the form for editing the specified RoleUser.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $roleUser = $this->roleUserRepository->findWithoutFail($id);

        if (empty($roleUser)) {
            Flash::error('Role User not found');

            return redirect(route('admin.roleUsers.index'));
        }

        return view('admin.role_users.edit')->with('roleUser', $roleUser);
    }

    /**
     * Update the specified RoleUser in storage.
     *
     * @param  int              $id
     * @param UpdateRoleUserRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRoleUserRequest $request)
    {
        $roleUser = $this->roleUserRepository->findWithoutFail($id);

        if (empty($roleUser)) {
            Flash::error('Role User not found');

            return redirect(route('admin.roleUsers.index'));
        }

        $roleUser = $this->roleUserRepository->update($request->all(), $id);

        Flash::success('Role User updated successfully.');

        return redirect(route('admin.roleUsers.index'));
    }

    /**
     * Remove the specified RoleUser from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $roleUser = $this->roleUserRepository->findWithoutFail($id);

        if (empty($roleUser)) {
            Flash::error('Role User not found');

            return redirect(route('admin.roleUsers.index'));
        }

        $this->roleUserRepository->delete($id);

        Flash::success('Role User deleted successfully.');

        return redirect(route('admin.roleUsers.index'));
    }
}
