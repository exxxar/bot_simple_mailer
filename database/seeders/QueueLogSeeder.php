<?php

namespace Database\Seeders;

use App\Models\QueueLog;
use Illuminate\Database\Seeder;

class QueueLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QueueLog::factory()->count(5)->create();
    }
}
