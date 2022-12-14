<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Subscriber;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SubscriberCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * 
     * @var [type]
     */
    private $subscriber;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( Subscriber $subscriber )
    {
        //
        $this->subscriber = $subscriber;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
                    ->from( env('MAIL_FROM_ADDRESS') )
                    ->view('emails.admin.subscriber-created', ['subscriber' => $this->subscriber]);
    }
}
