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
 File: show.short.php
-----------------------------------------------------
 Use:  view short news
=====================================================
*/

if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( $allow_active_news ) {
	
	$news_count = $cstart;
	$global_news_count = 0;
	$news_found = false;
	$tpl->news_mode = true;
	$page_description = "";

	if( $view_template != "rss" ) {
		if( $category_id and $cat_info[$category_id]['short_tpl'] != '' ) $tpl->load_template( $cat_info[$category_id]['short_tpl'] . '.tpl' );
		else $tpl->load_template( 'shortstory.tpl' );
	}

	$xfields = xfieldsload();

	if(count($xfields)) {
		$xfound = true;
	} else $xfound = false;

	if( $config['allow_banner'] AND count( $banners ) AND isset( $ban_short ) AND !$smartphone_detected) {
		
		$news_c = 1;
		$banners_topz = $banners_cenz = $banners_downz = '';
		
		if ( isset($ban_short['top']) AND is_array($ban_short['top']) AND count($ban_short['top']) ) {
			for($indx = 0, $max = sizeof( $ban_short['top'] ), $banners_topz = ''; $indx < $max; $indx ++) {
				if( isset($ban_short['top'][$indx]['zakr']) AND $ban_short['top'][$indx]['zakr'] ) {
					$banners_topz .= $ban_short['top'][$indx]['text'];
					unset( $ban_short['top'][$indx] );
				}
			}
		}

		if ( isset($ban_short['cen']) AND is_array($ban_short['cen']) AND count($ban_short['cen']) ) {		
			for($indx = 0, $max = sizeof( $ban_short['cen'] ), $banners_cenz = ''; $indx < $max; $indx ++) {
				if( isset($ban_short['cen'][$indx]['zakr']) AND $ban_short['cen'][$indx]['zakr'] ) {
					$banners_cenz .= $ban_short['cen'][$indx]['text'];
					unset( $ban_short['cen'][$indx] );
				}
			}
		}
		
		if ( isset($ban_short['down']) AND is_array($ban_short['down']) AND count($ban_short['down']) ) {		
			for($indx = 0, $max = sizeof( $ban_short['down'] ), $banners_downz = ''; $indx < $max; $indx ++) {
				if( isset($ban_short['down'][$indx]['zakr']) AND $ban_short['down'][$indx]['zakr'] ) {
					$banners_downz .= $ban_short['down'][$indx]['text'];
					unset( $ban_short['down'][$indx] );
				}
			}
		}
		
		$middle = floor( $config['news_number'] / 2 ) + 1;
		
		if($middle < 2 ) $middle = 2;
		
		$middle_s = round( $middle / 2 );
		
		if($middle_s < 2 ) $middle_s = 2;
		
		if($middle_s == $middle ) {
			if( (is_array($ban_short['cen']) AND count($ban_short['cen'])) OR  $banners_cenz )  $middle_s = 0;
		}
		
		$middle_e = floor( $middle + (($config['news_number'] - $middle) / 2) + 1 );
		
		if($middle AND $middle_e == $middle ) {
			if( (is_array($ban_short['cen']) AND count($ban_short['cen'])) OR  $banners_cenz )  $middle_e = 0;
		}
		
		if($middle_s AND $middle_e == $middle_s ) {
			if( (is_array($ban_short['top']) AND count($ban_short['top'])) OR  $banners_topz )  $middle_e = 0;
		}
		
	}

	$sql_result = $db->query( $sql_select );
	
	while ( $row = $db->get_row( $sql_result ) ) {
		
		$news_found = TRUE;
		$attachments[] = $row['id'];
		$row['date'] = strtotime( $row['date'] );

		if( $row['editdate'] AND $row['editdate'] > $_DOCUMENT_DATE ) $_DOCUMENT_DATE = $row['editdate'];
		elseif( $row['date'] > $_DOCUMENT_DATE ) $_DOCUMENT_DATE = $row['date'];
		
		if( $config['allow_banner'] AND count( $banners ) ) {
			
			foreach ( $banners as $name => $value ) {
				$tpl->copy_template = str_replace( "{banner_" . $name . "}", $value, $tpl->copy_template );

				if ( $value ) {
					$tpl->copy_template = str_replace ( "[banner_" . $name . "]", "", $tpl->copy_template );
					$tpl->copy_template = str_replace ( "[/banner_" . $name . "]", "", $tpl->copy_template );
				}
			}
		}

		$tpl->set_block( "'{banner_(.*?)}'si", "" );
		$tpl->set_block ( "'\\[banner_(.*?)\\](.*?)\\[/banner_(.*?)\\]'si", "" );

		if( isset( $middle ) ) {

			if( $news_c == $middle_s ) {
				$tpl->copy_template = bannermass( $banners_topz, $ban_short['top'] ).$tpl->copy_template;
			} else if( $news_c == $middle ) {
				$tpl->copy_template = bannermass( $banners_cenz, $ban_short['cen'] ).$tpl->copy_template;
			} else if( $news_c == $middle_e ) {
				$tpl->copy_template = bannermass( $banners_downz, $ban_short['down'] ).$tpl->copy_template;
			}
			
			$news_c ++;
		}
		
		$news_count ++;
		
		if( !$row['category'] ) {
			
			$my_cat = "---";
			$my_cat_link = "---";
			
			$tpl->set( '[not-has-category]', "" );
			$tpl->set( '[/not-has-category]', "" );
			$tpl->set_block( "'\\[has-category\\](.*?)\\[/has-category\\]'si", "" );
			
		} else {
			
			$my_cat = array ();
			$my_cat_link = array ();
			$cat_list = $row['cats'] = explode( ',', $row['category'] );
			
			$tpl->set( '[has-category]', "" );
			$tpl->set( '[/has-category]', "" );
			$tpl->set_block( "'\\[not-has-category\\](.*?)\\[/not-has-category\\]'si", "" );
			
			if( count( $cat_list ) == 1 OR ($view_template == "rss" AND $config['rss_format'] == 2)) {
				
				if( $cat_info[$cat_list[0]]['id'] ) {
					
					$my_cat[] = $cat_info[$cat_list[0]]['name'];
					$my_cat_link = get_categories( $cat_list[0], $config['category_separator']);
					
				} else $my_cat_link = "---";
			
			} else {
				
				foreach ( $cat_list as $element ) {
					
					if( $element AND $cat_info[$element]['id'] ) {
						$my_cat[] = $cat_info[$element]['name'];
						if( $config['allow_alt_url'] ) $my_cat_link[] = "<a href=\"" . $config['http_home_url'] . get_url( $element ) . "/\">{$cat_info[$element]['name']}</a>";
						else $my_cat_link[] = "<a href=\"$PHP_SELF?do=cat&category={$cat_info[$element]['alt_name']}\">{$cat_info[$element]['name']}</a>";
					}
					
				}
				
				if( count( $my_cat_link ) ) {
					$my_cat_link = implode( $config['category_separator'], $my_cat_link );
				} else $my_cat_link = "---";

			}
			
			if( count( $my_cat ) ) {
				$my_cat = implode( $config['category_separator'], $my_cat );
			} else $my_cat = "---";
			
		}

		$url_cat = $category_id;
	
		if (stripos ( $tpl->copy_template, "[category=" ) !== false) {
			$tpl->copy_template = preg_replace_callback ( "#\\[(category)=(.+?)\\](.*?)\\[/category\\]#is", "check_category", $tpl->copy_template );
		}
		
		if (stripos ( $tpl->copy_template, "[not-category=" ) !== false) {
			$tpl->copy_template = preg_replace_callback ( "#\\[(not-category)=(.+?)\\](.*?)\\[/not-category\\]#is", "check_category", $tpl->copy_template );
		}
	
		$category_id = $row['category'];
	
		if( strpos( $tpl->copy_template, "[catlist=" ) !== false ) {
			$tpl->copy_template = preg_replace_callback ( "#\\[(catlist)=(.+?)\\](.*?)\\[/catlist\\]#is", "check_category", $tpl->copy_template );
		}
								
		if( strpos( $tpl->copy_template, "[not-catlist=" ) !== false ) {
			$tpl->copy_template = preg_replace_callback ( "#\\[(not-catlist)=(.+?)\\](.*?)\\[/not-catlist\\]#is", "check_category", $tpl->copy_template );
		}
	
		$temp_rating = $config['rating_type'];
		$config['rating_type'] = if_category_rating( $row['category'] );
		
		if ( $config['rating_type'] === false ) {
			$config['rating_type'] = $temp_rating;
		}
		
		$category_id = $url_cat;

		if( $config['allow_alt_url'] ) {
			
			if( $config['seo_type'] == 1 OR $config['seo_type'] == 2  ) {
				
				if( $row['category'] and $config['seo_type'] == 2 ) {

					$cats_url = get_url( $row['category'] );
					
					if($cats_url) {
						
						$full_link = $config['http_home_url'] . $cats_url . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
						
					} else $full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
				
				} else {
					
					$full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
				
				}
			
			} else {
				
				$full_link = $config['http_home_url'] . date( 'Y/m/d/', $row['date'] ) . $row['alt_name'] . ".html";
			}
		
		} else {
			
			$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['id'];
		
		}
		
		if ( $row['category'] ) {
			
			if( $config['allow_alt_url'] ) {
				
				$cats_url = get_url( $row['category'] );
				
				if( $cats_url ) $cats_url .= "/";
			
				$tpl->set( '{category-url}', $config['http_home_url'] . $cats_url );
				
			} else {
				
				$cats_url = intval($row['category']);
				$tpl->set( '{category-url}', "{$PHP_SELF}?do=cat&category=".$cat_info[$cats_url]['alt_name'] );
				
			}
			
		} else $tpl->set( '{category-url}', "#" );	
	

		$row['category'] = intval( $row['category'] );
		
		$news_find = array ('{comments-num}' => number_format($row['comm_num'], 0, ',', ' '), '{views}' => number_format($row['news_read'], 0, ',', ' '), '{category}' => $my_cat, '{link-category}' => $my_cat_link, '{news-id}' => $row['id'] );
		
		$tpl->set( '', $news_find );
	
		if( $row['category'] AND $cat_info[$row['category']]['icon'] ) {
			
			$tpl->set( '{category-icon}', $cat_info[$row['category']]['icon'] );
			$tpl->set( '[category-icon]', "" );
			$tpl->set( '[/category-icon]', "" );
			$tpl->set_block( "'\\[not-category-icon\\](.*?)\\[/not-category-icon\\]'si", "" );
			
		} else {
			
			$tpl->set( '{category-icon}', "{THEME}/dleimages/no_icon.gif" );
			$tpl->set( '[not-category-icon]', "" );
			$tpl->set( '[/not-category-icon]', "" );
			$tpl->set_block( "'\\[category-icon\\](.*?)\\[/category-icon\\]'si", "" );
		}
		
		if( date( 'Ymd', $row['date'] ) == date( 'Ymd', $_TIME ) ) {
			
			$tpl->set( '{date}', $lang['time_heute'] . langdate( ", H:i", $row['date'], $short_news_cache ) );
		
		} elseif( date( 'Ymd', $row['date'] ) == date( 'Ymd', ($_TIME - 86400) ) ) {
			
			$tpl->set( '{date}', $lang['time_gestern'] . langdate( ", H:i", $row['date'], $short_news_cache ) );
		
		} else {
			
			$tpl->set( '{date}', langdate( $config['timestamp_active'], $row['date'], $short_news_cache ) );
		
		}

		$news_date = $row['date'];
		$tpl->copy_template = preg_replace_callback ( "#\{date=(.+?)\}#i", "formdate", $tpl->copy_template );

		$global_news_count ++;

		if (strpos ( $tpl->copy_template, "[newscount=" ) !== false) {
			$tpl->copy_template = preg_replace_callback ( "#\\[(newscount)=(.+?)\\](.*?)\\[/newscount\\]#is", "check_newscount", $tpl->copy_template );
		}

		if (strpos ( $tpl->copy_template, "[not-newscount=" ) !== false) {
			$tpl->copy_template = preg_replace_callback ( "#\\[(not-newscount)=(.+?)\\](.*?)\\[/not-newscount\\]#is", "check_newscount", $tpl->copy_template );
		}

		if ( $row['fixed'] ) {

			$tpl->set( '[fixed]', "" );
			$tpl->set( '[/fixed]', "" );
			$tpl->set_block( "'\\[not-fixed\\](.*?)\\[/not-fixed\\]'si", "" );

		} else {

			$tpl->set( '[not-fixed]', "" );
			$tpl->set( '[/not-fixed]', "" );
			$tpl->set_block( "'\\[fixed\\](.*?)\\[/fixed\\]'si", "" );
		}
		
		if ( $row['comm_num'] ) {
			
			$tpl->set( '[comments]', "" );
			$tpl->set( '[/comments]', "" );
			$tpl->set_block( "'\\[not-comments\\](.*?)\\[/not-comments\\]'si", "" );

		} else {
				
			$tpl->set( '[not-comments]', "" );
			$tpl->set( '[/not-comments]', "" );
			$tpl->set_block( "'\\[comments\\](.*?)\\[/comments\\]'si", "" );
		}

		if ( $row['votes'] ) {

			$tpl->set( '[poll]', "" );
			$tpl->set( '[/poll]', "" );
			$tpl->set_block( "'\\[not-poll\\](.*?)\\[/not-poll\\]'si", "" );

		} else {

			$tpl->set( '[not-poll]', "" );
			$tpl->set( '[/not-poll]', "" );
			$tpl->set_block( "'\\[poll\\](.*?)\\[/poll\\]'si", "" );
		}		

		if( strpos( $tpl->copy_template, "{poll}" ) !== false AND $view_template != "rss" ) {
	
			if( $row['votes'] ) {
	
				include (DLEPlugins::Check(ENGINE_DIR . '/modules/poll.php'));
	
				$tpl->set( '{poll}', $tpl->result['poll'] );
	
			} else {
	
				$tpl->set( '{poll}', '' );
	
			}
		}

		if( $row['view_edit'] and $row['editdate'] ) {
			
			if( date( 'Ymd', $row['editdate'] ) == date( 'Ymd', $_TIME ) ) {
				
				$tpl->set( '{edit-date}', $lang['time_heute'] . langdate( ", H:i", $row['editdate'], $short_news_cache ) );
			
			} elseif( date( 'Ymd', $row['editdate'] ) == date( 'Ymd', ($_TIME - 86400) ) ) {
				
				$tpl->set( '{edit-date}', $lang['time_gestern'] . langdate( ", H:i", $row['editdate'], $short_news_cache ) );
			
			} else {
				
				$tpl->set( '{edit-date}', langdate( $config['timestamp_active'], $row['editdate'], $short_news_cache ) );
			
			}
			
			$tpl->set( '{editor}', $row['editor'] );
			$tpl->set( '{edit-reason}', $row['reason'] );
			
			if( $row['reason'] ) {
				
				$tpl->set( '[edit-reason]', "" );
				$tpl->set( '[/edit-reason]', "" );
			
			} else
				$tpl->set_block( "'\\[edit-reason\\](.*?)\\[/edit-reason\\]'si", "" );
			
			$tpl->set( '[edit-date]', "" );
			$tpl->set( '[/edit-date]', "" );
		
		} else {
			
			$tpl->set( '{edit-date}', "" );
			$tpl->set( '{editor}', "" );
			$tpl->set( '{edit-reason}', "" );
			$tpl->set_block( "'\\[edit-date\\](.*?)\\[/edit-date\\]'si", "" );
			$tpl->set_block( "'\\[edit-reason\\](.*?)\\[/edit-reason\\]'si", "" );
		}
		
		if( $config['allow_tags'] and $row['tags'] ) {
			
			$tpl->set( '[tags]', "" );
			$tpl->set( '[/tags]', "" );
			
			$tags = array ();
			
			$row['tags'] = explode( ",", $row['tags'] );
			
			foreach ( $row['tags'] as $value ) {
				
				$value = trim( $value );
				$url_tag = str_replace(array("&#039;", "&quot;", "&amp;", "/"), array("'", '"', "&", "&frasl;"), $value);
								
				if( $config['allow_alt_url'] ) $tags[] = "<a href=\"" . $config['http_home_url'] . "tags/" . rawurlencode( dle_strtolower($url_tag) ) . "/\">" . $value . "</a>";
				else $tags[] = "<a href=\"$PHP_SELF?do=tags&amp;tag=" . rawurlencode( dle_strtolower($url_tag) ) . "\">" . $value . "</a>";
			
			}
			
			$tpl->set( '{tags}', implode( $config['tags_separator'], $tags ) );
		
		} else {
			
			$tpl->set_block( "'\\[tags\\](.*?)\\[/tags\\]'si", "" );
			$tpl->set( '{tags}', "" );
		
		}
		
		if ( $config['rating_type'] == "1" ) {
				$tpl->set( '[rating-type-2]', "" );
				$tpl->set( '[/rating-type-2]', "" );
				$tpl->set_block( "'\\[rating-type-1\\](.*?)\\[/rating-type-1\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-3\\](.*?)\\[/rating-type-3\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-4\\](.*?)\\[/rating-type-4\\]'si", "" );
		} elseif ( $config['rating_type'] == "2" ) {
				$tpl->set( '[rating-type-3]', "" );
				$tpl->set( '[/rating-type-3]', "" );
				$tpl->set_block( "'\\[rating-type-1\\](.*?)\\[/rating-type-1\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-2\\](.*?)\\[/rating-type-2\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-4\\](.*?)\\[/rating-type-4\\]'si", "" );
		} elseif ( $config['rating_type'] == "3" ) {
				$tpl->set( '[rating-type-4]', "" );
				$tpl->set( '[/rating-type-4]', "" );
				$tpl->set_block( "'\\[rating-type-1\\](.*?)\\[/rating-type-1\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-2\\](.*?)\\[/rating-type-2\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-3\\](.*?)\\[/rating-type-3\\]'si", "" );
		} else {
				$tpl->set( '[rating-type-1]', "" );
				$tpl->set( '[/rating-type-1]', "" );
				$tpl->set_block( "'\\[rating-type-4\\](.*?)\\[/rating-type-4\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-3\\](.*?)\\[/rating-type-3\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-2\\](.*?)\\[/rating-type-2\\]'si", "" );	
		}
		
		if( $row['allow_rate'] ) {
			
			if( $config['short_rating'] AND $user_group[$member_id['user_group']]['allow_rating'] ) {
				
				$tpl->set( '{rating}', ShowRating( $row['id'], $row['rating'], $row['vote_num'], 1 ) );
				
				if ( $config['rating_type'] ) {
					
					$tpl->set( '[rating-plus]', "<a href=\"#\" onclick=\"doRate('plus', '{$row['id']}'); return false;\" >" );
					$tpl->set( '[/rating-plus]', '</a>' );
					
					if ( $config['rating_type'] == "2" OR $config['rating_type'] == "3" ) {
						
						$tpl->set( '[rating-minus]', "<a href=\"#\" onclick=\"doRate('minus', '{$row['id']}'); return false;\" >" );
						$tpl->set( '[/rating-minus]', '</a>' );
						
					} else {
						$tpl->set_block( "'\\[rating-minus\\](.*?)\\[/rating-minus\\]'si", "" );
					}
					
				} else {
					$tpl->set_block( "'\\[rating-plus\\](.*?)\\[/rating-plus\\]'si", "" );
					$tpl->set_block( "'\\[rating-minus\\](.*?)\\[/rating-minus\\]'si", "" );
				}
				
			} else {
				
				$tpl->set( '{rating}', ShowRating( $row['id'], $row['rating'], $row['vote_num'], 0 ) );
				$tpl->set_block( "'\\[rating-plus\\](.*?)\\[/rating-plus\\]'si", "" );
				$tpl->set_block( "'\\[rating-minus\\](.*?)\\[/rating-minus\\]'si", "" );
				
			}
			
			if( $row['vote_num'] ) $ratingscore = str_replace( ',', '.', round( ($row['rating'] / $row['vote_num']), 1 ) );
			else $ratingscore = 0;

			$tpl->set( '{ratingscore}', $ratingscore );
			
			$dislikes = ($row['vote_num'] - $row['rating'])/2;
			$likes = $row['vote_num'] - $dislikes;
			
			$tpl->set( '{likes}', "<span id=\"likes-id-".$row['id']."\">".$likes."</span>" );
			$tpl->set( '{dislikes}', "<span id=\"dislikes-id-".$row['id']."\">".$dislikes."</span>" );
			$tpl->set( '{vote-num}', "<span id=\"vote-num-id-".$row['id']."\">".$row['vote_num']."</span>" );
			$tpl->set( '[rating]', "" );
			$tpl->set( '[/rating]', "" );
		
		} else {
			
			$tpl->set( '{rating}', "" );
			$tpl->set( '{vote-num}', "" );
			$tpl->set( '{likes}', "" );
			$tpl->set( '{dislikes}', "" );
			$tpl->set( '{ratingscore}', "" );
			$tpl->set_block( "'\\[rating\\](.*?)\\[/rating\\]'si", "" );
			$tpl->set_block( "'\\[rating-plus\\](.*?)\\[/rating-plus\\]'si", "" );
			$tpl->set_block( "'\\[rating-minus\\](.*?)\\[/rating-minus\\]'si", "" );
		}
		
		$config['rating_type'] = $temp_rating;
		
		if( $config['allow_alt_url'] ) {
			
			$go_page = $config['http_home_url'] . "user/" . urlencode( $row['autor'] ) . "/";
			$tpl->set( '[day-news]', "<a href=\"".$config['http_home_url'] . date( 'Y/m/d/', $row['date'])."\" >" );
		
		} else {
			
			$go_page = "$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['autor'] );
			$tpl->set( '[day-news]', "<a href=\"$PHP_SELF?year=".date( 'Y', $row['date'])."&amp;month=".date( 'm', $row['date'])."&amp;day=".date( 'd', $row['date'])."\" >" );
		
		}

		$tpl->set( '[/day-news]', "</a>" );
		$tpl->set( '[profile]', "<a href=\"" . $go_page . "\">" );
		$tpl->set( '[/profile]', "</a>" );
		$tpl->set_block( "'\\[not-news\\](.*?)\\[/not-news\\]'si", "" );

		$tpl->set( '{login}', $row['autor'] );
		
		$tpl->set( '{author}', "<a onclick=\"ShowProfile('" . urlencode( $row['autor'] ) . "', '" . $go_page . "', '" . $user_group[$member_id['user_group']]['admin_editusers'] . "'); return false;\" href=\"" . $go_page . "\">" . $row['autor'] . "</a>" );
		
		if( $allow_userinfo and ($member_id['name'] == $row['autor'] and ! $user_group[$member_id['user_group']]['allow_all_edit']) ) {

			$tpl->set( '[edit]', "<a href=\"" . $config['http_home_url'] . "index.php?do=addnews&id=" . $row['id'] . "\" >" );
			$tpl->set( '[/edit]', "</a>" );

		} elseif( $is_logged and (($member_id['name'] == $row['autor'] and $user_group[$member_id['user_group']]['allow_edit']) or $user_group[$member_id['user_group']]['allow_all_edit']) ) {
			
			$_SESSION['referrer'] = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, $config['charset'] );
			$tpl->set( '[edit]', "<a onclick=\"return dropdownmenu(this, event, MenuNewsBuild('" . $row['id'] . "', 'short'), '170px')\" href=\"#\">" );
			$tpl->set( '[/edit]', "</a>" );
			$allow_comments_ajax = true;

		} else $tpl->set_block( "'\\[edit\\](.*?)\\[/edit\\]'si", "" );

		if( ($row['full_story'] < 13) AND $config['hide_full_link'] ) $tpl->set_block( "'\\[full-link\\](.*?)\\[/full-link\\]'si", "" );
		else {
			
			$tpl->set( '[full-link]', "<a href=\"" . $full_link . "\">" );
			
			$tpl->set( '[/full-link]', "</a>" );
		}
		
		$tpl->set( '{full-link}', $full_link );
		
		if( $row['allow_comm'] OR (!$row['allow_comm'] AND $row['comm_num']) ) {
			
			$tpl->set( '[com-link]', "<a href=\"" . $full_link . "#comment\">" );
			$tpl->set( '[/com-link]', "</a>" );
		
		} else $tpl->set_block( "'\\[com-link\\](.*?)\\[/com-link\\]'si", "" );
		
		if( $is_logged ) {
			
				$tpl->set( '{favorites}', "{-favorites-{$row['id']}}" );
				$tpl->set( '[add-favorites]', "[add-favorites-{$row['id']}]" );
				$tpl->set( '[/add-favorites]', "[/add-favorites-{$row['id']}]" );
				$tpl->set( '[del-favorites]', "[del-favorites-{$row['id']}]" );
				$tpl->set( '[/del-favorites]', "[/del-favorites-{$row['id']}]" );
		
		} else {
			
			$tpl->set( '{favorites}', "" );
			$tpl->set_block( "'\\[add-favorites\\](.*?)\\[/add-favorites\\]'si", "" );
			$tpl->set_block( "'\\[del-favorites\\](.*?)\\[/del-favorites\\]'si", "" );
			
		}
		
		$tpl->set( '[complaint]', "<a href=\"javascript:AddComplaint('" . $row['id'] . "', 'news')\">" );
		$tpl->set( '[/complaint]', "</a>" );
		
		if( $allow_userinfo) {
			
			$tpl->set( '{approve}', $lang['approve'] );
		
		} else $tpl->set( '{approve}', "" );
			

		$row['xfields'] = stripslashes( $row['xfields'] );
		$all_xf_content = array();

		if( $xfound AND count($xfields) ) {
			$row['xfields_array'] = xfieldsdataload( $row['xfields'] );
		}
		
		if( $xfound AND count($xfields) ) {
			$xfieldsdata = $row['xfields_array'];
			
			foreach ( $xfields as $value ) {
				$preg_safe_name = preg_quote( $value[0], "'" );
				
				if( $value[20] ) {
				  
				  $value[20] = explode( ',', $value[20] );
				  
				  if( $value[20][0] AND !in_array( $member_id['user_group'], $value[20] ) ) {
					  $xfieldsdata[$value[0]] = "";
				  }
				  
				}
			
				if ( $value[3] == "yesorno" ) {
					
				    if( intval($xfieldsdata[$value[0]]) ) {
						$xfgiven = true;
						$xfieldsdata[$value[0]] = $lang['xfield_xyes'];
					} else {
						$xfgiven = false;
						$xfieldsdata[$value[0]] = $lang['xfield_xno'];
					}
					
				} else {
					
					if( isset($xfieldsdata[$value[0]]) AND $xfieldsdata[$value[0]] ) $xfgiven = true; else $xfgiven = false;
					
				}
				
				if( !$xfgiven ) {
					$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
					$tpl->copy_template = str_ireplace( "[xfnotgiven_{$value[0]}]", "", $tpl->copy_template );
					$tpl->copy_template = str_ireplace( "[/xfnotgiven_{$value[0]}]", "", $tpl->copy_template );
				} else {
					$tpl->copy_template = preg_replace( "'\\[xfnotgiven_{$preg_safe_name}\\](.*?)\\[/xfnotgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
					$tpl->copy_template = str_ireplace( "[xfgiven_{$value[0]}]", "", $tpl->copy_template );
					$tpl->copy_template = str_ireplace( "[/xfgiven_{$value[0]}]", "", $tpl->copy_template );
				}
				
				if(strpos( $tpl->copy_template, "[ifxfvalue {$value[0]}" ) !== false ) {
					$tpl->copy_template = preg_replace_callback ( "#\\[ifxfvalue(.+?)\\](.+?)\\[/ifxfvalue\\]#is", "check_xfvalue", $tpl->copy_template );
				}
				
				if ( $value[6] AND !empty( $xfieldsdata[$value[0]] ) ) {
					$temp_array = explode( ",", $xfieldsdata[$value[0]] );
					$value3 = array();

					foreach ($temp_array as $value2) {

						$value2 = trim($value2);
						
						if($value2) {
							
							$value4 = str_replace(array("&#039;", "&quot;", "&amp;", "&#123;", "&#91;", "&#58;", "/"), array("'", '"', "&", "{", "[", ":", "&frasl;"), $value2);
							
							if( $value[3] == "datetime" ) {
							
								$value2 = strtotime( $value4 );
							
								if( !trim($value[24]) ) $value[24] = $config['timestamp_active'];
								
								if( $value[25] ) {
									
									if($value[26]) $value2 = langdate($value[24], $value2);
									else $value2 = langdate($value[24], $value2, false, $customlangdate);
									
								} else $value2 = date( $value[24], $value2 );

							}
	
							if( $config['allow_alt_url'] ) $value3[] = "<a href=\"" . $config['http_home_url'] . "xfsearch/" .$value[0]."/". rawurlencode( dle_strtolower($value4) ) . "/\">" . $value2 . "</a>";
							else $value3[] = "<a href=\"$PHP_SELF?do=xfsearch&amp;xfname=".$value[0]."&amp;xf=" . rawurlencode( dle_strtolower($value4) ) . "\">" . $value2 . "</a>";
							
						}

					}
					
					if( empty($value[21]) ) $value[21] = ", ";
					
					$xfieldsdata[$value[0]] = implode($value[21], $value3);

					unset($temp_array);
					unset($value2);
					unset($value3);
					unset($value4);

				} elseif ( $value[3] == "datetime" AND !empty($xfieldsdata[$value[0]]) ) {

					$xfieldsdata[$value[0]] = strtotime( str_replace("&#58;", ":", $xfieldsdata[$value[0]]) );

					if( !trim($value[24]) ) $value[24] = $config['timestamp_active'];

					if( $value[25] ) {
						
						if($value[26]) $xfieldsdata[$value[0]] = langdate($value[24], $xfieldsdata[$value[0]]);
						else $xfieldsdata[$value[0]] = langdate($value[24], $xfieldsdata[$value[0]], false, $customlangdate);
									
					} else $xfieldsdata[$value[0]] = date( $value[24], $xfieldsdata[$value[0]] );
					
					
				}
				
				if ($config['allow_links'] AND $value[3] == "textarea" AND function_exists('replace_links') ) $xfieldsdata[$value[0]] = replace_links ( $xfieldsdata[$value[0]], $replace_links['news'] );

				if($value[3] == "image" AND isset($xfieldsdata[$value[0]]) AND $xfieldsdata[$value[0]] ) {
					
					$temp_array = explode('|', $xfieldsdata[$value[0]]);
						
					if (count($temp_array) == 1 OR count($temp_array) == 5 ){
							
						$temp_alt = '';
						$temp_value = implode('|', $temp_array );
							
					} else {
							
						$temp_alt = $temp_array[0];
						$temp_alt = str_replace( "&amp;#44;", "&#44;", $temp_alt );
						$temp_alt = str_replace( "&amp;#124;", "&#124;", $temp_alt );
						
						unset($temp_array[0]);
						$temp_value =  implode('|', $temp_array );
							
					}

					$path_parts = get_uploaded_image_info($temp_value);
					
					if( $value[12] AND $path_parts->thumb ) {
						
						$tpl->set( "[xfvalue_thumb_url_{$value[0]}]", $path_parts->thumb);
						$xfieldsdata[$value[0]] = "<a href=\"{$path_parts->url}\" class=\"highslide\" target=\"_blank\"><img class=\"xfieldimage {$value[0]}\" src=\"{$path_parts->thumb}\" alt=\"{$temp_alt}\"></a>";

					} else {
						
						$tpl->set( "[xfvalue_thumb_url_{$value[0]}]", $path_parts->url);
						$xfieldsdata[$value[0]] = "<img class=\"xfieldimage {$value[0]}\" src=\"{$path_parts->url}\" alt=\"{$temp_alt}\">";

					}
					
					$tpl->set( "[xfvalue_image_url_{$value[0]}]", $path_parts->url);
					$tpl->set( "[xfvalue_image_description_{$value[0]}]", $temp_alt);
					
					if( $value[28] ) {
						
						if( !$path_parts->thumb ) $path_parts->thumb = $path_parts->url;
						
						$xfields_in_news['[xfvalue_image_url_'.$value[0].']'] = $path_parts->url;
						$xfields_in_news['[xfvalue_image_description_'.$value[0].']'] = $temp_alt;
						$xfields_in_news['[xfvalue_thumb_url_'.$value[0].']'] = $path_parts->thumb;
					}

				}
				
				$xfieldsdata[$value[0]] = isset($xfieldsdata[$value[0]]) ? $xfieldsdata[$value[0]] : '';
				
				if($value[3] == "image" AND !$xfieldsdata[$value[0]]) {
					$tpl->set( "[xfvalue_thumb_url_{$value[0]}]", "");
					$tpl->set( "[xfvalue_image_url_{$value[0]}]", "");
					$tpl->set( "[xfvalue_image_description_{$value[0]}]", "");
				}
				
				if($value[3] == "imagegalery" AND $xfieldsdata[$value[0]]) {
					
					$fieldvalue_arr = explode(',', $xfieldsdata[$value[0]]);
					$gallery_image = array();
					$gallery_single_image = array();
					$xf_image_count = 0;
					
					foreach ($fieldvalue_arr as $temp_value) {
						
						$xf_image_count ++;
						
						$temp_value = trim($temp_value);
				
						if($temp_value == "") continue;
						
						$temp_array = explode('|', $temp_value);
						
						if (count($temp_array) == 1 OR count($temp_array) == 5 ){
								
							$temp_alt = '';
							$temp_value = implode('|', $temp_array );
								
						} else {
								
							$temp_alt = $temp_array[0];
							$temp_alt = str_replace( "&amp;#44;", "&#44;", $temp_alt );
							$temp_alt = str_replace( "&amp;#124;", "&#124;", $temp_alt );
							
							unset($temp_array[0]);
							$temp_value =  implode('|', $temp_array );
								
						}
	
						$path_parts = get_uploaded_image_info($temp_value);
					
						if($value[12] AND $path_parts->thumb) {
							
							$gallery_image[] = "<li><a href=\"{$path_parts->url}\" onclick=\"return hs.expand(this, { slideshowGroup: 'xf_{$row['id']}_{$value[0]}' })\" target=\"_blank\"><img src=\"{$path_parts->thumb}\" alt=\"{$temp_alt}\"></a></li>";
							$gallery_single_image['[xfvalue_'.$value[0].' image="'.$xf_image_count.'"]'] = "<a href=\"{$path_parts->url}\" class=\"highslide\" target=\"_blank\"><img class=\"xfieldimage {$value[0]}\" src=\"{$path_parts->thumb}\" alt=\"{$temp_alt}\"></a>";

						} else {
							
							$gallery_image[] = "<li><img src=\"{$path_parts->url}\" alt=\"{$temp_alt}\"></li>";
							$gallery_single_image['[xfvalue_'.$value[0].' image="'.$xf_image_count.'"]'] = "<img class=\"xfieldimage {$value[0]}\" src=\"{$path_parts->url}\" alt=\"{$temp_alt}\">";
							
						}
						
						if( !$path_parts->thumb ) $path_parts->thumb = $path_parts->url;
						
						$gallery_single_image['[xfvalue_'.$value[0].' image-description="'.$xf_image_count.'"]'] = $temp_alt;
						$gallery_single_image['[xfvalue_'.$value[0].' image-thumb-url="'.$xf_image_count.'"]'] = $path_parts->thumb;
						$gallery_single_image['[xfvalue_'.$value[0].' image-url="'.$xf_image_count.'"]'] = $path_parts->url;

						$tpl->copy_template = str_ireplace( '[xfgiven_'.$value[0].' image="'.$xf_image_count.'"]', "", $tpl->copy_template );
						$tpl->copy_template = str_ireplace( '[/xfgiven_'.$value[0].' image="'.$xf_image_count.'"]', "", $tpl->copy_template );
						$tpl->copy_template = preg_replace( "'\\[xfnotgiven_{$preg_safe_name} image=\"{$xf_image_count}\"\\](.*?)\\[/xfnotgiven_{$preg_safe_name} image=\"{$xf_image_count}\"\\]'is", "", $tpl->copy_template );
					
					}
					
					if(count($gallery_single_image) ) {
						
						foreach($gallery_single_image as $temp_key => $temp_value) {
							
							$tpl->set( $temp_key, $temp_value);
							
							if( $value[28] ) {
								$xfields_in_news[$temp_key] = $temp_value;
							}
							
						}
					}
					
					$xfieldsdata[$value[0]] = "<ul class=\"xfieldimagegallery {$value[0]}\">".implode($gallery_image)."</ul>";
					
				}
				
				$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name} image=\"(\d+)\"\\](.*?)\\[/xfgiven_{$preg_safe_name} image=\"(\d+)\"\\]'is", "", $tpl->copy_template );
				$tpl->copy_template = preg_replace( "'\\[xfnotgiven_{$preg_safe_name} image=\"(\d+)\"\\]'i", "", $tpl->copy_template );
				$tpl->copy_template = preg_replace( "'\\[/xfnotgiven_{$preg_safe_name} image=\"(\d+)\"\\]'i", "", $tpl->copy_template );	
				
				if ($config['image_lazy']) $xfieldsdata[$value[0]] = preg_replace_callback ( "#<(img|iframe)(.+?)>#i", "enable_lazyload", $xfieldsdata[$value[0]] );

				if( $view_template == "rss" ) {
					$xfieldsdata[$value[0]] = clear_rss_content ( $xfieldsdata[$value[0]] );
				}
				
				$tpl->set( "[xfvalue_{$value[0]}]", $xfieldsdata[$value[0]]);
	
				if( $value[28] ) {
					$xfields_in_news['[xfvalue_'.$value[0].']'] = $xfieldsdata[$value[0]];
				}
				
				if( !$page_description ) {
					if( ($value[3] == "text" OR $value[3] == "textarea") AND $xfieldsdata[$value[0]]) {
						$all_xf_content[] = $xfieldsdata[$value[0]];
					}	
				}
				
				if ( preg_match( "#\\[xfvalue_{$preg_safe_name} limit=['\"](.+?)['\"]\\]#i", $tpl->copy_template, $matches ) ) {
					$tpl->set( $matches[0], clear_content($xfieldsdata[$value[0]], $matches[1]) );
				} 

			}
		}
		
		if( count($all_xf_content) ) $all_xf_content = implode(" ", $all_xf_content);
		else $all_xf_content = "";
		
		$row['short_story'] = stripslashes($row['short_story']);
		
		if( !$page_description ) {
			$page_description = clear_content( $row['short_story']." ".$all_xf_content );	
		}
		
		unset($all_xf_content);

		if (stripos ( $tpl->copy_template, "image-" ) !== false) {

			$images = array();
			preg_match_all('/(img|src)=("|\')[^"\'>]+/i', $row['short_story'].$row['xfields'], $media);
			$data=preg_replace('/(img|src)("|\'|="|=\')(.*)/i',"$3",$media[0]);
	
			foreach($data as $url) {
				$info = pathinfo($url);
				if (isset($info['extension'])) {
					if ($info['filename'] == "spoiler-plus" OR $info['filename'] == "spoiler-minus" OR strpos($info['dirname'], 'engine/data/emoticons') !== false) continue;
					$info['extension'] = strtolower($info['extension']);
					if (($info['extension'] == 'jpg') || ($info['extension'] == 'jpeg') || ($info['extension'] == 'gif') || ($info['extension'] == 'png') || ($info['extension'] == 'bmp') || ($info['extension'] == 'webp') || ($info['extension'] == 'avif')) array_push($images, $url);
				}
			}
	
			if ( count($images) ) {
				$i_count=0;
				foreach($images as $url) {
					$i_count++;
					$tpl->copy_template = str_replace( '{image-'.$i_count.'}', $url, $tpl->copy_template );
					$tpl->copy_template = str_replace( '[image-'.$i_count.']', "", $tpl->copy_template );
					$tpl->copy_template = str_replace( '[/image-'.$i_count.']', "", $tpl->copy_template );
					$tpl->copy_template = preg_replace( "#\[not-image-{$i_count}\](.+?)\[/not-image-{$i_count}\]#is", "", $tpl->copy_template );
				}
	
			}
	
			$tpl->copy_template = preg_replace( "#\[image-(.+?)\](.+?)\[/image-(.+?)\]#is", "", $tpl->copy_template );
			$tpl->copy_template = preg_replace( "#\\{image-(.+?)\\}#i", "{THEME}/dleimages/no_image.jpg", $tpl->copy_template );
			$tpl->copy_template = preg_replace( "#\[not-image-(.+?)\]#i", "", $tpl->copy_template );
			$tpl->copy_template = preg_replace( "#\[/not-image-(.+?)\]#i", "", $tpl->copy_template );
	
		}
		
		$row['title'] = stripslashes( $row['title'] );
		$tpl->set( '{title}', str_replace("&amp;amp;", "&amp;",  htmlspecialchars( $row['title'], ENT_QUOTES, $config['charset'] ) ) );

		if ( preg_match( "#\\{title limit=['\"](.+?)['\"]\\}#i", $tpl->copy_template, $matches ) ) {
			$tpl->set( $matches[0], clear_content($row['title'], $matches[1]) );
		}
			
		if( $view_template == "rss" ) {
			
			$tpl->set( '{rsslink}', $full_link );
			$tpl->set( '{rssauthor}', $row['autor'] );
			$tpl->set( '{rssdate}', date( "r", $row['date'] ) );

			
			if($row['allow_rss_turbo']) {
				$tpl->set( '{allow-turbo}', "true" );
				$tpl->set( '[allow-turbo]', "" );
				$tpl->set( '[/allow-turbo]', "" );
			} else {
				$tpl->set( '{allow-turbo}', "false" );
				$tpl->set_block( "'\\[allow-turbo\\](.*?)\\[/allow-turbo\\]'si", "" );
			}
			
			if($row['allow_rss_dzen']) {
				$tpl->set( '[allow-dzen]', "" );
				$tpl->set( '[/allow-dzen]', "" );
			} else {
				$tpl->set_block( "'\\[allow-dzen\\](.*?)\\[/allow-dzen\\]'si", "" );
			}
			
			$row['full_story'] = stripslashes( $row['full_story'] );
			if( strlen($row['full_story']) < 13 ) $row['full_story'] = $row['short_story'];
			
			$row['short_story'] = clear_rss_content($row['short_story']);
			$row['short_story'] = str_ireplace( "{short-story}", "&#123;short-story}", $row['short_story'] );
			$row['short_story'] = str_ireplace( "{full-story}", "&#123;full-story}", $row['short_story'] );
			
			if( $config['rss_format'] != 1 ) {
				
				$tpl->copy_template = preg_replace( "#\<\!\[CDATA\[(.*?)\{short\-story\}(.*?)\]\]\>#i", "<![CDATA[\\1 ".preg_quote_replacement($row['short_story'])." \\2]]>", $tpl->copy_template );
				
				$row['short_story'] = str_replace( "><", "> <", $row['short_story'] );
				$row['short_story'] = htmlspecialchars( strip_tags( str_replace( array("<br>", "<br />"), " ", $row['short_story'] ) ), ENT_QUOTES, $config['charset'] );
				$row['short_story'] = preg_replace('/\s+/u', ' ', $row['short_story']);
				
				$tpl->set( '{short-story}',  trim($row['short_story']));

			} else {
				
				
				$tpl->set( '{short-story}', $row['short_story'] );
			}
			
			if( $config['rss_format'] == 2 ) {

				$images = array();
				preg_match_all('/(img|src)=("|\')[^"\'>]+/i', $row['full_story'], $media);
				$data=preg_replace('/(img|src)("|\'|="|=\')(.*)/i',"$3",$media[0]);
	
				foreach($data as $url) {
					$info = pathinfo($url);
					if (isset($info['extension'])) {
						if ($info['filename'] == "spoiler-plus" OR $info['filename'] == "spoiler-minus" OR strpos($info['dirname'], 'engine/data/emoticons') !== false) continue;
						$info['extension'] = strtolower($info['extension']);
						if (($info['extension'] == 'jpg') || ($info['extension'] == 'jpeg') || ($info['extension'] == 'gif') || ($info['extension'] == 'png') || ($info['extension'] == 'bmp') || ($info['extension'] == 'webp') || ($info['extension'] == 'avif')) { if($info['extension'] == 'jpg') $info['extension'] ='jpeg'; array_push($images, "<enclosure url=\"{$url}\" type=\"image/{$info['extension']}\" />"); }
					}
				}

				if ( count($images) ) {

					$tpl->set( '{images}', "\n".implode("\n", $images) );

				} else { $tpl->set( '{images}', '' ); }
			
			}
			
			$row['full_story'] = clear_rss_content( $row['full_story'] );
			$row['full_story'] = str_ireplace( "{short-story}", "&#123;short-story}", $row['full_story'] );
			$row['full_story'] = str_ireplace( "{full-story}", "&#123;full-story}", $row['full_story'] );
		
			$tpl->copy_template = preg_replace( "#\<\!\[CDATA\[(.*?)\{full\-story\}(.*?)\]\]\>#is", "<![CDATA[\\1 ".preg_quote_replacement($row['full_story'])." \\2]]>", $tpl->copy_template );
			
			$row['full_story'] = str_replace( "><", "> <", $row['full_story'] );
			$row['full_story'] = htmlspecialchars( strip_tags( str_replace( array("<br>", "<br />"), " ", $row['full_story'] ) ), ENT_QUOTES, $config['charset'] );
			$row['full_story'] = preg_replace('/\s+/u', ' ', $row['full_story']);
			
			$tpl->set( '{full-story}',  trim($row['full_story']) );

		
		} else {

			if ($config['allow_links'] AND function_exists('replace_links') AND isset($replace_links['news'])) $row['short_story'] = replace_links ( $row['short_story'], $replace_links['news'] );

			if ($smartphone_detected) {

				if (!$config['allow_smart_format']) {

						$row['short_story'] = strip_tags( $row['short_story'], '<p><br><a>' );

				} else {


					if ( !$config['allow_smart_images'] ) {
	
						$row['short_story'] = preg_replace( "#<!--TBegin(.+?)<!--TEnd-->#is", "", $row['short_story'] );
						$row['short_story'] = preg_replace( "#<!--MBegin(.+?)<!--MEnd-->#is", "", $row['short_story'] );
						$row['short_story'] = preg_replace( "#<img(.+?)>#is", "", $row['short_story'] );
	
					}
	
					if ( !$config['allow_smart_video'] ) {
	
						$row['short_story'] = preg_replace( "#<!--dle_video_begin(.+?)<!--dle_video_end-->#is", "", $row['short_story'] );
						$row['short_story'] = preg_replace( "#<!--dle_audio_begin(.+?)<!--dle_audio_end-->#is", "", $row['short_story'] );
						$row['short_story'] = preg_replace( "#<!--dle_media_begin(.+?)<!--dle_media_end-->#is", "", $row['short_story'] );
	
					}

				}

			}

			if ($config['image_lazy']) $row['short_story'] = preg_replace_callback ( "#<(img|iframe)(.+?)>#i", "enable_lazyload", $row['short_story'] );
			
			$tpl->set( '{short-story}', $row['short_story'] );
		
		}

		if ( preg_match( "#\\{short-story limit=['\"](.+?)['\"]\\}#i", $tpl->copy_template, $matches ) ) {
			$tpl->set( $matches[0], clear_content($row['short_story'], $matches[1]) );
		}

		if( $config['user_in_news'] ) {
			include (DLEPlugins::Check(ENGINE_DIR . '/modules/profile_innews.php'));
		}
		
		$tpl->compile( 'content', true, false );

		if(is_array($xfields_in_news) AND count($xfields_in_news) ) {
			
			if (stripos ( $tpl->result['content'], "[xf" ) !== false ) {
				
				foreach ( $xfields_in_news as $key => $value) {
					$tpl->result['content'] = str_replace ( $key, $value, $tpl->result['content'] );
				}
				
			}
			
			$xfields_in_news = array();
		}
	
	}
	
	if( !$news_found AND !$allow_userinfo AND $do != 'newposts' AND $do != 'favorites') {
		
		if( preg_match( "'\\[not-news\\](.*?)\\[/not-news\\]'si", $tpl->copy_template, $match ) ) {
			$need_404 = true;
			$tpl->result['content'] .= $match[1];
		}
	
	}

	if (stripos ( $tpl->result['content'], "[hide" ) !== false ) {
		
		$tpl->result['content'] = preg_replace_callback ( "#\[hide(.*?)\](.+?)\[/hide\]#is", 
			function ($matches) use ($member_id, $user_group, $lang) {
				
				$matches[1] = str_replace(array("=", " "), "", $matches[1]);
				$matches[2] = $matches[2];

				if( $matches[1] ) {
					
					$groups = explode( ',', $matches[1] );

					if( in_array( $member_id['user_group'], $groups ) OR $member_id['user_group'] == "1") {
						return $matches[2];
					} else return "<div class=\"quote dlehidden\">" . $lang['news_regus'] . "</div>";
					
				} else {
					
					if( $user_group[$member_id['user_group']]['allow_hide'] ) return $matches[2]; else return "<div class=\"quote dlehidden\">" . $lang['news_regus'] . "</div>";
					
				}

		}, $tpl->result['content'] );
	}

	$tpl->result['content'] = str_ireplace( "{PAGEBREAK}", '', $tpl->result['content'] );

	if ( $config['allow_banner'] AND count($banner_in_news) AND !$view_template ){

		foreach ( $banner_in_news as $name) {
			$tpl->result['content'] = str_replace( "{banner_" . $name . "}", $banners[$name], $tpl->result['content'] );

			if( $banners[$name] ) {
				$tpl->result['content'] = str_replace ( "[banner_" . $name . "]", "", $tpl->result['content'] );
				$tpl->result['content'] = str_replace ( "[/banner_" . $name . "]", "", $tpl->result['content'] );
			}
		}

		$tpl->result['content'] = preg_replace( "'\\[banner_(.*?)\\](.*?)\\[/banner_(.*?)\\]'si", '', $tpl->result['content'] );
	
	} elseif ( $view_template ) {

		$tpl->result['content'] = preg_replace( "'{banner_(.*?)}'si", '', $tpl->result['content'] );
		$tpl->result['content'] = preg_replace( "'\\[banner_(.*?)\\](.*?)\\[/banner_(.*?)\\]'si", '', $tpl->result['content'] );

	}
	
	$tpl->news_mode = false;
	$tpl->clear();
	$db->free( $sql_result );

	if( $news_found AND !$view_template ) {
		
		$count_all = get_count_from_cache( $sql_count );

		if( !$count_all ) {
		
			$count_all = $db->super_query( $sql_count.$where_date );
			
			if( !$count_all['count'] ) {
				$db->query("ANALYZE TABLE `" . PREFIX . "_post`, `" . PREFIX . "_post_extras`");
				$count_all = $db->super_query( $sql_count );
			}
			
			$count_all = $count_all['count'];
			
			if( $count_all ) {
				set_count_to_cache( $sql_count,  $count_all);
			}
		}
	
	} else $count_all = 0;
	
	
	if( $do == "" ) $do = $subaction;
	if( $do == "" and $year ) $do = "date";

	if( !$news_found AND $allow_userinfo AND $member_id['name'] == $user AND $user_group[$member_id['user_group']]['allow_adds'] ) {

		$tpl->load_template( 'info.tpl' );
		$tpl->set( '{error}', $lang['mod_list_f'] );
		$tpl->set( '{title}', $lang['all_info'] );
		$tpl->compile( 'content' );
		$tpl->clear();

	} elseif( !$news_found AND $do == 'newposts' AND $view_template != 'rss') {

		msgbox( $lang['all_info'], $lang['newpost_notfound'] );

	} elseif( !$news_found AND $do == 'favorites' ) {

		if (!$count_all) msgbox( $lang['all_info'], $lang['fav_notfound'] ); else msgbox( $lang['all_info'], $lang['fav_notfound_1'] );
		
	}
	

	if( !$view_template AND $count_all AND $config['news_navigation'] ) {
		
		$tpl->load_template( 'navigation.tpl' );
		
		//----------------------------------
		// Previous link
		//----------------------------------
		

		$no_prev = false;
		$no_next = false;
		
		if( isset( $cstart ) and $cstart != "" and $cstart > 0 ) {
			$prev = $cstart / $config['news_number'];
			
			if( $config['allow_alt_url'] ) {

				if ($prev == 1)
					$prev_page = $url_page . "/";
				else
					$prev_page = $url_page . "/page/" . $prev . "/";

				$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<a href=\"" . $prev_page . "\">\\1</a>" );

			} else {

				if ($prev == 1) {
					
					if ($user_query) $prev_page = $PHP_SELF . "?" . $user_query;
					else $prev_page = $config['http_home_url'];
					
				} else {
					
					if ($user_query) $prev_page = $PHP_SELF . "?cstart=" . $prev . "&amp;" . $user_query;
					else $prev_page = $PHP_SELF . "?cstart=" . $prev;
				}

				$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<a href=\"" . $prev_page . "\">\\1</a>" );
			}
		
		} else {
			$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<span>\\1</span>" );
			$no_prev = TRUE;
		}
		
		//----------------------------------
		// Pages
		//----------------------------------
		if( $config['news_number'] ) {

			$pages = "";
			
			if( $count_all > $config['news_number'] ) {
				
				$enpages_count = @ceil( $count_all / $config['news_number'] );
				
				$cstart = ($cstart / $config['news_number']) + 1;

				if( $enpages_count <= 10 ) {
					
					for($j = 1; $j <= $enpages_count; $j ++) {
						
						if( $j != $cstart ) {
							
							if( $config['allow_alt_url'] ) {

								if ($j == 1)
									$pages .= "<a href=\"" . $url_page . "/\">$j</a> ";
								else
									$pages .= "<a href=\"" . $url_page . "/page/" . $j . "/\">$j</a> ";

							} else {

								if ($j == 1) {
									
									if ($user_query) {
										$pages .= "<a href=\"{$PHP_SELF}?{$user_query}\">$j</a> ";
									} else $pages .= "<a href=\"{$config['http_home_url']}\">$j</a> ";
									
								} else {
									
									if ($user_query) {
										$pages .= "<a href=\"$PHP_SELF?cstart=$j&amp;$user_query\">$j</a> ";
									} else $pages .= "<a href=\"$PHP_SELF?cstart=$j\">$j</a> ";
									
								}

							}
						
						} else {
							
							$pages .= "<span>$j</span> ";
							
						}
					
					}
				
				} else {
					
					$start = 1;
					$end = 10;
					$nav_prefix = "<span class=\"nav_ext\">{$lang['nav_trennen']}</span> ";
					
					if( $cstart > 0 ) {
						
						if( $cstart > 6 ) {
							
							$start = $cstart - 4;
							$end = $start + 8;
							
							if( $end >= $enpages_count-1 ) {
								$start = $enpages_count - 9;
								$end = $enpages_count - 1;
							}
						
						}
					
					}
					
					if( $end >= $enpages_count-1 ) $nav_prefix = ""; else $nav_prefix = "<span class=\"nav_ext\">{$lang['nav_trennen']}</span> ";
					
					if( $start >= 2 ) {

						if( $start >= 3 ) $before_prefix = "<span class=\"nav_ext\">{$lang['nav_trennen']}</span> "; else $before_prefix = "";

						if( $config['allow_alt_url'] ) $pages .= "<a href=\"" . $url_page . "/\">1</a> ".$before_prefix;
						else {
							if($user_query) $pages .= "<a href=\"$PHP_SELF?{$user_query}\">1</a> ".$before_prefix;
							else $pages .= "<a href=\"{$config['http_home_url']}\">1</a> ".$before_prefix;
						}
					
					} 
					
					for($j = $start; $j <= $end; $j ++) {
						
						if( $j != $cstart ) {

							if( $config['allow_alt_url'] ) {

								if ($j == 1)
									$pages .= "<a href=\"" . $url_page . "/\">$j</a> ";
								else
									$pages .= "<a href=\"" . $url_page . "/page/" . $j . "/\">$j</a> ";

							} else {

								if ($j == 1) {
									
									if ($user_query) {
										$pages .= "<a href=\"{$PHP_SELF}?{$user_query}\">$j</a> ";
									} else $pages .= "<a href=\"{$config['http_home_url']}\">$j</a> ";
									
								} else {
									
									if ($user_query) {
										$pages .= "<a href=\"$PHP_SELF?cstart=$j&amp;$user_query\">$j</a> ";
									} else $pages .= "<a href=\"$PHP_SELF?cstart=$j\">$j</a> ";
									
								}

							}
						
						} else {
							
							$pages .= "<span>$j</span> ";
						}
					
					}
					
					if( $cstart != $enpages_count ) {
						
						if( $config['allow_alt_url'] ) {
							
							$pages .= $nav_prefix . "<a href=\"" . $url_page . "/page/{$enpages_count}/\">{$enpages_count}</a>";
							
						} else {
							
							if ($user_query) $pages .= $nav_prefix . "<a href=\"$PHP_SELF?cstart={$enpages_count}&amp;$user_query\">{$enpages_count}</a>";
							else $pages .= $nav_prefix . "<a href=\"$PHP_SELF?cstart={$enpages_count}\">{$enpages_count}</a>";
							
						}
					
					} else
						$pages .= "<span>{$enpages_count}</span> ";
				
				}
			
			}
			$tpl->set( '{pages}', $pages );
		}
		
		//----------------------------------
		// Next link
		//----------------------------------
		if( $config['news_number'] AND $config['news_number'] < $count_all AND $news_count < $count_all ) {
			$next_page = $news_count / $config['news_number'] + 1;
			
			if( $config['allow_alt_url'] ) {
				$next = $url_page . '/page/' . $next_page . '/';
				$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<a href=\"" . $next . "\">\\1</a>" );
			} else {
				
				if ($user_query) $next = $PHP_SELF . "?cstart=" . $next_page . "&amp;" . $user_query;
				else $next = $PHP_SELF . "?cstart=" . $next_page;
				
				$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<a href=\"" . $next . "\">\\1</a>" );
			}
		
		} else {
			$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<span>\\1</span>" );
			$no_next = TRUE;
		}
		
		if( !$no_prev OR !$no_next ) {
			
			$tpl->compile( 'navigation' );
			
			switch ( $config['news_navigation'] ) {

				case "2" :
					
					$tpl->result['content'] = '{newsnavigation}'.$tpl->result['content'];
					break;

				case "3" :
					
					$tpl->result['content'] = '{newsnavigation}'.$tpl->result['content'].'{newsnavigation}';
					break;

				default :
					$tpl->result['content'] .= '{newsnavigation}';
					break;
			
			}
			
		} else $tpl->result['navigation'] = "";
		
		$tpl->clear();
		
	} else $tpl->result['navigation'] = "";
	
}
?>