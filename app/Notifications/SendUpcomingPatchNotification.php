<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendUpcomingPatchNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($params, $threshold)
    {
        $this->assets = $params;
        $this->threshold = $threshold;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return $notifyBy = ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail()
    {
        $message = (new MailMessage())->markdown('notifications.markdown.upcoming-patches',
            [
                'assets'  => $this->assets,
                'threshold'  => $this->threshold,
            ])
            ->subject(trans_choice('mail.upcoming-patches', $this->assets->count(), ['count' => $this->assets->count(), 'threshold' => $this->threshold]));

        return $message;
    }
}
