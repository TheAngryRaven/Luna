<!--
@include('nav')
-->

<div id="messageBox" style="margin:0;">
    <ul class="actions" style="margin:0;">
        <button id="viewBtn" style="width: 100%; color: black;" onclick="viewMessage()">View Message</button>
        <p style="margin: 1em 0 0 0">Reminder: Messages can only be viewed once.</p>

    </ul>
</div>

<script src="{{ URL::asset('js/drop/view.js') }}"></script>