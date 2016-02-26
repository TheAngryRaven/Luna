<!--
@include('nav')
-->

<h2>Lunar Messaging</h2>
<p>Hot drop service, no account required.</p>
<hr>
<div>
    <div class="field">
        <input type="email" id="sendTo" placeholder="Send link to email (optional)" />
    </div>
    <br>
    <div class="field">
        <input type="password" id="encryptWith" placeholder="Password [min: 6] (optional)" />
    </div>
    <div>
        <br>
        <p>Make sure your recipient knows the password!</p>
    </div>
    <!--div class="field">
        <input type="radio" id="askMessage" name="question" checked="checked"/><label for="askMessage">Message</label>
        <input type="radio" id="askImage" name="question" /><label for="askImage">Image</label>
    </div-->
    <hr>
    <div class="field" id="messageField">
        <textarea id="clientMessage" placeholder="Message" rows="4"></textarea>
    </div>
    <!--div class="field" id="imageField">
        <input id="clientImage" type="file" />
        <br>
        <p>Size limit currently 512kb</p>
    </div-->
    <ul class="actions" style="margin-top: 1em;">
        <li><button id="encryptBtn" class="button" onclick="encryptMessage()">Get/Send Encrypted Link</button></li>
    </ul>
</div>
<hr>
<p style="font-size: 90%">
<b>EVERY</b> message is deleted the moment it is viewed.<br><br>
All information is transmitted with AES encryption to prevent snooping attacks.<br><br>
By default messages are saved as plaintext for an easy messaging system.<br><br>
If you include a password, this will create full end-to-end encryption.<br><br>
On top of all the encryption we provide, you are also secured with HTTPS.
</p>

<script src="{{ URL::asset('js/drop/encrypt.js') }}"></script>
