function viewMessage(){
    logConsole( "%c Human verification ", 'background: black; color: white');
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
            'human':         true
        },
        success:function(data){
            if (data.status === 302) {
                loadPage( '#drop' );
            } else {
                showMessage(data);
            }
        },error:function(){
            alert("[something happened]");
            $('#decryptBtn').prop("disabled",false);
        }
    }); //end of ajax
}
function showMessage(data){
    var decryptedServerMessage = GibberishAES.dec( data.pageData, satellite.lss.aesKey);
    logConsole( "%c Sending message data to page ", 'background: black; color: white');

    var serverResp = $("#messageBox");
    serverResp.html(decryptedServerMessage);

    logConsole( "%c DONE WITH MESSAGE ", 'background: #222; color: #FF7519');
}