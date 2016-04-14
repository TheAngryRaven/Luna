<?php
$message = Session::get('encMessage');
$type = Session::get('encMessageType');
if( $type == 0 ){
?>
<section id="finalMessage" style="text-align: left;font-size: 120%;">{{ Session::get('encMessage') }}</section>
<?php
} else {
?>
<div id="finalMessage" style="max-width:100%;max-height:100%;">{{ Session::get('encMessage') }}</div>
<?php
}
?>
<hr>
<p style="font-size: 80%;">Reminder: this message has already been deleted and cannot be accessed again.</p>