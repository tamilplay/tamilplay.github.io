[allow-download]

<div class="mt-2 my-2 relative p-4 w-full bg-white dark:text-white dark:bg-slate-900 rounded-lg overflow-hidden shadow hover:shadow-md" style=" text-align: center; ">
	

	<h2 class="dark:text-white mt-2 text-gray-800 text-sm font-semibold line-clamp-1">
	  {name}
	</h2>
    

	<p class="dark:text-white mt-2 text-gray-800 text-sm">Size: {size}</p>

	<span onclick="location.href='{link}'" class=" px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-md w-full">
        <i class="fa-solid fa-cloud-arrow-down"></i> Download {count}</span>
  </div>


[/allow-download]
[allow-online]<br />View file online: <a href="{online-view-link}" target="_blank">{name}</a>[/allow-online]
[not-allow-download]<div class="attach clr">You do not have access to download files from our server</div>[/not-allow-download]