<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Bot;
use App\Models\BotUser;
use App\Models\Queue;
use App\Models\QueueLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\QueueLogController
 */
final class QueueLogControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $queueLogs = QueueLog::factory()->count(3)->create();

        $response = $this->get(route('queue-logs.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\QueueLogController::class,
            'store',
            \App\Http\Requests\QueueLogStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $bot = Bot::factory()->create();
        $bot_user = BotUser::factory()->create();
        $queue = Queue::factory()->create();

        $response = $this->post(route('queue-logs.store'), [
            'bot_id' => $bot->id,
            'bot_user_id' => $bot_user->id,
            'queue_id' => $queue->id,
        ]);

        $queueLogs = QueueLog::query()
            ->where('bot_id', $bot->id)
            ->where('bot_user_id', $bot_user->id)
            ->where('queue_id', $queue->id)
            ->get();
        $this->assertCount(1, $queueLogs);
        $queueLog = $queueLogs->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $queueLog = QueueLog::factory()->create();

        $response = $this->get(route('queue-logs.show', $queueLog));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\QueueLogController::class,
            'update',
            \App\Http\Requests\QueueLogUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $queueLog = QueueLog::factory()->create();
        $bot = Bot::factory()->create();
        $bot_user = BotUser::factory()->create();
        $queue = Queue::factory()->create();

        $response = $this->put(route('queue-logs.update', $queueLog), [
            'bot_id' => $bot->id,
            'bot_user_id' => $bot_user->id,
            'queue_id' => $queue->id,
        ]);

        $queueLog->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($bot->id, $queueLog->bot_id);
        $this->assertEquals($bot_user->id, $queueLog->bot_user_id);
        $this->assertEquals($queue->id, $queueLog->queue_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $queueLog = QueueLog::factory()->create();

        $response = $this->delete(route('queue-logs.destroy', $queueLog));

        $response->assertNoContent();

        $this->assertModelMissing($queueLog);
    }
}
