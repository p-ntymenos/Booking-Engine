<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Requests\API\Admin\CreateAccountAPIRequest;
use App\Http\Requests\API\Admin\UpdateAccountAPIRequest;
use App\Models\Admin\Account;
use App\Repositories\Admin\AccountRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AccountController
 * @package App\Http\Controllers\API\Admin
 */

class AccountAPIController extends AppBaseController
{
    /** @var  AccountRepository */
    private $accountRepository;

    public function __construct(AccountRepository $accountRepo)
    {
        $this->accountRepository = $accountRepo;
    }

    /**
     * Display a listing of the Account.
     * GET|HEAD /accounts
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->accountRepository->pushCriteria(new RequestCriteria($request));
        $this->accountRepository->pushCriteria(new LimitOffsetCriteria($request));
        $accounts = $this->accountRepository->all();

        return $this->sendResponse($accounts->toArray(), 'Accounts retrieved successfully');
    }

    /**
     * Store a newly created Account in storage.
     * POST /accounts
     *
     * @param CreateAccountAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateAccountAPIRequest $request)
    {
        $input = $request->all();

        $accounts = $this->accountRepository->create($input);

        return $this->sendResponse($accounts->toArray(), 'Account saved successfully');
    }

    /**
     * Display the specified Account.
     * GET|HEAD /accounts/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Account $account */
        $account = $this->accountRepository->findWithoutFail($id);

        if (empty($account)) {
            return $this->sendError('Account not found');
        }

        return $this->sendResponse($account->toArray(), 'Account retrieved successfully');
    }

    /**
     * Update the specified Account in storage.
     * PUT/PATCH /accounts/{id}
     *
     * @param  int $id
     * @param UpdateAccountAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAccountAPIRequest $request)
    {
        $input = $request->all();

        /** @var Account $account */
        $account = $this->accountRepository->findWithoutFail($id);

        if (empty($account)) {
            return $this->sendError('Account not found');
        }

        $account = $this->accountRepository->update($input, $id);

        return $this->sendResponse($account->toArray(), 'Account updated successfully');
    }

    /**
     * Remove the specified Account from storage.
     * DELETE /accounts/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Account $account */
        $account = $this->accountRepository->findWithoutFail($id);

        if (empty($account)) {
            return $this->sendError('Account not found');
        }

        $account->delete();

        return $this->sendResponse($id, 'Account deleted successfully');
    }
}
