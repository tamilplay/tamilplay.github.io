<div class="form-wrap">
<h1>Feedback</h1>
[not-logged]
<div class="form-item clearfix imp">
<label>Your name:</label>
<input type="text" maxlength="35" name="name" placeholder="your name" />
</div>
<div class="form-item clearfix imp">
<label>Your E-Mail:</label>
<input type="text" maxlength="35" name="email" placeholder="your email" />
</div>
[/not-logged]
<div class="form-item clearfix">
<label>Select to:</label>
<div class="form-secure">
            {recipient}
</div>
</div>
<div class="form-item clearfix">
<label>Message subject:</label>
<input type="text" maxlength="45" name="subject" placeholder="Message subject" />
</div>
<div class="form-textarea">
<label>Your letter:</label>
<textarea name="message" style="height: 160px" ></textarea>
</div>
[attachments]
<div class="form-item clearfix">
<label for="question_answer">Attach files:</label>
<input name="attachments[]" type="file" multiple style="line-height:40px;">
</div>
[/attachments]
[question]
<div class="form-item clearfix imp">
<label>Question:</label>
<div class="form-secure"><div style="margin-bottom:10px;">{question}</div>
        <input type="text" name="question_answer" id="question_answer" placeholder="Enter the answer to the question" required />
</div>
</div>
[/question]
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
<button name="send_btn" type="submit">Submit</button>
</div>
</div>