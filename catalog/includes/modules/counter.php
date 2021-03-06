<?php
/*
$Id: counter.php 1692 2012-02-26 01:26:50Z michael.oscmax@gmail.com $

  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/

  $counter_query = tep_db_query("select startdate, counter from " . TABLE_COUNTER);

  if (!tep_db_num_rows($counter_query)) {
    $date_now = date('Ymd');
    tep_db_query("insert into " . TABLE_COUNTER . " (startdate, counter) values ('" . $date_now . "', '1')");
    $counter_startdate = $date_now;
    $counter_now = 1;
  } else {
    $counter = tep_db_fetch_array($counter_query);
    $counter_startdate = $counter['startdate'];
    $counter_now = ($counter['counter'] + 1);
    tep_db_query("update " . TABLE_COUNTER . " set counter = '" . $counter_now . "'");
  }

  $counter_startdate_formatted = strftime(DATE_FORMAT_LONG, mktime(0, 0, 0, substr($counter_startdate, 4, 2), substr($counter_startdate, -2), substr($counter_startdate, 0, 4)));
?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr class="headerNavigation">
    <td class="breadcrumb_left" width="5"><?php echo tep_draw_separator('pixel_trans.gif', '1', '24'); ?></td>
    <td class="breadcrumb" align="left">&nbsp;&nbsp;<?php echo strftime(DATE_FORMAT_LONG); ?></td>
    <td class="breadcrumb" align="right"><?php echo $counter_now . ' ' . FOOTER_TEXT_REQUESTS_SINCE . ' ' . $counter_startdate_formatted; ?>&nbsp;&nbsp;</td>
    <td class="breadcrumb_right" width="5">&nbsp;</td>
  </tr>
</table>
<table>
  <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
  </tr>
</table>