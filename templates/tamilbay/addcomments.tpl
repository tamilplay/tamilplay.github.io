<!--noindex-->
<div class="add-comms" id="add-comms">
[not-logged]
<div class="ac-inputs fx-row">
<input type="text" maxlength="35" name="name" id="name" placeholder="your name" />
<input type="text" maxlength="35" name="mail" id="mail" placeholder="Your email (optional)" />
</div>
[/not-logged]
<div class="ac-textarea">{editor}</div>

[not-group=1]
<div class="ac-protect">
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
<input type="text" name="sec_code" id="sec_code" placeholder="Enter code from image" maxlength="45" required />{sec_code}
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
</div>
[/not-group]

<div class="ac-submit">
<button name="submit" type="submit">Add a comment</button>
</div>

</div>
<!--/noindex-->