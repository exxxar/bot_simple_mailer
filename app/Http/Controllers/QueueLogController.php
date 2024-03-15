<?php

namespace App\Http\Controllers;

use App\Http\Requests\QueueLogStoreRequest;
use App\Http\Requests\QueueLogUpdateRequest;
use App\Http\Resources\QueueLogCollection;
use App\Http\Resources\QueueLogResource;
use App\Models\QueueLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QueueLogController extends Controller
{
    public function index(Request $request): Response
    {
        $queueLogs = QueueLog::all();

        return new QueueLogCollection($queueLogs);
    }

    public function store(QueueLogStoreRequest $request): Response
    {
        $queueLog = QueueLog::create($request->validated());

        return new QueueLogResource($queueLog);
    }

    public function show(Request $request, QueueLog $queueLog): Response
    {
        return new QueueLogResource($queueLog);
    }

    public function update(QueueLogUpdateRequest $request, QueueLog $queueLog): Response
    {
        $queueLog->update($request->validated());

        return new QueueLogResource($queueLog);
    }

    public function destroy(Request $request, QueueLog $queueLog): Response
    {
        $queueLog->delete();

        return response()->noContent();
    }
}
