<?php
/*
Plugin Name: Warm cache
Plugin URI: http://www.mijnpress.nl
Description: Crawls your website-pages based on any XML sitemap plugin. If you have a caching plugin this wil keep your cache warm. Speeds up your site.
Version: 1.8
Author: Ramon Fincken
Author URI: http://www.mijnpress.nl
*/
if (!defined('ABSPATH')) 
{
	if(!isset($_GET['warm_cache']))
	{
		die("Aren't you supposed to come here via WP-Admin?");
	}
}


if(!class_exists('mijnpress_plugin_framework'))
{
	include('mijnpress_plugin_framework.php');
}

class warm_cache extends mijnpress_plugin_framework
{
	public $google_sitemap_generator_options;
	public $sitemap_url;
	public $keep_time;
	
	function warm_cache()
	{
		$this->keep_time = 60*60*24*7; // 7 days for now (TODO: admin setting)
	}

	function addPluginSubMenu()
	{
		parent::addPluginSubMenu('Warm cache',array('warm_cache', 'admin_menu'),__FILE__);
	}

	/**
	 * Additional links on the plugin page
	 */
	function addPluginContent($links, $file) {
		$links = parent::addPluginContent('warm_cache/warm-cache.php',$links,$file);
		return $links;
	}

	public function admin_menu()
	{

		load_plugin_textdomain('plugin_warm_cache','/wp-content/plugins/warm-cache/language/');		
		$warm_cache_admin = new warm_cache();
		$warm_cache_admin->plugin_title = 'Warm cache';
		if(!$warm_cache_admin->configuration_check())
		{
			$warm_cache_admin->content_start();

			$msg = '<strong>You need to install the Google XML sitemap plugin<br/>';
			$msg .= 'Download the zipfile from <a href="http://wordpress.org/extend/plugins/google-sitemap-generator/">http://wordpress.org/extend/plugins/google-sitemap-generator/</a><br/>';
			$msg .= 'Or use <a href="./plugin-install.php">plugin-install.php</a> to search for "google sitemap generator"</strong>';
			$warm_cache_admin->show_message($msg);

			$warm_cache_admin->content_end();
		}
		else
		{
			$warm_cache_admin->content_start();
			$stats = $warm_cache_admin->get_stats();

			if(!$stats['crawl'])
			{
				$msg = 'Ok, we have detected your sitemap url but it has not been visited by the plugin\'s crawler.<br/>';
				$warm_cache_api_url = trailingslashit(get_bloginfo('url')).'?warm_cache='.get_option('plugin_warm_cache_api');
				$msg .= 'The url you should call from a cronjob is: '.$warm_cache_api_url.'<br/>';
				$msg .= 'To re-set the key, visit this url: '.admin_url('plugins.php?page=warm-cache/warm-cache.php&resetkey=true').'<br/>';
				$msg .= 'If you are in need of an external cronjob service, you might like to use Easycron.com (affiliate link) <a href="http://www.easycron.com/?ref=12201">http://www.easycron.com/?ref=12201</a>';
				

				$warm_cache_admin->show_message($msg);
				echo '<br/><br/>';
			}
			else
			{
				$msg = 'Crawled in total '.$stats['stats_pages'].' pages in a total of '.$stats['stats_times']. ' seconds<br/>';
				if($stats['stats_pages'])
				{
					$msg .= 'Average page to load a page in seconds: '. $stats['stats_times']/$stats['stats_pages'];
				}
				$warm_cache_admin->show_message($msg);
			}
			echo '<table class="widefat">';
			echo '<tr><th class="manage-column" style="width: 150px;">Crawled at</th><th class="manage-column">Time needed</th><th class="manage-column" style="width: 120px;">Number of pages</th><th class="manage-column">Average load time per page</th><th class="manage-column">Pages</th></tr>';
			echo $stats['table_string'];
			echo '</table>';
			$warm_cache_admin->content_end();
		}
	}
	
	/**
	 * Add or update the API key
	 */
	private function change_apikey()
	{
			$special_chars = false;
			delete_option('plugin_warm_cache_api');
			add_option('plugin_warm_cache_api',wp_generate_password(9, $special_chars));		
	}

	/**
	* Gets table and stats
	*/
	private function get_stats()
	{
		$statdata = get_option('plugin_warm_cache_statdata');
		if(!isset($statdata) || !is_array($statdata))
		{
			add_option('plugin_warm_cache_statdata', array(), NULL, 'no');
			$this->change_apikey();
		}

		$table_string = '';
		if(!count($statdata))
		{
			$table_string .= '<tr><td valign="top" colspan="5">';
			$table_string .= __('Your site has not been crawled by the plugin','plugin_warm_cache');
			$table_string .= '</td></tr>';
			return array('crawl' => false, 'table_string' => $table_string);
		}

		$stats_pages = 0;
		$stats_times = 0;
		$site_url = site_url();
		foreach($statdata as $key => $value)
		{
			$temp = get_transient($value);
			$string_length = 0;
			if($temp !== false)
			{
				$table_string .= '<tr><td valign="top">';
				$table_string .= date('l jS F Y h:i:s A',$temp['time_start']).'</td><td valign="top" style="text-align: center;">';
				$table_string .= $temp['time'].'</td><td valign="top">';
				$table_string .= $temp['pages_count'].'</td><td valign="top">';
				$table_string .= (intval($temp['pages_count'])!=0) ? $temp['time']/$temp['pages_count'].'</td><td valign="top">' : '- </td><td valign="top">';
				
				if(intval($temp['pages_count']) > 0)
				{
					foreach($temp['pages'] as $p_key => $p_value)
					{
						$table_string .= '<a href="'.$p_value.'" title="'.$p_value.'">';
						$temp_string = str_replace($site_url,'',$p_value);
						if($temp_string == '/')	{ $temp_string = $site_url; } // Site url, show this instead of "/"			
						$table_string .= $temp_string;
						$table_string .= '</a>';
						$string_length += strlen($temp_string);
						if($string_length > 70) {$string_length =0; $table_string .= '<br/>';} // New line
						$table_string .= "\n";
					}
				}
				$table_string .= '</td></tr>';
				$table_string .= "\n\n";

				$stats_pages += $temp['pages_count'];
				$stats_times += $temp['time'];
			}
		}
		return array('crawl' => true, 'stats_pages' => $stats_pages, 'stats_times' => $stats_times, 'table_string' => $table_string);
	}

	/**
	 * Updates sitemap url override
	 * @param unknown_type $url
	 */
	private function update_sitemap_overide_url($url)
	{
		delete_option('plugin_warm_cache_sitemap_override');
		add_option('plugin_warm_cache_sitemap_override',htmlspecialchars($url));			
	}
	
	private function configuration_check()
	{
		$this->google_sitemap_generator_options = get_option("sm_options");
		$msg = '';
		if(isset($_GET['resetkey']))
		{
			$this->change_apikey();
			$msg .= __('API key has changed','plugin_warm_cache');
			$msg .= '<br/>';			
		}
		
		if(isset($_POST['update_sitemap']))
		{
			$this->update_sitemap_overide_url($_POST['update_sitemap']);
		}
		
		$msg .= '<form method="post" action="'.admin_url('plugins.php?page=warm-cache/warm-cache.php'). '">Experienced users only, enter your full sitemap url if we cannot detect it automatically (do not forget the http:// up front): ';
		$msg .= '<br/><input type="text" value="'.get_option('plugin_warm_cache_sitemap_override').'" name="update_sitemap" size="60" /><input type="submit" value="Use this sitemap" /></form>';
		
		if(!($this->google_sitemap_generator_options && is_array($this->google_sitemap_generator_options)) || !warm_cache::get_sitemap_url()) {
			$msg .= __('Could not find sitemap options, did you install and configure a sitemap plugin ?','plugin_warm_cache');
			$returnvar = false;
		}
		else
		{
			$msg .= 'Sitemap url: <a href="'.warm_cache::get_sitemap_url().'">'.$this->sitemap_url.'</a><br/>';
			$warm_cache_api_url = trailingslashit(get_bloginfo('url')).'?warm_cache='.get_option('plugin_warm_cache_api');
			$msg .= 'The url you should call from a cronjob is: '.$warm_cache_api_url.'<br/>';
			$msg .= 'To re-set the key, visit this url: '.admin_url('plugins.php?page=warm-cache/warm-cache.php&resetkey=true').'<br/>';
			$msg .= 'If you are in need of an external cronjob service, you might like to use Easycron.com (affiliate link) <a href="http://www.easycron.com/?ref=12201">http://www.easycron.com/?ref=12201</a>';
			
			$returnvar = true;
		}
		$this->show_message($msg);

		return $returnvar;
	}

	public function get_sitemap_url()
	{
		if($this->google_sitemap_generator_options["sm_b_location_mode"]=="manual") {
			$sitemap_url = $this->google_sitemap_generator_options["sm_b_fileurl_manual"];
		} else {
			$sitemap_url =  trailingslashit(get_bloginfo('url')). $this->google_sitemap_generator_options["sm_b_filename"];
		}
		
		$override = get_option('plugin_warm_cache_sitemap_override');
		if($override && !empty($override) && $override != 'http://')
		{
			$sitemap_url = $override;
		}
		
		$this->sitemap_url = $sitemap_url;
		return $this->sitemap_url;
	}
}
	
if(isset($_GET['warm_cache']) && !empty($_GET['warm_cache']) && $_GET['warm_cache'] == get_option('plugin_warm_cache_api'))
{
	define('CALLED',true);
	include('warm_cache_crawl.php');
}
else
{
	if(is_admin())
	{
		add_action('admin_menu',  array('warm_cache', 'addPluginSubMenu'));
		add_filter('plugin_row_meta',array('warm_cache', 'addPluginContent'), 10, 2);
	}
}
?>
