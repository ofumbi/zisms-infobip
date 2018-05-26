<?php

namespace ZiSMS\Infobip;

use ZiSMS\Infobip\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;
use infobip\api\model\sms\mt\send\textual\SMSTextualRequest;

class InfobipChannel
{
    /**
     * @var Infobip
     */
    protected $infobip;

    /**
     * InfobipChannel constructor.
     *
     * @param Infobip     $infobip
     */
    public function __construct(Infobip $infobip)
    {
        $this->infobip = $infobip;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \ZiSMS\Infobip\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        try {
            $to = $this->getTo($notifiable);

            $message = $notification->toInfobip($notifiable);

            $message->setTo($to);

            return $this->infobip->sendSmsMessage($message);

        } catch (Exception $exception) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($response);
        }
    }

    protected function getTo($notifiable)
    {
        if ($infobip = $notifiable->routeNotificationFor('infobip')) {
            return $infobip;
        }

        if (isset($notifiable->phone_number)) {
            return $notifiable->phone_number;
        }

        if (isset($notifiable->mobil)) {
            return $notifiable->mobil;
        }

        throw CouldNotSendNotification::invalidReceiver();
    }

}
