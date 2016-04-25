//for loading password protected messages
//sends password hash to server to check
function decryptMessage(){
    logConsole( "%c Attempting to decrypt YOUR message ", 'background: #222; color: #66FF33');

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
        var encryptedHash = GibberishAES.enc( passwordHash, satellite.lss.serverAES );
        //password.val('');
        satellite.mPass = password.val();

        logConsole( "%c Ask server if we got it right  ", 'background: black; color: white');
        //call new url for attempt
        var currentURL = window.location.href;
        var ajaxToken = $('#token').val();
        //todo encrypt
        $.ajax({
            //url: currentURL,
            url: extractDomain()+findPage(),
            type:"POST",
            data: {
                '_token':       satellite.lss.token,
                'handshake':    satellite.lss.handshake,
                'hash':         encryptedHash
            },
            success:function(data){
                if (data.status === 302) {
                    loadPage( '#drop' );
                } else {
                    attemptDecryption( data );
                }
            },error:function(){
                alert("[something happened]");
                $('#decryptBtn').prop("disabled",false);
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
        var decryptedServerMessage = GibberishAES.dec( data.pageData, satellite.lss.aesKey);

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