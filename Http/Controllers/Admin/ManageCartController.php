<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\ManageCartDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateManageCartRequest;
use App\Http\Requests\Admin\UpdateManageCartRequest;
use App\Repositories\Admin\ManageCartRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class ManageCartController extends AppBaseController
{
    /** @var  ManageCartRepository */
    private $manageCartRepository;

    public function __construct(ManageCartRepository $manageCartRepo)
    {
        $this->manageCartRepository = $manageCartRepo;
    }

    /**
     * Display a listing of the ManageCart.
     *
     * @param ManageCartDataTable $manageCartDataTable
     * @return Response
     */
    public function index(ManageCartDataTable $manageCartDataTable)
    {
        return $manageCartDataTable->render('admin.manage_carts.index');
    }

    /**
     * Show the form for creating a new ManageCart.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.manage_carts.create');
    }

    /**
     * Store a newly created ManageCart in storage.
     *
     * @param CreateManageCartRequest $request
     *
     * @return Response
     */
    public function store(CreateManageCartRequest $request)
    {
        $input = $request->all();

        $manageCart = $this->manageCartRepository->create($input);

        Flash::success('Manage Cart saved successfully.');

        return redirect(route('admin.manageCarts.index'));
    }

    /**
     * Display the specified ManageCart.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $manageCart = $this->manageCartRepository->findWithoutFail($id);

        if (empty($manageCart)) {
            Flash::error('Manage Cart not found');

            return redirect(route('admin.manageCarts.index'));
        }

        return view('admin.manage_carts.show')->with('manageCart', $manageCart);
    }

    /**
     * Show the form for editing the specified ManageCart.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $manageCart = $this->manageCartRepository->findWithoutFail($id);

        if (empty($manageCart)) {
            Flash::error('Manage Cart not found');

            return redirect(route('admin.manageCarts.index'));
        }

        return view('admin.manage_carts.edit')->with('manageCart', $manageCart);
    }

    /**
     * Update the specified ManageCart in storage.
     *
     * @param  int              $id
     * @param UpdateManageCartRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateManageCartRequest $request)
    {
        $manageCart = $this->manageCartRepository->findWithoutFail($id);

        if (empty($manageCart)) {
            Flash::error('Manage Cart not found');

            return redirect(route('admin.manageCarts.index'));
        }

        $manageCart = $this->manageCartRepository->update($request->all(), $id);

        Flash::success('Manage Cart updated successfully.');

        return redirect(route('admin.manageCarts.index'));
    }

    /**
     * Remove the specified ManageCart from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $manageCart = $this->manageCartRepository->findWithoutFail($id);

        if (empty($manageCart)) {
            Flash::error('Manage Cart not found');

            return redirect(route('admin.manageCarts.index'));
        }

        $this->manageCartRepository->delete($id);

        Flash::success('Manage Cart deleted successfully.');

        return redirect(route('admin.manageCarts.index'));
    }
}
