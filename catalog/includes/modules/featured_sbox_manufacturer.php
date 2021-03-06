<?php
/* 
$Id: featured_sbox_manufacturer.php 1872 2012-09-20 13:16:03Z mfleeson@gmail.com $

  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/

/*
  Open Featured Sets manufacturer listing module
*/

if (isset($featured_manufacturer_products_array)) {
if (sizeof($featured_manufacturer_products_array) <> '0') { 

  $num_columns = (sizeof($featured_manufacturer_products_array)>(int)FEATURED_MANUFACTURER_COLUMNS?FEATURED_MANUFACTURER_COLUMNS:sizeof($featured_manufacturer_products_array));

  echo '&nbsp;<br>';  
  echo '<table border="0" width="100%" cellspacing="4" cellpadding="2"><tr>';
  echo '<td valign="middle" align="right" width="40%" class="main"><a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $featured_manufacturer_products_array[0]['mid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[0]['mimage'], $featured_manufacturer_products_array[0]['mname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></td><td valign="middle" align="left" width="60%" class="main">'.OPEN_FEATURED_BOX_MANUFACTURER_HEADING.'<br><b>'.$featured_manufacturer_products_array[0]['mname'].'</b></td>';
  echo '</tr></table>';  

  echo '&nbsp;<br>'; 

  echo '<table border="0" width="100%"'.($num_columns==1?' cellspacing="0" cellpadding="0"':' cellspacing="4" cellpadding="2"').'><tr>';

  for($i=0,$col=1; $i<sizeof($featured_manufacturer_products_array); $i++,$col++) {
  
  	$pf->loadProduct($featured_manufacturer_products_array[$i]['id'], $languages_id, NULL, NULL);

    $products_price = $pf->getPriceStringShort() . '<br>';
	
    if ($featured_manufacturer_products_array[$i]['pshortdescription'] != '') { 
      $current_description = $featured_manufacturer_products_array[$i]['pshortdescription']; 
    } else { 
	  if (OPEN_FEATURED_LIMIT_DESCRIPTION_BY=='words') {
        $bah = explode(" ", $featured_manufacturer_products_array[$i]['pdescription']); 
        $word_count = count($bah);
		$current_description = '';
		for($desc=0 ; $desc<min(MAX_FEATURED_MANUFACTURER_WORD_DESCRIPTION, $word_count); $desc++) 
        { 
          $current_description .= $bah[$desc]." "; 
        }  
	  } else {
        $current_description = substr($featured_manufacturer_products_array[$i]['pdescription'],0,MAX_FEATURED_MANUFACTURER_WORD_DESCRIPTION)." "; 
	  }  
    } 
	
    echo '<td valign="top" align="center" width="'.floor(100/$num_columns).'%">';

    if (FEATURED_SET_SHOW_BUY_NOW_BUTTONS=='true') {
      $buy_now_link = $pf->getProductButtons($featured_manufacturer_products_array[$i]['id'], basename($PHP_SELF), $featured_manufacturer_products_array[$i]['model'], $featured_manufacturer_products_array[$i]['name']);
	  //sid killer buy now button: http://www.oscommerce.com/community/contributions,952
      //$buy_now_link = '<br><form name="buy_now" method="post" action="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now', 'NONSSL') . '"><input type="hidden" name="products_id" value="' . $featured_manufacturer_products_array[$i]['pid'] . '">' . tep_image_submit('button_buy_now.gif', IMAGE_BUTTON_BUY_NOW) . '</form>';
    } else {
      //disabled
      $buy_now_link = '';
    }



  if ((FEATURED_MANUFACTURER_SET == '1') && (FEATURED_MANUFACTURER_SET_STYLE == '1')) { 
    echo '<table border="0" width="100%" cellspacing="0" cellpadding="'.MANUFACTURER_CELLPADDING.'"><tr><td width="' . (SMALL_IMAGE_WIDTH + 25) . '" align="center" valign="top" class="featuredManufacturerWP"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></td><td align="left" valign="top" class="featuredManufacturerWP"><div align="left"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . $featured_manufacturer_products_array[$i]['pname'] . '</a></div>';
    echo $current_description; 
    echo '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '"><font color="#FF0000">' . TEXT_MORE_INFO . '</font></a>&nbsp;</td><td align="left" valign="top" class="featuredManufacturerWP">' . OPEN_FEATURED_TABLE_HEADING_PRICE . str_replace('&nbsp;&nbsp;','<br>',$products_price) . '' . $buy_now_link . '</td></tr></table>'."\n";
  }


  if ((FEATURED_MANUFACTURER_SET == '1') && ((FEATURED_MANUFACTURER_SET_STYLE == '2') || (FEATURED_MANUFACTURER_SET_STYLE == '5'))) {
    $info_box_contents = array();
    /* MF - Added $ to featured_manufacturer_products_array */
    $info_box_contents[] = array('text' => '<table border="0" width="100%" cellspacing="0" cellpadding="' . MANUFACTURER_CELLPADDING . '"><tr><td width="' . (SMALL_IMAGE_WIDTH + 25) . '" align="left" valign="top" class="featuredManufacturerWP"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></td><td align="left" valign="top" class="featuredManufacturerWP"><div align="left"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . $featured_manufacturer_products_array[$i]['pname'] . '</a></div>');
    $info_box_contents[0]['text'] .= $current_description; 
	$info_box_contents[0]['text'] .= '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '"><font color="#FF0000">' . TEXT_MORE_INFO . '</font></a>&nbsp;</td><td align="left" valign="top" class="featuredManufacturerWP">' . OPEN_FEATURED_TABLE_HEADING_PRICE . str_replace('&nbsp;&nbsp;','<br>',$products_price) . '' . $buy_now_link . '</td></tr></table>'."\n";
    new infoBox($info_box_contents);
  }


  if ((FEATURED_MANUFACTURER_SET == '1') && (FEATURED_MANUFACTURER_SET_STYLE == '3')) {
    echo '<table border="0" width="100%" cellspacing="0" cellpadding="'.MANUFACTURER_CELLPADDING.'"><tr><td width="' . (SMALL_IMAGE_WIDTH + 25) . '" align="left" valign="top" class="featuredManufacturerWP" style="height: '.MANUFACTURER_VLINE_IMAGE_HEIGHT.'px;"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></td><td align="left" valign="top" class="featuredManufacturerWP"><div align="left"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . $featured_manufacturer_products_array[$i]['pname'] . '</a></div>';
    echo $current_description; 
    echo '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '"><font color="#FF0000">' . TEXT_MORE_INFO . '</font></a>&nbsp;</td><td align="left" valign="top" class="featuredManufacturerWP">' . OPEN_FEATURED_TABLE_HEADING_PRICE . str_replace('&nbsp;&nbsp;','<br>',$products_price) . '' . $buy_now_link . '</td>'.(((FEATURED_MANUFACTURER_COLUMNS>1)&&(($col/$num_columns)!=floor($col/$num_columns)))?'<td align="center" class="featuredManufacturerWP" width="'.MANUFACTURER_LINE_THICKNESS.'" style="height: '.MANUFACTURER_VLINE_IMAGE_HEIGHT.'px;"><div style="background-color: #'.MANUFACTURER_LINE_COLOR.'; height: '.MANUFACTURER_VLINE_IMAGE_HEIGHT.'px; width: '.MANUFACTURER_LINE_THICKNESS.'px;">' . tep_draw_separator('pixel_trans.gif', MANUFACTURER_LINE_THICKNESS, MANUFACTURER_VLINE_IMAGE_HEIGHT) . '</div></td>':'').'</tr></table>'."\n";
  }


  if ((FEATURED_MANUFACTURER_SET == '1') && ((FEATURED_MANUFACTURER_SET_STYLE == '4') || (FEATURED_MANUFACTURER_SET_STYLE == '6'))) {
    $info_box_contents = array();
    $info_box_contents[] = array('text' => '<table border="0" width="100%" cellspacing="0" cellpadding="'.MANUFACTURER_CELLPADDING.'"><tr><td width="' . (SMALL_IMAGE_WIDTH + 25) . '" align="left" valign="top" class="featuredManufacturerWP"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></td><td align="left" valign="top" class="featuredManufacturerWP"><div align="left"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . $featured_manufacturer_products_array[$i]['pname'] . '</a></div>');
    $info_box_contents[0]['text'] .= $current_description; 
	$info_box_contents[0]['text'] .= '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '"><font color="#FF0000">' . TEXT_MORE_INFO .        '</font></a>&nbsp;</td><td align="left" valign="top" class="featuredManufacturerWP">' . OPEN_FEATURED_TABLE_HEADING_PRICE . str_replace('&nbsp;&nbsp;','<br>',$products_price) . '' . $buy_now_link . '</td></tr></table>'."\n";
    new infoBox($info_box_contents);
    //echo '<img src="images/info_box_' . FEATURED_MANUFACTURER_SET_STYLE_SHADOW . '_shadow.gif" width="100%" height="13" alt="">'."\n";
  }




  if ((FEATURED_MANUFACTURER_SET == '2') && (FEATURED_MANUFACTURER_SET_STYLE == '1')) { 
    echo '<table border="0" width="100%" cellspacing="0" cellpadding="'.MANUFACTURER_CELLPADDING.'"><tr><td align="left" valign="top" class="featuredManufacturerWP"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></td></tr><tr><td align="center" valign="top" class="featuredManufacturerWP"><div align="left"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . $featured_manufacturer_products_array[$i]['pname'] . '</a></div></td></tr><tr><td valign="top" class="featuredManufacturerWP">';
    echo $current_description; 
    echo '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '"><font color="#FF0000">' . TEXT_MORE_INFO . '</font></a>&nbsp;</td></tr><tr><td align="left" valign="top" class="featuredManufacturerWP">' . OPEN_FEATURED_TABLE_HEADING_PRICE . $products_price . '' . $buy_now_link . '</td></tr></table>'."\n";
  }



  if ((FEATURED_MANUFACTURER_SET == '2') && ((FEATURED_MANUFACTURER_SET_STYLE == '2') || (FEATURED_MANUFACTURER_SET_STYLE == '5'))) {
    $info_box_contents = array();
    $info_box_contents[] = array('text' => '<table border="0" width="100%" cellspacing="0" cellpadding="'.MANUFACTURER_CELLPADDING.'"><tr><td align="left" valign="top" class="featuredManufacturerWP"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></td></tr><tr><td align="center" valign="top" class="featuredManufacturerWP"><div align="left"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . $featured_manufacturer_products_array[$i]['pname'] . '</a></div></td></tr><tr><td valign="top" class="featuredManufacturerWP">');
    $info_box_contents[0]['text'] .= $current_description; 
	$info_box_contents[0]['text'] .= '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '"><font color="#FF0000">' . TEXT_MORE_INFO . '</font></a>&nbsp;</td></tr><tr><td align="left" valign="top" class="featuredManufacturerWP">' . OPEN_FEATURED_TABLE_HEADING_PRICE . $products_price . '' . $buy_now_link . '</td></tr></table>'."\n";
    new infoBox($info_box_contents);
  }



  if ((FEATURED_MANUFACTURER_SET == '2') && (FEATURED_MANUFACTURER_SET_STYLE == '3')) {
    echo '<table border="0" width="100%" cellspacing="0" cellpadding="'.MANUFACTURER_CELLPADDING.'"><tr><td align="left" valign="top" class="featuredManufacturerWP" style="height: '.MANUFACTURER_VLINE_IMAGE_HEIGHT.'px;"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><div align="left"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . $featured_manufacturer_products_array[$i]['pname'] . '</a></div><br>';
    echo $current_description; 
    echo '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '"><font color="#FF0000">' . TEXT_MORE_INFO . '</font></a>&nbsp;<br>' . OPEN_FEATURED_TABLE_HEADING_PRICE . $products_price . '' . $buy_now_link . '</td>'.(((FEATURED_MANUFACTURER_COLUMNS>1)&&(($col/$num_columns)!=floor($col/$num_columns)))?'<td align="center" class="featuredManufacturerWP" width="'.MANUFACTURER_LINE_THICKNESS.'" style="height: '.MANUFACTURER_VLINE_IMAGE_HEIGHT.'px;"><div style="background-color: #'.MANUFACTURER_LINE_COLOR.'; height: '.MANUFACTURER_VLINE_IMAGE_HEIGHT.'px; width: '.MANUFACTURER_LINE_THICKNESS.'px;">' . tep_draw_separator('pixel_trans.gif', MANUFACTURER_LINE_THICKNESS, MANUFACTURER_VLINE_IMAGE_HEIGHT) . '</div></td>':'').'</tr></table>'."\n";
  }



  if ((FEATURED_MANUFACTURER_SET == '2') && ((FEATURED_MANUFACTURER_SET_STYLE == '4') || (FEATURED_MANUFACTURER_SET_STYLE == '6'))) {
    $info_box_contents = array();
    $info_box_contents[] = array('text' => '<table border="0" width="100%" cellspacing="0" cellpadding="'.MANUFACTURER_CELLPADDING.'"><tr><td align="left" valign="top" class="featuredManufacturerWP"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></td></tr><tr><td align="center" valign="top" class="featuredManufacturerWP"><div align="left"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . $featured_manufacturer_products_array[$i]['pname'] . '</a></div></td></tr><tr><td valign="top" class="featuredManufacturerWP">');
    $info_box_contents[0]['text'] .= $current_description; 
	$info_box_contents[0]['text'] .= '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '"><font color="#FF0000">' . TEXT_MORE_INFO .       '</font></a>&nbsp;</td></tr><tr><td align="left" valign="top" class="featuredManufacturerWP">' . OPEN_FEATURED_TABLE_HEADING_PRICE . $products_price . '' . $buy_now_link . '</td></tr></table>'."\n";
    new infoBox($info_box_contents);
    //echo '<img src="images/info_box_' . FEATURED_MANUFACTURER_SET_STYLE_SHADOW . '_shadow.gif" width="100%" height="13" alt="">'."\n";
  }




  if ((FEATURED_MANUFACTURER_SET == '3') && (FEATURED_MANUFACTURER_SET_STYLE == '1')) {
    echo '<table border="0" width="100%" cellspacing="0" cellpadding="'.MANUFACTURER_CELLPADDING.'"><tr><td width="' . (SMALL_IMAGE_WIDTH + 25) . '" align="center" valign="top" class="featuredManufacturerWP"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br>' . OPEN_FEATURED_TABLE_HEADING_PRICE . str_replace('&nbsp;&nbsp;','<br>',$products_price) . '' . $buy_now_link . '</td><td align="left" valign="top" class="featuredManufacturerWP"><div align="left"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . $featured_manufacturer_products_array[$i]['pname'] . '</a></div>';
    echo $current_description; 
    echo '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '"><font color="#FF0000">' . TEXT_MORE_INFO . '</font></a>&nbsp;</td></tr></table>'."\n";
  }



  if ((FEATURED_MANUFACTURER_SET == '3') && ((FEATURED_MANUFACTURER_SET_STYLE == '2') || (FEATURED_MANUFACTURER_SET_STYLE == '5'))) {
    $info_box_contents = array();
    $info_box_contents[] = array('text' => '<table border="0" width="100%" cellspacing="0" cellpadding="'.MANUFACTURER_CELLPADDING.'"><tr><td width="' . (SMALL_IMAGE_WIDTH + 25) . '" align="center" valign="top" class="featuredManufacturerWP"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br>' . OPEN_FEATURED_TABLE_HEADING_PRICE . str_replace('&nbsp;&nbsp;','<br>',$products_price) . '' . $buy_now_link . '</td><td align="left" valign="top" class="featuredManufacturerWP"><div align="left"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . $featured_manufacturer_products_array[$i]['pname'] . '</a></div>');
    $info_box_contents[0]['text'] .= $current_description; 
	$info_box_contents[0]['text'] .= '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '"><font color="#FF0000">' . TEXT_MORE_INFO . '</font></a>&nbsp;</td></tr></table>'."\n";
    new infoBox($info_box_contents);
  }



  if ((FEATURED_MANUFACTURER_SET == '3') && (FEATURED_MANUFACTURER_SET_STYLE == '3')) {
    echo '<table border="0" width="100%" cellspacing="0" cellpadding="'.MANUFACTURER_CELLPADDING.'"><tr><td width="' . (SMALL_IMAGE_WIDTH + 25) . '" align="center" valign="top" class="featuredManufacturerWP" style="height: '.MANUFACTURER_VLINE_IMAGE_HEIGHT.'px;"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . 
	  '</a><br>' . OPEN_FEATURED_TABLE_HEADING_PRICE . str_replace('&nbsp;&nbsp;','<br>',$products_price) . '' . $buy_now_link . '</td><td align="left" valign="top" class="featuredManufacturerWP"><div align="left"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . $featured_manufacturer_products_array[$i]['pname'] . '</a></div>';
    echo $current_description; 
    echo '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '"><font color="#FF0000">' . TEXT_MORE_INFO . '</font></a>&nbsp;</td>'.(((FEATURED_MANUFACTURER_COLUMNS>1)&&(($col/$num_columns)!=floor($col/$num_columns)))?'<td align="center" class="featuredManufacturerWP" width="'.MANUFACTURER_LINE_THICKNESS.'" style="height: '.MANUFACTURER_VLINE_IMAGE_HEIGHT.'px;"><div style="background-color: #'.MANUFACTURER_LINE_COLOR.'; height: '.MANUFACTURER_VLINE_IMAGE_HEIGHT.'px; width: '.MANUFACTURER_LINE_THICKNESS.'px;">' . tep_draw_separator('pixel_trans.gif', MANUFACTURER_LINE_THICKNESS, MANUFACTURER_VLINE_IMAGE_HEIGHT) . '</div></td>':'').'</tr></table>'."\n";
  }



  if ((FEATURED_MANUFACTURER_SET == '3') && ((FEATURED_MANUFACTURER_SET_STYLE == '4') || (FEATURED_MANUFACTURER_SET_STYLE == '6'))) {
    $info_box_contents = array();
    $info_box_contents[] = array('text' => '<table border="0" width="100%" cellspacing="0" cellpadding="'.MANUFACTURER_CELLPADDING.'"><tr><td width="' . (SMALL_IMAGE_WIDTH + 25) . '" align="center" valign="top" class="featuredManufacturerWP"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br>' . OPEN_FEATURED_TABLE_HEADING_PRICE . str_replace('&nbsp;&nbsp;','<br>',$products_price) . '' . $buy_now_link . '</td><td align="left" valign="top" class="featuredManufacturerWP"><div align="left"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . $featured_manufacturer_products_array[$i]['pname'] . '</a></div>');
    $info_box_contents[0]['text'] .= $current_description; 
	$info_box_contents[0]['text'] .= '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '"><font color="#FF0000">' . TEXT_MORE_INFO . '</font></a>&nbsp;</td></tr></table>'."\n";
    new infoBox($info_box_contents);
    //echo '<img src="images/info_box_' . FEATURED_SET_STYLE_SHADOW . '_shadow.gif" width="100%" height="13" alt="">';
  }




  if ((FEATURED_MANUFACTURER_SET == '4') && (FEATURED_MANUFACTURER_SET_STYLE == '1')) {
    echo '<table border="0" width="100%" cellspacing="0" cellpadding="'.MANUFACTURER_CELLPADDING.'"><tr><td width="' . (SMALL_IMAGE_WIDTH + 25);
    echo '" align="center" valign="top" class="featuredManufacturerWP"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">';
	echo $featured_manufacturer_products_array[$i]['pname'] . '</a><br>' . OPEN_FEATURED_TABLE_HEADING_PRICE . str_replace('&nbsp;&nbsp;','<br>',$products_price) . '' . $buy_now_link . '</td></tr></table>'."\n";
  }



  if ((FEATURED_MANUFACTURER_SET == '4') && ((FEATURED_MANUFACTURER_SET_STYLE == '2') || (FEATURED_MANUFACTURER_SET_STYLE == '5'))) {
    $info_box_contents = array();
    $info_box_contents[] = array('text' => '<table border="0" width="100%" cellspacing="0" cellpadding="'.MANUFACTURER_CELLPADDING.'"><tr><td align="center" valign="top" class="featuredManufacturerWP"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . $featured_manufacturer_products_array[$i]['pname'] . '</a><br>' . OPEN_FEATURED_TABLE_HEADING_PRICE . str_replace('&nbsp;&nbsp;','<br>',$products_price) . '' . $buy_now_link . '</td></tr></table>'."\n");
    new infoBox($info_box_contents);
  }



  if ((FEATURED_MANUFACTURER_SET == '4') && (FEATURED_MANUFACTURER_SET_STYLE == '3')) {
    echo '<table border="0" width="100%" cellspacing="0" cellpadding="0"><tr><td style="height: '.MANUFACTURER_VLINE_IMAGE_HEIGHT.'px;"><table border="0" width="100%" cellspacing="0" cellpadding="'.MANUFACTURER_CELLPADDING.'"><tr><td align="center" valign="top" class="featuredManufacturerWP"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' . 
	    tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . $featured_manufacturer_products_array[$i]['pname'] . '</a><br>' . OPEN_FEATURED_TABLE_HEADING_PRICE . str_replace('&nbsp;&nbsp;','<br>',$products_price) . '' . $buy_now_link . '</td></tr></table></td>'.(((FEATURED_MANUFACTURER_COLUMNS>1)&&(($col/$num_columns)!=floor($col/$num_columns)))?'<td align="center" class="featuredManufacturerWP" width="'.MANUFACTURER_LINE_THICKNESS.'" style="height: '.MANUFACTURER_VLINE_IMAGE_HEIGHT.'px;"><div style="background-color: #'.MANUFACTURER_LINE_COLOR.'; height: '.MANUFACTURER_VLINE_IMAGE_HEIGHT.'px; width: '.MANUFACTURER_LINE_THICKNESS.'px;">' . tep_draw_separator('pixel_trans.gif', MANUFACTURER_LINE_THICKNESS, MANUFACTURER_VLINE_IMAGE_HEIGHT) . '</div></td>':'').'</tr></table>'."\n";
  }



  if ((FEATURED_MANUFACTURER_SET == '4') && ((FEATURED_MANUFACTURER_SET_STYLE == '4') || (FEATURED_MANUFACTURER_SET_STYLE == '6'))) {
    $info_box_contents = array();
    $info_box_contents[] = array('text' => '<table border="0" width="100%" cellspacing="0" cellpadding="'.MANUFACTURER_CELLPADDING.'"><tr><td align="center" valign="top" class="featuredManufacturerWP"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $featured_manufacturer_products_array[$i]['pimage'], $featured_manufacturer_products_array[$i]['pname'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_manufacturer_products_array[$i]['pid'], 'NONSSL') . '">' . $featured_manufacturer_products_array[$i]['pname'] . '</a><br>' . OPEN_FEATURED_TABLE_HEADING_PRICE . str_replace('&nbsp;&nbsp;','<br>',$products_price) . '' . $buy_now_link . '</td></tr></table>'."\n");
    new contentBox($info_box_contents);
    //echo '<img src="images/info_box_' . FEATURED_SET_STYLE_SHADOW . '_shadow.gif" width="100%" height="13" alt="">';
  }


    echo '</td>'."\n";

    if (($col/$num_columns) == floor($col/$num_columns)) { 
      if ((((FEATURED_MANUFACTURER_SET == '1') && (FEATURED_MANUFACTURER_SET_STYLE == '3')) or ((FEATURED_MANUFACTURER_SET == '2') && (FEATURED_MANUFACTURER_SET_STYLE == '3')) or ((FEATURED_MANUFACTURER_SET == '3') && (FEATURED_MANUFACTURER_SET_STYLE == '3')) or ((FEATURED_MANUFACTURER_SET == '4') && (FEATURED_MANUFACTURER_SET_STYLE == '3'))) && (($i+1) != sizeof($featured_manufacturer_products_array))){
        echo '</tr><tr><td colspan="' . $num_columns . '" align="center" valign="top" class="featuredManufacturerWP"><div style="background-color: #'.MANUFACTURER_LINE_COLOR.'; height: '.MANUFACTURER_LINE_THICKNESS.'px; width: 100%;">' . tep_draw_separator('pixel_trans.gif', '1', MANUFACTURER_LINE_THICKNESS) . '</div></td>'."\n"; 
      }else{
        echo '</tr><tr><td colspan="' . $num_columns . '" class="featuredManufacturerWP">&nbsp;</td>'."\n"; 
      }
      if (($i+1) != sizeof($featured_manufacturer_products_array)) { 
	    echo '</tr><tr>'."\n";
      } 
    }
	
  } // end: for()
  while (($i/$num_columns) != floor($i/$num_columns)) {
    echo '<td>&nbsp;</td>'."\n";
	$i++;
  }
  echo '</tr></table>'."\n";
  
  echo tep_draw_separator('pixel_trans.gif', '100%', '10');
  
} // end: if()
}
?>