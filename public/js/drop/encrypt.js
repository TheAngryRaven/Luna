var dropMessageField = $('#messageField');
var dropClientMessage = $('#clientMessage');

var dropSendTo = $('#sendTo');
var dropEncryptWith = $('#encryptWith');
var dropEncryptBtn = $('#encryptBtn');



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

//setDisplays();

//for message upload page
function encryptMessage() {
    var message = $('#clientMessage').val();
    message = message.trim();

    dropEncryptBtn.prop("disabled",true);

    //removed all image capabilities for now
    var type = 'text';

    //check message size (there's also a serverside check)
    var limit = 1024;

    var mPassword = dropEncryptWith.val();

    if (type === 'text' && message === '' || message === null) {
        alert('Cannot encrypt nothing');
        //re-enable button
        $('#encryptBtn').prop("disabled",false);
    } else if (type === 'text' && message.length > limit) {
        alert('Message exceeds ' + limit + ' characters');
        //re-enable button
        $('#encryptBtn').prop("disabled",false);
    } else {
        logConsole( "%c Attempting to send message ", 'background: #222; color: #66FF33');

        var error = false;
        //check if email
        var email = dropSendTo.val();

        //make sure only to error if something in box
        if( ( email !== '' && email !== null) && (validateEmail(email) === false)){
            error = true;
            alert('Please Use a proper Email or remove it');
        } else if ( email !== '' && email !== null){
            logConsole( "%c Send to email  ", 'background: black; color: white');
            logConsole( email );

            //encrypt email
            email = GibberishAES.enc(email, satellite.lss.serverAES);
        }

        //password validation
        if( mPassword !== '' && mPassword !== null){
            if(mPassword.length < 6){
                error = true;
                alert('Minimum password length is 6 characters');
            } else {
                //encrypt the message pre send
                logConsole( "%c Has password, encrypting message  ", 'background: black; color: white');

                message = GibberishAES.enc(message, mPassword);

                logConsole( "%c AES Message Size  ", 'background: black; color: white');

                logConsole( message.length );
                //logConsole( message );

                //now its encrypted hash the password
                logConsole( "%c Hashed password (sha256)  ", 'background: black; color: white');
                mPassword = CryptoJS.SHA256(mPassword).toString();
                logConsole( mPassword );

                //now encrypt
                logConsole( "%c Encrypting password  ", 'background: black; color: white');
                mPassword = GibberishAES.enc(mPassword, satellite.lss.serverAES);
            }
        }

        //finally do shit
        if( error !== true ){
            logConsole( "%c Encrypt Message with server AES ", 'background: black; color: white');

            var encryptedMessage = GibberishAES.enc(message, satellite.lss.serverAES);
            var ajaxToken = $('#token').val();
            //logConsole( encryptedMessage );

            //todo: encrypt
            $.ajax({
                url: 'encrypt',
                type: "POST",
                data: {
                    '_token': satellite.lss.token,
                    'handshake': satellite.lss.handshake,
                    'message': encryptedMessage,
                    'password': mPassword,
                    'email': email,
                    'type': type
                },
                success: function (data) {
                    serverResponse(data);
                }, error: function () {
                    alert("[something happened encrypting your message]");
                    $('#encryptBtn').prop("disabled",false);
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
    dropClientMessage.val('');
    dropSendTo.val('');
    dropEncryptWith.val('');

    //reset image "buffer"
    //satellite.drop.imageBuffer = null;
    //dropClientImage.val("");

    //re-enable button
    dropEncryptBtn.prop("disabled",false);

    //we done
    logConsole( "%c MESSAGE SECURELY UPLOADED ", 'background: #222; color: #FF7519');
}


