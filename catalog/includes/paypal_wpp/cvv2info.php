<?php
/*
$Id: cvv2info.php 1274 2011-03-20 19:16:00Z michael.oscmax@gmail.com $

  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/
// Copyright 2008 Brian Burton

  chdir('../..');
  require('includes/application_top.php');
  require(DIR_WS_INCLUDES . 'paypal_wpp/languages/' . $language . '/' . FILENAME_CVV2INFO);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//Dtd HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo PAGE_TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<style type="text/css">
  html,body {background-color:#fff}
</style>
</head>

<body>

<table>
  <tr>
    <td colspan="2" class="pageHeading" vAlign="top"><?php echo PAGE_HEADER; ?></td>
  </tr>
  <tr>
    <td colSpan="2" class="main"><?php echo CVV2_INTRO; ?></td>
  </tr>
  <tr>
    <td valign="top">
      <img src="<?php echo DIR_WS_INCLUDES . 'paypal_wpp/images/visacsc.gif'; ?>" width="181" height="126" />
    </td>
    <td valign="top">
      <table border="0" width="100%">
    		<tr>
    		  <td valign="top" class="formAreaTitle"><?php echo VISA_USERS; ?></tr>
    		</tr>
    		<tr>
    		  <td valign="top" class="main"><?php echo VISA_CVV2_INSTRUCTIONS; ?><br>
    		  </td>
    		</tr>
      </table>
    </td>
  </tr>
  <tr>
    <td valign="top">
      <img src="<?php echo DIR_WS_INCLUDES . 'paypal_wpp/images/amexcsc.gif'; ?>" width="181" height="132" />
    </td>
    <td>
  	  <table border="0" width="100%">
    		<tr>
    		  <td valign="top" class="formAreaTitle"><?php echo AMEX_USERS; ?></tr>
    		</tr>
    		<tr>
    		  <td valign="top" class="main"><?php echo AMEX_CVV2_INSTRUCTIONS; ?><br>
    		  </td>
    		</tr>
  	  </table>
    </td>
  </tr>
</table>
</body>
</html>