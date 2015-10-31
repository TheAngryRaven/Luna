<h2>Lunar Messaging</h2>
<div  style="margin:0;">
    <div class="field">
        <input type="password" id="decryptPassword" placeholder="Please Input Password" />
    </div>
    <br>
    <ul class="actions" style="margin:0;">
        <li><button id="decryptBtn" onclick="decryptMessage()">Decrypt Message</button></li>
    </ul>
</div>

<script src="{{ URL::asset('js/drop/decrypt.js') }}"></script>