<h2>Lunar Messaging Prototype</h2>
<p>One way encrypted messaging, no account required.</p>
<hr>
<a href="https://www.kickstarter.com/projects/1483928940/lunarmessagingnet" target="_blank">Currently Raising Funds on Kickstarter!</a>
<hr>
<div>
    <p>Once your message has been received you will be provided with a link that needs to be given to your recepient.</p>
    <div class="field">
        <input type="email" id="sendTo" placeholder="Automatically send link to an email (optional)" />
    </div>
    <br>
    <div class="field">
        <input type="password" id="encryptWith" placeholder="Password [min: 6] (optional)" />
    </div>

    <div>
        <br>
        <p>Make sure your recipient knows the password!</p>
    </div>
    <hr>
    <div class="field" id="messageField">
        <textarea id="clientMessage" placeholder="Message" rows="4"></textarea>
    </div>
    <ul class="actions" style="margin-top: 1em;">
        <li><button id="encryptBtn" class="button" onclick="encryptMessage()">Get Message URL</button></li>
    </ul>
</div>

<script src="{{ URL::asset('js/drop/encrypt.js') }}"></script>
