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


//rover is going to be the standard post data object
var rover = {
    Email: null,
    UserName: null,
    PasswordHash: null,
    PassphraseHash: null
};

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
        rover.Email = userEmail.val();
        userEmail.val('');

        rover.UserName = userName.val();
        userName.val();

        rover.PasswordHash = CryptoJS.SHA256( userPassword.val()).toString();
        userPassword.val('');
        userPassword.val('');

        rover.PassphraseHash = CryptoJS.SHA256( userPassphrase.val()).toString();
        userPassphrase.val('');
        userPassphrase.val('');
    }
}

function callServer( roverObject ){
    console.log( roverObject );
}