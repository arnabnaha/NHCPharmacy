<?php 
/* 
$Id: featured_sets.php 1692 2012-02-26 01:26:50Z michael.oscmax@gmail.com $

  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/

/*
  Open Featured Sets
*/ 
?>
<?php
//  $configuration_query = tep_db_query("SELECT configuration_key as cfgKey, configuration_value as cfgValue from " . TABLE_CONFIGURATION);
//  while ($configuration = tep_db_fetch_array($configuration_query)) {
//	  define($configuration['cfgKey'], $configuration['cfgValue']);
//  }

  if ( ((OPEN_FEATURED_LIMIT_PRODUCTS_FEATURES=='true') || (OPEN_FEATURED_LIMIT_CATEGORIES_FEATURES=='true')) && tep_not_null(isset($product_info['products_id'])) ) { // products info page
    
	$the_products_catagory_query = tep_db_query("select products_id, categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $product_info['products_id'] . "'" . " order by products_id,categories_id");
    $the_products_catagory = tep_db_fetch_array($the_products_catagory_query);
    $featured_product_category_id = $the_products_catagory['categories_id'];
  
  }
  
  if ( ((OPEN_FEATURED_LIMIT_PRODUCTS_FEATURES=='true') || (OPEN_FEATURED_LIMIT_CATEGORIES_FEATURES=='true')) && !empty($current_category_id) ) { /// We are in category depth 
  
	$categories_query_addition = "p2c.categories_id = '" . (int)$current_category_id . "'";
	$categories = array();
	tep_get_sub_categories($categories, $current_category_id);
	
	foreach ($categories AS $key => $category ) {
	  $categories_query_addition .= " OR p2c.categories_id = '" . (int)$category . "'";
	}
  
  }
  
  
// do product features

if ( (OPEN_FEATURED_LIMIT_PRODUCTS_FEATURES=='true') && !empty($current_category_id) ) { /// We are in category depth
  $featured_products_query_raw = "SELECT distinct p.products_id, p.products_model, pd.products_name, p.products_image, p.products_tax_class_id, pd.products_description, pd.products_short, s.status as specstat, s.specials_new_products_price, p.products_price from(( " . TABLE_PRODUCTS . " p )left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c )left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' where p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and (".$categories_query_addition.") and p.products_status = '1' and p.products_featured = '1' and find_in_set('" . $customer_group_id . "', p.products_hide_from_groups) = '0' order by " . FEATURED_PRODUCTS_SORT_ORDER . " " . FEATURED_PRODUCTS_DIRECTION . ' limit ' . MAX_DISPLAY_FEATURED_PRODUCTS;
} else if ( (OPEN_FEATURED_LIMIT_PRODUCTS_FEATURES=='true') && tep_not_null(isset($product_info['products_id'])) ) { // products info page
  $featured_products_query_raw = "SELECT distinct p.products_id, p.products_model, pd.products_name, p.products_image, p.products_tax_class_id, pd.products_description, pd.products_short, s.status as specstat, s.specials_new_products_price, p.products_price from(( " . TABLE_PRODUCTS . " p )left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c )left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' where p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and p2c.categories_id = '" . (int)$featured_product_category_id . "' and p.products_status = '1' and p.products_featured = '1' and find_in_set('" . $customer_group_id . "', p.products_hide_from_groups) = '0' order by " . FEATURED_PRODUCTS_SORT_ORDER . " " . FEATURED_PRODUCTS_DIRECTION . ' limit ' . MAX_DISPLAY_FEATURED_PRODUCTS;
} else { // default
  $featured_products_query_raw = "SELECT p.products_id, p.products_model, pd.products_name, pd.products_description, pd.products_short, p.products_image, p.products_tax_class_id, s.status as specstat, s.specials_new_products_price, p.products_price from(( " . TABLE_PRODUCTS . " p )left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id )left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' where p.products_status = '1' and p.products_featured = '1' and find_in_set('" . $customer_group_id . "', p.products_hide_from_groups) = '0' order by " . FEATURED_PRODUCTS_SORT_ORDER . " " . FEATURED_PRODUCTS_DIRECTION . ' limit ' . MAX_DISPLAY_FEATURED_PRODUCTS;
}

  $featured_products_query = tep_db_query($featured_products_query_raw); 
  while ($featured_products = tep_db_fetch_array($featured_products_query)) {
	
	// Set special price only for specific groups  
	$featured_products['specials_new_products_price'] = tep_get_products_special_price($featured_products['products_id']);

    $featured_products_array[] = array('id' => $featured_products['products_id'],
	    'model' => $featured_products['products_model'], 
		'name' => $featured_products['products_name'], 
		'description' => $featured_products['products_description'], 
		'shortdescription' => $featured_products['products_short'], 
		'image' => $featured_products['products_image'], 
		'price' => $featured_products['products_price'], 
		'specials_price' => $featured_products['specials_new_products_price'],
		'specials_status' => $featured_products['specstat'],  
		'tax_class_id' => $featured_products['products_tax_class_id']);
  }  


// do manufacturer features

  $featured_manufacturers_id = isset($manufacturers_id); 
  
  if ((!isset($featured_manufacturers_id)) || ($featured_manufacturers_id == '0')) {
    $featured_manufacturers_query_raw = "SELECT m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, m.manufacturers_featured_until, mi.manufacturers_id, mi.languages_id, mi.manufacturers_url from " . TABLE_MANUFACTURERS .
	  " m, " . TABLE_MANUFACTURERS_INFO . " mi where m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '" . (int)$languages_id . "' and m.manufacturers_featured = '1' order by " . FEATURED_MANUFACTURER_SORT_ORDER . " " . FEATURED_MANUFACTURERS_DIRECTION . ' limit ' . MAX_DISPLAY_FEATURED_MANUFACTURERS; 

    $featured_manufacturers_query = tep_db_query($featured_manufacturers_query_raw); 
    while ($featured_manufacturers = tep_db_fetch_array($featured_manufacturers_query)) { 

      $featured_manufacturers_array[] = array('id' => $featured_manufacturers['manufacturers_id'], 
		'name' => $featured_manufacturers['manufacturers_name'], 
		'image' => $featured_manufacturers['manufacturers_image'],
		'url' => $featured_manufacturers['manufacturers_url']); 
    } 
  }


// do manufacturer w/ product features

  $featured_manufacturer_products_id = isset($manufacturers_id); 

  if ((!isset($featured_manufacturer_products_id)) || ($featured_manufacturer_products_id == '0')) {
    $featured_manufacturer_products_query_raw = "select p.products_id, p.products_model, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_name, pd.products_description, pd.products_short, p.products_image, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price, m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, m.manufacturer_featured_until, mi.manufacturers_id, mi.languages_id, mi.manufacturers_url from (" . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_MANUFACTURERS_INFO . " mi) left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where p.products_status = '1' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '" . (int)$languages_id . "' and m.manufacturer_featured = '1' and find_in_set('" . $customer_group_id . "', p.products_hide_from_groups) = '0' order by " . FEATURED_MANUFACTURER_SORT_ORDER . " " . FEATURED_MANUFACTURER_DIRECTION . ' limit ' . MAX_DISPLAY_FEATURED_MANUFACTURER;


    $featured_manufacturer_products_query = tep_db_query($featured_manufacturer_products_query_raw); 
    while ($featured_manufacturer_products = tep_db_fetch_array($featured_manufacturer_products_query)) {
		
	  // Set special price only for specific groups  
	  $featured_manufacturer_products['specials_new_products_price'] = tep_get_products_special_price($featured_manufacturer_products['products_id']);	 

      $featured_manufacturer_products_array[] = array('pid' => $featured_manufacturer_products['products_id'], 
	    'pmodel' => $featured_manufacturer_products['products_model'], 
		'pname' => $featured_manufacturer_products['products_name'], 
		'pdescription' => $featured_manufacturer_products['products_description'], 
		'pshortdescription' => $featured_manufacturer_products['products_short'], 
		'pimage' => $featured_manufacturer_products['products_image'], 
		'pprice' => $featured_manufacturer_products['products_price'], 
		'pspecials_price' => $featured_manufacturer_products['specials_new_products_price'], 
		'ptax_class_id' => $featured_manufacturer_products['products_tax_class_id'], 
		'manufacturer' => $featured_manufacturer_products['manufacturers_name'],
		'mid' => $featured_manufacturer_products['manufacturers_id'],
		'mname' => $featured_manufacturer_products['manufacturers_name'], 
		'mimage' => $featured_manufacturer_products['manufacturers_image'],
		'murl' => $featured_manufacturer_products['manufacturers_url']); 
    }
  }


// do catagory features

    if ( (OPEN_FEATURED_LIMIT_CATEGORIES_FEATURES=='true') && !empty($current_category_id) ) { /// We are in category depth
      $featured_categories_query_raw = "select c.categories_id, c.categories_image, c.parent_id, c.categories_featured_until, cd.categories_name, p.products_id, p.products_model, p.products_price, p.products_tax_class_id, p.products_image, pd.products_name, pd.products_description, pd.products_short, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from(( " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p )left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id, " . TABLE_PRODUCTS_TO_CATEGORIES .
        " p2c )left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where p.products_status = '1' and c.categories_featured = '1' and cd.language_id = '" . (int)$languages_id . "' and c.categories_id = cd.categories_id and c.categories_id = p2c.categories_id and cd.categories_id = p2c.categories_id and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and (".$categories_query_addition.") and pd.language_id = '" . (int)$languages_id . "' and find_in_set('" . $customer_group_id . "', p.products_hide_from_groups) = '0' order by " . FEATURED_CATEGORIES_SORT_ORDER . " " . FEATURED_CATEGORIES_DIRECTION . ' limit ' . MAX_DISPLAY_FEATURED_CATEGORIES;
    } else if ( (OPEN_FEATURED_LIMIT_CATEGORIES_FEATURES=='true') && tep_not_null(isset($product_info['products_id'])) ) { // products info page
      $featured_categories_query_raw = "select c.categories_id, c.categories_image, c.parent_id, c.categories_featured_until, cd.categories_name, p.products_id, p.products_model, p.products_price, p.products_tax_class_id, p.products_image, pd.products_name, pd.products_description, pd.products_short, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from(( " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p )left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id, " . TABLE_PRODUCTS_TO_CATEGORIES .
        " p2c )left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where p.products_status = '1' and c.categories_featured = '1' and cd.language_id = '" . (int)$languages_id . "' and c.categories_id = cd.categories_id and c.categories_id = p2c.categories_id and cd.categories_id = p2c.categories_id and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and p2c.categories_id = '" . (int)$featured_product_category_id . "' and pd.language_id = '" . (int)$languages_id . "'  and find_in_set('" . $customer_group_id . "', p.products_hide_from_groups) = '0' order by " . FEATURED_CATEGORIES_SORT_ORDER . " " . FEATURED_CATEGORIES_DIRECTION . ' limit ' . MAX_DISPLAY_FEATURED_CATEGORIES;
    } else { // default
      $featured_categories_query_raw = "select c.categories_id, c.categories_image, c.parent_id, c.categories_featured_until, cd.categories_name, p.products_id,  p.products_model, p.products_price, p.products_tax_class_id, p.products_image, pd.products_name, pd.products_description, pd.products_short, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from(( " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p )left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id, " . TABLE_PRODUCTS_TO_CATEGORIES .
        " p2c )left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where p.products_status = '1' and c.categories_featured = '1' and cd.language_id = '" . (int)$languages_id . "' and c.categories_id = cd.categories_id and c.categories_id = p2c.categories_id and cd.categories_id = p2c.categories_id and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and find_in_set('" . $customer_group_id . "', p.products_hide_from_groups) = '0' order by " . FEATURED_CATEGORIES_SORT_ORDER . " " . FEATURED_CATEGORIES_DIRECTION . ' limit ' . MAX_DISPLAY_FEATURED_CATEGORIES;
    }

    $featured_categories_query = tep_db_query($featured_categories_query_raw); 
    while ($featured_categories = tep_db_fetch_array($featured_categories_query)) { 
	
	  // Set special price only for specific groups  
	  $featured_categories['specials_new_products_price'] = tep_get_products_special_price($featured_categories['products_id']);	

      $featured_categories_array[] = array('cid' => $featured_categories['categories_id'], 
		'cname' => $featured_categories['categories_name'],
		'cimage' => $featured_categories['categories_image'],
	    'pid' => $featured_categories['products_id'], 
		'pname' => $featured_categories['products_name'], 
		'pmodel' =>  $featured_categories['products_model'],
		'pdescription' => $featured_categories['products_description'], 
		'pshortdescription' => $featured_categories['products_short'], 
		'pimage' => $featured_categories['products_image'], 
		'pprice' => $featured_categories['products_price'], 
		'pspecials_price' => $featured_categories['specials_new_products_price'], 
		'ptax_class_id' => $featured_categories['products_tax_class_id']); 
    }


echo '<!-- Featured_Sets_bof //-->'."\n";
echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">' . "\n";
echo '  <tr>' . "\n";
echo '    <td>' . "\n";


if((FEATURED_PRODUCTS_DISPLAY == 'true') && (FEATURED_PRODUCTS_POSITION == '1')) {
    include(DIR_WS_MODULES  . (FEATURED_PRODUCTS_GROUPING=='sbox'?FILENAME_FEATURED_SBOX_PRODUCTS:FILENAME_FEATURED_GBOX_PRODUCTS));
  }else if ((FEATURED_MANUFACTURERS_DISPLAY == 'true') && (FEATURED_MANUFACTURERS_POSITION == '1')) { 
    include(DIR_WS_MODULES  . FILENAME_FEATURED_MANUFACTURERS);
  }else if ((FEATURED_MANUFACTURER_DISPLAY == 'true') && (FEATURED_MANUFACTURER_POSITION == '1')) {
    include(DIR_WS_MODULES  . (FEATURED_MANUFACTURER_GROUPING=='sbox'?FILENAME_FEATURED_SBOX_MANUFACTURER:FILENAME_FEATURED_GBOX_MANUFACTURER));
  }else if ((FEATURED_CATEGORIES_DISPLAY == 'true') && (FEATURED_CATEGORIES_POSITION == '1')) {
    include(DIR_WS_MODULES  . (FEATURED_CATEGORIES_GROUPING=='sbox'?FILENAME_FEATURED_SBOX_CATEGORIES:FILENAME_FEATURED_GBOX_CATEGORIES));
  }
if((FEATURED_PRODUCTS_DISPLAY == 'true') && (FEATURED_PRODUCTS_POSITION == '2')) {
    include(DIR_WS_MODULES  . (FEATURED_PRODUCTS_GROUPING=='sbox'?FILENAME_FEATURED_SBOX_PRODUCTS:FILENAME_FEATURED_GBOX_PRODUCTS));
  }else if ((FEATURED_MANUFACTURERS_DISPLAY == 'true') && (FEATURED_MANUFACTURERS_POSITION == '2')) { 
    include(DIR_WS_MODULES  . FILENAME_FEATURED_MANUFACTURERS);
  }else if ((FEATURED_MANUFACTURER_DISPLAY == 'true') && (FEATURED_MANUFACTURER_POSITION == '2')) {
    include(DIR_WS_MODULES  . (FEATURED_MANUFACTURER_GROUPING=='sbox'?FILENAME_FEATURED_SBOX_MANUFACTURER:FILENAME_FEATURED_GBOX_MANUFACTURER));
  }else if ((FEATURED_CATEGORIES_DISPLAY == 'true') && (FEATURED_CATEGORIES_POSITION == '2')) {
    include(DIR_WS_MODULES  . (FEATURED_CATEGORIES_GROUPING=='sbox'?FILENAME_FEATURED_SBOX_CATEGORIES:FILENAME_FEATURED_GBOX_CATEGORIES));
  }
if((FEATURED_PRODUCTS_DISPLAY == 'true') && (FEATURED_PRODUCTS_POSITION == '3')) {
    include(DIR_WS_MODULES  . (FEATURED_PRODUCTS_GROUPING=='sbox'?FILENAME_FEATURED_SBOX_PRODUCTS:FILENAME_FEATURED_GBOX_PRODUCTS));
  }else if ((FEATURED_MANUFACTURERS_DISPLAY == 'true') && (FEATURED_MANUFACTURERS_POSITION == '3')) { 
    include(DIR_WS_MODULES  . FILENAME_FEATURED_MANUFACTURERS);
  }else if ((FEATURED_MANUFACTURER_DISPLAY == 'true') && (FEATURED_MANUFACTURER_POSITION == '3')) {
    include(DIR_WS_MODULES  . (FEATURED_MANUFACTURER_GROUPING=='sbox'?FILENAME_FEATURED_SBOX_MANUFACTURER:FILENAME_FEATURED_GBOX_MANUFACTURER));
  }else if ((FEATURED_CATEGORIES_DISPLAY == 'true') && (FEATURED_CATEGORIES_POSITION == '3')) {
    include(DIR_WS_MODULES  . (FEATURED_CATEGORIES_GROUPING=='sbox'?FILENAME_FEATURED_SBOX_CATEGORIES:FILENAME_FEATURED_GBOX_CATEGORIES));
  }
if((FEATURED_PRODUCTS_DISPLAY == 'true') && (FEATURED_PRODUCTS_POSITION == '4')) {
    include(DIR_WS_MODULES  . (FEATURED_PRODUCTS_GROUPING=='sbox'?FILENAME_FEATURED_SBOX_PRODUCTS:FILENAME_FEATURED_GBOX_PRODUCTS));
  }else if ((FEATURED_MANUFACTURERS_DISPLAY == 'true') && (FEATURED_MANUFACTURERS_POSITION == '4')) { 
    include(DIR_WS_MODULES  . FILENAME_FEATURED_MANUFACTURERS);
  }else if ((FEATURED_MANUFACTURER_DISPLAY == 'true') && (FEATURED_MANUFACTURER_POSITION == '4')) {
    include(DIR_WS_MODULES  . (FEATURED_MANUFACTURER_GROUPING=='sbox'?FILENAME_FEATURED_SBOX_MANUFACTURER:FILENAME_FEATURED_GBOX_MANUFACTURER));
  }else if ((FEATURED_CATEGORIES_DISPLAY == 'true') && (FEATURED_CATEGORIES_POSITION == '4')) {
    include(DIR_WS_MODULES  . (FEATURED_CATEGORIES_GROUPING=='sbox'?FILENAME_FEATURED_SBOX_CATEGORIES:FILENAME_FEATURED_GBOX_CATEGORIES));
  } 
?>
	</td>
  </tr>
</table>
<!-- Featured_Sets_eof //-->