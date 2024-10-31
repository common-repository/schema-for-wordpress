<?php
function sfwp_getRoot($dir) {
  if (file_exists($dir."/wp-config.php")) {
    define('ABSPATH', $dir.'/');
    return;
  } else {
    sfwp_getRoot(dirname($dir));
  }
}
if ( !defined('ABSPATH') ) {
    sfwp_getRoot(dirname(__FILE__));
}

?>