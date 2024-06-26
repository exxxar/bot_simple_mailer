<?php

namespace App\Console\Commands;

use App\Models\BotUser;
use App\Models\Bot;
use App\Models\Queue;
use App\Models\QueueLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramOtherException;
use Telegram\Bot\Exceptions\TelegramSDKException;

class Mailing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:mailing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ежедневная рассылка сообщений';

    /**
     * Execute the console command.
     * @throws TelegramSDKException
     * @throws \Exception
     */
    public function handle(): void
    {
        Log::info("current time=>".Carbon::now("+0")->format("Y-m-d H:i:s"));
        $queues = Queue::query()
            ->whereNull("sent_at")
            ->get();

        if (count($queues) == 0){
            ini_set('max_execution_time', 300);
            return;
        }


        $timeLimit = 0;
        foreach ($queues as $queue) {
            $botUsersCount = BotUser::query()
                ->where("bot_id", $queue->bot_id)
                ->count();

            $timeLimit += $botUsersCount;

        }

        ini_set('max_execution_time', $timeLimit * 2 + 60);

        foreach ($queues as $queue) {

            if (!is_null($queue->cron_time)) {
                $now = Carbon::now("+0")->timestamp;

                if ($now < Carbon::parse($queue->cron_time)->timestamp)
                    continue;
            }


            $queue->sent_at = Carbon::now("+0");
            $queue->save();

            $bot = Bot::query()
                ->where("id", $queue->bot_id)
                ->first();

            $botUsers = BotUser::query()
                ->where("bot_id", $bot->id)
                ->get();


            foreach ($botUsers as $botUser) {
                $queueLog = QueueLog::query()
                    ->create([
                        'bot_id' => $bot->id,
                        'bot_user_id' => $botUser->id,
                        'queue_id' => $queue->id,
                        'sent_at' => Carbon::now("+0"),
                    ]);

                $replyKeyboard = $queue->reply_keyboard ?? null;
                $inlineKeyboard = $queue->inline_keyboard ?? null;
                $text = $queue->content ?? 'Текст рассылки';
                $images = $queue->images ?? null;

                $hasPhoto = false;

                if (empty($images ?? []) || is_null($images[0] ?? null))
                    $tmp = [
                        'chat_id' => $botUser->telegram_chat_id,
                        "text" => $text,
                        "parse_mode" => "HTML",

                    ];

                if (!is_null($images[0] ?? null)) {
                    $hasPhoto = true;
                    $tmp = [
                        'chat_id' => $botUser->telegram_chat_id,
                        "caption" => mb_substr($text, 0, 1000),
                        "parse_mode" => "HTML",
                        "photo" => $images[0]
                    ];

                }

                if (!is_null($inlineKeyboard)) {

                    $tmp["reply_markup"] = json_encode([
                        'inline_keyboard' => $inlineKeyboard,
                    ]);
                }

                try {
                    $telegram = new Api($bot->bot_token);

                    if (!$hasPhoto)
                        $telegram->sendMessage($tmp);
                    else {
                        $telegram->sendPhoto($tmp);
                        if (mb_strlen($text) > 1000)
                            $telegram->sendMessage([
                                'chat_id' => $botUser->telegram_chat_id,
                                "text" => mb_substr($text, 1001),
                                "parse_mode" => "HTML",
                            ]);
                    }


                    $queueLog->status = true;
                    $queueLog->save();
                } catch (TelegramOtherException $e) {
                    $queueLog->status = false;
                    $queueLog->save();

                } catch (Exception $e) {
                    Log::info($e);
                }


                sleep(random_int(0, 1));
            }
        }

        ini_set('max_execution_time', 300);
    }
}
