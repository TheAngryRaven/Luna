@include('nav')

<div>
    <div class="field">
        <input type="email" id="userEmail" placeholder="Your Email (optional)" />
        <br>
        <input type="text" id="userName" placeholder="Display / User Name">
    </div>
    <div>
        <p>An email can be used to reset an account, (but not recover any information)</p>
    </div>

    <hr>
    <p>This tells the the service who you are.</p>

    <div class="field">
        <input type="password" id="userPassword" placeholder="Account Password (minimum: 6)" />
        <br>
        <input type="password" id="userPasswordConfirm" placeholder="confirm" />
    </div>
    <hr>
    <p>This is the password your browser uses to encrypt private data.</p>
    <div class="field">
        <input type="password" id="userPassphrase" placeholder="Encryption Passphrase (minimum: 10)" />
        <br>
        <input type="password" id="userPassphraseConfirm" placeholder="confirm" />
    </div>
    <br>
    <p>You will need to remember both to login every time</p>


    <ul class="actions" style="margin-top: 1em;">
        <li><button id="registerBtn" class="button" onclick="registerUser()">Register</button></li>
    </ul>
</div>
<p style="font-size: 90%">We only save a SHA256 hash of your Password and Passphrase<br>Your passphrase is used to encrypt all of your private keys and other sensitive data.<br>Your email will only be used for site related affairs.</p>

<script src="{{ URL::asset('js/public/register.js') }}"></script>

