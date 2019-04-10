<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Requests\API\Admin\CreateUserSessionAPIRequest;
use App\Http\Requests\API\Admin\UpdateUserSessionAPIRequest;
use App\Models\Admin\UserSession;
use App\Repositories\Admin\UserSessionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class UserSessionController
 * @package App\Http\Controllers\API\Admin
 */

class UserSessionAPIController extends AppBaseController
{
    /** @var  UserSessionRepository */
    private $userSessionRepository;

    public function __construct(UserSessionRepository $userSessionRepo)
    {
        $this->userSessionRepository = $userSessionRepo;
    }

    /**
     * Display a listing of the UserSession.
     * GET|HEAD /userSessions
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->userSessionRepository->pushCriteria(new RequestCriteria($request));
        $this->userSessionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $userSessions = $this->userSessionRepository->all();

        return $this->sendResponse($userSessions->toArray(), 'User Sessions retrieved successfully');
    }

    /**
     * Store a newly created UserSession in storage.
     * POST /userSessions
     *
     * @param CreateUserSessionAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateUserSessionAPIRequest $request)
    {
        $input = $request->all();

        $userSessions = $this->userSessionRepository->create($input);

        return $this->sendResponse($userSessions->toArray(), 'User Session saved successfully');
    }

    /**
     * Display the specified UserSession.
     * GET|HEAD /userSessions/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var UserSession $userSession */
        $userSession = $this->userSessionRepository->findWithoutFail($id);

        if (empty($userSession)) {
            return $this->sendError('User Session not found');
        }

        return $this->sendResponse($userSession->toArray(), 'User Session retrieved successfully');
    }

    /**
     * Update the specified UserSession in storage.
     * PUT/PATCH /userSessions/{id}
     *
     * @param  int $id
     * @param UpdateUserSessionAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserSessionAPIRequest $request)
    {
        $input = $request->all();

        /** @var UserSession $userSession */
        $userSession = $this->userSessionRepository->findWithoutFail($id);

        if (empty($userSession)) {
            return $this->sendError('User Session not found');
        }

        $userSession = $this->userSessionRepository->update($input, $id);

        return $this->sendResponse($userSession->toArray(), 'UserSession updated successfully');
    }

    /**
     * Remove the specified UserSession from storage.
     * DELETE /userSessions/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var UserSession $userSession */
        $userSession = $this->userSessionRepository->findWithoutFail($id);

        if (empty($userSession)) {
            return $this->sendError('User Session not found');
        }

        $userSession->delete();

        return $this->sendResponse($id, 'User Session deleted successfully');
    }
}
