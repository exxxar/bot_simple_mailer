<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Bot;
use App\Models\Queue;

class QueueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Queue::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'bot_id' => Bot::factory(),
            'content' => $this->faker->paragraphs(3, true),
            'reply_keyboard' => '{}',
            'inline_keyboard' => '{}',
            'images' => '{}',
            'sent_at' => $this->faker->dateTime(),
        ];
    }
}
