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
 File: google.class.php
-----------------------------------------------------
 Use: Google Sitemap
=====================================================
*/

include_once ENGINE_DIR . '/classes/composer/vendor/autoload.php';

use Melbahja\Seo\Sitemap;
use Melbahja\Seo\Factory;

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

class googlemap {
	
	public $allow_url = "";
	public $home = "";
	public $limit = 0;
	
	public $news_priority = "";
	public $stat_priority = "";
	public $cat_priority = "";
	
	public $news_changefreq = "";
	public $stat_changefreq = "";
	public $cat_changefreq = "";
	
	public $priority = "0.6";
	public $changefreq = "daily";
	
	public $news_per_file = 40000;
	
	public  $sitemap = null;
	private $db_result = null;
	
	private $googlenews = array();

	
	function __construct($config) {
		
		if (strpos($config['http_home_url'], "//") === 0) $config['http_home_url'] = "https:".$config['http_home_url'];
		elseif (strpos($config['http_home_url'], "/") === 0) $config['http_home_url'] = "https://".$_SERVER['HTTP_HOST'].$config['http_home_url'];

		$this->allow_url = $config['allow_alt_url'];
		$this->home = $config['http_home_url'];
		$this->limit = $config['sitemap_limit'];
		$this->news_per_file = $config['sitemap_news_per_file'];
		
		$this->news_priority = $config['sitemap_news_priority'];
		$this->stat_priority = $config['sitemap_stat_priority'];
		$this->cat_priority = $config['sitemap_cat_priority'];
		
		$this->news_changefreq = $config['sitemap_news_changefreq'];
		$this->stat_changefreq = $config['sitemap_stat_changefreq'];
		$this->cat_changefreq = $config['sitemap_cat_changefreq'];
		
		$this->sitemap = new Sitemap($this->home);
		$this->sitemap->setSavePath(ROOT_DIR. '/uploads');
		$this->sitemap->setSitemapsUrl($this->home.'uploads');
		$this->sitemap->setIndexName('sitemap.xml');

	}
	
	function generate() {
		
		$this->generate_static();
		$this->generate_categories();
		$this->generate_tags();
		$this->generate_news();	
		$this->sitemap->save();
		
		if( count($this->googlenews) ) {
			
			$this->sitemap = new Sitemap($this->home);
			$this->sitemap->setSavePath(ROOT_DIR. '/uploads');
			$this->sitemap->setSitemapsUrl($this->home.'uploads');
			$this->sitemap->setIndexName('index.xml');
			
			$this->sitemap->news('google_news.xml', function($map) {
				global $config, $lang;
			
				foreach( $this->googlenews as $news) {
					
					$map->setPublication($config['home_title'], $lang['wysiwyg_language']);
				
					$map->loc($news['loc'])->news(
					[
					   'title' => $news['title'],
					   'publication_date' => date('c', $news['last']),
					]);
				}
				
			});
			
			$this->sitemap->save();
			unlink(ROOT_DIR. '/uploads/index.xml');

		}
		
	}
	
	function generate_news() {
		
		global $db, $config, $user_group;

		$allow_list = explode ( ',', $user_group[5]['allow_cats'] );
		$not_allow_cats = explode ( ',', $user_group[5]['not_allow_cats'] );
		$stop_list = "";
		$cat_join = "";
	
		if ($allow_list[0] != "all") {
			
			if ($config['allow_multi_category']) {
				
				$cat_join = "INNER JOIN (SELECT DISTINCT(" . PREFIX . "_post_extras_cats.news_id) FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN (" . implode ( ',', $allow_list ) . ")) c ON (p.id=c.news_id) ";
			
			} else {
				
				$stop_list = "category IN ('" . implode ( "','", $allow_list ) . "') AND ";
			
			}
			
		}
	
		if( $not_allow_cats[0] != "" ) {
			
			if ($config['allow_multi_category']) {
				
				$stop_list = "p.id NOT IN ( SELECT DISTINCT(" . PREFIX . "_post_extras_cats.news_id) FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN (" . implode ( ',', $not_allow_cats ) . ") ) AND ";
	
				
			} else {
				
				$stop_list = "category NOT IN ('" . implode ( "','", $not_allow_cats ) . "') AND ";
			
			}
			
		}
		
		$thisdate = date( "Y-m-d H:i:s", time() );
		if( $config['no_date'] AND !$config['news_future'] ) $where_date = " AND date < '" . $thisdate . "'";
		else $where_date = "";
	
		$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post p {$cat_join}WHERE {$stop_list}approve=1{$where_date}" );
	
		if ( !$this->limit ) $this->limit = $row['count'];
		
		if ( $this->limit > $this->news_per_file ) {
	
			$pages_count = @ceil( $row['count'] / $this->news_per_file );
			
			$n = 0;
	
			for ($i =0; $i < $pages_count; $i++) {
	
				$n = $n+1;
	
				$this->get_news($n);
	
			}
	
	
		} else {
	
			$this->get_news();
		
		}
	
	}
	
	function generate_categories() {
		
		$this->priority = $this->cat_priority;
		$this->changefreq = $this->cat_changefreq;

		$this->sitemap->links('category_pages.xml', function($map) {
			
			global $db, $user_group;
			
			$cat_info = get_vars( "category" );
			
			if( !is_array( $cat_info ) ) {
				$cat_info = array ();
				
				$db->query( "SELECT * FROM " . PREFIX . "_category ORDER BY posi ASC" );
				
				while ( $row = $db->get_row() ) {
					
					if( !$row['active'] ) continue;
					
					$cat_info[$row['id']] = array ();
					
					foreach ( $row as $key => $value ) {
						$cat_info[$row['id']][$key] = $value;
					}
				
				}
				
				set_vars( "category", $cat_info );
				$db->free();
			}
		
			$allow_list = explode ( ',', $user_group[5]['allow_cats'] );
			$not_allow_cats = explode ( ',', $user_group[5]['not_allow_cats'] );
		
			foreach ( $cat_info as $cats ) {
				
				if ($allow_list[0] != "all") {
					if (!$user_group[5]['allow_short'] AND !in_array( $cats['id'], $allow_list )) continue;
				}
				
				if ($not_allow_cats[0] != "") {
					if (!$user_group[5]['allow_short'] AND in_array( $cats['id'], $not_allow_cats )) continue;
				}
				
				if( $this->allow_url ) $loc = $this->get_url( $cats['id'], $cat_info ) . "/";
				else $loc = "index.php?do=cat&category=" . $cats['alt_name'];
				
				$map->loc($loc)->freq($this->changefreq)->lastMod(date('c'))->priority( $this->priority );
				
			}
			
		});
		
	}
	
	function generate_static() {
		
		global $db;
		
		$this->priority = $this->stat_priority;
		$this->changefreq = $this->stat_changefreq;
		
		$this->db_result = $db->query( "SELECT name, sitemap, disable_index, password FROM " . PREFIX . "_static" );

		$this->sitemap->links('static_pages.xml', function($map) {
			
			global $db;
			
			while ( $row = $db->get_row( $this->db_result ) ) {
				
				if( $row['name'] == "dle-rules-page" ) continue;
				if( !$row['sitemap'] OR $row['disable_index'] OR $row['password']) continue;
				
				if( $this->allow_url ) $loc = $row['name'] . ".html";
				else $loc = "index.php?do=static&page=" . $row['name'];
				
				$map->loc($loc)->freq($this->changefreq)->lastMod(date('c'))->priority( $this->priority );
				
			}
			
		});
		
	}
	
	function generate_tags() {
		
		global $db;
		
		$this->priority = $this->cat_priority;
		$this->changefreq = $this->cat_changefreq;
		
		$this->db_result = $db->query( "SELECT tag FROM " . PREFIX . "_tags GROUP BY tag LIMIT 0, 40000" );
		
		$this->sitemap->links('tags_pages.xml', function($map) {
			
			global $db;
			
			while ( $row = $db->get_row( $this->db_result ) ) {
				
				$row['tag'] = str_replace(array("&#039;", "&quot;", "&amp;"), array("'", '"', "&"), $row['tag']);
				
				if( $this->allow_url ) $loc = "tags/" . rawurlencode( dle_strtolower($row['tag']) ) . "/";
				else $loc = "index.php?do=tags&tag=" .  rawurlencode( dle_strtolower($row['tag']) );	
				
				$map->loc($loc)->freq($this->changefreq)->lastMod(date('c'))->priority( $this->priority );
				
			}
			
		});
		
	}
	
	function get_news( $page = false ) {
		
		global $db, $config, $user_group;
		
		$this->priority = $this->news_priority;
		$this->changefreq = $this->news_changefreq;
		$prefix_page = '';
		
		if ( $page ) {
			
			if( $page != 1 ) $prefix_page = $page;

			$page = $page - 1;
			$page = $page * $this->news_per_file;
			$this->limit = " LIMIT {$page}, {$this->news_per_file}";

		} else {

			if( $this->limit < 1 ) $this->limit = false;
			
			if( $this->limit ) {
				
				$this->limit = " LIMIT 0," . $this->limit;
			
			} else {
				
				$this->limit = "";
			
			}
		}
		
		$thisdate = date( "Y-m-d H:i:s", time() );
		if( $config['no_date'] AND !$config['news_future'] ) $where_date = " AND date < '" . $thisdate . "'";
		else $where_date = "";

		$allow_list = explode ( ',', $user_group[5]['allow_cats'] );
		$not_allow_cats = explode ( ',', $user_group[5]['not_allow_cats'] );
		$stop_list = "";
		$cat_join = "";

		if ($allow_list[0] != "all") {
			
			if ($config['allow_multi_category']) {
				
				$cat_join = " INNER JOIN (SELECT DISTINCT(" . PREFIX . "_post_extras_cats.news_id) FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN (" . implode ( ',', $allow_list ) . ")) c ON (p.id=c.news_id) ";
			
			} else {
				
				$stop_list = "category IN ('" . implode ( "','", $allow_list ) . "') AND ";
			
			}
		
		}

		if( $not_allow_cats[0] != "" ) {
			
			if ($config['allow_multi_category']) {
				
				$stop_list = "p.id NOT IN ( SELECT DISTINCT(" . PREFIX . "_post_extras_cats.news_id) FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN (" . implode ( ',', $not_allow_cats ) . ") ) AND ";
			
			} else {
				
				$stop_list = "category NOT IN ('" . implode ( "','", $not_allow_cats ) . "') AND ";
			
			}
			
		}
		
		$this->db_result = $db->query( "SELECT p.id, p.title, p.date, p.alt_name, p.category, e.access, e.editdate, e.disable_index, e.need_pass FROM " . PREFIX . "_post p {$cat_join}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$stop_list}approve=1" . $where_date . " ORDER BY date DESC" . $this->limit );
		
		$this->sitemap->links("news_pages{$prefix_page}.xml", function($map) {
			
			global $db, $config, $user_group;
			
			$two_days = time() - (2 * 3600 * 24);
			
			while ( $row = $db->get_row( $this->db_result ) ) {
				
				$row['date'] = strtotime($row['date']);
				
				$row['category'] = intval( $row['category'] );
	
				if ( $row['disable_index'] ) continue;
				
				if ( $row['need_pass'] ) continue;
				
				if (strpos( $row['access'], '5:3' ) !== false) continue;
	
				if( $this->allow_url ) {
					
					if( $config['seo_type'] == 1 OR  $config['seo_type'] == 2 ) {
						
						if( $row['category'] and $config['seo_type'] == 2 ) {
							
							$cats_url = get_url( $row['category'] );
							
							if($cats_url) {
								
								$loc = $cats_url . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
								
							} else $loc = $row['id'] . "-" . $row['alt_name'] . ".html";
						
						} else {
							
							$loc = $row['id'] . "-" . $row['alt_name'] . ".html";
						
						}
					
					} else {
						
						$loc = date( 'Y/m/d/', $row['date'] ) . $row['alt_name'] . ".html";
					}
				
				} else {
					
					$loc = "index.php?newsid=" . $row['id'];
				
				}
	
				if ( $row['editdate'] AND $row['editdate'] > $row['date'] ){
				
					$row['date'] =  $row['editdate'];
				
				}
				
				if( $row['date'] > $two_days ) {
					$this->googlenews[] = array('title' => stripslashes($row['title']), 'loc' => $loc, 'last' => $row['date']);
				}
			
				$map->loc($loc)->freq($this->changefreq)->lastMod( date('c', $row['date'] ) )->priority( $this->priority );
				
			}
			
		});
		

	}
	
	function get_url($id, $cat_info) {
		
		if( ! $id ) return;
		
		$parent_id = $cat_info[$id]['parentid'];
		
		$url = $cat_info[$id]['alt_name'];
		
		while ( $parent_id ) {
			
			$url = $cat_info[$parent_id]['alt_name'] . "/" . $url;
			
			$parent_id = $cat_info[$parent_id]['parentid'];
			
			if( isset($cat_info[$parent_id]['parentid']) AND $cat_info[$parent_id]['parentid'] == $cat_info[$parent_id]['id'] ) break;
		
		}
		
		return $url;
	}

}

?>