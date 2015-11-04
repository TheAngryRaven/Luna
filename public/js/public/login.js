
var userName                = $('#userName');
var userPassword            = $('#userPassword');
var userPassphrase          = $('#userPassphrase');
var authBtn                 = $('#authBtn');

function authUser(){
    //eventually to prevent double posting and shit
    authBtn.prop("disabled",true);

    if( userName.val() === '' ) {
        //todo: ajax check
        alert('Must enter a username');
        authBtn.prop("disabled", false);
    } else if( validateUserName( userName.val() ) === false ){
        alert('Username must be between 3-20 characters, and contain no spaces. only allowed characters are A-Z 0-9 _ and -');
        authBtn.prop("disabled",false);
    }else if( userPassword.val() === '' || userPassword.val() == null ){
        alert('You must include a password');
        authBtn.prop("disabled",false);
    }else if( userPassword.val().length < 6 ){
        alert('Password Not Long Enough [min: 6]');
        authBtn.prop("disabled",false);
    }else if( userPassphrase.val() === '' || userPassphrase.val() == null ){
        alert('You must include a passphrase');
        authBtn.prop("disabled",false);
    }else if( userPassphrase.val().length < 10 ){
        alert('Passphrase Not Long Enough [min: 10]');
        authBtn.prop("disabled",false);
    }else  {
        //finally were good to fuckin go

        //setup post object
        //rover is going to be the standard post data object
        var rover = {
            UserName: null,
            PasswordHash: null,
            PassphraseHash: null
        };

        rover.UserName = userName.val();
        userName.val('');

        rover.PasswordHash = CryptoJS.SHA256( userPassword.val()).toString();
        userPassword.val('');

        rover.PassphraseHash = CryptoJS.SHA256( userPassphrase.val()).toString();
        userPassphrase.val('');

        //calls the server using the rover object
        callServer( rover );
    }
}

function callServer( rover ){
    console.log( rover );

    //turn rover to string
    var roverString = JSON.stringify( rover );
    var roverEncrypted = GibberishAES.enc( roverString, satellite.connection.serverAES );

    //laravel "form" token
    var ajaxToken = $('#token').val();

    $.ajax({
        url: 'auth',
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
    var decryptedData = GibberishAES.dec( data.cipherText, satellite.connection.aesKey);
    decryptedData = JSON.parse( decryptedData );

    console.log( decryptedData );

    if( decryptedData.status === true ){
        //yay its good
        //alert( decryptedData.message );
        window.location.href = extractDomain();
    } else {
        //nop u fuked up
        alert( decryptedData.message );
    }

    //reset button
    authBtn.prop("disabled",false);
}