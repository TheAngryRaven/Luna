<?php
    if( UserService::isLoggedIn() == false ){
?>
    <div id="nav" style="margin-bottom: 1em;">
        <a href="#home" class="button">home</a>
        <!--a href="#login" class="button">Login</a>
        <--a href="#register" class="button">Register</a-->
        <a href="#drop" class="button">drop service</a>
    </div>
<?php
    } else {
?>
    <div id="nav" style="margin-bottom: 1em;">
        <a href="#user/dashboard" class="button">Dashboard</a>
        <!--a href="#user/contacts" class="button">Contacts</a>
        <a href="#user/account" class="button">Account</a-->
        <a href="{{ URL::to('logoff') }}" class="button">log off</a>
    </div>
<?php
    }
?>