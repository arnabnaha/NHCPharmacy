<?php
/*
$Id: product_reviews_write.php 1959 2013-03-05 17:01:31Z michael.oscmax@gmail.com $

  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/

// Most of this file is changed or moved to BTS - Basic Template System - format.
// For adding in contribution or modification - parts of this file has been moved to: catalog\templates\fallback\contents\<filename>.tpl.php as a default (sub 'fallback' with your current template to see if there is a template specife change).
//       catalog\templates\fallback\contents\<filename>.tpl.php as a default (sub 'fallback' with your current template to see if there is a template specife change).
// (Sub 'fallback' with your current template to see if there is a template specific file.)

  require('includes/application_top.php');
  
  // start modification for reCaptcha
  if (RECAPTCHA_ON == 'true' && RECAPTCHA_PRODUCT_REVIEWS_WRITE == 'true') {
    require_once('includes/classes/recaptchalib.php');
    $publickey = RECAPTCHA_PUBLIC_KEY;
    $privatekey = RECAPTCHA_PRIVATE_KEY;
  }
  // end modification for reCaptcha

// LINE ADDED: MOD - Added for Dynamic MoPics v3.000
  require(DIR_WS_FUNCTIONS . 'dynamic_mopics.php');
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, 'review_message=1', 'SSL'));
  }

//  $product_info_query = tep_db_query("select p.products_id, p.products_model, p.products_image, p.products_price, p.products_tax_class_id, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . (int)$_GET['products_id'] . "' and p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'");

// BOF Separate Pricing Per Customer 
 
  if (isset($_SESSION['sppc_customer_group_id']) && $_SESSION['sppc_customer_group_id'] != '0') {
    $customer_group_id = $_SESSION['sppc_customer_group_id'];
  } else {
    $customer_group_id = '0';
  }

  $product_info_query = tep_db_query("select p.products_id, p.products_model, p.products_image, p.products_price, p.products_tax_class_id, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd left join " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c using(products_id) left join " . TABLE_CATEGORIES . " c using(categories_id) where p.products_id = '" . (int)$_GET['products_id'] . "' and p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and find_in_set('".$customer_group_id."', products_hide_from_groups) = 0 and find_in_set('" . $customer_group_id . "', categories_hide_from_groups) = 0");
// EOF Separate Pricing Per Customer, Hide products and categories from groups 

  if (!tep_db_num_rows($product_info_query)) {
    tep_redirect(tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params(array('action'))));
  } else {
    $product_info = tep_db_fetch_array($product_info_query);
  }

  $customer_query = tep_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customer_id . "'");
  $customer = tep_db_fetch_array($customer_query);

  if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
    $rating = tep_db_prepare_input(isset($_POST['rating']) ? $_POST['rating'] : '');
    $review = tep_db_prepare_input($_POST['review']);

    $error = false;
	
	// start modification for reCaptcha
    if (RECAPTCHA_ON == 'true' && RECAPTCHA_PRODUCT_REVIEWS_WRITE == 'true') {

	  // the response from reCAPTCHA
      $resp = null;

	  // was there a reCaptcha response?
      $resp = recaptcha_check_answer ($privatekey,
      $_SERVER["REMOTE_ADDR"],
      $_POST["recaptcha_challenge_field"],
      $_POST["recaptcha_response_field"]);
    }
    // end modification for reCaptcha
	
    if (strlen($review) < REVIEW_TEXT_MIN_LENGTH) {
      $error = true;
      $messageStack->add('review', JS_REVIEW_TEXT);
    }

    if (($rating < 1) || ($rating > 5)) {
      $error = true;
      $messageStack->add('review', JS_REVIEW_RATING);
    }
	
	// start modification for reCaptcha
    if (RECAPTCHA_ON == 'true' && RECAPTCHA_PRODUCT_REVIEWS_WRITE == 'true') {
	  if (!$resp->is_valid) { 
	    $error = true;
        $messageStack->add('review', ENTRY_SECURITY_CHECK_ERROR);
      }
    }
    // end modification for reCaptcha

    if ($error == false) {
      tep_db_query("insert into " . TABLE_REVIEWS . " (products_id, customers_id, customers_name, reviews_rating, date_added) values ('" . (int)$_GET['products_id'] . "', '" . (int)$customer_id . "', '" . tep_db_input($customer['customers_firstname']) . ' ' . tep_db_input($customer['customers_lastname']) . "', '" . tep_db_input($rating) . "', now())");
      $insert_id = tep_db_insert_id();

      tep_db_query("insert into " . TABLE_REVIEWS_DESCRIPTION . " (reviews_id, languages_id, reviews_text) values ('" . (int)$insert_id . "', '" . (int)$languages_id . "', '" . tep_db_input($review) . "')");

      tep_redirect(tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params(array('action')) . 'message=display'));
    }
  }

// BOF: MOD - Separate Pricing Per Customer 
// deleted for Hide products and categories from groups
//  if(!tep_session_is_registered('sppc_customer_group_id')) { 
//  $customer_group_id = '0';
//  } else {
//   $customer_group_id = $sppc_customer_group_id;
//  }
  
     if ($customer_group_id !='0') {
	$customer_group_price_query = tep_db_query("select customers_group_price from " . TABLE_PRODUCTS_GROUPS . " where products_id = '" . (int)$_GET['products_id'] . "' and customers_group_id =  '" . $customer_group_id . "' and customers_group_price != null");
	  if ($customer_group_price = tep_db_fetch_array($customer_group_price_query)) {
	    $product_info['products_price'] = $customer_group_price['customers_group_price'];
	  }
     }
// EOF: MOD - Separate Pricing Per Customer
  if ($new_price = tep_get_products_special_price($product_info['products_id'])) {
    $products_price = '<span style="text-decoration:line-through">' . $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span> <span class="productSpecialPrice">' . $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span>';
  } else {
    $products_price = $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));
  }

  if (tep_not_null($product_info['products_model'])) {
    $products_name = $product_info['products_name'] . '<br><span class="smallText">[' . $product_info['products_model'] . ']</span>';
  } else {
    $products_name = $product_info['products_name'];
  }

  require(bts_select('language', FILENAME_PRODUCT_REVIEWS_WRITE));

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params()));

  $content = CONTENT_PRODUCT_REVIEWS_WRITE;
  $javascript = $content . '.js.php';

  include (bts_select('main')); // BTSv1.5


  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>