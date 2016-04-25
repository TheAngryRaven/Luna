<h2>Lunar Messaging Prototype</h2>
<p>One way encrypted messaging, no account required.</p>
<hr>

<div>
    <p>Once your message has been received you will be provided with a link that needs to be given to your recepient.</p>
    <div class="field">
        <input type="email" id="sendTo" placeholder="Automatically send link to an email (optional)" />
    </div>
    <br>
    <div class="field">
        <input type="password" id="encryptWith" placeholder="Password [min: 6] (optional)" />
    </div>

    <div>
        <br>
        <p>Make sure your recipient knows the password!</p>
    </div>
    <hr>
    <div class="field" id="messageField">
        <textarea id="clientMessage" placeholder="Message" rows="4"></textarea>
    </div>
    <ul class="actions" style="margin-top: 1em;">
        <li><button id="encryptBtn" class="button" onclick="encryptMessage()">Get Encrypted Link</button></li>
    </ul>
</div>

<hr>

<button id="aboutBtn" style="width: 100%; border-color: rgba(7, 128, 149, 0.5); color: rgb(13, 119, 137) !important;" onclick="(function showAbout(){ $('#aboutBlock').show();$('#aboutBtn').hide(); })();">About Lunar Messaging</button>

<div id="aboutBlock" style="text-align: left; padding: 1em;display: none;">
    <h4>Service Information</h4>
    <p>Lunar messaging comprises of two key components, content encryption and message encryption.</p>
    <p>Once the shell of this site loads, your device and our server generates a set of keys that allow for encrypted transmission of all content outside of your messages.</p>

    <h5>Important Prototype Notes</h5>
    <p><b>Currently, while all the transmissions are encrypted. Unless you add a password messages are saved plaintext in the database</b>.</p>

    <p>We cannot read password protected messages. That information is saved and encrypted on your devices not ours. Along with all of this protection, messages are deleted once they have been received.</p>

    <p>Once development has been completed every contact you add will have their own unique key set, and every single message that you send will be fully encrypted from you, to your recipient, with no settings to configure or passwords to remember. Again, at no time will we be able to intercept and read protected messages.</p>

    <p>Below is a bit of a roadmap explaing the goals of this service.</p>

    <hr>
    <h4>Roadmap</h4>
    <ul style="list-style: none;">
        <li>
            <input type="radio" id="check_0" checked disabled/><label for="check_0">Open Source</label>
            <p>This project is, and will always be open to the public. <a target="_blank" href="https://github.com/CrimsonDove/Luna">github.com/CrimsonDove/Luna</a></p>
        </li>
        <li>
            <input type="radio" id="check_1" checked disabled/><label for="check_1">Get RSA and AES working within javascript</label>
            <p>RSA is what's used for the public-private keypairs. Once a keypair has been generated for both parties, a random password is generated and used for AES encryption. This is what's used to encrypt the bulk of the data within the service.</p>
        </li>
        <li>
            <input type="radio" id="check_2" checked disabled/><label for="check_2">Create a core encryption system</label>
            <p>Codenamed "Apollo", this chunk of javascript handles all of the encryption, and page loading aspects of the site. Apollo also handles all of the dynamic page loading that makes this site possible.</p>
        </li>
        <li>
            <input type="radio" id="check_3" checked disabled/><label for="check_3">Basic one way message</label>
            <p>Create some kind of system to piggy back off "Apollo" for sending basic "low-security" messages, with the OPTION for "E2E Encryption". This is to act as a “proof of concept” to show that all these ideas would be possible.</p>
        </li>
        <li>
        <li>
            <input type="radio" id="check_4"  disabled/><label for="check_4">User registration and contact messaging</label>
            <p>The current "idea" is that you will login with two passwords. One will authenticate you and prove that you are you (ie: password123). The other will be a phrase (ie: battery horse staple). This phrase will be used to secure all encryption keys associated with your account keeping your private information private.</p>
            <p>Some progress has been made on this front, I keep re-doing the database. </p>
        </li>
        <li>
            <input type="radio" id="check_5" disabled/><label for="check_5">Image Messaging</label>
            <p>I removed images from the prototype because the system was a little wacky and I wanted to focus on patching up the whole system and building a solid framework, before adding more features.</p>
        </li>
        <li>
            <input type="radio" id="check_6" disabled/><label for="check_6">Voice Messaging</label>
            <p>Not to be confused with “encrypted calls”, this would be the equivalent of sending an encrypted voicemail.</p>
        </li>
        <li>
            <input type="radio" id="check_7" disabled/><label for="check_7">Two Factor Authentication</label>
            <p>An optional feature to always make sure there is never un-authorized access to your account.</p>
        </li>
        <li>
            <input type="radio" id="check_8" disabled/><label for="check_8">Fresh Coat of Paint</label>
            <p>The site is currently using a hodgepodge of templates. Once the site works as advertised I will focus on the actual design. If everything goes according to plan, worst case scenario the site will official January 2017.</p>
        </li>
        <li>
            <input type="radio" id="check_9" disabled/><label for="check_9">The future</label>
            <p>Once completed the adventure of features begin. With goals from html5 image editing, to a downloadable application for even more security, there is a lot of fun places this project can go.<p></p>As long as people are interested in this service, development will always continue.</p>
        </li>
    </ul>

    <button id="aboutBtn" style="width: 100%; border-color: rgba(7, 128, 149, 0.5); color: rgb(13, 119, 137) !important;" onclick="(function showAbout(){ $('#aboutBlock').hide();$('#aboutBtn').show(); })();">Hide Information</button>

</div>

<script src="{{ URL::asset('js/drop/encrypt.js') }}"></script>
