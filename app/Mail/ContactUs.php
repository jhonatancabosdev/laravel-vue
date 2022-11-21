<?php

namespace App\Mail;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactUs extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->type = $request['contact_type'];
        $this->name = $request['contact_name'];
        $this->email = $request['contact_email'];
        $this->phone = $request['contact_phone'];
        $this->message = $request['contact_message'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.admin.contact')
        ->with([
            'name'      => $this->name,
            'type'      => $this->type,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'message'   => $this->message,
        ]);
    }
}
