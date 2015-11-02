/**
 * ONLY PASSWORD HASHES ARE SENT OVER THE WIRE
 */
var userEmail               = $('#userEmail');
var userName                = $('#userName');
var userPassword            = $('#userPassword');
var userPasswordConfirm     = $('#userPasswordConfirm');
var userPassphrase          = $('#userPassphrase');
var userPassphraseConfirm   = $('#userPassphraseConfirm');
var registerBtn             = $('#registerBtn');

//checks all validation then launch function to send object
function registerUser(){
    //eventually to prevent double posting and shit
    registerBtn.prop("disabled",true);

    if( userEmail.val() !== ''&& validateEmail( userEmail.val() ) === false ){
        alert('If using an email, it must be a valid one');
        registerBtn.prop("disabled",false);
    }else if( userName.val() === '' ) {
        //todo: ajax check
        alert('Must enter a username');
        registerBtn.prop("disabled", false);
    } else if( validateUserName( userName.val() ) === false ){
        alert('Username must be between 3-20 characters, and contain no spaces. only allowed characters are A-Z 0-9 _ and -');
        registerBtn.prop("disabled",false);
    }else if( userPassword.val() === '' || userPassword.val() == null ){
        alert('You must include a password');
        registerBtn.prop("disabled",false);
    }else if( userPassword.val().length < 6 ){
        alert('Password Not Long Enough [min: 6]');
        registerBtn.prop("disabled",false);
    }else if( userPassword.val() !== userPasswordConfirm.val() ){
        alert('Password does not match confirmation');
        registerBtn.prop("disabled",false);
    }else if( userPassphrase.val() === '' || userPassphrase.val() == null ){
        alert('You must include a passphrase');
        registerBtn.prop("disabled",false);
    }else if( userPassphrase.val().length < 10 ){
        alert('Passphrase Not Long Enough [min: 10]');
        registerBtn.prop("disabled",false);
    }else if( userPassphrase.val() !== userPassphraseConfirm.val() ){
        alert('Passphrase does not match confirmation');
        registerBtn.prop("disabled",false);
    } else {
        //finally were good to fuckin go

        //setup post object
        //rover is going to be the standard post data object
        var rover = {
            Email: null,
            UserName: null,
            PasswordHash: null,
            PassphraseHash: null
        };

        //set null for null email
        if( userEmail.val() === '' ) {
            rover.Email = null;
        } else {
            rover.Email = userEmail.val();
            userEmail.val('');
        }

        rover.UserName = userName.val();
        userName.val('');

        rover.PasswordHash = CryptoJS.SHA256( userPassword.val()).toString();
        userPassword.val('');
        userPasswordConfirm.val('');

        rover.PassphraseHash = CryptoJS.SHA256( userPassphrase.val()).toString();
        userPassphrase.val('');
        userPassphraseConfirm.val('');

        //calls the server using the rover object
        callServer( rover );
    }
}

function callServer( rover ){
    //console.log( rover );

    //turn rover to string
    var roverString = JSON.stringify( rover );
    var roverEncrypted = GibberishAES.enc( roverString, satellite.connection.serverAES );

    //laravel "form" token
    var ajaxToken = $('#token').val();

    $.ajax({
        url: 'registration',
        type: "POST",
        data: {
            'X-CSRF-TOKEN': ajaxToken,
            '_token': ajaxToken,
            'handshake': satellite.connection.handshake,
            'rover': roverEncrypted
        },
        success: function (data) {
            roverResponse(data);
        }, error: function () {
            alert("[something happened attempting to register]");
            registerBtn.prop("disabled",false);
        }
    }); //end of ajax

}

function roverResponse( data ){

    console.log( data );

    //reset button
    registerBtn.prop("disabled",false);
}