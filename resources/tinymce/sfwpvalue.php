<?php
if(!isset($_POST["id"]) || intval($_POST["id"])<=0) {
  die();
}
$sfwp_id= intval($_POST["id"]);

require_once('../../sfwp/sfwp_path.php');
require_once(ABSPATH.'wp-load.php');

global $wpdb;
$val = $wpdb->get_row($wpdb->prepare("SELECT value FROM ".$wpdb->prefix."sfwp WHERE id= %d", $sfwp_id));
if($val!==null) {
    echo stripslashes(htmlspecialchars_decode($val->value));
}
die();
?>
