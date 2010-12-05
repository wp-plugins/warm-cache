<?php
// First version

/**
* Base class for plugin framework usage, backend-/gui-based.
* @author 	Ramon Fincken, http://www.mijnpress.nl
*/
class mijnpress_plugin_framework
{
	/**
	 * Left menu display in Plugin menu
	 */
	function addPluginSubMenu($title,$function, $file, $capability = 10) {	
		add_submenu_page("plugins.php", $title, $title, $capability, $file, $function);
	}

	/**
	* Extra info at plugin page
	*/
	function addPluginContent($filename,$links, $file, $config_url = NULL)
	{
		if($file == $filename)	
		{
			if($config_url) $links[] = '<a href="'.$config_url.'">' . __('Settings') . '</a>';
			$links[] = '<a href="http://donate.ramonfincken.com">' . __('Donate') . '</a>';
			$links[] = '<a href="http://www.mijnpress.nl">' . __('Custom WordPress coding nodig?') . '</a>';
		}
		return $links;
	}

	function show_message($msg)
	{
		echo '<div id="message" class="updated fade">';
		echo $msg;
		echo '</div>';
	}

	function content_start()
	{
		echo '<div style="width:75%; float: left;">';
	}

	function content_end()
	{
		echo '</div>';
		echo '<div style="width:20%; float: right; margin-right: 10px;">';
		$this->showcredits();
		echo '</div>';
		echo '<div style="clear: both;"></div>';
	}

	function showcredits()
	{
		$this->all_plugins = array('Admin renamer extended','Find replace','Simple add pages or posts','Force apply terms and conditions','GTmetrix website performance','Antispam for all fields','Mass Delete Tags','Auto Prune Posts','Warm cache');	
		mijnpress_plugin_framework_showcredits($this->plugin_title,$this->all_plugins);
	}

	/**
	 * Generating the url for current Plugin
	 *
	 * @param String $path
	 * @return String
	 */
	function get_plugin_url($path = '') {
	   global $wp_version;

	   if (version_compare($wp_version, '2.8', '<')) { // Using WordPress 2.7
	      $folder = dirname(plugin_basename(__FILE__));
	      if ('.' != $folder)
		 $path = path_join(ltrim($folder, '/'), $path);
	      return plugins_url($path);
	   }
	   return plugins_url($path, __FILE__);
	}
}

// Keep below class because we use plain HTML in PHP
function mijnpress_plugin_framework_showcredits($plugin_title,$all_plugins)
{
?>
	<div class="postbox">
		<h3 class="hndle"><span>About <?php echo $plugin_title; ?></span></h3>
		<div class="inside">
			This plugin was created by Ramon Fincken.<br>
He likes to create WordPress websites and plugins (currently only Dutch customers) and he is co-admin at the <a href="http://www.linkedin.com/groups?about=&gid=1644947&trk=anet_ug_grppro">Dutch LinkedIn WordPress group</a>.<br/><br/>Visit his WordPress website at: <a href="http://www.mijnpress.nl">MijnPress.nl</a><br/>
If you are a coder, you might like to visit <a href="http://www.ramonfincken.com/tag/wordpress.html">his WordPress blogposts</a>.
<br/><br/><a href="http://donate.ramonfincken.com/">PayPal Donations</a> (even as small as $1,- or &euro;1,- are welcome!.
			
		</div>
	</div>


	<div class="postbox">
		<h3 class="hndle"><span>More Plugins</span></h3>
		<div class="inside">
			If you like this plugin, you may also like:<br/>
<ul>

<?php
sort($all_plugins);
foreach($all_plugins as $plugin)
{
	if($plugin != $plugin_title)
	{
		$url = 'http://wordpress.org/extend/plugins/'.str_replace(' ','-',$plugin);
		echo '<li><a href="'.strtolower($url).'/">'.$plugin.'</a></li>';
	}
}
?>
</ul>
		</div>
	</div>
<?php
} // end mijnpress_plugin_framework_showcredits($plugin_title,$all_plugins)
?>