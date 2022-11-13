<div class="us-prof">
<div class="usp-cols fx-row">
<div class="usp-left">
<div class="usp-status">
[online]<p class="online">Online</p>[/online]
[offline]<p class="offline">Offline</p>[/offline]
</div>
<div class="usp-av img-box"><img src="{foto}" alt=""/></div>
<div class="usp-activ clearfix">
<div><div>{news-num}</div>Publications</div>
<div><div>{comm-num}</div>Comments</div>
</div>
<div class="usp-btn">{email}</div>
[not-group=5]<div class="usp-btn">{pm}</div>[/not-group]
[not-logged]<div class="usp-btn"> {edituser} </div>[/not-logged]
</div>
<div class="usp-right">
<div class="usp-name">
<h1>User: {usertitle}</h1>
<div class="usp-group">Group: {status} [time_limit]&nbsp;In group up to: {time_limit}[/time_limit]</div>
</div>
<ul class="usp-meta">
<li>Registration: {registration}</li>
<li>Logged in: {lastdate}</li>
[news-num]<li>{news}[rss], RSS [/rss]</li>[/news-num]
[comm-num]<li>{comments}</li>[/comm-num]
[not-group=5]
[fullname]<li>Fullname: {fullname}</li>[/fullname]
[land]<li>Residence: {land}</li>[/land]
<li>About me: {info}</li>
[signature]<li>Signature: {signature}</li>[/signature]
[/not-group]
</ul>
</div>
</div>
</div>

<div id="options" style="display:none; margin-bottom: 30px" class="form-wrap">
<h1>Edit profile:</h1>
<div class="form-item clearfix">
<label>Your Name:</label>
<input type="text" name="fullname" value="{fullname}" placeholder="Your Name" />
</div>
<div class="form-item clearfix">
<label>Your E-Mail:</label>
<input type="text" name="email" value="{editmail}" placeholder="Your E-Mail: {editmail}" />
</div>
<div class="form-checks">
{hidemail}
<input style="margin-left: 50px" type="checkbox" id="subscribe" name="subscribe" value="1" />
<label for="subscribe">Unsubscribe from subscribed news</label>
</div>
<div class="form-item clearfix">
<label>Residence:</label>
<input type="text" name="land" value="{land}" placeholder="Location" />
</div>
<div class="form-textarea">
<label>List of ignored users:</label>
{ignore-list}
</div>
<div class="form-item clearfix">
<label>Old password:</label>
<input type="password" name="altpass" placeholder="Old password" />
</div>
<div class="form-item clearfix">
<label>New password:</label>
<input type="password" name="password1" placeholder="New password" />
</div>
<div class="form-item clearfix">
<label>Retype password:</label>
<input type="password" name="password2" placeholder="Retype New Password" />
</div>
<div class="form-textarea">
<label>IP blocking (Your IP: {ip}):</label>
<textarea name="allowed_ip" style="height: 160px" rows="5" class="f_textarea" >{allowed-ip}</textarea>
<div style="margin-top: 10px">
<span style="color:red; font-size:12px;">
* Attention! Be careful when changing this setting.
Access to your account will be available only from the IP address or subnet that you specify.
You can enter multiple IP addresses, one address per line.
<br />
Example: 192.48.25.71 or 129.42.*.*</span>
</div>
</div>
<div class="form-item clearfix">
<label>Avatar:</label>
<input type="file" name="image" size="28" />
</div>
<div class="form-item clearfix">
<label>Service <a href="http://www.gravatar.com/" target="_blank">Gravatar</a>:</label>
<input type="text" name="gravatar" value="{gravatar}" placeholder="Enter E-Mail in this service" />
</div>
<div class="form-checks">
<input type="checkbox" name="del_foto" id="del_foto" value="yes" />
<label for="del_foto">Delete Avatar</label>
</div>
<div class="form-item clearfix">
<label>Timezone:</label>
{timezones}
</div>
<div class="form-textarea">
<label>About me:</label>
<textarea name="info" rows="5">{editinfo}</textarea>
</div>
<div class="form-textarea">
<label>Signature:</label>
<textarea name="signature" rows="5">{editsignature}</textarea>
</div>
<div class="form-xfield">
<table class="tableform">{xfields}</table>
</div>

<div class="form-checks">{twofactor-auth}</div>
<div class="form-checks">{news-subscribe}</div>
<div class="form-checks">{comments-reply-subscribe}</div>
<div class="form-checks">{unsubscribe}</div>

<div class="form-submit">
<button name="submit" type="submit">Submit</button>
<input name="submit" type="hidden" id="submit" value="submit" />
</div>
</div>