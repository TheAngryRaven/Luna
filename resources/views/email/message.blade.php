@extends('email.master')

@section('header', 'New Message')

<?php /* Already inside of a paragraph tag */ ?>
@section('body')
    You have a message waiting for you on our network, please <a href="{{ $messageURL }}">click here</a>.<br>Please note the message will be deleted once viewed.
@endsection
