<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Requests\API\Admin\CreateLogsAPIRequest;
use App\Http\Requests\API\Admin\UpdateLogsAPIRequest;
use App\Models\Admin\Logs;
use App\Repositories\Admin\LogsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LogsController
 * @package App\Http\Controllers\API\Admin
 */

class LogsAPIController extends AppBaseController
{
    /** @var  LogsRepository */
    private $logsRepository;

    public function __construct(LogsRepository $logsRepo)
    {
        $this->logsRepository = $logsRepo;
    }

    /**
     * Display a listing of the Logs.
     * GET|HEAD /logs
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->logsRepository->pushCriteria(new RequestCriteria($request));
        $this->logsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $logs = $this->logsRepository->all();

        return $this->sendResponse($logs->toArray(), 'Logs retrieved successfully');
    }

    /**
     * Store a newly created Logs in storage.
     * POST /logs
     *
     * @param CreateLogsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateLogsAPIRequest $request)
    {
        $input = $request->all();

        $logs = $this->logsRepository->create($input);

        return $this->sendResponse($logs->toArray(), 'Logs saved successfully');
    }

    /**
     * Display the specified Logs.
     * GET|HEAD /logs/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Logs $logs */
        $logs = $this->logsRepository->findWithoutFail($id);

        if (empty($logs)) {
            return $this->sendError('Logs not found');
        }

        return $this->sendResponse($logs->toArray(), 'Logs retrieved successfully');
    }

    /**
     * Update the specified Logs in storage.
     * PUT/PATCH /logs/{id}
     *
     * @param  int $id
     * @param UpdateLogsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLogsAPIRequest $request)
    {
        $input = $request->all();

        /** @var Logs $logs */
        $logs = $this->logsRepository->findWithoutFail($id);

        if (empty($logs)) {
            return $this->sendError('Logs not found');
        }

        $logs = $this->logsRepository->update($input, $id);

        return $this->sendResponse($logs->toArray(), 'Logs updated successfully');
    }

    /**
     * Remove the specified Logs from storage.
     * DELETE /logs/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Logs $logs */
        $logs = $this->logsRepository->findWithoutFail($id);

        if (empty($logs)) {
            return $this->sendError('Logs not found');
        }

        $logs->delete();

        return $this->sendResponse($id, 'Logs deleted successfully');
    }
}
