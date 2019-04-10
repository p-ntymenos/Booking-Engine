<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\CartDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateCartRequest;
use App\Http\Requests\Admin\UpdateCartRequest;
use App\Repositories\Admin\CartRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class CartController extends AppBaseController
{
    /** @var  CartRepository */
    private $cartRepository;

    public function __construct(CartRepository $cartRepo)
    {
        $this->cartRepository = $cartRepo;
    }

    /**
     * Display a listing of the Cart.
     *
     * @param CartDataTable $cartDataTable
     * @return Response
     */
    public function index(CartDataTable $cartDataTable)
    {
        return $cartDataTable->render('admin.carts.index');
    }

    /**
     * Show the form for creating a new Cart.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.carts.create');
    }

    /**
     * Store a newly created Cart in storage.
     *
     * @param CreateCartRequest $request
     *
     * @return Response
     */
    public function store(CreateCartRequest $request)
    {
        $input = $request->all();

        $cart = $this->cartRepository->create($input);

        Flash::success('Cart saved successfully.');

        return redirect(route('admin.carts.index'));
    }

    /**
     * Display the specified Cart.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $cart = $this->cartRepository->findWithoutFail($id);

        if (empty($cart)) {
            Flash::error('Cart not found');

            return redirect(route('admin.carts.index'));
        }

        return view('admin.carts.show')->with('cart', $cart);
    }

    /**
     * Show the form for editing the specified Cart.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $cart = $this->cartRepository->findWithoutFail($id);

        if (empty($cart)) {
            Flash::error('Cart not found');

            return redirect(route('admin.carts.index'));
        }

        return view('admin.carts.edit')->with('cart', $cart);
    }

    /**
     * Update the specified Cart in storage.
     *
     * @param  int              $id
     * @param UpdateCartRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCartRequest $request)
    {
        $cart = $this->cartRepository->findWithoutFail($id);

        if (empty($cart)) {
            Flash::error('Cart not found');

            return redirect(route('admin.carts.index'));
        }
        // echo "<pre>";
        // print_r( json_decode($request->input('Adults')) );
        // echo "</pre>";
        $request->merge( ['Adults' => json_decode($request->input('Adults')) ] );
        $request->merge( ['Children' => json_decode($request->input('Children')) ] );
        // echo "<pre>";
        // print_r( $request->all() );
        // echo "</pre>";
        // exit;
        $cart = $this->cartRepository->update($request->all(), $id);

        Flash::success('Cart updated successfully.');

        return redirect(route('admin.carts.index'));
    }

    /**
     * Remove the specified Cart from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $cart = $this->cartRepository->findWithoutFail($id);

        if (empty($cart)) {
            Flash::error('Cart not found');

            return redirect(route('admin.carts.index'));
        }

        $this->cartRepository->delete($id);

        Flash::success('Cart deleted successfully.');

        return redirect(route('admin.carts.index'));
    }
}
