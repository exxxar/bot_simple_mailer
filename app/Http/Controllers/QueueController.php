<?php

namespace App\Http\Controllers;

use App\Http\Requests\QueueStoreRequest;
use App\Http\Requests\QueueUpdateRequest;
use App\Http\Resources\QueueCollection;
use App\Http\Resources\QueueResource;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QueueController extends Controller
{
    public function index(Request $request): Response
    {
        $queues = Queue::all();

        return new QueueCollection($queues);
    }

    public function store(QueueStoreRequest $request): Response
    {
        $queue = Queue::create($request->validated());

        return new QueueResource($queue);
    }

    public function show(Request $request, Queue $queue): Response
    {
        return new QueueResource($queue);
    }

    public function update(QueueUpdateRequest $request, Queue $queue): Response
    {
        $queue->update($request->validated());

        return new QueueResource($queue);
    }

    public function destroy(Request $request, Queue $queue): Response
    {
        $queue->delete();

        return response()->noContent();
    }
}
