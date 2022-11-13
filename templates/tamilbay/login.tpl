<!--noindex-->

[not-group=5]
<div class="bg-slate-100 rounded-xl p-8 dark:bg-slate-800 dark:text-white login-box hidden">
<div class="login-close"><span class="fal fa-times"></span></div>
<div class="login-title nowrap title">{login}</div>
<div class="login-avatar img-box"><img src="{foto}" title="{login}" alt="{login}" /></div>
    
[group=1]
    <div class="my-5 px-6">
<a href="{admin-link}" class="text-gray-200 block rounded-lg text-center font-medium leading-6 px-6 py-3 bg-gray-900 hover:bg-black hover:text-white">Admin panel</a>
</div>
  [/group]
<ul class="login-menu fx-row fx-start">
<li ><a class="wajax" href="{addnews-link}"><span class="fa fa-plus"></span>Add Post</a></li>
<li><a href="{profile-link}"><span class="fa fa-cog"></span>My profile</a></li>
<li><a href="{pm-link}"><span class="fa fa-envelope-o"></span>Messages: ({new-pm})</a></li>
<li><a href="{favorites-link}"><span class="fa fa-heart-o"></span>My Favorites (<span id="l-fav">{favorite-count} </span>)</a></li>
<li><a href="{stats-link}"><span class="fa fa-bar-chart-o"></span>Statistics</a></li>
<li><a href="{newposts-link}"><span class="fa fa-file-text-o"></span>Unread</a></li>
<li><a href="/?do=lastcomments"><span class="fa fa-comments"></span>Comments</a></li>
<li><a href="{logout-link}"><span class="fa fa-sign-out"></span>Logout</a></li>
</ul>
</div>
[/not-group]
[group=5]
<div class="bg-slate-100 rounded-xl p-8 dark:bg-slate-800 dark:text-white login-box not-logged hidden">
<div class="login-close"><span class="fal fa-times"></span></div>
<form method="post">
<div class="login-title title">Login</div>
<div class="login-avatar"><span class="fal fa-user"></span></div>
<div class="login-input"><input type="text" name="login_name" id="login_name" placeholder="Your E-mail"/></div>
<div class="login-input"><input type="password" name="login_password" id="login_password" placeholder="Your password" /></div>
<div class="login check">
<label for="login_not_save">
<input type="checkbox" name="login_not_save" id="login_not_save" value="1"/>
<span>Remember</span>
</label>
</div>
<div class="login-btn"><button onclick="submit();" type="submit" title="Login">Sign In</button></div>
<input name="login" type="hidden" id="login" value="submit" />
<div class=" fx-row">
<a href="{registration-link}" class="dark:text-white log-register">Registration</a>
<a  class="dark:text-white" href="{lostpassword-link}">Reset password</a>
</div>
<div class="login-soc-title">Or login with</div>
<div class="login-soc-btns">
[vk]<a href="{vk_url}" target="_blank"><img src="{THEME}/images/social/vk.png" /></a>[/vk]
[odnoklassniki]<a href="{odnoklassniki_url}" target="_blank"><img src="{THEME}/images/social/ok.png" /></a>[/odnoklassniki]
[facebook]<a href="{facebook_url}" target="_blank"><img src="{THEME}/images/social/fb.png" /></a>[/facebook]
[mailru]<a href="{mailru_url}" target="_blank"><img src="{THEME}/images/social/mail.png" /></a>[/mailru]
[google]<a href="{google_url}" target="_blank"><img src="{THEME}/images/social/google.png" /></a>[/google]
[yandex]<a href="{yandex_url}" target="_blank"><img src="{THEME}/images/social/yandex.png" /></a>[/yandex]
</div>
</form>
</div>
[/group]
<!--/noindex-->