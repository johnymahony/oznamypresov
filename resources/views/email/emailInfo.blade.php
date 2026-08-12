<!doctype html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Newsletter</title>
    <style>
        /* -------------------------------------
            GLOBAL
        ------------------------------------- */


        img {
            border: none;
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
            margin: auto;

        }

        body {
            background-color: #f6f6f6;
            font-family: sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        table {
            border-collapse: separate;
            mso-table-lspace: 0;
            mso-table-rspace: 0;
            width: 100%;
        }

        table td {
            font-family: sans-serif;
            font-size: 14px;
            vertical-align: top;
        }

        /* -------------------------------------
            BODY & CONTAINER
        ------------------------------------- */

        .body {
            background-color: #f6f6f6;
            width: 100%;
        }


        .container {
            display: block;
            margin: 0 auto !important;

            max-width: 620px;

            width: 600px;
        }

        .content {
            box-sizing: border-box;
            display: block;
            margin: 0 auto;
            max-width: 620px;

        }

        /* -------------------------------------
            HEADER, FOOTER, MAIN
        ------------------------------------- */
        .main {
            background: #373c4e;

            width: 100%;

        }

        .wrapper {
            box-sizing: border-box;
            padding: 20px;
        }

        .content-block {
            padding-bottom: 10px;
            padding-top: 10px;

        }

        .footer {
            background-color: #373c4e;
            clear: both;
            /*margin-top: 10px;*/
            text-align: center;
            width: 100%;
        }

        .footer td,
        .footer p,
        .footer span,
        .footer a {
            color: orange;
            font-size: 12px;
            text-align: center;


        }

        .odhlasit {
            display: inline-block;
            margin-top: 40px !important;


        }

        /* -------------------------------------
            FONTS, LETTERS, FORMATS
        ------------------------------------- */
        h1,
        h2,
        h3,
        h4 {
            color: white;
            font-family: sans-serif;
            font-weight: 400;
            line-height: 1.4;
            margin: 0;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 35px;
            font-weight: 300;
            text-align: center;

        }

        p,
        ul,
        ol {
            font-family: sans-serif;
            font-size: 18px;
            font-weight: normal;
            margin: 0;
            margin-bottom: 15px;
            color: white;
        }

        p li,
        ul li,
        ol li {
            list-style-position: inside;
            margin-left: 5px;
        }

        a {
            color: #3498db;
            text-decoration: none;

        }

        .describe {
            font-size: 12px;
            font-style: italic;
            color: grey;
        }


        /* -------------------------------------
            BUTTONS
        ------------------------------------- */
        .btn {
            box-sizing: border-box;
            width: 100%;
        }

        .btn > tbody > tr > td {
            padding-bottom: 15px;
        }

        .btn table {
            width: auto;
        }

        .btn table td {
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center;
        }

        .btn a {
            background-color: #ffffff;
            border: solid 1px #3498db;
            border-radius: 5px;
            box-sizing: border-box;
            color: #3498db;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            padding: 10px 10px;
            text-decoration: none;

        }

        .btn-primary table td {
            background-color: #3498db;

        }

        .btn-primary a {
            background-color: #3498db;
            border-color: #3498db;
            color: #ffffff;
        }

        hr {
            display: block;
            height: 1px;
            border: 0;
            border-top: 1px solid #ccc;
            margin: 1em 0;
            padding: 0;
        }


        hr {
            border: 0;
            border-bottom: 1px solid #f6f6f6;
            margin: 20px 0;
        }

        /* -------------------------------------
            RESPONSIVE
        ------------------------------------- */
        @media only screen and (max-width: 620px) {
            table[class=body] h1, h2 {
                font-size: 20px !important;
                margin-bottom: 10px !important;
                text-align: center;
            }

            table[class=body] p {
                font-size: 13px !important;
                text-align: center;

            }


            table[class=body] ul,
            table[class=body] ol,
            table[class=body] td,
            table[class=body] span,
            table[class=body] a {
                font-size: 9px !important;
            }

            table[class=body] .wrapper,
            table[class=body] .article {
                padding: 0 !important;
            }

            table[class=body] .content {
                padding: 1px !important;
            }

            table[class=body] .container {
                padding: 2px !important;
                width: 100% !important;
                margin: 0 !important;
            }

            table[class=body] .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important;

            }

            table[class=body] .btn table {

                width: 100% !important;

            }

            table[class=body] .btn a {

                width: 100% !important;
            }

            table[class=body] .img-responsive {
                height: auto !important;
                max-width: 100% !important;
                width: auto !important;
                padding: 3px !important;


            }
        }

        /* OTHER SETTINGS */

        @media all {
            .ExternalClass {
                width: 100%;
            }

            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }

            .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important;
            }

            #MessageViewBody a {
                color: inherit;
                text-decoration: none;
                font-size: inherit;
                font-family: inherit;
                font-weight: inherit;
                line-height: inherit;
            }

            .btn-primary table td:hover {
                background-color: #34495e !important;
            }

            .btn-primary a:hover {
                background-color: #34495e !important;
                border-color: #34495e !important;
            }
        }

    </style>
</head>


<body class="">
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
        <td>&nbsp;</td>
        <td class="container">
            <div class="content">
                <table role="presentation" class="main">
                    <tr>
                        <td class="wrapper">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <div class="img-responsive">

                                            <div style="text-align: center;">
                                                <h2 style="color: white; font-size: 30px;">
                                                    Oznamy {{city()->humanName()}}</h2>

                                            </div>

                                            <img src="{{ asset('/images/'.strtolower(city()).'.jpg') }}"
                                                 alt="{{city()}}" width="500"
                                                 border="0"
                                                 style="border:0; outline:none; text-decoration:none; display:block;">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="wrapper">
                            @yield('content')
                            <hr>
                            <div class="footer">
                                <p>Ak si chcete zmeniť nastavenia zasielania, kliknite na tento link:</p>
                                <a href="{{url('/')}}">{{url('/')}}</a>
                                <br>
                                <br>
                                <p>Tento emailový spravodaj nieje oficiálnym kanálom mesta {{city()->humanName()}}. V
                                    prípade
                                    akýchkoľvek otázok ohľadom samotných oznamov kontaktujte mestský úrad na
                                    adrese</p>
                                <a href="{{city()->contactWeb()}}">{{city()->contactWeb()}}</a>
                                <br><br>
                                <p>S úctou, <br>
                                    tím {{city()->domain()}}</p>
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        @php
                                            $track = '/';
                                            if(!isset($blade)) {
                                                $blade = 'po';
                                            }
                                            if(isset($object) && $object && $object instanceof \App\Cityzen) {
                                                \App\TrackedEmail::firstOrCreate([
                                                    'cityzen_id' => $object->id,
                                                    'blade'      => $blade,
                                                    'date_sent'  => dateTimeNow()
                                                ]);
                                                $track = $object->id.'/'.time();
                                            }
                                        @endphp
                                        <td class="content-block">
                                            <img style="
                                            margin:0;
                                            padding-left:20px;
                                            margin-right: 100px;
                                            border:0;
                                            outline:none;
                                            text-decoration:none;
                                            width: 145px;
                                            height: 85px;"
                                                 src="{{url('/')}}/logo/{{$blade}}/{{$track}}"

                                                 align="left" alt="banner" width="350" height="90" border="0"
                                            >
                                        </td>
                                        @if(isset($object))
                                            <td class="content-block odhlasit">
                                                <a href="{{url('unsubscribe')}}/{{$object->hash}}" align="right">ODHLÁSIŤ
                                                    Z ODBERU</a>
                                            </td>
                                        @endif
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        <td>&nbsp;</td>
    </tr>
</table>
</body>
</html>