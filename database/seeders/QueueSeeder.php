<?php

namespace Database\Seeders;

use App\Models\Queue;
use Illuminate\Database\Seeder;

class QueueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Queue::factory()->count(5)->create();
    }
}
