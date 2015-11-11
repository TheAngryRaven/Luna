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
</head>
<body>

<!-- Wrapper -->
<div id="wrapper">


    <!-- Main -->
    <section id="main">
        <div id="encryptedContent">@include('nav')<p style="margin: 0;">Please Wait<br>Generating encryption keys</p></div>
    </section>

    <!-- lazy hack for now -->
    <div id="image"></div>

    <!-- Footer -->
    <footer id="footer">
        <ul class="icons">
            <li><a href="mailto:lunarmessagingservice@gmail.com" class="fa-envelope">Email</a></li>
            <li><a href="https://github.com/CrimsonDove/Luna" class="fa-github">Github</a></li>
        </ul>
    </footer>

</div>

<input type="hidden" id="handshake" value="{{ $sessionKeys['publickey'] }}">
<input type="hidden" id="token" value="{{ csrf_token() }}">

<!--
It's a bonfire, turn the lights out
I'm burnin' everything you muthafuckas talk about
-->

</body>
</html>