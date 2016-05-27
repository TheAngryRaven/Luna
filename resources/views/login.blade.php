@include('nav')
<div>
    <div class="field">
        <input type="text" id="userName" placeholder="Display / User Name">
        <br>
        <input type="password" id="userPassword" placeholder="Account Password (minimum: 6)" />
    </div>

    <ul class="actions" style="margin-top: 1em;">
        <li><button id="authBtn" class="button" onclick="authUser()">Log On</button></li>
    </ul>
</div>
<script src="{{ URL::asset('js/public/login.js') }}"></script>


