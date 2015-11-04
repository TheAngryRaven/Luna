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
    //logConsole( decryptedMessage );
    $("#encryptedContent").html( decryptedMessage );

    logConsole( "%c Setting server AES key ", 'background: black; color: white');
    satellite.connection.serverAES = GibberishAES.dec( data.serverAES, satellite.connection.aesKey );
    logConsole( satellite.connection.serverAES );

    logConsole( "%c PAGE LOADED AND DECRYPTED ", 'background: #222; color: #FF7519');
}

//easiest way without "hacking" with flash or something
//makes a pop up not a technical copy to clip function
function copyToClipboard(text) {
    window.prompt("Copy to clipboard: Send to friend", text);
}

//one comment to rule them all
function logConsole( text, style ){
    console.log(text, style);
}

//simple regex function
function validateEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}

//simple regex function
function validateUserName(userName) {
    var re = /^[a-zA-Z0-9_-]{3,20}$/i;
    return re.test(userName);
}

function extractDomain() {
    var url = window.location.href;

    var domain;
    //find & remove protocol (http, ftp, etc.) and get domain
    if (url.indexOf("://") > -1) {
        domain = url.split('/')[2];
    }
    else {
        domain = url.split('/')[0];
    }

    //find & remove port number
    domain = domain.split(':')[0];

    return 'http://'+domain+'/';
}

//maybe help show less white screen?
$( document ).ready(function() {
    configureCrypto();
    loadPage();
});