@extends('emails.customer.master')

@section('content')

    <!-- Visually Hidden Preheader Text : BEGIN -->
    <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; background: #fff; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
        Dear {{ ucwords($order->name) }}
    </div>
    <!-- Visually Hidden Preheader Text : END -->

    <!-- Create white space after the desired preview text so email clients don’t pull other distracting text into the inbox preview. Extend as necessary. -->
    <!-- Preview Text Spacing Hack : BEGIN -->
    <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; background: #fff; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
    </div>
    <!-- Preview Text Spacing Hack : END -->

    <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: 0 auto; background: #f5f5f5;">
        <tr>
            <td style="padding: 10px; font-family: sans-serif; font-size: 12px; line-height: 15px; text-align: center; color: #888888;">
            </td>
        </tr>
    </table>

    <!-- Email Body : BEGIN -->
    <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: 0 auto; background: #fff;" class="email-container">

        <!-- Hero Image, Flush : BEGIN -->
        <tr>
            <td style="padding: 30px 0 0 0; background-color: #fff; text-align: center;">

                <img src="{{ asset('images/logo.png') }}" width="402" height="51" alt="Green World" border="0" style="width: 100%; max-width: 250px; height: auto; background: #fff; font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555; margin: auto;" class="g-img">

            </td>
        </tr>

        <!-- Hero Image, Flush : END -->

        <!-- 1 Column Text + Button : BEGIN -->
        <tr>
            <td valign="middle" width="100%" style="background-color: #ffffff;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr width="100%">
                        <td  style="padding: 20px 20px 10px 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;" colspan="2">
                            
                            <h1 style="margin: 0px 0 0 0px; font-size: 22px; line-height: 30px; color: #333333; font-weight: normal; text-align: center;">
                                {{ ucwords($order->name) }}
                            </h1>

                            <h2 style="margin: 10px 0 0 0px; font-size: 19px; line-height: 30px; color: #333333; font-weight: normal; text-align: center;">  
                                Order <kbd>#{{ $order->id }}</kbd> has shipped! Your products are on their way!
                            </h2>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- 1 Column Text + Button : END -->


        <!-- Clear Spacer : BEGIN -->
        <tr>
            <td aria-hidden="true" height="10" style="font-size: 0px; line-height: 0px; background-color: #ffffff;">
                &nbsp;
            </td>
        </tr>
        <!-- Clear Spacer : END -->


        <!-- Thumbnail Left, Text Right : BEGIN -->
        <tr>
            <td dir="ltr" valign="middle" width="100%" style="padding: 0px 20px; background-color: #ffffff;" class="mobile-display">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">

                    @foreach( $order->trackingNumbers as $trackingNumber )

                        <tr style="padding: 8px 0px !important;" class="product-card">
                            <td width="100%" class="stack-column-center product-cover" style="padding: 0;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="middle" style="padding: 8px 0px 6px 0px; text-align: center;">
                                            <a style="font-size: 20px; font-family: sans-serif;" href="{{ $trackingNumber->url }}">
                                                @if (trim($trackingNumber->carrier_name) == 'UPS Ground')
                                                    UPS Tracking Number - 
                                                @elseif (trim($trackingNumber->carrier_name) !== '')
                                                    {{ $trackingNumber->carrier_name }} - 
                                                @endif
                                                {{ $trackingNumber->number }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                    @endforeach

                </table>
            </td>
        </tr>
        <!-- Thumbnail Left, Text Right : END -->


        <!-- Clear Spacer : BEGIN -->
        <tr>
            <td aria-hidden="true" height="10" style="font-size: 0px; line-height: 0px; background-color: #ffffff;">
                &nbsp;
            </td>
        </tr>
        <!-- Clear Spacer : END -->


        <!-- 1 Column Text + Button : BEGIN -->
        <tr>
            <td valign="middle" width="100%" style="background-color: #ffffff;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr width="100%">
                        <td  style="padding:10px 20px;" colspan="2">
                            
                            <h3 style="margin:0 0 0px; font-size: 19px; line-height: 30px; color: #333333; font-weight: normal; font-family: sans-serif; text-align: center;">

                            </h3>

                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
        <!-- 1 Column Text + Button : END -->


        <!-- Clear Spacer : BEGIN -->
        <tr>
            <td aria-hidden="true" height="40" style="font-size: 0px; line-height: 0px; background-color: #ffffff;">
                &nbsp;
            </td>
        </tr>
        <!-- Clear Spacer : END -->

        <!-- 1 Column Text : BEGIN -->
        <tr>
            <td style="background-color: #ffffff;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 0; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; text-align: justify; text-align-last: center;">
                            <p style="margin: 0 0 10px 0;">
                                <a style="color: #555555;" href="{{ config('app.url') }}">{{ config('app.url') }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- 1 Column Text : END -->


        <!-- Clear Spacer : BEGIN -->
        <tr>
            <td aria-hidden="true" height="40" style="font-size: 0px; line-height: 0px; background-color: #ffffff;">
                &nbsp;
            </td>
        </tr>
        <!-- Clear Spacer : END -->

    </table>
    <!-- Email Body : END -->

    <!-- Email Footer : BEGIN -->

    <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: 0 auto; background: #f5f5f5;">
        <tr>
            <td style="padding: 10px; font-family: sans-serif; font-size: 12px; line-height: 15px; text-align: center; color: #888888;">
                <p class="text-center" style="color: #b3b3b3; font-size: 10px; text-align: center; margin: 0px; padding: 0px;">
                    &nbsp;
                </p>
            </td>
        </tr>
    </table>
    <!-- Email Footer : END -->

@endsection