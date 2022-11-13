<div class="form-wrap">
<h1>[registration]Register[/registration][validation]Update profile[/validation]</h1>
<div class="full text">
[registration]
<b>Hello, dear visitor of our site!</b><br />
Registration on our site will allow you to be its full-fledged participant.
You can add news to the site, leave your comments, view hidden text and much more.
<br />In case of problems with registration, please contact <a href="/index.php?do=feedback">administrator</a> of the site.
[/registration]
[validation]
<b>Dear visitor,</b><br />
Your account has been registered on our website,
however, information about you is incomplete, so fill in the additional fields in your profile.
[/validation]
</div>

[registration]
<div class="form-item clearfix imp">
<label for="name">Login:</label>
<input type="text" name="name" id="name" required />
<input title="Check login availability for registration" onclick="CheckLogin(); return false;" type="button" value="Check name" />
</div>
<div id='result-registration'></div>
<div class="form-item clearfix imp">
<label for="password1">Password:</label>
<input type="password" name="password1" id="password1" required />
</div>
<div class="form-item clearfix imp">
<label for="password2">Repeat password:</label>
<input type="password" name="password2" id="password2" required />
</div>
<div class="form-item clearfix imp">
<label for="email">Your E-Mail:</label>
<input type="text" name="email" id="email" required />
</div>
[question]
<div class="form-item clearfix imp">
<label>Question:</label>
<div class="form-secure"><div style="margin-bottom:10px;">{question}</div>
        <input type="text" name="question_answer" placeholder="Enter the answer to the question" required />
</div>
</div>
[/question]
[sec_code]
<div class="form-item clearfix imp">
<label>Enter the code from the image:</label>
<div class="form-secure">
            <input type="text" name="sec_code" id="sec_code" placeholder="Enter code from image" maxlength="45" required />{reg_code}
</div>
</div>
[/sec_code]
[recaptcha]
<div class="form-item clearfix imp">
<label>Enter two words from the image:</label>
<div class="form-secure">
{recaptcha}
</div>
</div>
[/recaptcha]
[/registration]

[validation]
<div class="form-item clearfix">
<label for="fullname">Your Name:</label>
<input type="text" id="fullname" name="fullname" />
</div>
<div class="form-item clearfix">
<label for="land">Location:</label>
<input type="text" id="land" name="land" />
</div>
<div class="form-item clearfix">
<label for="image">Photo:</label>
<input type="file" id="image" name="image" />
</div>
<div class="form-textarea">
<label>About me:</label>
<textarea id="info" name="info" rows="8" /></textarea>
</div>
<div class="form-xfield">{xfields}</div>
[/validation]

<div class="form-submit">
<button name="submit" type="submit">Submit</button>
</div>

</div>