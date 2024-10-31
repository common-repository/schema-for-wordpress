<?php
if(!isset($_GET["sfwp"])) (die("Please select some values"));
require_once('sfwp_path.php');
require_once(ABSPATH.'wp-load.php');
global $wpdb;

$ids = explode(";", isset($_GET["sfwp"]) ? $_GET["sfwp"] : '');
$final_text = array();

if (is_array($ids)) {
  foreach($ids as $id) {
    if(intval($id)>0) {
      $entry = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."sfwp WHERE id = %d", intval($id)));
      $final_text[] = base64_encode($entry->name)."<;>".base64_encode($entry->description)."<;>".base64_encode($entry->value)."<;>".base64_encode($entry->type);
    }
  }
}

$final = implode("\r\n",$final_text);
header("Content-Type: text/plain");
header("Content-disposition: attachment; filename=export_sfwp_".date("d_m_y_H_i").".sfwp;");
header("Content-Length: ".strlen($final));
header('Content-Transfer-Encoding: Binary');
header('Accept-Ranges: bytes');
header('ETag: "'.md5($final).'"');
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
print $final;
die();
?>
