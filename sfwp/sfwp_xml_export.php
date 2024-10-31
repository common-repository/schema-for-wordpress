<?php
if(!isset($_GET["sfwp"])) (die("Please select some values"));
require_once('sfwp_path.php');
require_once(ABSPATH . '/wp-load.php');

global $wpdb;
$ids = explode(";", isset($_GET["sfwp"]) ? $_GET["sfwp"] : '');
$final_text = array();
$xml_array = array();
$xml = array();

if (is_array($ids)) {
  foreach($ids as $id) {
    if(intval($id) > 0) {
      $entry = $wpdb->get_row($wpdb->prepare("SELECT name, description, value, type FROM ".$wpdb->prefix."sfwp WHERE id = %d", $id), ARRAY_A);
      $xml_array[] = $entry;
    }
  }
}

$xml[] = "﻿<?xml version=\"1.0\" encoding=\"UTF-8\"?>";

foreach($xml_array as $x) {
  $xml[] = "<sfwpentry>";
  $xml[] = "<name>".$x["name"]."</name>";
  $xml[] = "<description>".$x["description"]."</description>";
  $xml[] = "<type>".$x["type"]."</type>";
  $xml[] = "<value><![CDATA[".htmlspecialchars_decode(stripslashes($x["value"]))."]]></value>";
  $xml[] = "</sfwpentry>";
}

$f = implode("\n", $xml);
if (!headers_sent()) {
  header("Content-Type: text/xml");
  header('Accept-Ranges: bytes');
}
echo $f;
die();
?>