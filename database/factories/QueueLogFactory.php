<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Bot;
use App\Models\BotUser;
use App\Models\Queue;
use App\Models\QueueLog;

class QueueLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = QueueLog::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'bot_id' => Bot::factory(),
            'bot_user_id' => BotUser::factory(),
            'queue_id' => Queue::factory(),
            'sent_at' => $this->faker->dateTime(),
        ];
    }
}
