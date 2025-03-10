<?php

namespace App\Notifications;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

class FcmNotification extends Notification
{
    use Queueable;

    const FCM_SERVER_KEY = "AAAAQQcX6VE:APA91bEUYO7pZ7cAGNckjWZVfiwSoL8nEm95MTqvVke9eXB66TT0V9B28nvvLcRVGFjB_oX-sReFYSGSVKDjUFtz5UlhArjgsQ9IoBoE9EOHBQ9p_zmi5kS331mItGpH2Ja-9uAgPDt2";

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Get the array representation of the notification.
     *
     * @param mixed $to
     * @return array
     */
    public static function send($to, $title, $body, $data = [])
    {

        $json = [
            "to" => $to,
            "notification" => [
                "id" => $data["id"],
                "title" => $title,
                "body" => $body,
                "type" => $data["type"],
                "image" => $data["image"],
            ],
            "data" => [
                "id" => $data["id"],
                "title" => $title,
                "body" => $body,
                "type" => $data["type"],
                "image" => $data["image"],
            ]
        ];

        $client = new Client();
        try {
            $result = $client->post('https://fcm.googleapis.com/fcm/send', [
                'json' =>
                    $json,
                'headers' => [
                    'Authorization' => 'key=' . self::FCM_SERVER_KEY,
                    'Content-Type' => 'application/json',
                ],
            ]);

            return json_decode($result->getBody(), true);

        } catch (Throwable $th) {
            Log::alert($th);
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
