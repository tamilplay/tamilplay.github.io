    
<article class="article ignore-select">
<div class="p-4 max-w-full bg-white sm:p-8 dark:bg-gray-800 dark:border-gray-700">
    
<nav class="flex mb-4" aria-label="Breadcrumb">
  <ol class="inline-flex items-center space-x-1 md:space-x-3">
    <li class="inline-flex items-center">
      <a href="/" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
        <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
        Home
      </a>
    </li>
    <li>
      <div class="flex items-center">
        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
        <a href="{category-url}" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">{category}</a>
      </div>
    </li>
  </ol>
</nav>
<h2 class="font-bold font-sans break-normal text-gray-900 text-3xl md:text-4xl md:text-4xl dark:text-white">{title}</h2> [edit]Edit[/edit]
<div class="mt-2 my-2 flex items-center">
      <div>
        <span> Published: <abbr class="timeago" title="{date=Y-m-d H:i:s}"></abbr> by </span>
        {author}
      </div>
    </div>
 
<div class="sect-content  full-text clearfix text-lg">
     [xfgiven_thumbnail]  
    <div class="rounded-lg relative rounded-xl overflow-auto">
<div class="relative rounded-lg text-center overflow-hidden w-full sm:w-full mx-auto">
<div class="rounded-lgabsolute inset-0 opacity-50 bg-stripes-gray"></div>
<img class="rounded-lg relative z-10 object-contain h-80 w-full" src="[xfvalue_thumbnail]"/>
</div>
</div>
    
 [/xfgiven_thumbnail]
            [poll] {poll} [/poll]
{full-story}
     [xfgiven_dlink]
    <div class="mt-2 my-2 relative p-4 w-full rounded-lg overflow-hidden" style=" text-align: center; ">
     [xfgiven_demolink]
<span onclick="location.href='[xfvalue_demolink]'" class=" px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-md w-full"><i class="fa-solid fa-eye"> </i> Demo</span>
   [/xfgiven_demolink]
 <span onclick="location.href='[/xfvalue_dlink]'" class=" px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-md w-full"><i class="fa-solid fa-cloud-arrow-down"></i> Download</span>
    
    </div>
     [/xfgiven_dlink]
    
    
    
     [xfgiven_protected]
    
     <div class="bg-white dark:bg-slate-900 rounded-lg p-3 shadow-lg">
      <div class="flex flex-row">
        <div class="px-2">
         <svg class="h-6 w-6 text-green-500"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
</svg>
        </div>
        <div class="ml-2 mr-6">
          <span class="text-black dark:text-white font-semibold">Password: jayasurya.co.in</span>
          
        </div>
      </div>
    </div>

     [/xfgiven_protected]
    
    
    
    
    
    [tags]
    <div class="my-3 p-4 inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">
    {tags}
</div>   
    [/tags]
</div>
    [rating-type-4]
<div class="p-4 frate fx-row fx-center fx-middle" style=" margin: 10px; " id="frate-{news-id}">
[rating-plus]<span class="fal fa-solid fa-face-laugh"></span> {likes}[/rating-plus]
[rating-minus]<span class="fal fa-solid fa-face-angry"></span> {dislikes}[/rating-minus]

</div>
[/rating-type-4]
</div>
     


<ul role="list" class="p-4 divide-y divide-gray-200 dark:divide-gray-700">

{related-news}
</ul>
    <div class="rounded-lg border shadow-md bg-white dark:bg-slate-900 sect sect-bg fcomms">
<div class="sect-header1 fx-row fx-middle">
<div class="text-slate-900 dark:text-white sect-title fx-1">Comments ({comments-num})</div>
<div class="btn fadd-comms anim">Add</div>
</div>
<div class="sect-content" id="full-comms">
{addcomments}
{comments}
{navigation}
</div>
</div>




</article>