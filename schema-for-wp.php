<?php
/*
Plugin Name: Schema for WordPress
Plugin URI: http://schemaforwordpress.com
Description: SFWP stores snippets of Schema code for inserting into a WordPress post or page.  Schema markup is used by the major search engines to correctly identify the content of a web site and as such is a vital component of search engine optimization (SEO).  Please visit the plugin site to download the latest Schema. This plugin is based on Global Content Blocks by Ben Magrill and I am very grateful to Maxim Voldachinsky for his input on fixing some security flaws.
Version: 1.5.1
Author: Ian Walters
Author URI: http://www.businesscornerstoneservices.com/
Text Domain: schema-for-wordpress 

Copyright 2011  Ian Walters

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


define('sfwp_VERSION','1.5.1');
$current_version = get_option("sfwp_db_version");



sfwp_check_update();

require_once 'sfwp/sfwp.class.php';

/*
 * Installs the plugin!
 */
function sfwp_install() {
    global $wpdb;
   $table_name = $wpdb->prefix . "sfwp";
     if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
         $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  name varchar(36) NOT NULL,
	  description text NOT NULL,
	  value text NOT NULL,
          type varchar(100) NOT NULL DEFAULT 'other',
	  UNIQUE KEY id (id)
	);";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
     add_option("sfwp_db_version", sfwp_VERSION);
     }
}

function sfwp_uninstall() {
    if(get_option("sfwp_complete_uninstall","no")=="yes") {
    global $wpdb;
      $table_name = $wpdb->prefix . "sfwp";
     if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
         $sql = "DROP TABLE ".$table_name;
         $wpdb->query($sql);
         delete_option("sfwp_db_version");
         delete_option("sfwp_complete_uninstall");
     }
    }
}

function sfwp_check_update() {
    $current_version = get_option("sfwp_db_version");
    if(version_compare($current_version, sfwp_VERSION)<0) {
        //we need to perform an update on the database        
        
        switch($current_version) {
            case "1.41":
            sfwp_add_db_column("type", "VARCHAR(100) NOT NULL DEFAULT 'other'");              
            break;
        }
         //update the option
         update_option("sfwp_db_version",sfwp_VERSION);
    }
}

function sfwp_add_db_column($name,$options) {
    global $wpdb;
    $table_name = $wpdb->prefix . "sfwp";
    //check if column exists
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = %s AND COLUMN_NAME = %s", $table_name, $table_name));

    if($row===null) {       
       $wpdb->query($wpdb->prepare("ALTER TABLE %s ADD %s %s", $table_name, $name, $options));
    }
}

function sfwp_add_submenu() {
    $sfwp_page = add_options_page( "Schema for WordPress", "Schema for WordPress", "publish_pages", "schema-for-wp", "sfwp_submenu");
    add_action( "admin_print_scripts-$sfwp_page", 'sfwp_loadjs_admin_head',5 );
}

function sfwp_loadjs_admin_head() {
    wp_enqueue_script('sfwp_uni_script', get_option('siteurl').'/wp-content/plugins/schema-for-wp/resources/extra/extra.js');
}

function sfwp_submenu() {
       global $wpdb;
        $msg = "";
    if(isset($_POST["sfwp_delete"])) {
        if(isset($_POST["sfwp_del"]) && is_array($_POST["sfwp_del"])) {
            foreach($_POST["sfwp_del"] as $bd) {
                 $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."sfwp WHERE id = %d limit 1", intval($bd)));
            }
            $msg = "Deleted!";
        }
    }

    if(isset($_POST["sfwp_unin"])) {
        if(isset($_POST["ch_unin"])) {
           update_option("sfwp_complete_uninstall","yes");
        }
        else
        {
            update_option("sfwp_complete_uninstall","no");
        }
    }

    if(isset($_POST["sfwp_import"])) {
        //importing files
       $msg = sfwp_import();
    }

    if(isset($_POST["sfwp_save"])) {
        $name = $_POST["sfwp_name"];
        $description = mysql_real_escape_string(htmlspecialchars($_POST['sfwp_description']));
        $type = mysql_real_escape_string(htmlspecialchars($_POST['sfwp_type']));
        $value = mysql_real_escape_string(htmlspecialchars($_POST['sfwp_value']));
       
        

        if(strlen($name) && strlen($value)) {
        if(isset($_POST["update_it"])) {
           $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."sfwp set name = %s, description = %s, value = %s, type = %s WHERE id = %d"), $name, $description, $value, $type, intval($_POST["update_it"]));
           $msg = "Entry updated!";
        }
        else
        {
            $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix . "sfwp (name, description, value, type) VALUES (%s, %s, %s, %s)", $name, $description, $value, $type));
            $msg = "Entry inserted!";
        }
        }
        else
        {
             $msg = "Name and Content are mandatory!";
        }
    }
    
    echo sfwp::main_page($msg);
}


function sfwp_import() {
    global $wpdb;
    $text = file_get_contents($_FILES["sfwp_import_file"]["tmp_name"]);
    echo "text:\n".$text;
    $xml = simplexml_load_string($text);
    print_r($xml);
    die();
    $entries1 = explode("\r\n",$text);
    $entries = array();
    foreach($entries1 as $e1) {
        $row = explode("<;>",$e1);
        $entries[] = array(
            "name"=>  mysql_real_escape_string(base64_decode($row[0])),
            "description"=>  mysql_real_escape_string(base64_decode($row[1])),
            "value"=> mysql_real_escape_string(base64_decode($row[2])),
            "type"=>  mysql_real_escape_string(base64_decode($row[3])),
        );
    }

    foreach($entries as $e) {
        $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix . "sfwp (name, description, value, type) VALUES (%s, %s, %s, %s)", $e["name"], $e["description"], $e["value"], $e["type"]));
    }
    return "Imported ".count($entries)." blocks";
}

function sfwp_shortcode_replacer($atts, $content=null, $code="") {
    $a = shortcode_atts( array('id' => 0), $atts );
    if($a["id"]==0) return "";
     global $wpdb;
     //does this one exist ?
     $ex = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) from ".$wpdb->prefix."sfwp WHERE id = %d", $a["id"]));
     if($ex==1) {
    $record = $wpdb->get_row($wpdb->prepare("SELECT value, type FROM ".$wpdb->prefix."sfwp WHERE id = %d", $a["id"]));

    if($record->type!="php") {
     return htmlspecialchars_decode(stripslashes($record->value));
    }
    else
    {
        //execute the php code        
        ob_start();
        $result = eval(" ".htmlspecialchars_decode(stripslashes($record->value)));
        $output = ob_get_contents();
	ob_end_clean();
        return $output . $result;
    }
     }
     else
     {   return "";    }
}


if (!function_exists("sfwp_settingslink")) {
	function sfwp_settingslink( $links, $file ){
		static $this_plugin;
		if ( ! $this_plugin ) {
			$this_plugin = plugin_basename(__FILE__);
		}	
		if ( $file == $this_plugin ){
			$settings_link = '<a href="options-general.php?page=schema-for-wp">' . __('Settings') . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}
}


/**
 * Hooks
 */
register_activation_hook(__FILE__,'sfwp_install');
register_deactivation_hook(__FILE__,'sfwp_uninstall');
add_action('admin_menu', 'sfwp_add_submenu',5);
add_shortcode('sfwp', 'sfwp_shortcode_replacer');



        // Load the custom TinyMCE plugin
function sfwp_mce_external_plugins( $plugins ) {
		$plugin_array['sfwpplugin'] =get_option('siteurl')."/wp-content/plugins/schema-for-wordpress/resources/tinymce/editor_plugin.js";
                return $plugin_array;
	}

function sfwp_mce_buttons( $buttons ) {
                array_push( $buttons,"|","sfwp");
		return $buttons;
}

function sfwp_addbuttons() {
// Don't bother doing this stuff if the current user lacks permissions
if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
            return;

// Register editor button hooks
if ( get_user_option('rich_editing') == 'true') {     
add_filter( 'mce_external_plugins','sfwp_mce_external_plugins',3);
add_filter( 'mce_buttons', 'sfwp_mce_buttons',3);
     }
}

function sfwp_my_refresh_mce($ver) {
  $ver += 3;
  return $ver;
}
// init process for button control
add_action('init', 'sfwp_addbuttons',3);
add_filter( 'tiny_mce_version', 'sfwp_my_refresh_mce',3);
add_filter( 'plugin_action_links', 'sfwp_settingslink', 10, 2 );
