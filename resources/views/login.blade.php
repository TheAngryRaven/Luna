@include('nav')

<h2>Lunar Messaging</h2>
<p>login</p>

<div>
    <div class="field">
        <input type="text" id="userName" placeholder="Display / User Name">
        <br>
        <input type="password" id="userPassword" placeholder="Account Password (minimum: 6)" />
        <br>
        <input type="password" id="userPassphrase" placeholder="Encryption Passphrase (minimum: 10)" />
    </div>

    <ul class="actions" style="margin-top: 1em;">
        <li><button id="authBtn" class="button" onclick="authUser()">Log On</button></li>
    </ul>
</div>
<script src="{{ URL::asset('js/public/login.js') }}"></script>


