
/**
 * The base object that javascript will store all client information
 * nicknamed this cause iunno moon theme, users are satellites
 */
var satellite = {
    connection: {
        crypt:      null,
        publicKey:  null,
        privateKey: null,
        aesKey:     null,
        serverRSA:  null,
        serverAES:  null
    },
    mPass:          null,
    imageBuffer:    null
};

//these two functions prep the "javascript ssl"
//all post AND page data but the core html wrapper and the javascript is encrypted
function configureCrypto(){
    logConsole( "%c Setting Up Crypto Enviroment ", 'background: #222; color: #66FF33');
    satellite.connection.crypt = new JSEncrypt();
    satellite.connection.crypt.getKey();

    satellite.connection.publicKey  = satellite.connection.crypt.getPublicKey();
    satellite.connection.privateKey = satellite.connection.crypt.getPrivateKey();

    //generate random AES key
    var aeskey = Math.random().toString(36).substring(7)+Math.random().toString(36).substring(7)+Math.random().toString(36).substring(7);
    satellite.connection.aesKey     = aeskey;
    satellite.connection.serverRSA  = $('#handshake').val();

    satellite.connection.handshake  = createHandshake( satellite.connection.serverRSA, satellite.connection.aesKey );
}

//encrypts your aes key (page data) with the server public key
function createHandshake( serverKey, aesKey ){
    var shakeCrypto = new JSEncrypt();
    shakeCrypto.setKey( serverKey );
    return shakeCrypto.encrypt( aesKey );
}

//submits a post to the same url, loads a new function which returns the encrypted page content
function loadPage(){
    var currentURL = window.location.href;
    var ajaxToken = $('#token').val();

    //finally run the request
    logConsole( "%c Asking server for encrypted page ", 'background: black; color: white');
    logConsole( satellite.connection.handshake );
    $.ajax({
        url: currentURL,
        type:"POST",
        data: {
            'X-CSRF-TOKEN': ajaxToken,
            '_token':       ajaxToken,
            'handshake':    satellite.connection.handshake
        },
        success:function(data){
            decryptPage( data );
        },error:function(){
            alert("[something happened]");
        }
    }); //end of ajax
}

// "ssl decryptor" for the randomly generated RSA KEYS
function decryptPage( data ){
    logConsole( "%c Decrypting server response ", 'background: black; color: white');
    var decryptedMessage = GibberishAES.dec( data.cipherText, satellite.connection.aesKey);
    //logConsole( data.cipherText );

    logConsole( "%c Sending data to page ", 'background: black; color: white');
    logConsole( decryptedMessage );
    $("#encryptedContent").html( decryptedMessage );

    logConsole( "%c Setting server AES key ", 'background: black; color: white');
    satellite.connection.serverAES = GibberishAES.dec( data.serverAES, satellite.connection.aesKey );
    logConsole( satellite.connection.serverAES );

    logConsole( "%c PAGE LOADED AND DECRYPTED ", 'background: #222; color: #FF7519');
}

//for message upload page
function encryptMessage() {
    var message = $('#clientMessage').val();
    message = message.trim();

    $('#encryptBtn').prop("disabled",true);

    //check message type
    var isMessage = $("#askMessage:checked").val();
    var type = null;

    if (isMessage === "on") {
        type = 'text';
    } else {
        type = 'image';
        message = satellite.imageBuffer;
    }

    //check message size (there's also a serverside check)
    var limit = 1024;

    var mPassword = $('#encryptWith').val();

    if (type === 'text' && message === '' || message === null) {
        alert('Cannot encrypt nothing');
        //re-enable button
        $('#encryptBtn').prop("disabled",false);
    } else if (type === 'text' && message.length > limit) {
        alert('Message exceeds ' + limit + ' characters');
        //re-enable button
        $('#encryptBtn').prop("disabled",false);
    } else if( type === 'image' && satellite.imageBuffer === null ){
        alert( 'no image selected' );
        //re-enable button
        $('#encryptBtn').prop("disabled",false);
    } else if( type === 'image' && ( mPassword === '' || mPassword === null ) ) {
        alert( "I'm sorry but images must be encrypted" );
        //re-enable button
        $('#encryptBtn').prop("disabled",false);
    } else {
        logConsole( "%c Attempting to send message ", 'background: #222; color: #66FF33');

        var error = false;
        //check if email
        var email = $('#sendTo').val();

        //make sure only to error if something in box
        if( ( email !== '' && email !== null) && (validateEmail(email) === false)){
            error = true;
            alert('Please Use a proper Email or remove it');
        } else if ( email !== '' && email !== null){
            logConsole( "%c Send to email  ", 'background: black; color: white');
            logConsole( email );

            //encrypt email
            email = GibberishAES.enc(email, satellite.connection.serverAES);
        }

        //password validation
        if( mPassword !== '' && mPassword !== null){
            if(mPassword.length < 6){
                error = true;
                alert('Minimum password length is 6 characters');
            } else {
                //encrypt the message pre send
                logConsole( "%c Has password, encrypting message  ", 'background: black; color: white');

                if( type === 'text' ) {
                    message = GibberishAES.enc(message, mPassword);
                } else {
                    message = GibberishAES.enc(satellite.imageBuffer, mPassword);
                }

                logConsole( "%c AES Message Size  ", 'background: black; color: white');

                logConsole( message.length );
                //logConsole( message );

                //now its encrypted hash the password
                logConsole( "%c Hashed password (sha256)  ", 'background: black; color: white');
                mPassword = CryptoJS.SHA256(mPassword).toString();
                logConsole( mPassword );

                //now encrypt
                logConsole( "%c Encrypting password  ", 'background: black; color: white');
                mPassword = GibberishAES.enc(mPassword, satellite.connection.serverAES);
            }
        }

        //finally do shit
        if( error !== true ){
            logConsole( "%c Encrypt Message with server AES ", 'background: black; color: white');

            var encryptedMessage = GibberishAES.enc(message, satellite.connection.serverAES);
            var ajaxToken = $('#token').val();
            //logConsole( encryptedMessage );

            $.ajax({
                url: 'encrypt',
                type: "POST",
                data: {
                    'X-CSRF-TOKEN': ajaxToken,
                    '_token': ajaxToken,
                    'handshake': satellite.connection.handshake,
                    'message': encryptedMessage,
                    'password': mPassword,
                    'email': email,
                    'type': type
                },
                success: function (data) {
                    serverResponse(data);
                }, error: function () {
                    alert("[something happened encrypting your message]");
                }
            }); //end of ajax

            logConsole( "%c DONE MESSAGE SAVED ", 'background: #222; color: #FF7519');
        } else {
            $('#encryptBtn').prop("disabled",false);
            logConsole( "%c DONE NO MESSAGE ", 'background: #222; color: #FF7519');
        }
    }
}

//returns the message URL
function serverResponse( data ){
    logConsole( "%c Received data from server ", 'background: black; color: white');
    //logConsole( data );
    //located in the inner blade not the master
    //$("#serverResponse").html( "<p>Copy and send this link</p><br/>"+data );

    //window popup for easy copying
    //copyToClipboard( data );
    if( data === 'sent') {
        alert('An email was dispatched');
    } else {
        copyToClipboard( data );
    }

    //resetting message box
    $('#clientMessage').val('');
    $('#sendTo').val('');
    $('#encryptWith').val('');

    //reset image "buffer"
    satellite.imageBuffer = null;
    $('#clientImage').val("");

    //re-enable button
    $('#encryptBtn').prop("disabled",false);

    //we done
    logConsole( "%c MESSAGE SECURELY UPLOADED ", 'background: #222; color: #FF7519');
}

//for loading password protected messages
//sends password hash to server to check
function decryptMessage(){
    logConsole( "%c Attempting to decrypt message ", 'background: #222; color: #66FF33');

    var password = $('#decryptPassword');
    //disable to prevent double post and lost message
    $('#decryptBtn').prop("disabled",true);

    if( password.val() === '' || password.val() === null) {
        alert('Please enter a password');
        logConsole( "%c DONE NO PASSWORD ", 'background: #222; color: #FF7519');

        //re-enable button
        $('#decryptBtn').prop("disabled",false);
    } else {
        var passwordHash = CryptoJS.SHA256( password.val() );
        var encryptedHash = GibberishAES.enc( passwordHash, satellite.connection.serverAES );
        //password.val('');
        satellite.mPass = password.val();

        logConsole( "%c Ask server if we got it right  ", 'background: black; color: white');
        //call new url for attempt
        var currentURL = window.location.href;
        var ajaxToken = $('#token').val();
        $.ajax({
            url: currentURL,
            type:"POST",
            data: {
                'X-CSRF-TOKEN': ajaxToken,
                '_token':       ajaxToken,
                'handshake':    satellite.connection.handshake,
                'hash':         encryptedHash
            },
            success:function(data){
                attemptDecryption( data );
            },error:function(){
                alert("[something happened]");
            }
        }); //end of ajax
    }
}

//actually decrypts the message and sticks it in the page
function attemptDecryption(data){
    if(data === 'no'){
        logConsole( "%c DONE INVALID PASSWORD ", 'background: #222; color: #FF7519');
        alert('invalid password');
        $('#decryptPassword').val('');
    } else {
        var decryptedServerMessage = GibberishAES.dec( data.cipherText, satellite.connection.aesKey);

        logConsole( "%c Sending encrypted data to page ", 'background: black; color: white');
        //logConsole( decryptedServerMessage );
        $("#encryptedContent").html( decryptedServerMessage );

        //now decrypt the message
        var serverResp = $("#finalMessage");
        var cryptic = serverResp.html();

        logConsole( "%c Decrypting Message ", 'background: black; color: white');
        var decrypted = GibberishAES.dec( cryptic, satellite.mPass );

        var imgDom = $("#image");
        if( data.other === 0){
            serverResp.html( decrypted );
        } else {
            serverResp.html( '' );
            imgDom.html( '<img style="max-width: 100%;" src="'+decrypted+'" alt="">' );
            $('#main').hide();//ugly hacks for now
        }

        logConsole( "%c DONE DECRYPTING MESSAGE ", 'background: #222; color: #FF7519');
    }
    $('#decryptBtn').prop("disabled",false);
}

//easiest way without "hacking" with flash or something
//makes a pop up not a technical copy to clip function
function copyToClipboard(text) {
    window.prompt("Copy to clipboard: Send to friend", text);
}

//one comment to rule them all
function logConsole( text, style ){
    //console.log(text, style);
}

//simple regex function
function validateEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}

//for some reason when you called both in the doc.ready it derps
function init(){
    configureCrypto();
    loadPage();
}

//maybe help show less white screen?
$( document ).ready(function() {
    init();
});

//sets up hiding the message box or the image field
function setDisplays(){
    $('#imageField').hide();

    //pick one nerd
    $( "input[name='question']" ).change(function() {
        var isMessage = $( "#askMessage:checked" ).val();
        var isImage = $( "#askImage:checked" ).val();
        if(isImage === "on"){
            //doing image
            $('#imageField').show();
            $('#messageField').hide();

            $('#clientMessage').val('');
        }
        if(isMessage === "on"){
            //doing message
            $('#messageField').show();
            $('#imageField').hide();

            //reset image "buffer"
            satellite.imageBuffer = null;
            $('#clientImage').val("");
        }

    });

    //for geting image data
    $("#clientImage").change(function(){
        readImage( this );
    });
}

//yea kinda coupled right now...
function readImage(input) {
    if ( input.files && input.files[0] ) {
        var FR= new FileReader();
        FR.onload = function(e) {
            logConsole( "%c Loading Image ", 'background: black; color: white');
            var image = e.target.result;

            if( image.substr(0, 15) === 'data:image/png;' ||
                image.substr(0, 15) === 'data:image/bmp;' ||
                image.substr(0, 15) === 'data:image/gif;' ||
                image.substr(0, 16) === 'data:image/jpeg;'
            ) {
                //console.log('loaded image');
                //console.log('image size ['+image.length+']');

                //tottaly an improper way to do this...
                if(image.length > 750000){
                    alert('Image must be under 512kb');

                    //reset image "buffer"
                    satellite.imageBuffer = null;
                    $('#clientImage').val("");
                } else {
                    satellite.imageBuffer = image;
                }

            } else {
                alert( 'Invalid File Type' );
                //reset image "buffer"
                satellite.imageBuffer = null;
                $('#clientImage').val("");
            }
        };
        FR.readAsDataURL( input.files[0] );
    }
}
