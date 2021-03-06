<?php
/*
$Id: downloads.php 1775 2012-04-01 19:13:57Z michael.oscmax@gmail.com $

  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/
?>
<!-- downloads //-->
<?php
  if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
// Get last order id for checkout_success
    $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where customers_id = '" . (int)$customer_id . "' order by orders_id desc limit 1");
    $orders = tep_db_fetch_array($orders_query);
    $last_order = $orders['orders_id'];
  } else {
    $last_order = $_GET['order_id'];
  }

// Now get all downloadable products in that order
// LINE CHANGED: MOD - Downloads Controller
// DEFINE WHICH ORDERS_STATUS TO USE IN downloads_controller.php
// USE last_modified instead of date_purchased
//$downloads_query = tep_db_query("select date_format(o.date_purchased, '%Y-%m-%d') as date_purchased_day, opd.download_maxdays, op.products_name, opd.orders_products_download_id, opd.orders_products_filename, opd.download_count, opd.download_maxdays from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " opd where o.customers_id = '" . (int)$customer_id . "' and o.orders_id = '" . (int)$last_order . "' and o.orders_id = op.orders_id and op.orders_products_id = opd.orders_products_id and opd.orders_products_filename != ''");
  $downloads_query = tep_db_query("select o.orders_status, date_format(o.last_modified, '%Y-%m-%d') as date_purchased_day, opd.download_maxdays, op.products_name, opd.orders_products_download_id, opd.orders_products_filename, opd.download_count, opd.download_maxdays from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " opd where o.customers_id = '" . (int)$customer_id . "' and o.orders_status >= '" . DOWNLOADS_CONTROLLER_ORDERS_STATUS . "' and o.orders_id = '" . (int)$last_order . "' and o.orders_id = op.orders_id and op.orders_products_id = opd.orders_products_id and opd.orders_products_filename != ''");
  if (tep_db_num_rows($downloads_query) > 0) {
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><b><?php echo HEADING_DOWNLOAD; ?></b></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td>
          
<!-- list of products -->
<?php
    while ($downloads = tep_db_fetch_array($downloads_query)) {
// MySQL 3.22 does not have INTERVAL
      list($dt_year, $dt_month, $dt_day) = explode('-', $downloads['date_purchased_day']);
      $download_timestamp = mktime(23, 59, 59, $dt_month, $dt_day + $downloads['download_maxdays'], $dt_year);
      $download_expiry = date('Y-m-d H:i:s', $download_timestamp);
?>
          <table border="0" width="100%" cellspacing="1" cellpadding="4" class="infoBox">
            <tr class="infoBoxContents">
              <td class="main" colspan="3">&nbsp;<?php echo $downloads['products_name']; ?></td>
		    </tr>
            <tr class="infoBoxContents">
          
<?php
// The link will appear only if:
// - Download remaining count is > 0, AND
// - The file is present in the DOWNLOAD directory, AND EITHER
// - No expiry date is enforced (maxdays == 0), OR
// - The expiry date is not reached

      
	  echo '            <td class="main" align="left">&nbsp;<b>' . $downloads['download_count'] . '</b>' . TABLE_HEADING_DOWNLOAD_COUNT . '</td>' . "\n";
	  
	  if ($downloads['download_maxdays'] != 0) {
        echo '            <td class="main">' . TABLE_HEADING_DOWNLOAD_DATE . ' ' . tep_date_long($download_expiry) . '</td>' . "\n";
	  } else {
		echo '<td>&nbsp;</td>';
	  }
      
	  if ( ($downloads['download_count'] > 0) && (file_exists(DIR_FS_DOWNLOAD . $downloads['orders_products_filename'])) && ( ($downloads['download_maxdays'] == 0) || ($download_timestamp > time())) ) {
        echo '           <td class="main" align="right"><a href="' . tep_href_link(FILENAME_DOWNLOAD, 'order=' . $last_order . '&id=' . $downloads['orders_products_download_id']) . '">' . tep_image_button('button_download.gif', '') . '</a></td>' . "\n";
      } else {
	    echo '<td class="main" align="right">' . TEXT_MISSING_DOWNLOAD . '</td>';
	  }
	  ?>
            </tr>
          </table>
          <table border="0" width="100%" cellspacing="0" cellpadding="0" >
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
          </table>
      <?php
    } // end while
// LINE REMOVED: MOD - Downloads Controller Show Button
?>
            
        </td>
      </tr>
<?php
    if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="smallText" colspan="4"><p><?php printf(FOOTER_DOWNLOAD, '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . HEADER_TITLE_MY_ACCOUNT . '</a>'); ?></p></td>
      </tr>
<?php
    }
  }
?>
<?php // BOF: MOD - Downloads Controller
// If there is a download in the order and they cannot get it, tell customer about download rules
  $downloads_check_query = tep_db_query("select o.orders_id, opd.orders_products_download_id from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " opd where o.orders_id = opd.orders_id and o.orders_id = '" . (int)$last_order . "' and opd.orders_products_filename != ''");

if (tep_db_num_rows($downloads_check_query) > 0 and tep_db_num_rows($downloads_query) < 1) {
// if (tep_db_num_rows($downloads_query) < 1) {
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td colspan="3" class="messageStackAlert" align="center"><?php echo DOWNLOADS_CONTROLLER_ON_HOLD_MSG; ?></td>
      </tr>
<?php
}
// EOF: MOD - Downloads Controller
?>
<!-- downloads_eof //-->
