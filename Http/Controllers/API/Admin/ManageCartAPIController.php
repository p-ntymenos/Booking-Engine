<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Requests\API\Admin\CreateManageCartAPIRequest;
use App\Http\Requests\API\Admin\UpdateManageCartAPIRequest;
use App\Models\Admin\ManageCart;
use App\Repositories\Admin\ManageCartRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ManageCartController
 * @package App\Http\Controllers\API\Admin
 */

class ManageCartAPIController extends AppBaseController
{
    /** @var  ManageCartRepository */
    private $manageCartRepository;

    public function __construct(ManageCartRepository $manageCartRepo)
    {
        $this->manageCartRepository = $manageCartRepo;
    }

    /**
     * Display a listing of the ManageCart.
     * GET|HEAD /manageCarts
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->manageCartRepository->pushCriteria(new RequestCriteria($request));
        $this->manageCartRepository->pushCriteria(new LimitOffsetCriteria($request));
        $manageCarts = $this->manageCartRepository->all();

        return $this->sendResponse($manageCarts->toArray(), 'Manage Carts retrieved successfully');
    }

    /**
     * Store a newly created ManageCart in storage.
     * POST /manageCarts
     *
     * @param CreateManageCartAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateManageCartAPIRequest $request)
    {
        $input = $request->all();

        $manageCarts = $this->manageCartRepository->create($input);

        return $this->sendResponse($manageCarts->toArray(), 'Manage Cart saved successfully');
    }

    /**
     * Display the specified ManageCart.
     * GET|HEAD /manageCarts/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ManageCart $manageCart */
        $manageCart = $this->manageCartRepository->findWithoutFail($id);

        if (empty($manageCart)) {
            return $this->sendError('Manage Cart not found');
        }

        return $this->sendResponse($manageCart->toArray(), 'Manage Cart retrieved successfully');
    }

    /**
     * Update the specified ManageCart in storage.
     * PUT/PATCH /manageCarts/{id}
     *
     * @param  int $id
     * @param UpdateManageCartAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateManageCartAPIRequest $request)
    {
        $input = $request->all();

        /** @var ManageCart $manageCart */
        $manageCart = $this->manageCartRepository->findWithoutFail($id);

        if (empty($manageCart)) {
            return $this->sendError('Manage Cart not found');
        }

        $manageCart = $this->manageCartRepository->update($input, $id);

        return $this->sendResponse($manageCart->toArray(), 'ManageCart updated successfully');
    }

    /**
     * Remove the specified ManageCart from storage.
     * DELETE /manageCarts/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ManageCart $manageCart */
        $manageCart = $this->manageCartRepository->findWithoutFail($id);

        if (empty($manageCart)) {
            return $this->sendError('Manage Cart not found');
        }

        $manageCart->delete();

        return $this->sendResponse($id, 'Manage Cart deleted successfully');
    }
}
