@include('nav')

<h2>Lunar Messaging</h2>
<p>Hot drop service, no account required.</p>
<div>
    <div class="field">
        <input type="email" id="sendTo" placeholder="Send link to email (optional)" />
    </div>
    <br>
    <div class="field">
        <input type="password" id="encryptWith" placeholder="Encrypt with Password (optional w/ text)" />
    </div>
    <div>
        <p>Make sure your recipient knows the password</p>
    </div>
    <div class="field">
        <input type="radio" id="askMessage" name="question" checked="checked"/><label for="askMessage">Message</label>
        <input type="radio" id="askImage" name="question" /><label for="askImage">Image</label>
    </div>
    <div class="field" id="messageField">
        <textarea id="clientMessage" placeholder="Message" rows="4"></textarea>
    </div>
    <div class="field" id="imageField">
        <input id="clientImage" type="file" />
        <br>
        <p>Size limit currently 512kb</p>
    </div>
    <ul class="actions" style="margin-top: 1em;">
        <li><button id="encryptBtn" class="button" onclick="encryptMessage()">Get Encrypted Link</button></li>
    </ul>
</div>
<p style="font-size: 90%">Messages are transmitted with AES And stored plaintext unless you add a password.<br>Create an account for auto key management and a contact list.<br><br>Messages are always sent as AES to prevent snooping attacks.</p>

<script src="{{ URL::asset('js/drop/encrypt.js') }}"></script>
