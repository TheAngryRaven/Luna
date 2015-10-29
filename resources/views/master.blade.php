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
</head>
<body>

<!-- Wrapper -->
<div id="wrapper">

@include('nav')

    <!-- Main -->
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
        </ul>
    </footer>

</div>

<input type="hidden" id="handshake" value="{{ $sessionKeys['publickey'] }}">
<input type="hidden" id="token" value="{{ csrf_token() }}">

<!--
It's a bonfire, turn the lights out
I'm burnin' everything you muthafuckas talk about
-->

<script src="{{ URL::asset('jquery-1.11.3.min.js') }}"></script>
<script src="{{ URL::asset('crypto/jsencrypt.js') }}"></script>
<script src="{{ URL::asset('crypto/gibberish-aes.js') }}"></script>
<script src="{{ URL::asset('crypto/sha256.js') }}"></script>

<script src="{{ URL::asset('core.js') }}"></script>
</body>
</html>