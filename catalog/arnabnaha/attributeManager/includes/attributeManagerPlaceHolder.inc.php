<?php
/*
$Id: attributeManagerPlaceHolder.inc.php 1857 2012-06-20 01:21:38Z michael.oscmax@gmail.com $

  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/
/*  
  Copyright © 2006 Kangaroo Partners
  http://kangaroopartners.com
  osc@kangaroopartners.com
*/


		require_once('attributeManager/classes/attributeManagerConfig.class.php');

if( isset($_GET['pID'])){

		require_once('attributeManager/classes/stopDirectAccess.class.php');
		stopDirectAccess::authorise(AM_SESSION_VALID_INCLUDE);
		
		echo '<div id="attributeManager">';
		echo '</div>';

} else {
		echo '<div id="topBar">';
		echo '<table width="100%"><tr><td class="messageStackAlert">' . tep_image(DIR_WS_ICONS . 'information.png', IMAGE_ICON_INFO) . '&nbsp;' . AM_AJAX_FIRST_SAVE . '</td></tr></table>';
		echo '</div>';
}
?>
