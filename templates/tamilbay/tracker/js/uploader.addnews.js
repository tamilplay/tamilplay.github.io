$(function(){
	var news_id = $('#uploaderNewsId').val();
	var uploader = new qq.FileUploaderBasic({
		button: $("#torrentUploader")[0],
		allowedExtensions: ['torrent'],
		acceptFiles:'.torrent,application/x-bittorrent',
		action: dle_root + 'engine/modules/tracker/upload.php',
		params: {"nid" : news_id},
		maxConnections: 1,
		encoding: 'multipart',
		multiple: false,
		messages: {
			typeError: "Файл {file} имеет неверное расширение. Только {extensions} разрешены к загрузке.",
			emptyError: "Файл {file} пустой, выберите файлы повторно."
		},
		onSubmit: function(id, fileName){
			ShowLoading('');
		},
		onComplete: function(id, fileName, response){
			HideLoading('');
			if(response.status=="ok") {
				$('#torrentUploadArea').hide().after('<div id="torrentAddNewsFile_'+response.fid+'">Файл загружен! <a href="/engine/download.php?id='+response.fid+'">[ скачать ]</a> <a href="#" onclick="doInsert(\'[attachment='+response.fid+']\',\'\',!1); return !1;">[ вставить ]</a> <a href="#" onclick="trackerAddNewsDel('+response.fid+'); return !1;">[ удалить ]</a></div>');
			} else {
				$('#torrentUploaderReason').html(response.reason).show();
				setTimeout(function() {
					$('#torrentUploaderReason').hide().html("");
				}, 7000);
			}
		},
		debug: false
	});
});
function trackerAddNewsDel(fid) {
	DLEconfirm('Удалить данный файл?', 'Удаление файла.',
		function(){
			ShowLoading('');
			$.post(dle_root + "engine/modules/tracker/ajax.php",
				{edit:'del', file_id:fid},
				function(data){
					HideLoading('');
					$('#torrentAddNewsFile_'+fid).remove();
					$('#torrentUploadArea').show();
				}
			);
		}
	);
}