<?php
/*
  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/

// Define the webserver and path parameters
// * DIR_FS_* = Filesystem directories (local/physical)
// * DIR_WS_* = Webserver directories (virtual/URL)
  define('HTTP_SERVER', 'http://nahahealthclinic.dyndns.org');
  define('HTTPS_SERVER', 'http://nahahealthclinic.dyndns.org');
  define('ENABLE_SSL', false);
  define('HTTP_COOKIE_DOMAIN', 'nahahealthclinic.dyndns.org');
  define('HTTPS_COOKIE_DOMAIN', 'nahahealthclinic.dyndns.org');
  define('HTTP_COOKIE_PATH', '/oscmax/catalog/');
  define('HTTPS_COOKIE_PATH', '/oscmax/catalog/');
  define('DIR_WS_HTTP_CATALOG', '/oscmax/catalog/');
  define('DIR_WS_HTTPS_CATALOG', '/oscmax/catalog/');
  define('DIR_WS_IMAGES', 'images/');
  define('DIR_WS_ICONS', DIR_WS_IMAGES . 'icons/');
  define('DIR_WS_INCLUDES', 'includes/');
  define('DIR_WS_BOXES', DIR_WS_INCLUDES . 'boxes/');
  define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/');
  define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/');
  define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/');
  define('DIR_WS_LANGUAGES', DIR_WS_INCLUDES . 'languages/');


  define('DIR_WS_DOWNLOAD_PUBLIC', 'pub/');
  define('DIR_FS_CATALOG', 'C:/xampp/htdocs/oscmax/catalog/');
  define('DIR_FS_DOWNLOAD', DIR_FS_CATALOG . 'download/');
  define('DIR_FS_DOWNLOAD_PUBLIC', DIR_FS_CATALOG . 'pub/');


// define our database connection
  define('DB_SERVER', 'localhost');
  define('DB_SERVER_USERNAME', 'oscmax');
  define('DB_SERVER_PASSWORD', 'oscmax');
  define('DB_DATABASE', 'oscmax');
  define('USE_PCONNECT', 'false');
  define('STORE_SESSIONS', 'mysql');
?>