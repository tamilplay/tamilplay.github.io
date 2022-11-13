<div class="form-wrap">
<h1>Add News</h1>
<div class="form-item clearfix imp">
<label for="title">Title:</label>
<input type="text" id="title" name="title" value="{title}" maxlength="150" placeholder="Enter title" required />
</div>
<div class="form-buts clearfix">
        <input title="Find similar" onclick="find_relates(); return false;" type="button" value="Find Similar" />
        <a href="#" class="button" onclick="$('.form-vote').toggle();return false;">Add Poll</a>
</div>
    <div id="related_news"></div>
<div class="form-vote" style="display:none;">
<div class="form-item clearfix">
<label>Title:</label>
<input type="text" name="vote_title" value="{votetitle}" maxlength="150" placeholder="Poll Title" />
</div>
<div class="form-item clearfix">
<label>The question itself:</label>
<input type="text" name="frage" value="{frage}" maxlength="150" placeholder="The question itself" />
</div>
<div class="form-textarea">
<label>Answer options (Each new line is a new answer option):</label>
<textarea name="vote_body" rows="10">{votebody}</textarea>
</div>
        <div class="form-checks">
            <input type="checkbox" name="allow_m_vote" value="1" {allowmvote}>
            <label>Allow multiple selections</label>
        </div>
</div>
[urltag]
<div class="form-item clearfix">
<label for="alt_name">Article URL:</label>
<input type="text" name="alt_name" value="{alt-name}" maxlength="150" placeholder="News URL" />
</div>
[/urltag]
<div class="form-textarea">
<label>Category selection:</label>
{category}
</div>
<div class="form-textarea">
<label>Details:</label>
[not-wysywyg]
            {bbcode}
            <textarea name="full_story" id="full_story" onfocus="setFieldName(this.name)" rows="20">{full-story}</textarea>
[/not-wysywyg]
{fullarea}
</div>
<div class="form-xfield"><table class="tableform">{xfields}</table></div>
<div class="form-item clearfix">
<label for="tags">Keywords:</label>
        <input type="text" name="tags" id="tags" value="{tags}" maxlength="150" autocomplete="off" />
</div>
<div class="form-checks">{admintag}</div>
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
        <button name="add" type="submit">Submit</button>
        <button name="nview" onclick="preview()" type="submit">Preview</button>
    </div>
</div>