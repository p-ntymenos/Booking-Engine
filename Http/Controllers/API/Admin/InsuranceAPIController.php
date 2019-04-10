<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Requests\API\Admin\CreateInsuranceAPIRequest;
use App\Http\Requests\API\Admin\UpdateInsuranceAPIRequest;
use App\Models\Admin\Insurance;
use App\Repositories\Admin\InsuranceRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class InsuranceController
 * @package App\Http\Controllers\API\Admin
 */

class InsuranceAPIController extends AppBaseController
{
    /** @var  InsuranceRepository */
    private $insuranceRepository;

    public function __construct(InsuranceRepository $insuranceRepo)
    {
        $this->insuranceRepository = $insuranceRepo;
    }

    /**
     * Display a listing of the Insurance.
     * GET|HEAD /insurances
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->insuranceRepository->pushCriteria(new RequestCriteria($request));
        $this->insuranceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $insurances = $this->insuranceRepository->all();

        return $this->sendResponse($insurances->toArray(), 'Insurances retrieved successfully');
    }

    /**
     * Store a newly created Insurance in storage.
     * POST /insurances
     *
     * @param CreateInsuranceAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateInsuranceAPIRequest $request)
    {
        $input = $request->all();

        $insurances = $this->insuranceRepository->create($input);

        return $this->sendResponse($insurances->toArray(), 'Insurance saved successfully');
    }

    /**
     * Display the specified Insurance.
     * GET|HEAD /insurances/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Insurance $insurance */
        $insurance = $this->insuranceRepository->findWithoutFail($id);

        if (empty($insurance)) {
            return $this->sendError('Insurance not found');
        }

        return $this->sendResponse($insurance->toArray(), 'Insurance retrieved successfully');
    }

    /**
     * Update the specified Insurance in storage.
     * PUT/PATCH /insurances/{id}
     *
     * @param  int $id
     * @param UpdateInsuranceAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateInsuranceAPIRequest $request)
    {
        $input = $request->all();

        /** @var Insurance $insurance */
        $insurance = $this->insuranceRepository->findWithoutFail($id);

        if (empty($insurance)) {
            return $this->sendError('Insurance not found');
        }

        $insurance = $this->insuranceRepository->update($input, $id);

        return $this->sendResponse($insurance->toArray(), 'Insurance updated successfully');
    }

    /**
     * Remove the specified Insurance from storage.
     * DELETE /insurances/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Insurance $insurance */
        $insurance = $this->insuranceRepository->findWithoutFail($id);

        if (empty($insurance)) {
            return $this->sendError('Insurance not found');
        }

        $insurance->delete();

        return $this->sendResponse($id, 'Insurance deleted successfully');
    }
}
