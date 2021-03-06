<?php
/*
$Id: account.tpl.php 1692 2012-02-26 01:26:50Z michael.oscmax@gmail.com $

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
        <td class="productinfo_header">
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
              <td class="pageHeading" align="right">&nbsp;</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  if ($messageStack->size('account') > 0) {
?>
      <tr>
        <td><?php echo $messageStack->output('account'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  }
?>
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="main"><b><?php echo MY_ACCOUNT_TITLE; ?></b></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
            <tr class="infoBoxContents">
              <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="10">&nbsp;</td>
                    <td width="40"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . tep_image(bts_select('icons', 'account_info.png')) . '</a>'; ?></td>
                    <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . MY_ACCOUNT_INFORMATION . '</a>'; ?></td>
                    <td width="10">&nbsp;</td>
                    <td width="40"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_NOTIFICATIONS, '', 'SSL') . '">' . tep_image(bts_select('icons', 'notifications.png')) . '</a>'; ?></td>
                    <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_NOTIFICATIONS, '', 'SSL') . '">' . EMAIL_NOTIFICATIONS_PRODUCTS . '</a>'; ?></td>
                    <td width="10">&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="10">&nbsp;</td>
                    <td width="40"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . tep_image(bts_select('icons', 'orders.png')) . '</a>'; ?></td>
                    <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . MY_ORDERS_VIEW . '</a>'; ?></td>
                    <td width="10">&nbsp;</td>
                    <td width="40"><?php echo '<a href="' . tep_href_link(FILENAME_WISHLIST, '', 'SSL') . '">' . tep_image(bts_select('icons', 'wish_list.png')) . '</a>'; ?></td>
                    <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_WISHLIST, '', 'SSL') . '">' . MY_WISH_LIST . '</a>'; ?></td>
                    <td width="10">&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="10">&nbsp;</td>
                    <td width="40"><?php echo '<a href="' . tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' . tep_image(bts_select('icons', 'address.png')) . '</a>'; ?></td>
                    <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' . MY_ACCOUNT_ADDRESS_BOOK . '</a>'; ?></td>
                    <td width="10">&nbsp;</td>
                    <td width="40"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_NEWSLETTERS, '', 'SSL') . '">' . tep_image(bts_select('icons', 'newsletters.png')) . '</a>'; ?></td>
                    <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_NEWSLETTERS, '', 'SSL') . '">' . EMAIL_NOTIFICATIONS_NEWSLETTERS . '</a>'; ?></td>
                    <td width="10">&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="10">&nbsp;</td>
                    <td width="40"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_PASSWORD, '', 'SSL') . '">' . tep_image(bts_select('icons', 'password.png')) . '</a>'; ?></td>
                    <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_PASSWORD, '', 'SSL') . '">' . MY_ACCOUNT_PASSWORD . '</a>'; ?></td>
                    <td width="10">&nbsp;</td>
                    <td width="40"><?php echo '<a href="' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . '">' . tep_image(bts_select('icons', 'account_log_off.png')) . '</a>'; ?></td>
                    <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . '">' . MY_ACCOUNT_LOG_OFF . '</a>'; ?></td>
                    <td width="10">&nbsp;</td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      
      <?php
  if (tep_count_customer_orders() > 0) {
?>
      <tr>
        <td>
          <table border="0" cellspacing="0" cellpadding="2">
            <tr>
              <td class="main"><b><?php echo OVERVIEW_TITLE; ?></b></td>
              <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '"><u>' . OVERVIEW_SHOW_ALL_ORDERS . '</u></a>'; ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
            <tr class="infoBoxContents">
              <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td>
                      <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
    $orders_query = tep_db_query("select o.orders_id, o.date_purchased, o.delivery_name, o.delivery_country, o.billing_name, o.billing_country, ot.text as order_total, s.orders_status_name from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" . (int)$customer_id . "' and o.orders_id = ot.orders_id and ot.class = 'ot_total' and o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and s.public_flag = '1' order by orders_id desc limit 3");
    while ($orders = tep_db_fetch_array($orders_query)) {
      if (tep_not_null($orders['delivery_name'])) {
        $order_name = $orders['delivery_name'];
        $order_country = $orders['delivery_country'];
      } else {
        $order_name = $orders['billing_name'];
        $order_country = $orders['billing_country'];
      } // end if 
?>
                        <tr class="moduleRow" onMouseOver="rowOverEffect(this)" onMouseOut="rowOutEffect(this)" onClick="document.location.href='<?php echo tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $orders['orders_id'], 'SSL'); ?>'">
                          <td class="main" width="80"><?php echo tep_date_short($orders['date_purchased']); ?></td>
                          <td class="main"><?php echo '#' . $orders['orders_id']; ?></td>
                          <td class="main"><?php echo tep_output_string_protected($order_name) . ', ' . $order_country; ?></td>
                          <td class="main"><?php echo $orders['orders_status_name']; ?></td>
                          <td class="main" align="right"><?php echo $orders['order_total']; ?></td>
                          <td class="main" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $orders['orders_id'], 'SSL') . '">' . tep_image_button('small_view.gif', SMALL_IMAGE_BUTTON_VIEW) . '</a>'; ?></td>
                        </tr>
<?php
    } // end while
?>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  } // end if (tep_count_customer_orders() > 0)
?>

    </table>