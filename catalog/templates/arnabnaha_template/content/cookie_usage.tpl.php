<?php
/*
$Id: cookie_usage.tpl.php 1692 2012-02-26 01:26:50Z michael.oscmax@gmail.com $

  osCmax e-Commerce
  http://www.osCmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '4'); ?></td>
      </tr>
	  <tr>
        <td class="productinfo_header"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right">&nbsp;</td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="content_text"><table border="0" width="40%" cellspacing="0" cellpadding="0" align="right">
          <tr>
            <td><?php new infoBoxHeading(array(array('text' => BOX_INFORMATION_HEADING))); ?></td>
          </tr>
          <tr>
            <td><?php new infoBox(array(array('text' => BOX_INFORMATION))); ?></td>
          </tr>
        </table><?php echo TEXT_INFORMATION; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table>
