<div class="pm-page">

<header class="sub-title"><h1>Personal Posts</h1></header>

<ul class="pm-menu">
<li>[inbox]Inbox[/inbox]</li>
<li>[outbox]Submitted[/outbox]</li>
<li>[new_pm]Create new[/new_pm]</li>
</ul>
<div class="pm-status">
<div>Private messages folders full on:</div>
{pm-progress-bar}
{proc-pm-limit}% of limit ({pm-limit} messages)
</div>

[pmlist]
<header class="sub-title"><h1>Post List</h1></header>
<div class="table-resp">{pmlist}</div>
[/pmlist]

[newpm]
<div class="form-wrap">
<h1>New message</h1>
<div class="form-item clearfix imp">
<label>To:</label>
<input type="text" name="name" placeholder="To" value="{author}" required />
</div>
<div class="form-item clearfix">
<label>Subject:</label>
<input type="text" name="subj" placeholder="Theme" value="{subj}" />
</div>
<div class="form-textarea">
<label>Your letter:</label>
{editor}
</div>
<div class="form-checks">
<input type="checkbox" id="outboxcopy" name="outboxcopy" value="1" />
<label for="outboxcopy">Save message in Sent Items</label>
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
<div class="form-submit">
<button type="submit" name="add">Submit</button>
<button type="button" onclick="dlePMPreview()">Preview</button>
</div>
</div>
[/newpm]

[readpm]
<header class="form-title"><h1>Your messages</h1><br></header>

<div class="comm-item fx-row">
<div>
<div class="comm-left img-box">
<img src="{foto}" alt="{login}"/>
</div></div>
<div class="comm-right fx-1">
<div class="comm-one clearfix">
<span>{author}</span>
<span>{group-name}</span>
<span>{date}</span>
</div>
<div class="comm-two clearfix">
{text}
</div>
<ul class="comm-three fx-row">
<li>[reply]Reply[/reply]</li>
<li>[ignore]Ignore[/ignore]</li>
<li>[complaint]Complaint[/complaint]</li>
<li>[del]Delete[/del]</li>
</ul>
</div>
</div>


[/readpm]

</div>