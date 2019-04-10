<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\UserSessionDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateUserSessionRequest;
use App\Http\Requests\Admin\UpdateUserSessionRequest;
use App\Repositories\Admin\UserSessionRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class UserSessionController extends AppBaseController
{
    /** @var  UserSessionRepository */
    private $userSessionRepository;

    public function __construct(UserSessionRepository $userSessionRepo)
    {
        $this->userSessionRepository = $userSessionRepo;
    }

    /**
     * Display a listing of the UserSession.
     *
     * @param UserSessionDataTable $userSessionDataTable
     * @return Response
     */
    public function index(UserSessionDataTable $userSessionDataTable)
    {
        return $userSessionDataTable->render('admin.user_sessions.index');
    }

    /**
     * Show the form for creating a new UserSession.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.user_sessions.create');
    }

    /**
     * Store a newly created UserSession in storage.
     *
     * @param CreateUserSessionRequest $request
     *
     * @return Response
     */
    public function store(CreateUserSessionRequest $request)
    {
        $input = $request->all();

        $userSession = $this->userSessionRepository->create($input);

        Flash::success('User Session saved successfully.');

        return redirect(route('admin.userSessions.index'));
    }

    /**
     * Display the specified UserSession.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $userSession = $this->userSessionRepository->findWithoutFail($id);

        if (empty($userSession)) {
            Flash::error('User Session not found');

            return redirect(route('admin.userSessions.index'));
        }

        return view('admin.user_sessions.show')->with('userSession', $userSession);
    }

    /**
     * Show the form for editing the specified UserSession.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $userSession = $this->userSessionRepository->findWithoutFail($id);

        if (empty($userSession)) {
            Flash::error('User Session not found');

            return redirect(route('admin.userSessions.index'));
        }

        return view('admin.user_sessions.edit')->with('userSession', $userSession);
    }

    /**
     * Update the specified UserSession in storage.
     *
     * @param  int              $id
     * @param UpdateUserSessionRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserSessionRequest $request)
    {
        $userSession = $this->userSessionRepository->findWithoutFail($id);

        if (empty($userSession)) {
            Flash::error('User Session not found');

            return redirect(route('admin.userSessions.index'));
        }

        $userSession = $this->userSessionRepository->update($request->all(), $id);

        Flash::success('User Session updated successfully.');

        return redirect(route('admin.userSessions.index'));
    }

    /**
     * Remove the specified UserSession from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $userSession = $this->userSessionRepository->findWithoutFail($id);

        if (empty($userSession)) {
            Flash::error('User Session not found');

            return redirect(route('admin.userSessions.index'));
        }

        $this->userSessionRepository->delete($id);

        Flash::success('User Session deleted successfully.');

        return redirect(route('admin.userSessions.index'));
    }
}
