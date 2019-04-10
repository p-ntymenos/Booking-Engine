<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\InsuranceDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateInsuranceRequest;
use App\Http\Requests\Admin\UpdateInsuranceRequest;
use App\Repositories\Admin\InsuranceRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class InsuranceController extends AppBaseController
{
    /** @var  InsuranceRepository */
    private $insuranceRepository;

    public function __construct(InsuranceRepository $insuranceRepo)
    {
        $this->insuranceRepository = $insuranceRepo;
    }

    /**
     * Display a listing of the Insurance.
     *
     * @param InsuranceDataTable $insuranceDataTable
     * @return Response
     */
    public function index(InsuranceDataTable $insuranceDataTable)
    {
        return $insuranceDataTable->render('admin.insurances.index');
    }

    /**
     * Show the form for creating a new Insurance.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.insurances.create');
    }

    /**
     * Store a newly created Insurance in storage.
     *
     * @param CreateInsuranceRequest $request
     *
     * @return Response
     */
    public function store(CreateInsuranceRequest $request)
    {
        $input = $request->all();

        $insurance = $this->insuranceRepository->create($input);

        Flash::success('Insurance saved successfully.');

        return redirect(route('admin.insurances.index'));
    }

    /**
     * Display the specified Insurance.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $insurance = $this->insuranceRepository->findWithoutFail($id);

        if (empty($insurance)) {
            Flash::error('Insurance not found');

            return redirect(route('admin.insurances.index'));
        }

        return view('admin.insurances.show')->with('insurance', $insurance);
    }

    /**
     * Show the form for editing the specified Insurance.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $insurance = $this->insuranceRepository->findWithoutFail($id);

        if (empty($insurance)) {
            Flash::error('Insurance not found');

            return redirect(route('admin.insurances.index'));
        }

        return view('admin.insurances.edit')->with('insurance', $insurance);
    }

    /**
     * Update the specified Insurance in storage.
     *
     * @param  int              $id
     * @param UpdateInsuranceRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateInsuranceRequest $request)
    {
        $insurance = $this->insuranceRepository->findWithoutFail($id);

        if (empty($insurance)) {
            Flash::error('Insurance not found');

            return redirect(route('admin.insurances.index'));
        }

        $insurance = $this->insuranceRepository->update($request->all(), $id);

        Flash::success('Insurance updated successfully.');

        return redirect(route('admin.insurances.index'));
    }

    /**
     * Remove the specified Insurance from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $insurance = $this->insuranceRepository->findWithoutFail($id);

        if (empty($insurance)) {
            Flash::error('Insurance not found');

            return redirect(route('admin.insurances.index'));
        }

        $this->insuranceRepository->delete($id);

        Flash::success('Insurance deleted successfully.');

        return redirect(route('admin.insurances.index'));
    }
}
