<?php
require_once('../../sfwp/sfwp_path.php');
require_once(ABSPATH.'wp-load.php');
global $wpdb;

if(!isset($_POST["name"]) || !isset($_POST["content"])) {die("invalid call!");}

$name = $_POST["name"];
$description = mysql_real_escape_string(htmlspecialchars($_POST['description']));
$type = mysql_real_escape_string(htmlspecialchars($_POST['type']));
$value = mysql_real_escape_string(htmlspecialchars($_POST['content']));

if(!strlen($name) || !strlen($value)) {die("invalid call!");}

$available_types = array(
  "itemtype"=>array("vname"=>"ItemType","img"=>"itemtype.png","help"=>"Schema for WP"),
  "itemprop"=>array("vname"=>"ItemProp","img"=>"itemprop.png","help"=>"Schema for WP"),
);

$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix . "sfwp (name,description,value,type) VALUES (%s, %s, %s, %s)", $name, $description, $value, $type));

$inserted_id = mysql_insert_id();

$return = array(
"id"=>$inserted_id,
"name"=>$name,
"img"=>$available_types[$type]["img"]
);
echo json_encode($return);die();
?>