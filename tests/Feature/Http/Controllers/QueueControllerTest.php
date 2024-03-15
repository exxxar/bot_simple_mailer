<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Bot;
use App\Models\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\QueueController
 */
final class QueueControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $queues = Queue::factory()->count(3)->create();

        $response = $this->get(route('queues.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\QueueController::class,
            'store',
            \App\Http\Requests\QueueStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $bot = Bot::factory()->create();
        $content = $this->faker->paragraphs(3, true);

        $response = $this->post(route('queues.store'), [
            'bot_id' => $bot->id,
            'content' => $content,
        ]);

        $queues = Queue::query()
            ->where('bot_id', $bot->id)
            ->where('content', $content)
            ->get();
        $this->assertCount(1, $queues);
        $queue = $queues->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $queue = Queue::factory()->create();

        $response = $this->get(route('queues.show', $queue));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\QueueController::class,
            'update',
            \App\Http\Requests\QueueUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $queue = Queue::factory()->create();
        $bot = Bot::factory()->create();
        $content = $this->faker->paragraphs(3, true);

        $response = $this->put(route('queues.update', $queue), [
            'bot_id' => $bot->id,
            'content' => $content,
        ]);

        $queue->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($bot->id, $queue->bot_id);
        $this->assertEquals($content, $queue->content);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $queue = Queue::factory()->create();

        $response = $this->delete(route('queues.destroy', $queue));

        $response->assertNoContent();

        $this->assertModelMissing($queue);
    }
}
