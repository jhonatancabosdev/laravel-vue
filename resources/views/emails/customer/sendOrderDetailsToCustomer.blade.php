<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8"> <!-- utf-8 works for most cases -->
    <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name="x-apple-disable-message-reformatting">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <title>{{ config('app.name') }}</title> <!-- The title tag shows in email notifications, like Android 4.4. -->

    <!-- Web Font / @font-face : BEGIN -->
    <!-- NOTE: If web fonts are not required, lines 10 - 27 can be safely removed. -->

    <!-- Desktop Outlook chokes on web font references and defaults to Times New Roman, so we force a safe fallback font. -->
    <!--[if mso]>
        <style>
            * {
                font-family: sans-serif !important;
            }
        </style>
    <![endif]-->

    <!-- All other clients get the webfont reference; some will render the font and others will silently fail to the fallbacks. More on that here: http://stylecampaign.com/blog/2015/02/webfont-support-in-email/ -->
    <!--[if !mso]><!-->
    <!-- insert web font reference, eg: <link href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'> -->
    <!--<![endif]-->

    <!-- Web Font / @font-face : END -->

    <!-- CSS Reset : BEGIN -->
    <style>

        /* What it does: Remove spaces around the email design added by some email clients. */
        /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }

        /* What it does: Stops email clients resizing small text. */
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        /* What it does: Centers email on Android 4.4 */
        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }

        /* What it does: Stops Outlook from adding extra spacing to tables. */
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        /* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }
        table table table {
            table-layout: auto;
        }

        /* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
        a {
            text-decoration: none;
        }

        /* What it does: Uses a better rendering method when resizing images in IE. */
        img {
            -ms-interpolation-mode:bicubic;
        }

        /* What it does: A work-around for email clients meddling in triggered links. */
        *[x-apple-data-detectors],  /* iOS */
        .unstyle-auto-detected-links *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
        .a6S {
           display: none !important;
           opacity: 0.01 !important;
       }
       /* If the above doesn't work, add a .g-img class to any image in question. */
       img.g-img + div {
           display: none !important;
       }

        /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
        /* Create one of these media queries for each additional viewport size you'd like to fix */

        /* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
        @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
            .email-container {
                min-width: 320px !important;
            }
        }
        /* iPhone 6, 6S, 7, 8, and X */
        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
            .email-container {
                min-width: 375px !important;
            }
        }
        /* iPhone 6+, 7+, and 8+ */
        @media only screen and (min-device-width: 414px) {
            .email-container {
                min-width: 414px !important;
            }
        }

    </style>
    <!-- CSS Reset : END -->
    <!-- Reset list spacing because Outlook ignores much of our inline CSS. -->
    <!--[if mso]>
    <style type="text/css">
        ul,
        ol {
            margin: 0 !important;
        }
        li {
            margin-left: 30px !important;
        }
        li.list-item-first {
            margin-top: 0 !important;
        }
        li.list-item-last {
            margin-bottom: 10px !important;
        }
    </style>
    <![endif]-->

    <!-- Progressive Enhancements : BEGIN -->
    <style>

        /* What it does: Hover styles for buttons */
        .button-td,
        .button-a {
            transition: all 100ms ease-in;
        }
        .button-td-primary:hover,
        .button-a-primary:hover {
            background: #555555 !important;
            border-color: #555555 !important;
        }

        /* Media Queries */
        @media screen and (max-width: 600px) {

            .email-container {
                width: 100% !important;
                margin: auto !important;
            }

            /* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */
            .fluid {
                max-width: 100% !important;
                height: auto !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            /* What it does: Forces table cells into full-width rows. */
            .stack-column,
            .stack-column-center {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }
            /* And center justify these ones. */
            .stack-column-center {
                text-align: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                padding-bottom: 12px;
            }

            .hide-content-small {
                display: none;
            }

            /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */
            .center-on-narrow {
                text-align: center !important;
                display: block !important;
                margin-left: auto !important;
                margin-right: auto !important;
                float: none !important;
            }
            table.center-on-narrow {
                display: inline-block !important;
            }

            /* What it does: Adjust typography on small screens to improve readability */
            .email-container p {
                font-size: 17px !important;
            }
        }

    </style>
    <!-- Progressive Enhancements : END -->

    <!-- What it does: Makes background images in 72ppi Outlook render at correct size. -->
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->

</head>
<!--
    The email background color (#222222) is defined in three places:
    1. body tag: for most email clients
    2. center tag: for Gmail and Inbox mobile apps and web versions of Gmail, GSuite, Inbox, Yahoo, AOL, Libero, Comcast, freenet, Mail.ru, Orange.fr
    3. mso conditional: For Windows 10 Mail
-->
<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-image: url({{ asset('assets/img/bg-header-xl.png')  }});">    <center style="width: 100%; background-image: {{ asset('assets/img/bg-header-xl.png')  }} ;">
    <!--[if mso | IE]>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #222222;">
    <tr>
    <td>
    <![endif]-->

        <!-- Visually Hidden Preheader Text : BEGIN -->
        <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
            Dear {{$order->name}}
        </div>
        <!-- Visually Hidden Preheader Text : END -->

        <!-- Create white space after the desired preview text so email clients don’t pull other distracting text into the inbox preview. Extend as necessary. -->
        <!-- Preview Text Spacing Hack : BEGIN -->
        <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
            &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
        </div>
        <!-- Preview Text Spacing Hack : END -->

        <!-- Email Body : BEGIN -->
        <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: 0 auto;" class="email-container">

            <tr>
                <td>
                    <br>                    
                </td>    
            </tr>

            <!-- Hero Image, Flush : BEGIN -->
            <tr>
                <td style="padding: 30px 0 0 0; background-color: #fff; text-align: center;">

                <img src="{{ asset(Voyager::setting('site.logo') ? Voyager::setting('site.logo') : 'images/logo.png') }}" width="402" height="51" alt="{{ Voyager::setting('site.title', config('app.name', 'eCommerce Site')) }}" border="0" style="width: 100%; max-width: 250px; height: auto; background: #fff; font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555; margin: auto;" class="g-img">

                </td>
            </tr>

            <!-- Hero Image, Flush : END -->

            <!-- 1 Column Text + Button : BEGIN -->
            <tr>
                <td valign="middle" width="100%" style="background-color: #ffffff;">
                    <table style="table-layout: auto !important;" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr width="100%">
                            <td  style="padding: 30px 20px 10px 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;" colspan="2">
                                <h1 style="margin: 0 0 0px; font-size: 22px; line-height: 30px; color: #333333; font-weight: normal; text-align: center;">
                                    Your order has been placed!
                                </h1>
                            </td>
                        </tr>
                        <tr width="100%">
                            <td class="stack-column-center" width="60%" style="padding: 15px 0px 0px 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">

                                <h3 style="border-bottom: 1px solid #555555; padding-left: 8px; padding-bottom: 10px; margin: 0 0 0px; font-size: 21px; line-height: 25px; margin-top: 15px;color: #333333; font-weight: normal;">
                                    <strong>Order : {{ $order->id }}</strong>
                                </3>

                            </td>
                            <td class="stack-column-center" width="40%" style="padding: 15px 20px 0px 0px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">

                                <h3 class="stack-column-center" style="border-bottom: 1px solid #555555; padding-right: 15px; padding-bottom: 10px; margin: 0 0 0px; text-align: right; font-size: 14px; line-height: 25px; margin-top: 15px;color: #3f3f3f; font-weight: normal;">
                                    Order Date : {{ $order->created_at->format('m/d/Y') }}
                                </h3>

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- 1 Column Text + Button : END -->

            <!-- 1 Column Text + Button : BEGIN -->
            <tr>
                <td style="background-color: #ffffff;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- 1 Column Text + Button : END -->

            <!-- Thumbnail Left, Text Right : BEGIN -->
            <tr>
                <td dir="ltr" valign="middle" width="100%" style="padding: 15px 20px; background-color: #ffffff;">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">

                        <tr style="border-bottom: 1px solid #555555;">
                            <!-- Column : BEGIN -->
                            <td width="25%" class="hide-content-small" style="padding-bottom: 10px;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="middle" style="font-family: sans-serif; font-size: 17px; line-height: 20px; color: #333333; text-align: center;" class="center-on-narrow">
                                            <strong>Product</strong>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Column : END -->
                            <!-- Column : BEGIN -->
                            <td width="45%" class="hide-content-small" style="padding-bottom: 10px;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="middle" style="font-family: sans-serif; font-size: 17px; line-height: 20px; color: #333333; text-align: left;" class="center-on-narrow">
                                            &nbsp;
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Column : BEGIN -->
                            <td width="15%" class="hide-content-small" style="padding-bottom: 10px;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="middle" style="font-family: sans-serif; font-size: 17px; line-height: 20px; color: #333333; padding:5px 0px; text-align: center;">
                                            <strong>Quantity</strong>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td width="15%" class="hide-content-small" style="padding-bottom: 10px;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="middle" style="font-family: sans-serif; font-size: 17px; line-height: 20px; color: #333333; text-align: center;">
                                            <strong>Price</strong>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Column : END -->
                        </tr>


                        @foreach( $products as $product )

                        @if ( is_array($product->pivot->options) )
                            @php
                                $product->pivot->options = json_encode($product->pivot->options);
                            @endphp
                        @endif

                        @php
                            $product->pivot->options = json_decode($product->pivot->options, true);
                        @endphp

                        <tr style="border-bottom: 1px solid #555555; padding: 10px 0px;">
                            <!-- Column : BEGIN -->
                            <td width="20%" class="stack-column-center" style="padding: 0;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="middle" style="text-align: center; padding: 10px 0px 6px 0px;">
                                            <a href="{{ route('product.show', $product) }}" target="_blank">
                                                <img  
                                                    src="{{ asset( Croppa::url("$product->small_main_image", 100, 100, ['resize', 'upscale' => true]) ) }}"
                                                    width="100" height="100" alt="{{$product->name }}" border="0" class="center-on-narrow" style="height: auto; background: #dddddd; font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555;">
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Column : END -->
                            <!-- Column : BEGIN -->
                            <td width="55%" class="stack-column-center">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="middle" style="font-family: sans-serif; font-size: 15px; line-height: 20px; padding: 15px; color: #000; text-align: left;" class="center-on-narrow">
                                            @if( trim($product->name) != '' )
                                                <h4 style="margin: 0px; font-weight: normal; font-size: 14px; line-height: 22px;">
                                                    <a href="{{ route('product.show', $product) }}" target="_blank" style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 16px; line-height: 20px; color: #333333; font-weight: bold;">
                                                       {{ $product->id }} - {{ $product->name }}
                                                    </a>
                                                </h4>
                                            @endif
                                            <p style="margin: 0px;">
                                                <a href="{{ route('product.show', $product) }}" target="_blank" style="margin: 0 0 0 0; font-family: sans-serif; font-size: 14px; line-height: 22px; color: #000; font-weight: normal;">
                                                    
                                                    @if ( trim($product->name) == '' )
                                                        {{ $product->id }} - 
                                                    @endif

                                                </a>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Column : BEGIN -->
                            <td width="15%" class="stack-column-center">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="middle" style="font-family: sans-serif; font-size: 15px; line-height: 20px; color: #000; padding: 0px; text-align: center;">
                                            <p style="margin: 0; font-size: 18px;">
                                                {{ $product->pivot->quantity }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td width="10%" class="stack-column-center">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="middle" style="font-family: sans-serif; font-size: 15px; line-height: 20px; color: #000; padding: 0px; text-align: center;">
                                            <p style="margin: 0; font-size: 18px;">
                                                ${{ number_format($product->price, 2) }} 
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Column : END -->
                        </tr>

                        @endforeach

                    </table>
                </td>
            </tr>
            <!-- Thumbnail Left, Text Right : END -->

            <!-- Thumbnail Left, Text Right : BEGIN -->
            <tr>
                <td dir="ltr" valign="middle" width="100%" style="padding: 15px 20px; background-color: #ffffff;">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">

                        <tr style="border-bottom: 1px solid #555555; vertical-align: top;">
                            <!-- Column : BEGIN -->
                            <td width="100%" class="stack-column-center" style="padding-bottom: 10px;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="middle" style="padding: 0 0 0 0; font-family: sans-serif; font-size: 17px; line-height: 20px; color: #333333;" class="center-on-narrow">

                                            <p style="margin: 0px 0 5px 0; font-size: 16px;">
                                                <strong>Billing Address</strong>
                                            </p>
                                            <p style="margin: 0px 0 15px 0; font-size: 15px;">
                                                {{ $order->name }} <br>
                                                {{ $billing_address->address_1 }} <br>
                                                @if( !!$billing_address->address_2 )
                                                    {{ $billing_address->address_2 }} <br>
                                                @endif

                                                @if ( $billing_address->city )
                                                
                                                    {{ ucfirst($billing_address->city) }},

                                                @endif

                                                {{ $billing_address->state->abv }} {{ $billing_address->zipcode }}
                                            </p>

                                        </td>
                                        <td dir="ltr" valign="middle" style="padding: 0 0 0 0; font-family: sans-serif; font-size: 17px; line-height: 20px; color: #333333;" class="center-on-narrow">

                                            <p style="margin: 0px 0 5px 0; font-size: 16px;">
                                                <strong>Shipping Address</strong>
                                            </p>
                                            <p style="margin: 0px 0 15px 0; font-size: 15px;">
                                                {{ $order->name }} <br>
                                                {{ $shipping_address->address_1 }} <br>
                                                @if( !!$shipping_address->address_2 )
                                                    {{ $shipping_address->address_2 }} <br>
                                                @endif

                                                
                                                @if ( $shipping_address->city )
                                                    {{ $shipping_address->city }},
                                                    {{-- {{ ucfirst($shipping_address->city->name) }}, --}}
                                                @endif

                                                {{ $shipping_address->state->abv }} {{ $shipping_address->zipcode }}
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td valign="top" style="font-family: sans-serif;">
                                            <p style="margin: 0; padding: 0 10px 0 0; font-size: 17px; line-height: 20px; color: #333333; text-align: left;">
                                                <strong>Payment</strong>                                           
                                            </p>

                                            @if($order->payment_method =="credit_card")
                                            <p style="margin: 15px 0 5px 0; font-size: 15px;">
                                                {{ ucwords($order->card_type) }}: XXXX-XXXX-XXXX-{{ $order->LastCCDigits }}
                                            </p>
                                            @else
                                            <p style="margin: 5px 0 15px 0; font-size: 15px;">
                                                Paypal
                                            </p>
                                            @endif                                            
                                            <p style="margin: 0; padding: 0 10px 0 0; font-size: 17px; line-height: 20px; color: #333333; text-align: left;">
                                                <strong>Shipping Method</strong>                                           
                                            </p>
                                            <p style="margin: 10px 0 5px 0; font-size: 15px;">
                                                {{ $shipping_name }}
                                            </p>

                                        </td>
                                        <td width="35%" class="stack-column-center" style="padding-bottom: 10px;">
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                <tr>
                                                    <td dir="ltr" valign="middle" style="padding: 0 10px 0 0; font-family: sans-serif; font-size: 17px; line-height: 20px; color: #333333; text-align: left;" class="center-on-narrow">
            
                                                        <p style="margin: 0px 0 5px 0; font-size: 16px;">
                                                            <strong>Details</strong>
                                                        </p>
            
                                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                            <tr>
                                                                <td style="font-size: 15px; padding: 7px 0px">
                                                                    Subtotal
                                                                </td>
                                                                <td style="font-size: 15px; padding: 7px 0px">
                                                                    ${{ number_format($order->subtotal, 2) }}
                                                                </td>
                                                            </tr>
                                                            @if ($discount)
                                                            <tr>
                                                                <td style="font-size: 15px; padding: 7px 0px">
                                                                    Discount
                                                                </td>
                                                                <td style="font-size: 15px; padding: 7px 0px">
                                                                    {{ $discount->amount }}
                                                                </td>
                                                            </tr>
                                                            @endif
                                                            <tr>
                                                                <td style="font-size: 15px; padding: 7px 0px">
                                                                    Tax
                                                                </td>
                                                                <td style="font-size: 15px; padding: 7px 0px">
                                                                    ${{ number_format($order->tax, 2) }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-size: 15px; padding: 7px 0px">
                                                                    Shipping
                                                                </td>
                                                                <td style="font-size: 15px; padding: 7px 0px">
                                                                    ${{ number_format($order->shipping_cost, 2) }}
                                                                </td>
                                                            </tr>
                                                            @if ( $order->ltl_indicator )
                                                            <tr>
                                                                <td style="font-size: 15px; padding: 7px 0px">
                                                                    LTL
                                                                </td>
                                                                <td style="font-size: 15px; padding: 7px 0px">
                                                                    ${{ number_format(setting('site.ltl_shipping_cost'), 2) }}
                                                                </td>
                                                            </tr>
                                                            @endif
                                                            <tr>
                                                                <td style="font-size: 15px; padding: 7px 0px">
                                                                    Grand Total
                                                                </td>
                                                                <td style="font-size: 15px; padding: 7px 0px">
                                                                    ${{ number_format($order->total, 2) }}
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Column : END -->

                        </tr>
                    </table>
                </td>
            </tr>
            <!-- Thumbnail Left, Text Right : END -->
            

            <!-- Clear Spacer : BEGIN -->
            <tr>
                <td aria-hidden="true" height="40" style="font-size: 0px; line-height: 0px; background-color: #ffffff;">
                    &nbsp;
                </td>
            </tr>
            <!-- Clear Spacer : END -->


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
                                <p style="margin: 0 0 10px 0; text-align: center;">
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
        <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: 0 auto;" class="email-container">
            <tr>
                <td style="padding: 20px; font-family: sans-serif; font-size: 12px; line-height: 15px; text-align: center; color: #888888;">
                    &nbsp;
                </td>
            </tr>
        </table>
        <!-- Email Footer : END -->

    <!--[if mso | IE]>
    </td>
    </tr>
    </table>
    <![endif]-->

<!-- 
@include('emails.customer.orderDetailsPlain')
-->

    </center>
</body>
</html>
