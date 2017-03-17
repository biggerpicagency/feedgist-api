<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class UnreadPosts extends Notification
{
    use Queueable;
    
    protected $notificationsData;
    /**
     * Create a new notification instance.
     *
     * @param $notificationsData
     */
    public function __construct($notificationsData)
    {
        $this->notificationsData = $notificationsData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [OneSignalChannel::class];
    }
    
    /**
     * Get the OneSignal representation of the notification.
     *
     * @param  mixed $notifiable
     * @return OneSignalMessage
     */
    public function toOneSignal($notifiable)
    {
        $message = 'You have ' . $this->notificationsData->countPosts . ' unread post(s)';
        
        if (!empty($this->notificationsData->pagesNames)) {
            $message .= ' from ' . $this->notificationsData->pagesNames;
        }
        
        if (!empty($this->notificationsData->pagesCount) && $this->notificationsData->pagesCount > 0) {
            $message .= ' and ' . $this->notificationsData->pagesCount . ' others';
        }

        return OneSignalMessage::create()
            ->subject('FeedGist')
            ->body($message);
    }

}
