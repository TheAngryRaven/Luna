var HOME = 'home'; //name to default to in findPage()

var satellite = {
    lss: {
        crypt:      null, //the JSEncrypt object in charge of your connection
        publicKey:  null,
        privateKey: null,
        aesKey:     null, //sever sends data back to you with this
        serverRSA:  null, //server publickey 
        serverAES:  null, //we send data to the server with this
        handshake:  null, //your lss aes key encrypted with the servers public
        token:      null //the laravel form token
    },
    drop: {
        messagePass: null, //set on encrypted message
        imageBuffer: null //currently saving the drop image as base64 here... seems to the the only way to really deal with images client side?
    }
};

//function sets all the base code on page load
$( document ).ready(function() {
    configureCrypto();
    loadPage( findPage() );
});

//the fancy page loader thing
$(window).bind('hashchange', function() {
    var hash = window.location.hash.replace(/^#/,'');
    //do whatever you need with the hash
    loadPage( hash );
});

//figures out the hash like above but on call (mainly on page load)
//default home page if no hash
function findPage(){
    var url = window.location.href;
    var hash = url.split('#');

    //returns the hash value to direct to
    if( $.isEmptyObject(hash[1]) === false ) {
        return hash[1];
    } else {
        return HOME;
    }
}

/*
 * SITE CORE FUNCTIONALITY
 */

//this function sets up everything to do with LSS
function configureCrypto() {
    logConsole( "%c Setting Up Crypto Enviroment ", 'background: #222; color: #66FF33');

    logConsole( "%c Setting up client-side keys ", 'background: black; color: white');

    satellite.lss.crypt = new JSEncrypt();
    satellite.lss.crypt.getKey();

    //sets session keys to js object
    satellite.lss.publicKey  = satellite.lss.crypt.getPublicKey();
    satellite.lss.privateKey = satellite.lss.crypt.getPrivateKey();

    //generate random AES key
    var aeskey = Math.random().toString(36).substring(7)+Math.random().toString(36).substring(7)+Math.random().toString(36).substring(7);
    satellite.lss.aesKey     = aeskey;

    logConsole( "%c gathering server publicKey and Handshake ", 'background: black; color: white');

    //get server details from page
    satellite.lss.serverRSA = $('#handshake').val();
    satellite.lss.token     = $('#token').val();

    //set the handshake to pass with each request
    //TODO: validation with the session key
    satellite.lss.handshake = createHandshake( satellite.lss.serverRSA, satellite.lss.aesKey );
}

//encrypts your aes key (page data key) with the server public key
function createHandshake( serverKey, aesKey ){
    logConsole( "%c creating lss handshake ", 'background: black; color: white');
    var shakeCrypto = new JSEncrypt();
    shakeCrypto.setKey( serverKey );
    return shakeCrypto.encrypt( aesKey );
}

//runs the ajax call for page data
function loadPage( pageName ){
    logConsole( "%c Attempting to load ["+pageName+"] ", 'background: #222; color: #66FF33');

    $.ajax({
        url: extractDomain()+pageName,
        type: 'POST',
        data: {
            '_token': satellite.lss.token,
            'handshake': satellite.lss.handshake
        },
        success: function(data, status, response) {
            loadResponse(data, response);
        },
        error: function(response) {
            //loadError(data);
            loadResponse(null,response)
        }
    });//end of ajax
}

//error handeler
function loadError(data){
    var statusCode  = data.status;
    alert('[something happened, status: '+statusCode+']');
    console.log( data );
}

//fancy function to handle a redirect or what
function loadResponse(data, response){
    var statusCode = response.status;
    if(statusCode === 500){
        //server issue
        alert('Technical issues requesting data');
    } else if(statusCode === 404) {
        //lol not found
        alert('Page Not Found');

        window.location.href = extractDomain()+'#'+HOME;
    } else if(statusCode === 200) {
        //good to go

        if( data.status === 302 ){
            window.location.href = extractDomain()+data.location;
        } else if( data.status === 500){
            alert( 'Technical issue with request, please refresh' );
        }else {
            decryptPage(data);
        }
    }
}

//this decrypts the page data the server has sent us
function decryptPage( data ){
    logConsole( "%c Attempting to decrypt page", 'background: #222; color: #66FF33');

    if( data === 'no' ){
        window.location.href = extractDomain()+"#home";
        logConsole( "%c error with message, redirected home ", 'background: #222; color: #FF7519');
    } else {

        //actually decrypt the message
        var decryptedMessage = GibberishAES.dec( data.pageData, satellite.lss.aesKey);

        //send data to pagte
        $("#encryptedContent").html( decryptedMessage );

        //get server aes
        satellite.lss.serverAES = GibberishAES.dec( data.handshake, satellite.lss.aesKey );

        logConsole( "%c PAGE LOADED AND DECRYPTED ", 'background: #222; color: #FF7519');
    }
}


/*
 * HELPER FUNCTIONS
 */

//easiest way without "hacking" with flash or something
//makes a pop up not a technical copy to clip function
function copyToClipboard(text) {
    window.prompt("Copy to clipboard: Send to friend", text);
}

//one comment to rule them all
function logConsole( text, style ){
    if(style === null) { style = '';}

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

//gets the domain name from the browser
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