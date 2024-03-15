<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bot_id')->constrained();
            $table->longText('content');
            $table->json('reply_keyboard')->nullable();
            $table->json('inline_keyboard')->nullable();
            $table->json('images')->nullable();
            $table->json('audios')->nullable();
            $table->json('videos')->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
