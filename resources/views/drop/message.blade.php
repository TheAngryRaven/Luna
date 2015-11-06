

<?php
$message = Session::get('encMessage');
$type = Session::get('encMessageType');
if( $type == 0 ){
?>
<section id="finalMessage" style="text-align: left;">{{ Session::get('encMessage') }}</section>
<?php
} else {
?>
<div id="finalMessage" style="max-width:100%;max-height:100%;">{{ Session::get('encMessage') }}</div>
<?php
}
?>