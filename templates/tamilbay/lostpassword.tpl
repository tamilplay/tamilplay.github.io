<div class="form-wrap">
<h1>Reset password</h1>
<div class="form-item clearfix">
<label>Your login:</label>
<input type="text" name="lostname" placeholder="Your login or E-Mail on the site" />
</div>
[sec_code]
<div class="form-item clearfix imp">
<label>Enter the code from the image:</label>
<div class="form-secure">
             <input type="text" name="sec_code" id="sec_code" placeholder="Enter code from image" maxlength="45" required />{code}
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
<div class="form-submit">
<button name="submit" type="submit">Submit</button>
</div>
</div>