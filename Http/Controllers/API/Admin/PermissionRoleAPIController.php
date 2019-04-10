<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Requests\API\Admin\CreatePermissionRoleAPIRequest;
use App\Http\Requests\API\Admin\UpdatePermissionRoleAPIRequest;
use App\Models\Admin\PermissionRole;
use App\Repositories\Admin\PermissionRoleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PermissionRoleController
 * @package App\Http\Controllers\API\Admin
 */

class PermissionRoleAPIController extends AppBaseController
{
    /** @var  PermissionRoleRepository */
    private $permissionRoleRepository;

    public function __construct(PermissionRoleRepository $permissionRoleRepo)
    {
        $this->permissionRoleRepository = $permissionRoleRepo;
    }

    /**
     * Display a listing of the PermissionRole.
     * GET|HEAD /permissionRoles
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->permissionRoleRepository->pushCriteria(new RequestCriteria($request));
        $this->permissionRoleRepository->pushCriteria(new LimitOffsetCriteria($request));
        $permissionRoles = $this->permissionRoleRepository->all();

        return $this->sendResponse($permissionRoles->toArray(), 'Permission Roles retrieved successfully');
    }

    /**
     * Store a newly created PermissionRole in storage.
     * POST /permissionRoles
     *
     * @param CreatePermissionRoleAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePermissionRoleAPIRequest $request)
    {
        $input = $request->all();

        $permissionRoles = $this->permissionRoleRepository->create($input);

        return $this->sendResponse($permissionRoles->toArray(), 'Permission Role saved successfully');
    }

    /**
     * Display the specified PermissionRole.
     * GET|HEAD /permissionRoles/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PermissionRole $permissionRole */
        $permissionRole = $this->permissionRoleRepository->findWithoutFail($id);

        if (empty($permissionRole)) {
            return $this->sendError('Permission Role not found');
        }

        return $this->sendResponse($permissionRole->toArray(), 'Permission Role retrieved successfully');
    }

    /**
     * Update the specified PermissionRole in storage.
     * PUT/PATCH /permissionRoles/{id}
     *
     * @param  int $id
     * @param UpdatePermissionRoleAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePermissionRoleAPIRequest $request)
    {
        $input = $request->all();

        /** @var PermissionRole $permissionRole */
        $permissionRole = $this->permissionRoleRepository->findWithoutFail($id);

        if (empty($permissionRole)) {
            return $this->sendError('Permission Role not found');
        }

        $permissionRole = $this->permissionRoleRepository->update($input, $id);

        return $this->sendResponse($permissionRole->toArray(), 'PermissionRole updated successfully');
    }

    /**
     * Remove the specified PermissionRole from storage.
     * DELETE /permissionRoles/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PermissionRole $permissionRole */
        $permissionRole = $this->permissionRoleRepository->findWithoutFail($id);

        if (empty($permissionRole)) {
            return $this->sendError('Permission Role not found');
        }

        $permissionRole->delete();

        return $this->sendResponse($id, 'Permission Role deleted successfully');
    }
}
