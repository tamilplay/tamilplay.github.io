<?php
if (!defined('DATALIFEENGINE') || !defined('LOGGED_IN')) {
    die('Hacking attempt!');
}
?>

	<div class="panel-body">
		<div class="table-responsive">
		    <table class="">
		    	<tr>
		            <td class="col-xs-6 col-sm-6 col-md-7"><h6 class="media-heading text-semibold">Деактивировать канал</h6>
		            	<span class="text-muted text-size-small hidden-xs">В случае включения данной опции при запуске скрипта данный канал не будет получать новые фиды.</span></td>
		            <td class="col-xs-6 col-sm-6 col-md-5"><input class="switch" type="checkbox" value="1" name="offline" id="offline" {$offline_checked}></td>
		        </tr>
		    </table>
		</div>
	</div>
	

	<div class="panel-body">
		<h3>Базовые настройки</h3>
	</div>
	<div class="table-responsive">
	    <table class="table table-striped">
	    	
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7"><h6 class="media-heading text-semibold">URL канала</h6>
	            	<span class="text-muted text-size-small hidden-xs">Ссылка на RSS канал в форматах: RSS 2.0 или Atom </span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input type="text" class="form-control" value="{$url}" name="url" id="url" placeholder="https://tcse-cms.com/rss.xml"></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7"><h6 class="media-heading text-semibold">Название канала</h6>
	            	<span class="text-muted text-size-small hidden-xs">Используется в том числе для текста внутри ссылки на источник публикации.</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input type="text" class="form-control" value="{$name}" name="name" id="name" placeholder="Студия TCSE"></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7"><h6 class="media-heading text-semibold">Теги для облака</h6>
	            	<span class="text-muted text-size-small hidden-xs">Указываются теги через запятую. Так же сюда будут добавлены теги из канала при парсинге, если они есть и если разрешено их добавление.</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input type="text" value="{$tags}" name="tags" id="tags" class="form-control" value="" placeholder="новости, юмор"></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7"><h6 class="media-heading text-semibold">Хештеги</h6>
	            	<span class="text-muted text-size-small hidden-xs">
					<p>
					Поддержка хештегов для публикаций если поле хештега в админке пустое - данный параметр игнорируется. Если в поле указано произвольный хештег с необходимым значением, (<b>1</b> - опубликовать, <b>0</b> - не публиковать) то модуль может как опубликовать, так и пропустить данный пост в зависимости от указанных параметров.<br>
					</p>
					<p>
					Пример хештегов:
					</p>
					<code>#vk|1</code>
					<i>если в теле публикации будет найден текст вида <b>#vk</b> то его содержимое БУДЕТ импортировано на сайт.</i><br>
					<code>#other|0 </code>
					<i>если в теле публикации будет найден текст вида <b>#other</b> то его содержимое НЕ БУДЕТ импортировано на сайт.</i><br>
	        	</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<textarea type="text" class="form-control" name="hashtag" id="hashtag">{$hashtag}</textarea>
	            </td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7"><h6 class="media-heading text-semibold">Yandex.Rss</h6>
	            	<span class="text-muted text-size-small hidden-xs">
	            		Данный RSS канал передает содержимое в формате Яндекс Новости<br>
	            		Подробнее о формате <a href="https://yandex.ru/support/news/feed.html" target="_blank">yandex.ru/support/news/feed.html  <i class="fa fa-external-link"></i></a>
	            	</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input class="switch" type="checkbox" name="yandex_rss" id="yandex_rss" value="0" {$yandex_rss}></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7"><h6 class="media-heading text-semibold">Брать теги из канала</h6>
	            	<span class="text-muted text-size-small hidden-xs">
	            		Разрешить автоматическое добавление тегов из RSS канала (если есть). В качестве тегов берутся категории записи из канала.
	            	</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input class="switch" type="checkbox" value="1" name="allowRssTags" id="allowRssTags" {$allowRssTags_checked}></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7"><h6 class="media-heading text-semibold">Проверять дубли</h6>
	            	<span class="text-muted text-size-small hidden-xs">
	            		Настройка добавит 1 лёгкий запрос на каждую новость.
	            	</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input class="switch" type="checkbox" value="1" name="checkDouble" id="checkDouble" {$checkDouble_checked}></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7"><h6 class="media-heading text-semibold">Публиковать на главной</h6><span class="text-muted text-size-small hidden-xs"></span></td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input class="switch" type="checkbox"  value="1" name="allow_main" id="allow_main" {$allow_main_checked}></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7"><h6 class="media-heading text-semibold">Разрешить рейтинг</h6><span class="text-muted text-size-small hidden-xs"></span></td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input class="switch" type="checkbox" value="1" name="allow_rating" id="allow_rating" {$allow_rating_checked}></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7"><h6 class="media-heading text-semibold">Разрешить комментарии</h6><span class="text-muted text-size-small hidden-xs"></span></td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input class="switch" type="checkbox" value="1" name="allow_comm" id="allow_comm" {$allow_comm_checked}></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7"><h6 class="media-heading text-semibold">Разрешить автоперенос строк</h6><span class="text-muted text-size-small hidden-xs"></span></td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input class="switch" type="checkbox" value="1" name="allow_br" id="allow_br" class="checkbox" {$allow_br_checked}></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold"> Дата публикации:</h6>
	            	<span class="text-muted text-size-small hidden-xs">Устанавливает дату публикации новости.</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<select class="uniform" name="date" id="date">
	                    <option value="1" {$date_1}>Установить текущую</option>
	                    <option value="0" {$date_0}>Установить из канала</option>
	                </select>
	            </td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7"><h6 class="media-heading text-semibold">Количество новостей</h6>
	            	<span class="text-muted text-size-small hidden-xs">Как правило канал отдаёт не более 10 элементов, так что ставить цифру больше не имеет смысла. К тому же импорт - довольно сложный процесс, который грузит сервер, имейте это ввиду.</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input type="text" value="{$max_news}" name="max_news" id="max_news" class="form-control" placeholder="10"></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold"> Категория:</h6>
	            	<span class="text-muted text-size-small hidden-xs">Устанавливает категорию публикации по умолчанию.</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<select class="uniform" name="category" id="category">
	            		{$category_list}
	                    <option value="1" style="">Главная (id:1)</option>
					    <option value="2" style="">&nbsp;&nbsp;&nbsp;&nbsp;информация о TCSE-cms (id:2)</option>
					    <option value="3" style="">&nbsp;&nbsp;&nbsp;&nbsp;Скрипты и советы (id:3)</option>
					    <option value="4" style="">&nbsp;&nbsp;&nbsp;&nbsp;Интернет-маркетинг (id:4)</option>
					    <option value="5" style="">&nbsp;&nbsp;&nbsp;&nbsp;блоговость (id:5)</option>
					    <option value="6" style="">Инструкции (id:6)</option>
					    <option value="7" style="">Продается готовый проект (id:7)</option>
					    <option value="8" style="">Портфолио (id:9)</option>
					    <option value="9" style="">Проекты студии TCSE (id:9)</option>
					    <option value="10" style="">Наши разработки (id:10)</option>
					    <option value="11" style="">Дизайн макеты сайтов (id:11)</option>
					    <option value="12" style="">TradeMod (id:12)</option>
					    <option value="13" style="">Партнеры сайта (id:13)</option>
					    <option value="14" style="">KeyShop (id:14)</option>
	                </select>
	            </td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold">Картинка-заглушка:</h6>
	            	<span class="text-muted text-size-small hidden-xs">Путь к картинке-заглушке на случай, если она отсутствует в новости.</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<input type="text" class="form-control" value="{$noimage}" name="noimage" id="noimage"  placeholder="{THEME}/images/no_image.jpg">
	            </td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold">Ограничение символов в краткой новости:</h6>
	            	<span class="text-muted text-size-small hidden-xs">Количество символов в краткой новости. Обрезка происходит до логического конца слова.</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<input type="text" class="form-control" value="{$textLimit}" name="textLimit" id="textLimit">
	            </td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold"> Тип полной новости</h6>
	            	<span class="text-muted text-size-small hidden-xs"><b>Первая картинка и текст</b> - будет взята первая картинка из новости (согласно общим настройкам) и текст, очищенный от html-тегов. <br><b>Текст и все картинки</b> - в текста будут оставлены теги p, h2, h3, b, img (по умолчанию) и все те, что будут разрешены в поле "Теги, которые не будут обрабатываться".</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<select class="uniform" name="fullStoryType" id="fullStoryType">
	                    <option value="0" {$fullStoryType_0}>Первая картинка и текст</option>
						<option value="1" {$fullStoryType_1}>Текст и все картинки</option>
	                </select>
	            </td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold">Теги, которые не будут обрабатываться</h6>
	            	<span class="text-muted text-size-small hidden-xs">
	            	Перечисляем через запятую теги, которые не будут обработаны парсером при обработке полной новости. Работает в паре с типом полной новости = <b>Текст и все картинки</b>.<br>
	            	Таким образом все указаныне теги попадут в тело полной новости.
	            </span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<input type="text" class="form-control" value="{$fullStoryTags}" name="fullStoryTags" id="fullStoryTags" placeholder="p, h2, h3, b, img, a, br, i,">
	            </td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold">Ограничение символов в ЧПУ:</h6>
	            	<span class="text-muted text-size-small hidden-xs">Количество символов в ЧПУ новости. Обрезка происходит до логического конца слова.</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<input type="text" class="form-control" value="{$chpuCut}" name="chpuCut" id="chpuCut">
	            </td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold">Логин автора новости:</h6>
	            	<span class="text-muted text-size-small hidden-xs">Всем входящим сообщениям публикуемым на сайте будет указан именно этот автор. Данный логин должен быть зарегистророван в базе сайта.</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<input type="text" class="form-control"  value="{$authorLogin}" name="authorLogin" id="authorLogin">
	            </td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold">Разрешить авторегистрацию юзеров</h6>
	            	<span class="text-muted text-size-small hidden-xs">
		            	Автоматически регистрировать авторов из канала. При этом для автора будет создан email по маске [транслит логина]@[адрес сайта] и пароль из восьми случайных цифр и букв.
		            </span>
		        </td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input class="switch" type="checkbox" value="1" name="allowNewUsers" id="allowNewUsers" {$allowNewUsers_checked}></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold"> Группа для новых пользователей</h6>
	            	<span class="text-muted text-size-small hidden-xs"> </span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<select class="uniform" name="newUserGroup" id="newUserGroup" >
	            		{$user_groups}
	            		<option value="2" {$fullStoryType_0}>Журналисты</option>
	                    <option value="0" {$fullStoryType_0}>Админы</option>
						<option value="1" {$fullStoryType_1}>Редакторы</option>
	                </select>
	            </td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold">Текст перед ссылкой на источник</h6>
	            	<span class="text-muted text-size-small hidden-xs">В конец полной новости добавляется ссылка на источник, тут можно задать текст, который добавится перед ссылкой.</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<input type="text" class="form-control" value="{$sourseTextName}" name="sourseTextName" id="sourseTextName" placeholder="Источник: ">
	            </td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold">Использовать "псевдоссылку" на источник</h6>
	            	<span class="text-muted text-size-small hidden-xs">
		            	Если отметить чекбокс - то ссылка будет заменена на span, а &quot;открытие&quot; ссылки будет обрабатываться через JS. Подобная &quot;ссылка&quot; не видна поисковикам.
		            </span>
		        </td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input class="switch" type="checkbox"  value="1" name="pseudoLinks" id="pseudoLinks" {$pseudoLinks_checked}></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold">Скрыть ссылку на источник</h6>
	            	<span class="text-muted text-size-small hidden-xs">
		            	Если отметить чекбокс - то ссылка на источник не будет добавляться в конце публикации.
		            </span>
		        </td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input class="switch" type="checkbox"  value="1" name="showLink" id="showLink" {$showLink}></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold"> Открывать ссылку</h6>
	            	<span class="text-muted text-size-small hidden-xs">
	            		Правила для ссылки на источник публикации.
	            	</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<select class="uniform" name="sourceTarget" id="sourceTarget">
	                    <option value="blank" {$sourceTarget_blank}>в новой вкладке</option>
						<option value="self" {$sourceTarget_self}>в текущей вкладке</option>
	                </select>
	            </td>
	        </tr>
	        
	    </table> 
	</div>      

	<div class="panel-body">
		<h3>Картинки</h3>
	</div>
	<div class="table-responsive">
	    <table class="table table-striped">
	    	
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold">Отключить парсинг картинок</h6>
	            	<span class="text-muted text-size-small hidden-xs">
		            	Если отметить чекбокс - то картинки из канала не будут обрабатываться.
		            </span>
		        </td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input class="switch" type="checkbox"  value="1" name="dasableImages" id="dasableImages" {$dasableImages_checked}></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold">Тянуть картинки к себе</h6>
	            	<span class="text-muted text-size-small hidden-xs">
		            	Если отметить чекбокс - то картинки из канала будут скачиваться на сайт.
		            </span>
		        </td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input class="switch" type="checkbox"  value="1" name="grabImages" id="grabImages" {$grabImages_checked}></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold">Сохранять оригиналы</h6>
	            	<span class="text-muted text-size-small hidden-xs">
		            	Если отметить чекбокс - то картинки из канала будут скачиваться на сайт и сохраняться ещё и в <b>оригинальном размере</b>, а в полную новость будут вставляться в виде миниатюр с увеличением по клику.
		            </span>
		        </td>
	            <td class="col-xs-6 col-sm-6 col-md-5"><input class="switch" type="checkbox" value="1" name="saveOriginalImages" id="saveOriginalImages" {$saveOriginalImages_checked}></td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold">Размер картинок</h6>
	            	<span class="text-muted text-size-small hidden-xs">Размер уменьшеных картинок, можно задавать как 200x150 (ширина x высота), так и просто 250</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<input type="text" class="form-control"  value="{$imgSize}" name="imgSize" id="imgSize" placeholder="500x300">
	            </td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold"> Тип ресайза картинок</h6>
	            	<span class="text-muted text-size-small hidden-xs">
	            		
	            	</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<select class="uniform" name="resizeType" id="resizeType">
	                    <option value="auto" {$resize_auto}>вписать в рамки (авто)</option>
						<option value="exact" {$resize_exact}>точный размер (без учёта пропорций)</option>
						<option value="landscape" {$resize_landscape}>уменьшение по ширине</option>
						<option value="portrait" {$resize_portrait}>уменьшение по высоте</option>
						<option value="crop" {$resize_crop}>crop (уменьшение и обрезка лишнего)</option>
	                </select>
	            </td>
	        </tr>
	        
	    </table> 
	</div>

	<div class="panel-body bg-primary">
		<h3>Тестовые - идеи на будущее</h3>
	</div>
	<div class="table-responsive">
	    <table class="table table-striped">
	    	<tr>
	            <td class="col-xs-6 col-sm-6 col-md-7">
	            	<h6 class="media-heading text-semibold"> Конвертирование новостей</h6>
	            	<span class="text-muted text-size-small hidden-xs">
	            		
	            	</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<select class="uniform" name="rss_text_type" id="rss_text_type">
	                    <option value="1" {1}>BBCODES</option>
						<option value="0" {0}>HTML</option>
	                </select>
	            </td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7"><h6 class="media-heading text-semibold">Маска для поиска</h6>
	            	<span class="text-muted text-size-small hidden-xs">
					<p>
					Маска поиска применяется для импортирования полных статей с сайтов Это регулярное выражение, которое использует следующие теги:<br>
					'<b>{skip}</b>' - пропускает любые символы, <br>а тег '<b>{get}</b>' - получает текст для новости
					</p>
	        	</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<textarea type="text" class="form-control" name="rss_search" id="rss_search"><html>{get}</html></textarea>
	            </td>
	        </tr>
	        <tr>
	            <td class="col-xs-6 col-sm-6 col-md-7"><h6 class="media-heading text-semibold">Cookies сайта</h6>
	            	<span class="text-muted text-size-small hidden-xs">
					<p>
					Иногда для получения полной информации с сайта необходима авторизация на сайте. Вы можете задать cookies которые использует сайт для авторизации, например для сайтов на DataLife Engine необходимо ввести<br /><br /><b>dle_user_id=id</b><br /><b>dle_password=71820d7c524</b><br /><br />На каждой новой строке задается новое значение cookies.
					</p>
	        	</span>
	            </td>
	            <td class="col-xs-6 col-sm-6 col-md-5">
	            	<textarea type="text" class="form-control" name="rss_cookie" id="rss_cookie">{$rss_cookie}</textarea>
	            </td>
	        </tr>
	    </table> 
	</div>

	<div class="panel-body">
		<div style="margin-bottom:30px;">
			<input type="hidden" name="mod" value="options">
			<input type="hidden" name="action" value="dosavesyscon">
			<input type="hidden" name="user_hash" value=" ">
			<button type="submit" class="btn bg-teal btn-raised position-left legitRipple"><i class="fa fa-floppy-o position-left"></i>Сохранить</button>
			<a class="btn active" href="{$config['admin_path']}?mod={$cfg['moduleName']}">Отменить</a>
		</div>
	</div>

