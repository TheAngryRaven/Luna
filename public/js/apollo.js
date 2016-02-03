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
        success: function(response) {
            pageLoadResponse(response);
        },
        error: function(response) {
            //loadError(data);
            pageLoadResponse(response)
        }
    });//end of ajax
}

//fancy function handles core page loading
//also deals with errors
//todo: eventually merge with ajax response unit
function pageLoadResponse(data) {
    var statusCode = data.status;

    //now actually check the code
    if (statusCode === 500) {
        //server issue
        displayAlert({ title: 'Whoops - 500', message: 'Technical issues requesting data, reloading page!', error: true });
        window.location.reload();
    } else if (statusCode === 404) {
        //route not found
        displayAlert({ title: 'Whoops - 404', message: 'Sorry, but that resource does not exist.', error: true });

        window.location.href = extractDomain() + '#' + HOME;
    } else if (data.status === 302) {
        //js cant detect redirects so we use this to force one if needed
        window.location.href = extractDomain() + data.location;
    } else if (statusCode === 200) {
        //good to go
        decryptPage(data);

    } else if(statusCode === 505) {
        //custom: basically saying the script failed and this is the default handler
        displayAlert({ title: 'Whoops - 505', message: 'Technical issues requesting data!', error: true });
    } else {
        displayAlert({ title: 'Whoops - ???', message: 'Technical issues requesting data!', error: true });
    }
}

//this decrypts the page data the server has sent us
function decryptPage( data ){
    logConsole( "%c Attempting to decrypt page", 'background: #222; color: #66FF33');

    //actually decrypt the message
    var decryptedMessage = GibberishAES.dec( data.pageData, satellite.lss.aesKey);

    //send data to pagte
    $("#encryptedContent").html( decryptedMessage );

    //get server aes
    satellite.lss.serverAES = GibberishAES.dec( data.handshake, satellite.lss.aesKey );

    logConsole( "%c PAGE LOADED AND DECRYPTED ", 'background: #222; color: #FF7519');
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

//fancy function i wrote to handle both erros and messages
//supports cutsom title, message and buttons, with callback functions

//it may be bootstrap (custom compiled just for glyphs and modals, and just for this)
//but doing it this way, there's only one function to replace
function displayAlert(dataBlock){
    //javascript cant do default values
    //so this checks if they exist if not, set them blank so we don't see "UNDEFINED" in the box
    var inputMessage    = ( dataBlock.message ? dataBlock.message : '' );
    var inputTitle      = ( dataBlock.title ? dataBlock.title : '' );

    //setup the icon to use
    var icon = null;
    var color = null;
    if( dataBlock.error != null && dataBlock.error == true){
        //this is just a simple message
        icon = 'glyphicon glyphicon-exclamation-sign';
        color = '#EE2F0C';
    } else {
        //this message is an error
        icon = 'glyphicon glyphicon-bullhorn';
        color = '#5144E5';
    }

    //setup the alert title
    var windowTitle = '<h3 style="margin-top: 1em;">'+inputTitle+'</h3>';

    //the html for the body
    var windowMessage = '<div class="row">'+
        '<div class="col-md-12">'+
        '<p style="min-height: 5em; margin: 0;"><i class="'+icon+'" style="color: '+color+'; font-size: 4em;float: left;margin-right: .3em;margin-bottom: .1em;"></i>'+inputMessage+'</p>'+
        '</div>'+
        '</div>';

    //default setup for the buttons
    //this is the default close button, or overwritten below
    var windowButtons = {
        close: {
            label: "Close",
            className: "btn-default",
            callback: function () {
                //no callback needed just close
            }
        }
    };

    //did we use custom buttons?
    if( typeof dataBlock === "object" ){
        if( dataBlock.other != null ) {
            windowButtons.other = {
                label:      dataBlock.other.label,
                className:  dataBlock.other.className ? dataBlock.other.className : 'btn-default',
                callback:   dataBlock.other.callback
            }
        }
        if( dataBlock.close != null ) {
            windowButtons.close = {
                label:      dataBlock.close.label,
                className:  dataBlock.close.className ? dataBlock.close.className : 'btn-default',
                callback:   dataBlock.close.callback
            }
        }
    }

    //ocd and order, and its semi important for ux reasons
    if( typeof dataBlock === "object" && windowButtons.other != null ){
        windowButtons = {
            other: windowButtons.other,
            close: windowButtons.close
        };
    }

    //the bootstrap powered plugin that handles all the other crap involving the popup
    bootbox.dialog({
            title: windowTitle,
            message: windowMessage,
            buttons: windowButtons
        }
    );
}

//test the 3 aspects of the alert function
//and show you how to call it
function testError(){
    displayAlert({
        title: 'Not Really an Error',
        message: "This was a test of the alert function",
        //optional, custom bootstrap class and button text
        close: {
            label: 'Close Window',
            className: 'btn-danger'
        },
        //also optional, basically giving the user the option to like go back to a form
        other: {
            label: 'Process Callback',
            className: 'btn-success',
            callback: function(){
                //returning false will keep the alert open FYI
                displayAlert({ title: 'Plain Message', message: 'You can also just do a super basic popup' });
            }
        },
        //tells the function to either show the red ! or the blue bullhorn
        error: true
    });
}

//new function for unified ajax calls