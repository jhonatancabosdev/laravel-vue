<?php

namespace App\Http\Controllers;

use App\Mail\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{ Http, Mail };

class ContactController extends Controller
{
    public function contact(Request $request)
    {
        $data = ['status' => null, 'messages' => [], 'time' => date('l, F jS, Y h:i:s A')];
        
        $validatedData = $request->validate([
            'contact_type'              => 'required',
            'contact_name'              => 'required',
            'contact_phone'             => 'required',
            'contact_email'             => 'required',
            'contact_message'   => 'required'         
        ]);

        if($data['status'] != 'invalid') {
            try
            {
                Mail::to(config('mail.from.address'))
                    ->send(new ContactUs($request));
            }
            catch (\Exception $e)
            {
                return back()->with('error','We\'re sorry. There was an error. Please try again later.');
            }
        }
    
        return back()->with('message','Thank you for contacting us!');
    }
}
