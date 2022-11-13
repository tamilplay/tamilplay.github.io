<!DOCTYPE html>
<html lang="en">
<head>
{headers}
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="shortcut icon" href="{THEME}/images/logo.svg" />
<link href="{THEME}/css/styles.css" type="text/css" rel="stylesheet" />
<link href="{THEME}/css/engine.css" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          sans: ['Inter', 'sans-serif'],
        },
      }
    }
  }
</script>
    <style>
        .full-text a {
    text-decoration: none;
    color: #06c;
}
        .frate a .fal {
    width: 40px;
    height: 40px;
    line-height: 40px;
    text-align: center;
    border-radius: 50%;
    margin-right: 8px;
    background-color: #6ab04c;
    color: #fff;
    font-size: xx-large;
    box-shadow: 0 2px 6px rgb(0 0 0 / 10%);
}
        abbr{
        text-decoration: unset !important;
        }
        </style>
<meta name="theme-color" content="#686de0">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600&display=swap&subset=cyrillic" rel="stylesheet">
<!-- Google tag (gtag.js) -->
    <meta name="google-site-verification" content="H_f0lQqSTjU5rRty-dUte3BFyiOgBF0eYNA9xvTu45E" />
</head>

<body id="hideme" class="font-sans	not-loaded antialiased text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-900">
  <!-- Header section -->
        
    <div class="mx-auto container grid grid-cols-5 gap-2	">
       <header style=" margin-bottom: 1px; " class="header col-span-5 bg-gray-800 border-gray-200 px-4 lg:px-6 py-2.5">
            <div class="header-in wrap-center fx-row fx-middle">
<a href="/" class="logo nowrap text-white">
    <span>Jaya</span>Surya<span class="logo-domain">.co.in</span></a>
<div class="search-wrap fx-1">
<form id="quicksearch" method="post">
<input type="hidden" name="do" value="search" />
<input type="hidden" name="subaction" value="search" />
<div class="search-box rounded-lg border-salte-900">
<input style=" border: 2px #949596  solid; " id="story" name="story" placeholder="Search Site..." type="text" />
<button type="submit" class="search-btn"><span class="fal fa-search"></span></button>
</div>
</form>
</div>
[group=5]<div class=" text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 js-login js-login">Login</div>[/group]
[not-group=5]<div class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 js-login">Account</div>[/not-group]
<div class="btn-menu"><span class="fal fa-bars"></span></div>
</div>
        </header>

        <!-- Left menu -->
       <aside class=" col-left rounded-lg border shadow-md col-span-5 md:col-span-1 p-3 w-full mx-auto bg-white border-b border-gray-200 dark:border-gray-600 dark:bg-gray-800">
            {catmenu}
        </aside>

        <!-- Main content -->
        <main id="wajax" class="md:p-3 rounded-lg border shadow-md col-span-5 md:col-span-3 h-auto p-2 w-full mx-auto bg-white border-b border-gray-200 dark:border-gray-600 dark:bg-gray-800">
            {info}
[available=main]{include file="main-page.tpl"}[/available]
[available=showfull]{content}[/available]
[available=cat|tags]{include file="main-page.tpl"}[/available]
[available=search]{content}[/available]
[not-available=cat|showfull|main|tags|search] {content} [/not-available]
        </main>

       [not-smartphone] <!-- Right sidebar -->
        <aside class="col-left rounded-lg border shadow-md col-span-5 md:col-span-1 p-3 w-full mx-auto bg-white border-b border-gray-200 dark:border-gray-600 dark:bg-gray-800">
           <center style="display: grid;">

    <button onclick="location.href='/addnews.html'" id="btnx" style="margin: 5px;"><i class="fa fa-plus" aria-hidden="true"></i> Add Post</button>

     
</center>
<div class="side-box sect-bg side-subscribe">
<div class="side-bt"><span class="fal fa-envelope"></span>Be aware!</div>
<div class="side-bc search-box">
<input name="" placeholder="Your email" type="text" />
<button type="submit" class="search-btn"><span class="fal fa-arrow-right"></span></button>
<div class="side-subscribe-caption">New music every day!</div>
</div>
</div>
<div class="side-box rounded-lg border shadow-md bg-white dark:bg-gray-800 dark:border-gray-700">
<div class="side-bt">Tags</div>
<div class="side-bc">
{tags}
</div>
</div>
        </aside>[/not-smartphone]

        <!-- Footter -->
        <footer class="col-span-5 p-4 bg-white rounded-lg shadow md:flex md:items-center md:justify-between md:p-6 dark:bg-gray-800">

    <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2022-2023 <a href="https://jayasurya.co.in/" class="hover:underline">Jayasurya™</a>. All Rights Reserved.
    </span>
    <ul class="flex flex-wrap items-center mt-3 text-sm text-gray-500 dark:text-gray-400 sm:mt-0">
        <li>
            <a href="#" class="mr-4 hover:underline md:mr-6 ">About</a>
        </li>
        <li>
            <a href="#" class="mr-4 hover:underline md:mr-6">Privacy Policy</a>
        </li>
        <li>
            <a href="#" class="mr-4 hover:underline md:mr-6">Licensing</a>
        </li>
        <li>
            <a href="#" class="hover:underline">Contact</a>
        </li>
    </ul>
        </footer>
    </div>

{login}
{jsfiles}


{AJAX}
<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/9729/jquery.timeago.js"></script>
<script src="{THEME}/js/ad.js"></script>
</body>
</html>