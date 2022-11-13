<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2004-2022 SoftNews Media Group
=====================================================
 This code is protected by copyright
=====================================================
 File: upload.php
-----------------------------------------------------
 Use: upload files
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

function xfparamload( $xfname ) {

	$path = ENGINE_DIR . '/data/xfields.txt';
	$filecontents = file( $path );
	
	if( !is_array( $filecontents ) ) {
		return false;
	}
	
	foreach ( $filecontents as $name => $value ) {
		$filecontents[$name] = explode( "|", trim( $value ) );
		if($filecontents[$name][0] == $xfname ) return $filecontents[$name];
	}
	
	return false;
}

$allowed_extensions = array ("gif", "jpg", "png", "jpeg", "webp" , "bmp", "avif");
$allowed_video = array ("mp4", "mp3", "m4v", "m4a", "mov", "webm", "m3u8", "mkv" );
$allowed_files = explode( ',', strtolower( $user_group[$member_id['user_group']]['files_type'] ) );

$maxupload = intval(str_replace( array ('M', 'm' ), '', @ini_get( 'upload_max_filesize' ) ));
$maxupload = $maxupload * 1024; 

if( intval( $_REQUEST['news_id'] ) ) $news_id = intval( $_REQUEST['news_id'] ); else $news_id = 0;
if( isset( $_REQUEST['area'] ) ) $area = totranslit( $_REQUEST['area'] ); else $area = "";
if( isset( $_REQUEST['wysiwyg'] ) ) $wysiwyg = totranslit( $_REQUEST['wysiwyg'], true, false ); else $wysiwyg = 0;
$_REQUEST['subaction'] = isset($_REQUEST['subaction']) ? $_REQUEST['subaction'] : '';


if( !$is_logged ) {
	die ( "{\"error\":\"{$lang['err_notlogged']}\"}" );
}

if( !$user_group[$member_id['user_group']]['allow_image_upload'] AND !$user_group[$member_id['user_group']]['allow_file_upload'] ) {
	if ( $area != "comments" ) {
		die ( "{\"error\":\"{$lang['err_noupload']}\"}" );	
	}
}

$author = $db->safesql($member_id['name']);

if( isset( $_REQUEST['author'] ) AND $_REQUEST['author'] ) {
	
	$author = strip_tags(urldecode( (string)$_REQUEST['author'] ) );
	
	if( preg_match( "/[\||\'|\<|\>|\[|\]|\"|\!|\?|\$|\@|\#|\/|\\\|\&\~\*\{\+]/", $author ) ) {
		die ( "{\"error\":\"{$lang['user_err_6']}\"}" );		
	}
	
	$author = $db->safesql($author);
	
}

if ( !$user_group[$member_id['user_group']]['allow_all_edit'] AND $area != "comments" ) $author = $db->safesql($member_id['name']);

if ( $area == "template" ) {

	if ( !$user_group[$member_id['user_group']]['admin_static'] ) die ( "{\"error\":\"{$lang['opt_denied']}\"}" );

}

if ( $area == "comments" AND !$user_group[$member_id['user_group']]['allow_up_image'] ) {

	die ( "{\"error\":\"{$lang['opt_denied']}\"}" );

}

if ( $area == "adminupload" ) {

	if ( $member_id['user_group'] != 1 ) die ( "{\"error\":\"{$lang['opt_denied']}\"}" );

}

if ( $news_id AND $area != "template" AND $area != "comments" ) {

	$row = $db->super_query( "SELECT id, autor, approve FROM " . PREFIX . "_post WHERE id = '{$news_id}'" );

	if ( !$row['id'] ) die( "Hacking attempt!" );

	if ( !$user_group[$member_id['user_group']]['allow_all_edit'] AND $row['autor'] != $member_id['name'] ) die( "Hacking attempt!" );
	
	if ($row['approve'] AND !$user_group[$member_id['user_group']]['moderation'] AND ($_REQUEST['subaction'] == "upload" OR $_POST['subaction'] == "deluploads") ) {
		$db->query( "UPDATE " . PREFIX . "_post SET approve='0' WHERE id='{$news_id}'" );
	}
}

if ( $news_id AND $area == "comments" ) {

	$row = $db->super_query( "SELECT id, user_id, date, is_register FROM " . PREFIX . "_comments WHERE id = '{$news_id}'" );

	if ( !$row['id'] ) die( "Hacking attempt!" );

	$have_perm = 0;
	$row['date'] = strtotime( $row['date'] );
	
	if( ($member_id['user_id'] == $row['user_id'] AND $row['is_register'] AND $user_group[$member_id['user_group']]['allow_editc']) OR $user_group[$member_id['user_group']]['edit_allc'] ) {
		$have_perm = 1;
	}
	
	if ( $user_group[$member_id['user_group']]['edit_limit'] AND (($row['date'] + ($user_group[$member_id['user_group']]['edit_limit'] * 60)) < $_TIME) ) {
		$have_perm = 0;
	}
	
	if ( !$have_perm ) die( "Hacking attempt!" );
	
}

if( $area == "comments" ) {
	
	$user_group[$member_id['user_group']]['allow_image_size'] = false;
	$user_group[$member_id['user_group']]['allow_file_upload'] = false;
	$config['max_up_side'] = $user_group[$member_id['user_group']]['up_image_side'];
	$config['max_up_size'] = $user_group[$member_id['user_group']]['up_image_size'];
	
	if ( !$user_group[$member_id['user_group']]['edit_allc'] ) $author = $db->safesql($member_id['name']);
	
}

//////////////////////
// go go upload
//////////////////////
if( $_REQUEST['subaction'] == "upload" ) {
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		echo "{\"error\":\"{$lang['sess_error']}\"}";
		die();
	
	}
	
	include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/uploads/upload.class.php'));

	if( isset($_REQUEST['mode']) AND $_REQUEST['mode'] == "quickload") $user_group[$member_id['user_group']]['allow_image_size'] = false;

	if( $user_group[$member_id['user_group']]['allow_image_size'] ) {

		if ( isset($_REQUEST['t_seite']) ) $t_seite = intval( $_REQUEST['t_seite'] ); else $t_seite = intval($config['t_seite']);
		if ( isset($_REQUEST['m_seite']) ) $m_seite = intval( $_REQUEST['m_seite'] ); else $m_seite = intval($config['t_seite']);
		if ( isset($_REQUEST['make_thumb']) ) $make_thumb = intval( $_REQUEST['make_thumb'] ); else $make_thumb = true;
		if ( isset($_REQUEST['make_medium']) ) $make_medium = intval( $_REQUEST['make_medium'] ); else $make_medium = true;

		$t_size = $_REQUEST['t_size'] ? $_REQUEST['t_size'] : $config['max_image'];
		$m_size = $_REQUEST['m_size'] ? $_REQUEST['m_size'] : $config['medium_image'];
		$make_watermark = $_REQUEST['make_watermark'] ? intval($_REQUEST['make_watermark']) : false;

		if(!$t_size) $make_thumb = false;
		if(!$m_size) $make_medium = false;

		if ( $area == "adminupload" ) {
		
			if ($config['allow_watermark']) $make_watermark = true; else $make_watermark = false;
			$t_seite = intval($config['t_seite']);
			$m_seite = intval($config['t_seite']);
			$t_size = $config['max_image'];
			$m_size = $config['medium_image'];
			$make_thumb = false;
			$make_medium = false;
		
		}

	} else {
		
		$t_seite = intval($config['t_seite']);
		$m_seite = intval($config['t_seite']);
		$t_size = $config['max_image'];
		$m_size = $config['medium_image'];
		$make_thumb = true;
		$make_medium = true;
		if ($config['allow_watermark']) $make_watermark = true; else $make_watermark = false;

		if(!$t_size) $make_thumb = false;
		if(!$m_size) $make_medium = false;
	
	}
	
	if( $area == "xfieldsimage" OR $area == "xfieldsimagegalery") {
		
		$xfparam = xfparamload( $_REQUEST['xfname'] );
		
		if( !is_array( $xfparam ) ) die("xfieldname not found");
		
		$_REQUEST['xfname'] = $xfparam[0];
		$t_seite = intval($config['t_seite']);
		$m_seite = intval($config['t_seite']);
		$t_size = $xfparam[13];
		$m_size = 0;
		$config['max_up_side'] = $xfparam[9];
		$config['max_up_size'] = $xfparam[10];
		$config['min_up_side'] = $xfparam[22];
		$config['files_allow'] = false;
		$user_group[$member_id['user_group']]['allow_file_upload'] = false;
		$make_watermark = $xfparam[11] ? true : false;
		$make_thumb = $xfparam[12] ? true : false;
		$make_medium = false;
		
	}
	
	if( $area == "xfieldsfile" ) {
		$xfparam = xfparamload( $_REQUEST['xfname'] );
		
		if( !is_array( $xfparam ) ) die("xfieldname not found");
		
		$_REQUEST['xfname'] = $xfparam[0];
		$_REQUEST['public_file'] = intval($xfparam[27]);
		
		$user_group[$member_id['user_group']]['allow_image_upload'] = false;
		$user_group[$member_id['user_group']]['files_type'] = $xfparam[14];
		$user_group[$member_id['user_group']]['max_file_size'] = $xfparam[15];
		$user_group[$member_id['user_group']]['allow_public_file_upload'] = intval($xfparam[27]);

	}
	
	if( $area == "comments" ) {
		$user_group[$member_id['user_group']]['allow_image_size'] = false;
		$user_group[$member_id['user_group']]['allow_file_upload'] = false;
		$user_group[$member_id['user_group']]['allow_image_upload'] = true;
		$config['max_up_side'] = $user_group[$member_id['user_group']]['up_image_side'];
		$config['max_up_size'] = $user_group[$member_id['user_group']]['up_image_size'];
		$config['min_up_side'] = $user_group[$member_id['user_group']]['min_image_side'];
		$t_seite = intval($config['t_seite']);
		$m_seite = intval($config['t_seite']);
		$t_size = $user_group[$member_id['user_group']]['up_thumb_size'];
		$m_size = 0;
		$make_watermark = $user_group[$member_id['user_group']]['allow_up_watermark'] ? true : false;
		$make_thumb = $user_group[$member_id['user_group']]['allow_up_thumb'] ? true : false;
		$make_medium = false;
	}

	$t_size = explode ("x", $t_size);
	
	if ( count($t_size) == 2) {
	
		$t_size = intval($t_size[0]) . "x" . intval($t_size[1]);
	
	} else {
	
		$t_size = intval( $t_size[0] );
	
	}

	$m_size = explode ("x", $m_size);
	
	if ( count($m_size) == 2) {
	
		$m_size = intval($m_size[0]) . "x" . intval($m_size[1]);
	
	} else {
	
		$m_size = intval( $m_size[0] );
	
	}

	$uploader = new FileUploader($area, $news_id, $author, $t_size, $t_seite, $make_thumb, $make_watermark, $m_size, $m_seite, $make_medium);
	$result = $uploader->FileUpload();
	echo $result;
	die();

}
//////////////////////
// go go delete uploaded files
//////////////////////
check_xss ();

if( $_REQUEST['subaction'] == "deluploads" ) {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	DLEFiles::init( $config['file_driver'], false );
	
	if( isset( $_POST['images'] ) ) {

		$row = $db->super_query( "SELECT images  FROM " . PREFIX . "_images WHERE author = '{$author}' AND news_id = '{$news_id}'" );
		
		$listimages = explode( "|||", $row['images'] );

		$temp_images = $listimages;

		foreach ( $_POST['images'] as $image ) {
			
			$i = 0;
			$image = get_uploaded_image_info($image);

			reset( $listimages );
			
			foreach ( $temp_images as $dataimage ) {
				
				$dataimage = get_uploaded_image_info($dataimage);
				
				if( $dataimage->remote AND DLEFiles::$driver == 'remote' ) $disk = 'remote';
				else $disk = 'local';
				
				if( $dataimage->path == $image->path ) {
					
					unset( $listimages[$i] );
	
					DLEFiles::Delete( "posts/" . $dataimage->path, $disk );
					
					if( $dataimage->thumb ) {
						
						DLEFiles::Delete( "posts/{$dataimage->folder}/thumbs/{$dataimage->name}", $disk );
						
					}
					
					if( $dataimage->medium ) {
						
						DLEFiles::Delete( "posts/{$dataimage->folder}/medium/{$dataimage->name}", $disk );
						
					}
				
				}
				
				$i ++;
			}
	
		}

		if( count( $listimages ) ) $row['images'] = implode( "|||", $listimages );
		else $row['images'] = "";

		if( $row['images'] ) $db->query( "UPDATE " . PREFIX . "_images set images='{$row['images']}' WHERE author = '{$author}' AND news_id = '{$news_id}'" );
		else $db->query( "DELETE FROM " . PREFIX . "_images WHERE news_id = '{$news_id}'" );

		if ($user_group[$member_id['user_group']]['allow_admin']) $db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '32', '{$news_id}')" );
	
	}

	if( $user_group[$member_id['user_group']]['allow_file_upload'] AND isset($_POST['files']) AND is_array($_POST['files']) AND count( $_POST['files'] ) ) {
		
		foreach ( $_POST['files'] as $file ) {
			
			if( is_numeric($file) ) {
				
				$file = intval( $file );
				$row = $db->super_query( "SELECT * FROM " . PREFIX . "_files WHERE author = '{$author}' AND news_id = '{$news_id}' AND id='{$file}'" );	
			} else {
				
				$file = $db->safesql( $file );
				$row = $db->super_query( "SELECT * FROM " . PREFIX . "_files WHERE author = '{$author}' AND news_id = '{$news_id}' AND onserver='{$file}'" );
				
			}	

			if ( $row['id'] AND $row['onserver'] ) {
				
				if( $row['driver'] AND DLEFiles::$driver == 'remote' ) $disk = 'remote';
				else $disk = 'local';
				
				if( trim($row['onserver']) == ".htaccess") die("Hacking attempt!");
				
				if( $row['is_public'] ) $uploaded_path = 'public_files/'; else $uploaded_path = 'files/';
	
				DLEFiles::Delete( $uploaded_path.$row['onserver'], $disk );

				$db->query( "DELETE FROM " . PREFIX . "_files WHERE id='{$row['id']}'" );
			}
		
		}

		if ($user_group[$member_id['user_group']]['allow_admin']) $db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '34', '{$news_id}')" );
	
	}

	if( $user_group[$member_id['user_group']]['admin_static'] AND isset($_POST['static_files']) AND is_array($_POST['static_files']) AND count( $_POST['static_files'] ) ) {
		
		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '33', '{$news_id}')" );
					
		foreach ( $_POST['static_files'] as $file ) {
			
			$file = intval( $file );
			
			$row = $db->super_query( "SELECT * FROM " . PREFIX . "_static_files WHERE static_id = '{$news_id}' AND id='{$file}'" );
			
			if( $row['id'] AND $row['onserver'] ) {
				
				if( $row['driver'] AND DLEFiles::$driver == 'remote' ) $disk = 'remote';
				else $disk = 'local';
					
				if( trim($row['onserver']) == ".htaccess") die("Hacking attempt!");
				
				if( $row['is_public'] ) $uploaded_path = 'public_files/'; else $uploaded_path = 'files/';
	
				DLEFiles::Delete( $uploaded_path.$row['onserver'], $disk );

				$db->query( "DELETE FROM " . PREFIX . "_static_files WHERE id='{$row['id']}'" );
			
			} else {
				
				if( $row['id'] ) {
				
					$dataimage = get_uploaded_image_info( $row['name'] );
					
					if( $row['driver'] AND DLEFiles::$driver == 'remote' ) $disk = 'remote';
					else $disk = 'local';
				
					DLEFiles::Delete( "posts/" . $dataimage->path, $disk );
					
					if( $dataimage->thumb ) {
						
						DLEFiles::Delete( "posts/{$dataimage->folder}/thumbs/{$dataimage->name}", $disk );
						
					}
					
					if( $dataimage->medium ) {
						
						DLEFiles::Delete( "posts/{$dataimage->folder}/medium/{$dataimage->name}", $disk );
						
					}
					
					$db->query( "DELETE FROM " . PREFIX . "_static_files WHERE id='{$row['id']}'" );
				
				}
			
			}
		}
	}

	if( $user_group[$member_id['user_group']]['allow_up_image'] AND isset($_POST['comments_files']) AND is_array($_POST['comments_files']) AND count( $_POST['comments_files'] ) ) {
		
		foreach ( $_POST['comments_files'] as $file ) {
			
			$file = intval( $file );

			$row = $db->super_query( "SELECT id, name, driver FROM " . PREFIX . "_comments_files WHERE c_id = '{$news_id}' AND id='{$file}' AND author = '{$author}'" );
				
			if( $row['id'] ) {
				
				$dataimage = get_uploaded_image_info( $row['name'] );
				
				if( $row['driver'] AND DLEFiles::$driver == 'remote' ) $disk = 'remote';
				else $disk = 'local';
				
				DLEFiles::Delete( "posts/" . $dataimage->path, $disk );
				
				if( $dataimage->thumb ) {
					
					DLEFiles::Delete( "posts/{$dataimage->folder}/thumbs/{$dataimage->name}", $disk );
					
				}
				
				$db->query( "DELETE FROM " . PREFIX . "_comments_files WHERE id='{$row['id']}'" );
			
			}
			
		}
	}

	die( "{\"status\": \"ok\"}" );
}

//////////////////////
// go go show
//////////////////////

include (ENGINE_DIR . '/data/videoconfig.php');


$uploaded_list = array();
$images_count = $files_count = 0;

if( $area == "template" OR $area == "comments" ) {

	if( $area == "template" ) $db->query( "SELECT id, name FROM " . PREFIX . "_static_files WHERE static_id = '{$news_id}' AND onserver = ''" );
	else $db->query( "SELECT id, name FROM " . PREFIX . "_comments_files WHERE c_id = '{$news_id}' AND author = '{$author}'" );

	while ( $row = $db->get_row() ) {
		
		$images_count ++;

		$image = get_uploaded_image_info( $row['name'], 'posts',  true );
		
		if( $area == "template" ) $del_name = 'static_files';
		else $del_name = "comments_files";

		$img_url =  $image->url;
		$size = $image->size;
		$dimension = $image->dimension;
		
		if( $size ) $size = "({$size})";
		
		if($image->medium) {
			
			$img_url = $image->medium;
			$medium_data = "yes";
			
		} else $medium_data = "no";
		
		if($image->thumb) {
			
			$img_url = $image->thumb;
			$thumb_data = "yes";
			
		} else $thumb_data = "no";
				
		$file_name = explode("_", $image->name);
		
		if( count($file_name) > 1 ) unset($file_name[0]);
		
		$file_name = implode("_", $file_name);

$uploaded_list[] = <<<HTML
<div class="file-preview-card" data-type="image" data-area="{$del_name}" data-deleteid="{$row['id']}" data-url="{$image->url}" data-path="{$image->path}" data-thumb="{$thumb_data}" data-medium="{$medium_data}">
	<div class="active-ribbon"><span><i class="mediaupload-icon mediaupload-icon-ok"></i></span></div>
	<div class="file-content">
		<img src="{$img_url}" class="file-preview-image">
	</div>
	<div class="file-footer">
		<div class="file-footer-caption">
			<div class="file-caption-info" rel="tooltip" title="{$image->name}">{$file_name}</div>
			<div class="file-size-info">{$dimension} {$size}</div>
		</div>
		<div class="file-footer-bottom">
			<div class="file-preview">
				<a onclick="document.activeElement.blur(); hs.expand(this); return false;" href="{$image->url}" rel="tooltip" title="{$lang['up_im_expand']}"><i class="mediaupload-icon mediaupload-icon-zoom"></i></a>
				<a class="clipboard-copy-link" href="#" rel="tooltip" title="{$lang['up_im_copy']}"><i class="mediaupload-icon mediaupload-icon-copy"></i></a>	
			</div>
			<div class="file-delete"><a class="file-delete-link" href="#"><i class="mediaupload-icon mediaupload-icon-trash"></i></a></div>
		</div>
	</div>
</div>
HTML;
	
	}

} else {
		
	$row = $db->super_query( "SELECT images  FROM " . PREFIX . "_images WHERE news_id = '{$news_id}' AND author = '{$author}'" );

	if( isset($row['images']) AND $row['images'] ) {

		$listimages = explode( "|||", $row['images'] );	
		$images_count = count($listimages);

		foreach ( $listimages as $dataimages ) {

			$image = get_uploaded_image_info( $dataimages, 'posts',  true );

			$img_url =  $image->url;
			$size = $image->size;
			$dimension = $image->dimension;
			
			if( $size ) $size = "({$size})";
			
			if($image->medium) {
				
				$img_url = $image->medium;
				$medium_data = "yes";
				
			} else $medium_data = "no";
			
			if($image->thumb) {
				
				$img_url = $image->thumb;
				$thumb_data = "yes";
				
			} else $thumb_data = "no";
					
			$file_name = explode("_", $image->name);
			
			if( count($file_name) > 1 ) unset($file_name[0]);
			
			$file_name = implode("_", $file_name);

$uploaded_list[] = <<<HTML
<div class="file-preview-card" data-type="image" data-area="images" data-deleteid="{$image->path}" data-url="{$image->url}" data-path="{$image->path}" data-thumb="{$thumb_data}" data-medium="{$medium_data}">
	<div class="active-ribbon"><span><i class="mediaupload-icon mediaupload-icon-ok"></i></span></div>
	<div class="file-content">
		<img src="{$img_url}" class="file-preview-image">
	</div>
	<div class="file-footer">
		<div class="file-footer-caption">
			<div class="file-caption-info" rel="tooltip" title="{$image->name}">{$file_name}</div>
			<div class="file-size-info">{$dimension} {$size}</div>
		</div>
		<div class="file-footer-bottom">
			<div class="file-preview">
				<a onclick="document.activeElement.blur(); hs.expand(this); return false;" href="{$image->url}" rel="tooltip" title="{$lang['up_im_expand']}"><i class="mediaupload-icon mediaupload-icon-zoom"></i></a>
				<a class="clipboard-copy-link" href="#" rel="tooltip" title="{$lang['up_im_copy']}"><i class="mediaupload-icon mediaupload-icon-copy"></i></a>
			</div>
			<div class="file-delete"><a class="file-delete-link" href="#"><i class="mediaupload-icon mediaupload-icon-trash"></i></a></div>
		</div>
	</div>
</div>
HTML;

		}
		
	}

}

if( $area != "comments" ) {
	
	if( $area == "template" ) {
		
		$db->query( "SELECT * FROM " . PREFIX . "_static_files WHERE static_id = '{$news_id}' AND onserver != ''" );
		$del_name = 'static_files';
		
	} else {
		
		$db->query( "SELECT *  FROM " . PREFIX . "_files WHERE author = '{$author}' AND news_id = '{$news_id}'" );
		$del_name = "files";
		
	}

	while ( $row = $db->get_row() ) {
		$files_count ++;
		
		$data_url = "#";
		
		if ( $row['driver'] ) {
			$http_url = $config['remote_url'];
		} else {
			$http_url = $config['http_home_url'] . "uploads/";
		}
			
		if( $row['is_public'] ) {
			
			$uploaded_path = 'public_files/';
			$data_url = $http_url . $uploaded_path . $row['onserver'];
			
		} else $uploaded_path = 'files/';
		
		if( $row['size'] ) {
			
			$size = formatsize( $row['size'] );
			
		} else {
			
			$size = formatsize( @filesize( ROOT_DIR . "/uploads/" . $uploaded_path . $row['onserver'] ) );
			
		}

		$file_type = explode( ".", $row['name'] );
		$file_type = totranslit( end( $file_type ) );
		$file_link = $config['http_home_url'] . "engine/skins/images/all_file.png";
		$file_play = "";

		if( in_array( $file_type, $allowed_video ) ) {
			$data_url = $http_url . $uploaded_path . $row['onserver'];
			
			if( $file_type == "mp3" ) {
					
				$file_link = $config['http_home_url'] . "engine/skins/images/mp3_file.png";
				$file_play = "audio";
				
			} else {
					
				$file_link = $config['http_home_url'] . "engine/skins/images/video_file.png";
				$file_play = "video";
			}
			
		}

$uploaded_list[] = <<<HTML
<div class="file-preview-card" data-type="file" data-area="{$del_name}" data-deleteid="{$row['id']}" data-url="{$data_url}" data-path="{$row['id']}:{$row['name']}" data-play="{$file_play}" data-public="{$row['is_public']}">
	<div class="active-ribbon"><span><i class="mediaupload-icon mediaupload-icon-ok"></i></span></div>
	<div class="file-content">
		<img src="{$file_link}" class="file-preview-image">
	</div>
	<div class="file-footer">
		<div class="file-footer-caption">
			<div class="file-caption-info" rel="tooltip" title="ID: {$row['id']}, {$row['name']}">{$row['name']}</div>
			<div class="file-size-info">({$size})</div>
		</div>
		<div class="file-footer-bottom">
			<div class="file-preview">
				<a class="clipboard-copy-link" href="#" rel="tooltip" title="{$lang['up_im_copy']}"><i class="mediaupload-icon mediaupload-icon-copy"></i></a>
			</div>
			<div class="file-delete"><a class="file-delete-link" href="#"><i class="mediaupload-icon mediaupload-icon-trash"></i></a></div>
		</div>
	</div>
</div>
HTML;


	}
}

if ( count ($uploaded_list) ) $uploaded_list = implode("", $uploaded_list); else $uploaded_list = "";

$image_align = array ('0' => '', 'left' => '', 'right' => '', 'center' => '');
$image_align[$config['image_align']] = "selected";

if( $user_group[$member_id['user_group']]['allow_file_upload'] ) {
		
	if( $user_group[$member_id['user_group']]['max_file_size'] ) {

		if($maxupload AND $user_group[$member_id['user_group']]['max_file_size'] > $maxupload ) $user_group[$member_id['user_group']]['max_file_size'] = $maxupload;
		if($maxupload AND $config['max_up_size'] > $maxupload ) $config['max_up_size'] = $maxupload;
			
		$lang['files_max_info'] = $lang['files_max_info'] . " " . formatsize( $user_group[$member_id['user_group']]['max_file_size'] * 1024 );
		
	} else {
			
		$lang['files_max_info'] = $lang['files_max_info_2'];
		
	}
		
	$lang['files_max_info_1'] = $lang['files_max_info'] . "<br>" . $lang['files_max_info_1'] . " " . formatsize( $config['max_up_size'] * 1024 );
	
} else {
		
	$lang['files_max_info_1'] = $lang['files_max_info_1'] . " " . formatsize( $config['max_up_size'] * 1024 );
	
}

if( $area != "template" AND $area != "adminupload" AND $area != "comments" AND $user_group[$member_id['user_group']]['max_images'] ) {
	
	$lang['files_max_info_4'] = str_ireplace (array('{count}', '{uploaded}', '{allowed}'), array($user_group[$member_id['user_group']]['max_images'], '<span id="imagesuploaded">'.$images_count.'</span>', '<span id="imagesallowmore">'.$user_group[$member_id['user_group']]['max_images'] - $images_count.'</span>'), $lang['files_max_info_4'] );
	
	$lang['files_max_info_1'] .=  "<br>".$lang['files_max_info_4'];
	
}

if( $area == "comments" AND $user_group[$member_id['user_group']]['up_count_image'] ) {
	
	$lang['files_max_info_4'] = str_ireplace (array('{count}', '{uploaded}', '{allowed}'), array($user_group[$member_id['user_group']]['up_count_image'], '<span id="imagesuploaded">'.$images_count.'</span>', '<span id="imagesallowmore">'.$user_group[$member_id['user_group']]['up_count_image'] - $images_count.'</span>'), $lang['files_max_info_4'] );
	
	$lang['files_max_info_1'] .=  "<br>".$lang['files_max_info_4'];

}

if( $area != "template" AND $user_group[$member_id['user_group']]['max_files'] ) {
	
	$lang['files_max_info_5'] = str_ireplace (array('{count}', '{uploaded}', '{allowed}'), array($user_group[$member_id['user_group']]['max_files'], '<span id="filesuploaded">'.$files_count.'</span>', '<span id="filesallowmore">'.$user_group[$member_id['user_group']]['max_files'] - $files_count.'</span>'), $lang['files_max_info_5'] );
	
	$lang['files_max_info_1'] .=  "<br>".$lang['files_max_info_5'];

}


$upload_param = "";

if( $user_group[$member_id['user_group']]['allow_image_size'] ) {
	
	$t_seite_selected = array('0' => '', '1' => '', '2' => '');
	$t_seite_selected[$config['t_seite']] = "selected";

	if ( $config['max_image'] )	{

		$upload_param .= <<<HTML
<div class="checkbox"><label class="checkbox-inline margin-left"><input class="icheck" type="checkbox" name="make_thumb" id="make_thumb" value="1" checked="checked">{$lang['images_ath']}</label><input class="classic margin-left" type="text" name="t_size" id="t_size" style="width:100px;" value="{$config['max_image']}"><select name="t_seite" id="t_seite" class="uniform"><option value="0" {$t_seite_selected[0]}>{$lang['upload_t_seite_1']}</option><option value="1" {$t_seite_selected[1]}>{$lang['upload_t_seite_2']}</option><option value="2" {$t_seite_selected[2]}>{$lang['upload_t_seite_3']}</option></select></div>
HTML;

	}

	if ( $config['medium_image'] )	{

		$upload_param .= <<<HTML
<div class="checkbox"><label class="checkbox-inline margin-left"><input class="icheck" type="checkbox" name="make_medium" id="make_medium" value="1" checked="checked">{$lang['images_amh']}</label><input class="classic margin-left" type="text" name="m_size" id="m_size" style="width:100px;" value="{$config['medium_image']}"><select name="m_seite" id="m_seite" class="uniform"><option value="0" {$t_seite_selected[0]}>{$lang['upload_t_seite_1']}</option><option value="1" {$t_seite_selected[1]}>{$lang['upload_t_seite_2']}</option><option value="2" {$t_seite_selected[2]}>{$lang['upload_t_seite_3']}</option></select></div>
HTML;

	}

	if( $config['allow_watermark'] ) $upload_param .= "<div class=\"checkbox\"><label class=\"checkbox-inline margin-left\"><input class=\"icheck\" type=\"checkbox\" name=\"make_watermark\" value=\"yes\" id=\"make_watermark\" checked=\"checked\">{$lang['images_water']}</label></div>";

}

if( $user_group[$member_id['user_group']]['allow_public_file_upload'] ) $upload_param .= "<div class=\"checkbox\"><label class=\"checkbox-inline margin-left\"><input class=\"icheck\" type=\"checkbox\" name=\"public_file\" value=\"1\" id=\"public_file\">{$lang['public_file_upl']}</label></div>";


if( $member_id['user_group'] == 1 AND $area != "comments" ) {
	
	$locate = "/uploads/files/";
	
	if( $config['file_driver'] ) {
		$locate = "Remote /files/";
	}

	$ftp_input = <<<HTML
	<div class="mediaupload-row">
		<div class="mediaupload-col1">
			<b>{$locate}</b>
		</div>
		<div class="mediaupload-col2">
			<input class="classic" type="text" id="ftpurl" name="ftpurl" style="width:100%;max-width:360px;">
		</div>
		<div class="mediaupload-col3">
			<button onclick="upload_from_url('ftp'); return false;">{$lang['db_load_a']}</button>
		</div>
	</div>
	<div id="upload-viaftp-status"></div>
HTML;

} else $ftp_input = "";
	
	if( $user_group[$member_id['user_group']]['allow_file_upload'] ) {
		
		if( ! $user_group[$member_id['user_group']]['max_file_size'] ) $max_file_size = 0;
		elseif( $user_group[$member_id['user_group']]['max_file_size'] > $config['max_up_size'] ) $max_file_size = ( int ) $user_group[$member_id['user_group']]['max_file_size'];
		else $max_file_size = ( int )$config['max_up_size'];
	
	} else {
		
		$max_file_size = ( int )$config['max_up_size'];
	
	}

	if($maxupload AND $max_file_size > $maxupload) $max_file_size = $maxupload;

	$max_file_size = $max_file_size * 1024;
	
	$config['max_file_count'] = intval( $config['max_file_count'] );

	$all_ext = "*." . implode( ";*.", $allowed_extensions );
	$simple_ext = implode( "', '", $allowed_extensions );

	if( $config['files_allow'] and $user_group[$member_id['user_group']]['allow_file_upload'] ) {

		$all_ext .= ";*." . implode( ";*.", $allowed_files );
		$simple_ext .= "', '" . implode( "', '", $allowed_files );
	}

	$author = urlencode($author);
	
	$root = explode( "engine/ajax/controller.php", $_SERVER['PHP_SELF'] );
	$root = reset( $root );
	
	if( $area != "comments") {
		$gen_tab = "<li><a href='#' id=\"link3\" onclick=\"tabClick(1); return false;\" title=\"{$lang['images_lgem']}\"><span>{$lang['images_lgem']}</span></a></li>";
		$hidden_params="";
	} else {
		$gen_tab = "";
		$hidden_params=" style=\"display:none;\"";
	}
	
echo <<<HTML
<div class="tabs">
	<div class="tabsitems">
	  <ul>
		<li><a href='#' id="link1" onclick="tabClick(2); return false;" title='{$lang['media_upload_st']}' class="current" ><span>{$lang['media_upload_st']}</span></a></li>
		<li><a href='#' id="link2" onclick="tabClick(0); return false;" title='{$lang['images_iln']}'><span>{$lang['images_iln']}</span></a></li>
		{$gen_tab}
	  </ul>
	</div>
	<div id="check-all-box">
	  <label><input class="icheck" type="checkbox" name="check_all" value="1"  onchange="check_all(this); return false;"> {$lang['edit_selall']}</label>
	</div>
</div>
<div style="clear: both;"></div>
<div class="mediaupload-box">
<div id="stmode" class="file-upload-box" >
	<div class="media-upload-button-area">
		<div id="file-uploader"></div>
	</div>
	<div class="mediaupload-row">
		<div class="mediaupload-col1">
			{$lang['images_upurl']}
		</div>
		<div class="mediaupload-col2">
			<input class="classic" type="text" id="copyurl" name="copyurl" style="width:100%;max-width:360px;">
		</div>
		<div class="mediaupload-col3">
			<button onclick="upload_from_url('url'); return false;">{$lang['db_load_a']}</button>
		</div>
	</div>
	<div id="upload-viaurl-status"></div>
	{$ftp_input}
	<div class="upload-options">{$upload_param}</div>
	<div class="upload-restriction">{$lang['files_max_info_1']}</div>
</div>
<div id="cont1" class="file-preview-box file-can-all-selected" style="display:none;">{$uploaded_list}</div>
<div id="cont2" style="display:none;"></div>

<div id="mediaupload-buttonpane" style="display:none;">
	<div class="mediaupload-insert-params" style="display:none;">
		<div class="mediaupload-image-title" style="display:none;">
			<div class="insert-imagetitle"><input id="imagetitle" name="imagetitle" type="text" value="" placeholder="{$lang['media_upload_title']}" class="classic" style="width:100%;"></div>
			<div class="insert-properties"><span class="margin-left">{$lang['images_align']}</span><select id="imagealign" name="imagealign" class="dropup uniform" data-width="auto" data-dropdown-align-right="true" data-dropup-auto="false">
				  <option value="none" {$image_align[0]}>{$lang['opt_sys_no']}</option>
				  <option value="left" {$image_align['left']}>{$lang['images_left']}</option>
				  <option value="right" {$image_align['right']}>{$lang['images_right']}</option>
				  <option value="center" {$image_align['center']}>{$lang['images_center']}</option>
				</select>
		</div>
		</div>
		<div class="mediaupload-thumbs-params" style="display:none;"><span class="mediaupload-insert-descr">{$lang['media_upload_b1']}</span>
			<label id="mediaupload-thumb" class="radio-inline" style="display:none;"><input class="icheck" type="radio" name="thumbimg" id="thumbimg" value="1">{$lang['media_upload_ip2']}</label>
			<label id="mediaupload-medium" class="radio-inline" style="display:none;"><input class="icheck" type="radio" name="thumbimg" id="thumbimg1" value="2">{$lang['media_upload_ip6']}</label>
			<label id="mediaupload-original" class="radio-inline margin-left" style="display:none;"><input class="icheck" type="radio" name="thumbimg" id="thumbimg2" value="0">{$lang['media_upload_ip3']}</label>
			<label id="mediaupload-enlarge" class="checkbox-inline" style="display:none;"><input class="icheck" type="checkbox" name="insertoriginal" id="insertoriginal" value="1" checked="checked">{$lang['media_upload_ip7']}</label>
		</div>
		
		<div class="mediaupload-file-params" style="display:none;"><span class="mediaupload-insert-descr">{$lang['media_upload_b2']}</span>
			<label class="radio-inline"><input id="attachfordownload" class="icheck" type="radio" name="filemode" value="1" checked="checked">{$lang['media_upload_ip4']}</label>
			<label class="radio-inline"><input id="attachforplayer" class="icheck" type="radio" name="filemode" value="0">{$lang['media_upload_ip5']}</label>
		</div>
		
	</div>
	<div class="mediaupload-footer ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
		<div class="ui-dialog-buttonset">
		<button id='mediaupload-delete' type="button" onclick="media_delete_selected(); return false;" class="ui-button" style="display:none;">{$lang['images_del']}</button>
		<button type="button" class="ui-button" onclick="$('#mediaupload').dialog('close'); return false;">{$lang['p_cancel']}</button>
		<button id='mediaupload-insert' type="button" onclick="media_insert_selected(); return false;" class="ui-button" style="display:none;">{$lang['images_all_insert']}</button></div>
	</div>
</div>
HTML;



$max_file_size = number_format($max_file_size, 0, '', '');

if ( $uploaded_list ) $im_show = "tabClick(0);"; else $im_show = "";

echo <<<HTML
<script>
jQuery(function($){

	setTimeout(function() {
		initmediauploadpopup();
	}, 200);

});

function initmediauploadpopup() {
	
	{$im_show}
	
	LoadDLEFont();
	
	if (typeof $.fn.selectpicker === "function") {
	
		$('.dle-popup-mediaupload select.uniform').selectpicker();
		
		$('.dle-popup-mediaupload select.uniform').on('hide.bs.select', function () {
		
			setTimeout(function() {
				$('.dle-popup-mediaupload .insert-properties .btn-group.bootstrap-select.uniform').addClass('dropup');
			}, 10);
		
		});
	
	}
	
	if (typeof $.fn.tooltip === "function") {
	
		$('[rel=tooltip]').tooltip({
		  container: 'body'
		});
	
	}
	
	if (typeof $.fn.uniform === "function") {
		
		$(".dle-popup-mediaupload .icheck").uniform({
			radioClass: 'choice',
			wrapperClass: 'border-teal-600 text-teal-800',
			fileButtonClass: 'btn bg-teal btn-sm btn-raised'
		});

	}

	$(document).off("click", '.file-preview-card .clipboard-copy-link');
	$(document).off("click", '.file-preview-card .file-delete-link');
	$(document).on("click", '.file-preview-card .file-delete-link',	function(e){
		e.preventDefault();
		media_delete_file( $(this).closest('.file-preview-card') );
		
		return false;
	});

	$(document).on("click", '.file-preview-card .clipboard-copy-link',	function(e){
	
		e.preventDefault();
		document.activeElement.blur();
		var box = $(this).closest('.file-preview-card');
		var copytext = '';

		if ( box.data('type') == 'image') {
		
			copytext = box.data('url');
			
		} else {
		
			if ( (box.data('play') == "video" || box.data('play') == "audio") && $('#attachforplayer').prop('checked') ) {
				copytext = '['+box.data('play')+'='+box.data('url')+']';
			} else {
				if(box.data('public') == "1") {
					copytext = box.data('url');
				} else {
					copytext = '[attachment='+box.data('path')+']';
				}
			}

		}
		
		DLEcopyToClipboard(copytext);
		
		return false;
	});	

	$(document).off("click", '.file-preview-card .file-content');
	$(document).on("click", '.file-preview-card .file-content',	function(e){
		e.preventDefault();
		$(this).parent().toggleClass("active");
		insert_props_panel();
		
		return false;
	});

	
	if (typeof qq == "undefined" ) {
		$.getCachedScript('{$root}engine/classes/uploads/html5/fileuploader.js').done(function() {
			loadmediauploader();
		});
	} else {
		loadmediauploader();
	}
	
	if (typeof hs == "undefined" ) {
		$.getCachedScript('{$root}engine/classes/highslide/highslide.js').done(function() {
			hs.numberOfImagesToPreload = 0;
			hs.captionEval = 'this.thumb.alt';
			hs.showCredits = false;
			hs.transitions = ['expand', 'crossfade'];
			hs.wrapperClassName = 'less';
			hs.align = 'center';
			hs.outlineType = null;
		});
	}

	setTimeout(function() {
		get_shared_list('');
	}, 1000);
	
};
  
function LoadDLEFont() {
    const elem = document.createElement('i');
    elem.className = 'mediaupload-icon';
	elem.style.position = 'absolute';
	elem.style.left = '-9999px';
	document.body.appendChild(elem);

	if ($( elem ).css('font-family') !== 'mediauploadicons') {
		$('head').append('<link rel="stylesheet" type="text/css" href="{$root}engine/classes/uploads/html5/fileuploader.css">');
	}
  
    document.body.removeChild(elem);
};

function DLEcopyToClipboard(text) {

   try {
		const elem = document.createElement('textarea');
		elem.value = text;
		elem.setAttribute('readonly', '');
		elem.style.position = 'absolute';
		elem.style.left = '-9999px';
		document.body.appendChild(elem);
		elem.select();
		document.execCommand('copy');
		document.body.removeChild(elem);
		
		if (typeof $.fn.jGrowl === "function") {
			$.jGrowl( '{$lang['up_im_copy1']}', {
				life: 1000,
				theme: 'alert-styled-left alert-styled-custom alpha-teal text-teal-900'
			});
		}
	
  } catch (err) {
  
    console.log('Unable to copy');
	
  }

};

function loadmediauploader() {
	
	var totaladded = 0;
	var totaluploaded = 0;
	
	var uploader = new qq.FileUploader({
		element: document.getElementById('file-uploader'),
		action: '{$root}engine/ajax/controller.php?mod=upload',
		maxConnections: 1,
		encoding: 'multipart',
        sizeLimit: {$max_file_size},
		allowedExtensions: ['{$simple_ext}'],
	    params: {"subaction" : "upload", "news_id" : "{$news_id}", "area" : "{$area}", "author" : "{$author}", "user_hash" : "{$dle_login_hash}"},
		classes: {button:"media-upload-button",drop:"media-upload-drop-area",dropActive:"media-upload-drop-area-active",list:"qq-upload-list",file:"qq-upload-file",spinner:"qq-upload-spinner",size:"qq-upload-size",cancel:"qq-upload-cancel",success:"qq-upload-success",fail:"qq-upload-fail"},
        template: '<div class="media-uploader">' + 
                '<div class="media-upload-drop-area"><div>{$lang['media_upload_st5']}</div></div>' +
                '<div class="media-upload-button">{$lang['media_upload_st4']}</div>' +
                '<ul class="qq-upload-list" style="display:none;"></ul>' + 
             '</div>',
		onSubmit: function(id, fileName) {

					uploader._options.params['t_size'] = $('#t_size').val();
					uploader._options.params['t_seite'] = $('#t_seite').val();
					uploader._options.params['make_thumb'] = $("#make_thumb").is(":checked") ? 1 : 0;
					uploader._options.params['m_size'] = $('#m_size').val();
					uploader._options.params['m_seite'] = $('#m_seite').val();
					uploader._options.params['make_medium'] = $("#make_medium").is(":checked") ? 1 : 0;
					uploader._options.params['make_watermark'] = $("#make_watermark").is(":checked") ? 1 : 0;
					uploader._options.params['public_file'] = $("#public_file").is(":checked") ? 1 : 0;

					totaladded ++;

					$('<div id="uploadfile-'+id+'" class="mediaupload-file-box"><span class="qq-upload-file-status">{$lang['media_upload_st6']}</span><span class="qq-upload-file"><b>&nbsp;'+fileName+'&nbsp;</b></span><span class="qq-status"><span class="qq-upload-size"></span></span><div class="mediaupload-progress"><div class="mediaupload-progress-bar mediaupload-progress-blue" style="width: 0%"><span>0%</span></div></div></div>').appendTo('#file-uploader');

        },
		onProgress: function(id, fileName, loaded, total){
					$('#uploadfile-'+id+' .qq-upload-size').text(uploader._formatSize(loaded)+' {$lang['media_upload_st8']} '+uploader._formatSize(total));
					var proc = Math.round(loaded / total * 100);
					$('#uploadfile-'+id+' .mediaupload-progress-bar').css( "width", proc + '%' );

		},
		onComplete: function(id, fileName, response){
						totaluploaded ++;

						if ( response.success ) {
						
							var timeout = 1000;
							var returnbox = response.returnbox;

							returnbox = returnbox.replace(/&lt;/g, "<");
							returnbox = returnbox.replace(/&gt;/g, ">");
							returnbox = returnbox.replace(/&amp;/g, "&");

							if( $( '#imagesallowmore' ).length ) {
								
								if ( $('<div>' + returnbox + '</div>').find( ".file-preview-card" ).data('type') == "image" ) {
								
									var allow_more = parseInt( $('#imagesallowmore').text() );
									var images_uploaded = parseInt( $('#imagesuploaded').text() );
									
									allow_more --;
									images_uploaded ++;
									
									if( allow_more < 0 ) allow_more = 0;
									
									$('#imagesallowmore').text(allow_more);
									$('#imagesuploaded').text(images_uploaded);
								
								}
							}
							
							if( $( '#filesallowmore' ).length ) {
								
								if ( $('<div>' + returnbox + '</div>').find( ".file-preview-card" ).data('type') == "file" ) {
								
									var allow_more = parseInt( $('#filesallowmore').text() );
									var files_uploaded = parseInt( $('#filesuploaded').text() );
									
									allow_more --;
									files_uploaded ++;
									
									if( allow_more < 0 ) allow_more = 0;
									
									$('#filesallowmore').text(allow_more);
									$('#filesuploaded').text(files_uploaded);
								
								}
							}
							
							$('#uploadfile-'+id+' .qq-status').html('{$lang['media_upload_st9']}');
							
							if(!response.tinypng_error && !response.remote_error) {
								$('#uploadfile-'+id+'').addClass('mediaupload-file-success');
							}
							
							if( response.tinypng_error ) {
								timeout = 10000;
								$('#uploadfile-'+id+'').addClass('mediaupload-file-warning');
								$('#uploadfile-'+id+' .qq-status').append( '<br><span style="color:red;">{$lang['tinyapi_error']}<br>' + response.tinypng_error + '</span>' );
							}
							
							if( response.remote_error ) {
								timeout = 10000;
								$('#uploadfile-'+id+'').addClass('mediaupload-file-warning');
								$('#uploadfile-'+id+' .qq-status').append( '<br><span style="color:red;">{$lang['remote_error']}<br>' + response.remote_error + '</span><br>{$lang['remote_error_1']}' );
							}
							
							$('#cont1').append( returnbox );
							
							if (typeof $.fn.tooltip === "function") {
							
								$('[rel=tooltip]').tooltip({
								  container: 'body'
								});
							
							}
							
							if (totaluploaded == totaladded ) {
								setTimeout(function() {
									tabClick(0);
								}, timeout);
							
							}

							setTimeout(function() {
								$('#uploadfile-'+id).fadeOut('slow', function() { $(this).remove(); });
							}, timeout);

						} else {
						
							$('#uploadfile-'+id+'').addClass('mediaupload-file-error');
							$('#uploadfile-'+id+' .qq-status').html('{$lang['media_upload_st10']}');

							if( response.error ) $('#uploadfile-'+id+' .qq-status').append( '<br /><span style="color:red;">' + response.error + '</span>' );

							setTimeout(function() {
								$('#uploadfile-'+id).fadeOut('slow');
							}, 20000);
						}
		},
        messages: {
            typeError: "{$lang['media_upload_st11']}",
            sizeError: "{$lang['media_upload_st12']}",
            emptyError: "{$lang['media_upload_st13']}"
        },
		debug: false
    });
	
	
}

function check_all( obj ) {

	if(obj && obj.checked) {
		$('.file-can-all-selected .file-preview-card').addClass("active");
	} else {
		$('.file-preview-card').removeClass("active");
	}
	
	insert_props_panel();
	return false;
}

function insert_props_panel() {

	if( $('.file-preview-card.active').length ) {
	
		var backup_state = $('.mediaupload-insert-params').outerHeight();
		
		$('#mediaupload-insert').show();
		$('#mediaupload-delete').show();
		
		var show = false;
		$('.mediaupload-image-title').hide();
		$('.mediaupload-thumbs-params').hide();
		$('#mediaupload-thumb').hide();
		$('#mediaupload-medium').hide();
		$('#mediaupload-original').hide();
		$('#mediaupload-enlarge').hide();
		$('.mediaupload-file-params').hide();

		$('.file-preview-card.active').each(function(){
		
			if($(this).data('type') == 'image'){
				show = true;
				$('.mediaupload-image-title').show();
				
				if( $(this).data('thumb') == 'yes' || $(this).data('medium') == 'yes' ) {
					$('.mediaupload-thumbs-params').show();
					$('#mediaupload-original').show();
					$('#mediaupload-enlarge').show();
				}

				if( $(this).data('thumb') == 'yes' ) {
					$('#mediaupload-thumb').show();
					$('#thumbimg').prop('checked', true);
				}
				
				if( $(this).data('medium') == 'yes' ) {
					$('#mediaupload-medium').show();
					if( !$('#thumbimg').prop('checked') || ($(this).data('thumb') != 'yes' && !$('#mediaupload-thumb').is(':visible')) ) {
						$('#thumbimg1').prop('checked', true);
					}
				}
				
				if (typeof $.fn.uniform === "function") {	
					$.uniform.update();
				}

			
			} else {

				if ( $(this).data('play') == "video" || $(this).data('play') == "audio" ) {
					show = true;
					$('.mediaupload-file-params').show();
					
					if (typeof $.fn.uniform === "function") {	
						$.uniform.update();
					}
				}
				
			}
			
			
		});
			
		if( $('.mediaupload-insert-params').is(':visible') ) {
			var current_state = $('.mediaupload-insert-params').outerHeight();
			
			if(current_state != backup_state) {
				current_state = current_state - backup_state;
				$('.mediaupload-body').height( $('.mediaupload-body').height() - current_state );
			}
			
		} else {
			if( show ) {
				$('.mediaupload-insert-params').show();
				$('.mediaupload-body').height( $('.mediaupload-body').height() - $('.mediaupload-insert-params').outerHeight() );				
			}
		}
		
		
	} else {
		
		$('#mediaupload-insert').hide();
		$('#mediaupload-delete').hide();
		
		if( $('.mediaupload-insert-params').is(':visible') ) {		
				$('.mediaupload-body').height( $('.mediaupload-body').height() + $('.mediaupload-insert-params').outerHeight() );
				$('.mediaupload-insert-params').hide();
		}
		
	}

	return false;
}

function tabClick(n) {

	if (n == 0) {
		$("#cont2").hide();
		$("#stmode").hide();
		$("#linkbox").hide();
		$("#cont1").fadeTo('slow', 1);
		$("#link2").addClass("current");
		$("#link1").removeClass("current");
		$("#link3").removeClass("current");
		$("#check-all-box").show();

	}

	if (n == 1) {
		$("#stmode").hide();
		$("#cont1").hide();
		$("#linkbox").hide();
		$("#cont2").fadeTo('slow', 1);
		$("#link3").addClass("current");
		$("#link1").removeClass("current");
		$("#link2").removeClass("current");
		$("#check-all-box").hide();
	}

	if (n == 2) {
		$("#cont2").hide();
		$("#cont1").hide();
		$("#linkbox").hide();
		$("#stmode").fadeTo('slow', 1);
		$("#link1").addClass("current");
		$("#link2").removeClass("current");
		$("#link3").removeClass("current");
		$("#check-all-box").hide();
	}

};


function media_insert_selected() {

    var frm = document.delimages;
    var wysiwyg = '{$wysiwyg}';
	var allways_bbimages = '{$config['bbimages_in_wysiwyg']}';
	var links = new Array();
	var align = $('#imagealign').val();
	var content = '';
	var t = 0;
	var url = ''
	var have_images = false;

	if( $('.file-preview-card.active').length ) {
	
		$('.file-preview-card.active').each(function() {
		
			if($(this).data('type') == 'image'){
			
				have_images = true;
				url = $(this).data('url');
				
				if ( !$('#insertoriginal').prop('checked') ) {
					
					if( $('#thumbimg').prop('checked') || $('#thumbimg1').prop('checked') ) {
			
						if( $('#thumbimg').prop('checked') ) {
							var folder="thumbs";
						} else {
							var folder="medium";
						}
			
						url = url.split('/');
						var filename = url.pop();
						url.push(folder);
						url.push(filename);
						url = url.join('/');
					
					}
			
					links[t] = buildimage (url);
			
				} else {
			
					if ( $(this).data('thumb') == "yes" || $(this).data('medium') == "yes" ) {
					
						if( $('#thumbimg').prop('checked') ) {
						
							links[t] = buildthumb (url, 'thumb');
							
						} else if( $('#thumbimg1').prop('checked') ) {
						
							links[t] = buildthumb (url, 'medium');
							
						} else {
						
							links[t] = buildimage ( url );
	
						}
					} else {
					
						links[t] = buildimage ( url );
						
					}
			
				}	
			

				
			} else {

				if ( ($(this).data('play') == "video" || $(this).data('play') == "audio") && $('#attachforplayer').prop('checked') ) {
					links[t] = '['+$(this).data('play')+'='+$(this).data('url')+']';
				} else {
					if( $(this).data('public') == "1" ) {
						if (wysiwyg != 'no') {
							links[t] = '<a href="'+$(this).data('url')+'">'+$(this).data('url')+'</a>';
						} else {
							links[t] = '[url='+$(this).data('url')+']'+$(this).data('url')+'[/url]';
						}
						
					} else {
						links[t] = '[attachment='+$(this).data('path')+']';
					}
				}
			}
			
			t++;
		});
		
	}

	if (wysiwyg != 'no') {
	
		if( $('.file-preview-card.active').length > 1 ) {
		
			if( !have_images ) {
			
				content = links.join(' ');
				
			} else if (align == 'center') {
			
				if(allways_bbimages == '1') {
					content = links.join('</p><p style="text-align: center;">');
					content = '<p style="text-align: center;">'+ content +'</p>';
				} else {
					content = links.join('</p><p>');
					content = '<p>'+ content +'</p>';
				}
				
			} else {
				content = links.join(' ');
			}
			
		} else { content = links.join(''); }

	} else {
	
		if( !have_images ) {
		
			content = links.join(' ');
			
		} else if( align == 'left' || align == 'right' ) {
		
			content = links.join('');
			
		} else {
		
			content = links.join('\\n');
			
		}
		
		if (align == 'center' && content != "" && have_images ) { content = '[center]'+ content +'[/center]'; }
	}

	insertcontent( content );

};


function buildthumb( image, tag ) {

	var align = $('#imagealign').val();
	var imagealt = $('#imagetitle').val();
	var content = '';
    var wysiwyg = '{$wysiwyg}';
	var allways_bbimages = '{$config['bbimages_in_wysiwyg']}';

	if( (wysiwyg == '1' || wysiwyg == '2') && allways_bbimages != '1') {
	
		if( tag == 'thumb' ) {
			var folder="thumbs";
		} else {
			var folder="medium";
		}
		
		url = image.split('/');
		var filename = url.pop();
		url.push(folder);
		url.push(filename);
		url = url.join('/');

		content = '<a href="'+image+'" class="highslide" target="_blank">';
		content += buildimage( url );
		content += '</a>';
		
	} else {
	
		var imgoption = "";
	
		if (imagealt != "") { 
	
			imgoption = "|"+imagealt;
	
		}
	
		if (align != "none" && align != "center") { 

			imgoption = align+imgoption;

		}
	
		if (imgoption != "" ) {
	
			imgoption = "="+imgoption;

		}
	
		content = '['+tag+''+imgoption+']'+ image +'[/'+tag+']';
	
	}


	return content;
};

function buildimage( image ) {

    var wysiwyg = '{$wysiwyg}';
	var content = '';
	var align = $('#imagealign').val();
	var imagealt = $('#imagetitle').val();
	var allways_bbimages = '{$config['bbimages_in_wysiwyg']}';
	
	imagealt = escapeHtml(imagealt);
	
	if (wysiwyg != 'no' && allways_bbimages == '1') {
		wysiwyg = 'no';
	}
	
	if (wysiwyg != 'no') {
		
		if ( wysiwyg == '1' ) {
			var img_opt;
			
			if (align == 'center') {
				img_opt = "fr-dib";				
			} else if(align == 'none') {
				img_opt = "fr-dii";
			} else if(align == 'left') {
				img_opt = "fr-dii fr-fil";
			} else {
				img_opt = "fr-dii fr-fir";	
			}
			
			content = '<img src="'+ image +'" alt="'+ imagealt +'" class="'+ img_opt +'">';

		} else {
		
			if (align == 'center' || align == 'none') {
			
				if(align == 'center') {
					img_opt = " style=\"display: block; margin-left: auto; margin-right: auto;\"";
				} else {
					img_opt = "";
				}
				
				content = '<img src="'+ image +'" alt="'+ imagealt +'"'+ img_opt +'>';
				
			} else {
			
				content = '<img src="'+ image +'" style="float:' + align+ ';" alt="'+ imagealt +'">';
				
			}
			
		}

	} else {

		var imgoption = "";
		var imagealt = $('#imagetitle').val();

		if (imagealt != "") { 

			imgoption = "|"+imagealt;

		}

		if (align != "none" && align != "center") { 

			imgoption = align+imgoption;

		}

		if (imgoption != "" ) {

			imgoption = "="+imgoption;

		}

		content = '[img'+imgoption+']'+ image +'[/img]';

	}

	return content;
};

function insertcontent( content ) {
    var wysiwyg = '{$wysiwyg}';
	var allways_bbimages = '{$config['bbimages_in_wysiwyg']}';

	if ( wysiwyg == '1' ) {
		active_editor.events.focus();
		active_editor.selection.restore();
		active_editor.undo.saveStep();
		if(allways_bbimages == '1') {
			active_editor.html.insert( content );
		} else {
			active_editor.html.insert( content + $.FE.MARKERS );
		}
		active_editor.undo.saveStep();


	} else if (wysiwyg == '2') {
		if(allways_bbimages == '1') {
			tinymce.activeEditor.insertContent( content );
		} else {
			tinymce.activeEditor.insertContent( content + '&nbsp;' );
		}

	} else {
		doInsert( content, '', false );
	}
	
	$('#mediaupload').dialog('close');
	
	return false;
};

function escapeHtml( string ) {

	var entityMap = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#39;',
		'/': '&#x2F;',
		'`': '&#x60;',
		'=': '&#x3D;',
		'?': '&#x3F'
	};
	
	return String(string).replace(/[&<>"'`=\/\?]/g, function (match) {
		return entityMap[match];
	});
	
}

function upload_from_url( url ) {

	var t_size = $('#t_size').val();
	var t_seite = $('#t_seite').val();
	var m_size = $('#m_size').val();
	var m_seite = $('#m_seite').val();
	var make_thumb = $("#make_thumb").is(":checked") ? 1 : 0;
	var make_medium = $("#make_medium").is(":checked") ? 1 : 0;
	var make_watermark = $("#make_watermark").is(":checked") ? 1 : 0;
	var public_file = $("#public_file").is(":checked") ? 1 : 0;

	if (url == 'url' ) {

		var copyurl = $('#copyurl').val();
		var ftpurl = '';
		var error_id = 'upload-viaurl-status';		
	} else {

		var ftpurl = $('#ftpurl').val();
		var copyurl = '';
		var error_id = 'upload-viaftp-status';
	}

	$('#'+error_id).html( '<span style="color:green;">{$lang['ajax_info']}</span>' );

	$.post( "{$root}engine/ajax/controller.php?mod=upload", { news_id: "{$news_id}", imageurl: copyurl, ftpurl: ftpurl, t_size: t_size, t_seite: t_seite, make_thumb: make_thumb, m_size: m_size, m_seite: m_seite, make_medium: make_medium, make_watermark: make_watermark, public_file: public_file, area: "{$area}", author: "{$author}", subaction: "upload", user_hash : "{$dle_login_hash}" }, function(data){

		if ( data.success ) {

			var returnbox = data.returnbox;

			returnbox = returnbox.replace(/&lt;/g, "<");
			returnbox = returnbox.replace(/&gt;/g, ">");
			returnbox = returnbox.replace(/&amp;/g, "&");

			$('#cont1').append( returnbox );

			$('#'+error_id).html('');

			if (url == 'url' ) {
				$('#copyurl').val('');
			} else {
				$('#ftpurl').val('');
			}

			tabClick(0);

		} else {

			if( data.error ) $('#'+error_id).html( '<span style="color:red;">' + data.error + '</span>' );

		}

	}, "json");
	return false;

};

function media_delete_file( file ) {

	DLEconfirm( '{$lang['file_delete']}', '{$lang['p_info']}', function () {
	
		var formData = new FormData();
		formData.append('subaction', 'deluploads');
		formData.append('user_hash', '{$dle_login_hash}');
		formData.append('area', '{$area}');
		formData.append('news_id', '{$news_id}');
		formData.append('author', '{$author}');
		formData.append( file.data('area')+'[]', file.data('deleteid') );

		if( $( '#imagesallowmore' ).length ) {
			
			if ( file.data('area') == "images" ) {
			
				var allow_more = parseInt( $('#imagesallowmore').text() );
				var images_uploaded = parseInt( $('#imagesuploaded').text() );
				
				allow_more ++;
				images_uploaded --;
				
				if( allow_more < 0 ) allow_more = 0;
				
				$('#imagesallowmore').text(allow_more);
				$('#imagesuploaded').text(images_uploaded);
			
			}
		}
		
		if( $( '#filesallowmore' ).length ) {
			
			if ( file.data('area') == "files" ) {
			
				var allow_more = parseInt( $('#filesallowmore').text() );
				var files_uploaded = parseInt( $('#filesuploaded').text() );
				
				allow_more ++;
				files_uploaded --;
				
				if( allow_more < 0 ) allow_more = 0;
				
				$('#filesallowmore').text(allow_more);
				$('#filesuploaded').text(files_uploaded);
			
			}
		}

		ShowLoading('');
	
		$.ajax({
			url: "{$root}engine/ajax/controller.php?mod=upload",
			data: formData,
			processData: false,
			contentType: false,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				HideLoading('');
			
				if (data.status) {
	
					file.fadeOut("slow", function() {
						file.remove();
					});
	
				} else {

					DLEalert('{$lang['files_del_error']}', dle_info);
	
				}

			}
		});
		
		return false;
		
	} );
	
	return false;
};


function media_delete_selected() {

	if( $('.file-preview-card.active').length ) {
	
		DLEconfirm( '{$lang['delete_selected']}', '{$lang['p_info']}', function () {
		
			var allow_del = true;
			var formData = new FormData();
			formData.append('subaction', 'deluploads');
			formData.append('user_hash', '{$dle_login_hash}');
			formData.append('area', '{$area}');
			formData.append('news_id', '{$news_id}');
			formData.append('author', '{$author}');
			
			
			
			$('.file-preview-card.active').each(function(){
			
				if( $(this).data('area') == 'shared' ) {
				
					allow_del = false;
					check_all();
					return false;
					
				} else if( $(this).data('deleteid') ) {
				
					formData.append( $(this).data('area')+'[]', $(this).data('deleteid') );
					
					if( $( '#imagesallowmore' ).length ) {
						
						if ( $(this).data('area') == "images" ) {
						
							var allow_more = parseInt( $('#imagesallowmore').text() );
							var images_uploaded = parseInt( $('#imagesuploaded').text() );
							
							allow_more ++;
							images_uploaded --;
							
							if( allow_more < 0 ) allow_more = 0;
							
							$('#imagesallowmore').text(allow_more);
							$('#imagesuploaded').text(images_uploaded);
						
						}
					}
					
					if( $( '#filesallowmore' ).length ) {
						
						if ( $(this).data('area') == "files" ) {
						
							var allow_more = parseInt( $('#filesallowmore').text() );
							var files_uploaded = parseInt( $('#filesuploaded').text() );
							
							allow_more ++;
							files_uploaded --;
							
							if( allow_more < 0 ) allow_more = 0;
							
							$('#filesallowmore').text(allow_more);
							$('#filesuploaded').text(files_uploaded);
						
						}
					}
					
		
				}
			
			});
		
			if(!allow_del) {
				return false;
			}
			
			ShowLoading('');
		
			$.ajax({
				url: "{$root}engine/ajax/controller.php?mod=upload",
				data: formData,
				processData: false,
				contentType: false,
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					HideLoading('');
				
					if (data.status) {
		
						$('.file-preview-card.active').fadeOut("slow", function() {
							$('.file-preview-card.active').remove();
							check_all();
						});
		
					} else {
	
						DLEalert('{$lang['files_del_error']}', dle_info);
		
					}
	
				}
			});
			
			return false;
	
	
	
	
		} );
	
	}	
	return false;
};
function get_shared_list( userdir ) {

	if( !$('#link3').length ){
		return false;
	}

	$.get("{$root}engine/ajax/controller.php?mod=adminfunction", { action: 'viewshared', userdir: userdir, user_hash: '{$dle_login_hash}' }, function(data){

		if (data.success) {
		
			$('#cont2').html(data.response);

		} else {
		
			$('#cont2').html('<div class="mediaupload-file-box mediaupload-file-error" style="margin:10px;">' + data.error + '</div>');
			
		}

	}, "json").fail(function(jqXHR, textStatus, errorThrown ) {

			var error_status = '';
		
			if (jqXHR.status < 200 || jqXHR.status >= 300) {
			  error_status = 'HTTP Error: ' + jqXHR.status;
			} else {
				error_status = 'Invalid JSON: ' + jqXHR.responseText;
			}
	
			$('#cont2').html('<div class="mediaupload-file-box mediaupload-file-error" style="margin:10px;">' + error_status + '</div>');
		
	});
	
	return false;
	
};
		
</script>
HTML;

?>