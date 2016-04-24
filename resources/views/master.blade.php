<?php
$sessionKeys = Session::get("serverKeys");
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Lunar Messaging</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="{{ URL::asset('theme/main.css')  }}" />
    <link rel="stylesheet" href="{{ URL::asset('bootstrap/css/bootstrap.min.css')  }}" />

    <script src="{{ URL::asset('js/jquery-1.11.3.min.js') }}"></script>
    <script src="{{ URL::asset('crypto/jsencrypt.js') }}"></script>
    <script src="{{ URL::asset('crypto/gibberish-aes.js') }}"></script>
    <script src="{{ URL::asset('crypto/sha256.js') }}"></script>

    <script src="{{ URL::asset('js/apollo.js') }}"></script>

    <script src="{{ URL::asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('bootstrap/bootbox.min.js') }}"></script>


    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ URL::asset('favicon') }}/apple-touch-icon-144x144.png" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="{{ URL::asset('favicon') }}/apple-touch-icon-152x152.png" />
    <link rel="icon" type="image/png" href="{{ URL::asset('favicon') }}/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ URL::asset('favicon') }}/favicon-16x16.png" sizes="16x16" />
    <meta name="application-name" content="LunarMessaging.net"/>
    <meta name="msapplication-TileColor" content="#3B3B3B" />
    <meta name="msapplication-TileImage" content="{{ URL::asset('favicon') }}/mstile-144x144.png" />

</head>
<body>

<!-- Wrapper -->
<div id="wrapper">


    <!-- Main -->
    <a href="{{ URL::to('/') }}"><img style="width: 90%; max-width:387px;margin-bottom:2em;" src="{{ URL::asset('header.png') }}" alt="LunarMessaging Logo"></a>
    <section id="main">
        <div id="encryptedContent"><p style="margin: 0;">Please Wait<br>Generating encryption keys</p></div>
    </section>

    <!-- lazy hack for now -->
    <div id="image"></div>

    <!-- Footer -->
    <footer id="footer">
        <ul class="icons">
            <li><a href="mailto:lunarmessagingservice@gmail.com" class="fa-envelope">Email</a></li>
            <li><a href="https://github.com/CrimsonDove/Luna" class="fa-github">Github</a></li>
            <li><a onclick="window.prompt('lunar Messaging Bitcoin Wallet, operation costs are currently $12 a month.', '1AZnBnmMz7Le6kpMPCRgtCoWzi8iVnVRab');" class="fa-bitcoin">Bitcoin</a></li>


        </ul>
    </footer>

</div>

<input type="hidden" id="handshake" value="{{ $sessionKeys['publickey'] }}">
<input type="hidden" id="token" value="{{ csrf_token() }}">

<!--
It's a bonfire, turn the lights out,
I'm burnin' everything you muthafuckas talk about!
-->

</body>
</html>