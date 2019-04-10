<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\LogsDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateLogsRequest;
use App\Http\Requests\Admin\UpdateLogsRequest;
use App\Repositories\Admin\LogsRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class LogsController extends AppBaseController
{
    /** @var  LogsRepository */
    private $logsRepository;

    public function __construct(LogsRepository $logsRepo)
    {
        $this->logsRepository = $logsRepo;
    }

    /**
     * Display a listing of the Logs.
     *
     * @param LogsDataTable $logsDataTable
     * @return Response
     */
    public function index(LogsDataTable $logsDataTable)
    {
        return $logsDataTable->render('admin.logs.index');
    }

    /**
     * Show the form for creating a new Logs.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.logs.create');
    }

    /**
     * Store a newly created Logs in storage.
     *
     * @param CreateLogsRequest $request
     *
     * @return Response
     */
    public function store(CreateLogsRequest $request)
    {
        $input = $request->all();

        $logs = $this->logsRepository->create($input);

        Flash::success('Logs saved successfully.');

        return redirect(route('admin.logs.index'));
    }

    /**
     * Display the specified Logs.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $logs = $this->logsRepository->findWithoutFail($id);

        if (empty($logs)) {
            Flash::error('Logs not found');

            return redirect(route('admin.logs.index'));
        }

        return view('admin.logs.show')->with('logs', $logs);
    }

    /**
     * Show the form for editing the specified Logs.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $logs = $this->logsRepository->findWithoutFail($id);

        if (empty($logs)) {
            Flash::error('Logs not found');

            return redirect(route('admin.logs.index'));
        }

        return view('admin.logs.edit')->with('logs', $logs);
    }

    /**
     * Update the specified Logs in storage.
     *
     * @param  int              $id
     * @param UpdateLogsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLogsRequest $request)
    {
        $logs = $this->logsRepository->findWithoutFail($id);

        if (empty($logs)) {
            Flash::error('Logs not found');

            return redirect(route('admin.logs.index'));
        }

        $logs = $this->logsRepository->update($request->all(), $id);

        Flash::success('Logs updated successfully.');

        return redirect(route('admin.logs.index'));
    }

    /**
     * Remove the specified Logs from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $logs = $this->logsRepository->findWithoutFail($id);

        if (empty($logs)) {
            Flash::error('Logs not found');

            return redirect(route('admin.logs.index'));
        }

        $this->logsRepository->delete($id);

        Flash::success('Logs deleted successfully.');

        return redirect(route('admin.logs.index'));
    }
}
