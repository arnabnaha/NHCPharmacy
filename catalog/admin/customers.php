<?php
/*
$Id: customers.php 1959 2013-03-05 17:01:31Z michael.oscmax@gmail.com $

  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  // +Country-State Selector
  $refresh = (isset($_POST['refresh']) ? $_POST['refresh'] : 'false');
  // -Country-State Selector

  $count_groups_array = array();
  $error = false;
  $processed = false;

  if (tep_not_null($action)) {
    switch ($action) {
	  case 'deladdress':
        if ((!isset($_GET['cID']) || !isset($_GET['add_id'])) && ($_GET['add_id'] != $cInfo->customers_default_address_id)) // if either id not set
          tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'add_id'))));

        $check_default_query = tep_db_query("select customers_default_address_id as defid from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$_GET['cID'] . "'");

        if ($default = tep_db_fetch_array($check_default_query)) {
          if ($_GET['add_id'] == $default['defid']) {// may not delete default address
            tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action')) . 'action=deladdress'));
          } else {
            tep_db_query("delete from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$_GET['cID'] . "' and address_book_id = '" . (int)$_GET['add_id'] . "'");
            tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'add_id')) . 'action=edit'));
          }
        } else { // no match on cID
          tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action', 'add_id'))));
        }
        break;
     case 'addaddress':
            $sql_data_array = array('customers_id' => $_GET['cID']);
            tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
            $get_address_id = tep_db_query("SELECT address_book_id FROM address_book WHERE customers_id = '" . (int)$_GET['cID'] . "' ORDER BY address_book_id DESC LIMIT 1"); 
            $address_id = tep_db_fetch_array($get_address_id);
            $new_address_id = $address_id['address_book_id'];
            tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action','add_id')) . 'cID=' . $_GET['cID'] . '&action=edit&add_id=' . $new_address_id));
            break;
	  case 'mailchimp':
	    include_once(DIR_WS_CLASSES . "MCAPI.class.php");
        $api = new MCAPI(MAILCHIMP_API);
        $list_id = MAILCHIMP_ID; 
		if (MAILCHIMP_LAST_SYNC == '') {
		  $last_update = null;
		} else {
		  $last_update = MAILCHIMP_LAST_SYNC;
		}
		//Check for subscribed customers
        $retval = $api->listMembers($list_id, 'subscribed', $last_update, 0, 5000 );
        if ($api->errorCode){
          echo "Unable to load listMembers()!";
          echo "\n\tCode=".$api->errorCode;
          echo "\n\tMsg=".$api->errorMessage."\n";
          echo "Members returned: ". sizeof($retval). "\n";
        } else {
          $sub_list_size = sizeof($retval);
          foreach($retval as $member) {
			// Now update local database to reflect mailchimp settings
			tep_db_query("update " . TABLE_CUSTOMERS . " set customers_newsletter = '1' where customers_email_address = '" . $member['email'] ."'");	
          }
        }
		
		//Check for unsubscribed customers
        $retval = $api->listMembers($list_id, 'unsubscribed', $last_update, 0, 5000 );
        if ($api->errorCode){
          echo "Unable to load listMembers()!";
          echo "\n\tCode=".$api->errorCode;
          echo "\n\tMsg=".$api->errorMessage."\n";
          echo "Members returned: ". sizeof($retval). "\n";
        } else {
          $unsub_list_size = sizeof($retval);
          foreach($retval as $member) {
			// Now update local database to reflect mailchimp settings
			tep_db_query("update " . TABLE_CUSTOMERS . " set customers_newsletter = '0' where customers_email_address = '" . $member['email'] ."'");
		  }
        }
		
		// Now set MailChimp configuration setting to reflect latest update
		tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $member['timestamp'] . "' where configuration_key = 'MAILCHIMP_LAST_SYNC'"); 			
	   	break;
// BOF : PGM EDITS CUSTOMER NOTES
	  case 'deletenotes':
	  	tep_db_query("DELETE FROM customers_notes WHERE customers_notes_id = ".$_GET["notesid"]." AND customers_id = ".$_GET["cID"]);
		tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $_GET["cID"] . '&action=notes'));
	  break;
	  case 'newnotes':
	  	tep_db_query("INSERT INTO customers_notes (customers_id, customers_notes_editor, customers_notes_message, customers_notes_date) VALUES (".$_GET["cID"].", '".$_POST["editor"]."', '".$_POST["message"]."', NOW())");
		tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $_GET["cID"] . '&action=default'));
	  break;
// EOF : PGM EDITS CUSTOMER NOTES
      case 'update':
        $customers_id = tep_db_prepare_input($_GET['cID']);
        $customers_firstname = tep_db_prepare_input($_POST['customers_firstname']);
        $customers_lastname = tep_db_prepare_input($_POST['customers_lastname']);
		$entry_firstname = tep_db_prepare_input($_POST['entry_firstname']);
        $entry_lastname = tep_db_prepare_input($_POST['entry_lastname']);
        $customers_email_address = tep_db_prepare_input($_POST['customers_email_address']);
        $customers_telephone = tep_db_prepare_input($_POST['customers_telephone']);
        $customers_fax = tep_db_prepare_input($_POST['customers_fax']);
        $customers_newsletter = tep_db_prepare_input($_POST['customers_newsletter']);
// BOF: MOD - Separate Pricing per Customer
        $customers_group_id = tep_db_prepare_input($_POST['customers_group_id']);
        $customers_group_ra = tep_db_prepare_input($_POST['customers_group_ra']);
        $entry_company_tax_id = tep_db_prepare_input($_POST['entry_company_tax_id']);
        if ($_POST['customers_payment_allowed'] && $_POST['customers_payment_settings'] == '1') {
          $customers_payment_allowed = tep_db_prepare_input($_POST['customers_payment_allowed']);
        } else { // no error with subsequent re-posting of variables
          $customers_payment_allowed = '';
          if ($_POST['payment_allowed'] && $_POST['customers_payment_settings'] == '1') {
            foreach ($_POST['payment_allowed'] as $val) {
              if ($val == true) {
                $customers_payment_allowed .= tep_db_prepare_input($val).';';
              }
            } // end while
            $customers_payment_allowed = substr($customers_payment_allowed,0,strlen($customers_payment_allowed)-1);
          } // end if ($_POST['payment_allowed'])
        } // end else ($_POST['customers_payment_allowed']
        if ($_POST['customers_shipment_allowed'] && $_POST['customers_shipment_settings'] == '1') {
          $customers_shipment_allowed = tep_db_prepare_input($_POST['customers_shipment_allowed']);
        } else { // no error with subsequent re-posting of variables

          $customers_shipment_allowed = '';
          if ($_POST['shipping_allowed'] && $_POST['customers_shipment_settings'] == '1') {
            foreach ($_POST['shipping_allowed'] as $val) {
              if ($val == true) {
                $customers_shipment_allowed .= tep_db_prepare_input($val).';';
              }
            } // end while
            $customers_shipment_allowed = substr($customers_shipment_allowed,0,strlen($customers_shipment_allowed)-1);
          } // end if ($_POST['shipment_allowed'])
        } // end else ($_POST['customers_shipment_allowed']
// EOF: MOD - Separate Pricing per Customer
        $customers_gender = tep_db_prepare_input($_POST['customers_gender']);
        $customers_dob = tep_db_prepare_input($_POST['customers_dob']);

        $default_address_id = (isset($_GET['add_id']) ? tep_db_prepare_input($_GET['add_id']) : tep_db_prepare_input($_POST['default_address_id']));
        $entry_street_address = tep_db_prepare_input($_POST['entry_street_address']);
        $entry_suburb = tep_db_prepare_input($_POST['entry_suburb']);
        $entry_postcode = tep_db_prepare_input($_POST['entry_postcode']);
        $entry_city = tep_db_prepare_input($_POST['entry_city']);
        $entry_country_id = tep_db_prepare_input($_POST['entry_country_id']);

        $entry_company = tep_db_prepare_input($_POST['entry_company']);
        $entry_state = tep_db_prepare_input($_POST['entry_state']);
        // +Country-State Selector
        if (isset($_POST['entry_zone_id'])) {
           $entry_zone_id = tep_db_prepare_input($_POST['entry_zone_id']);
        } else {
           $entry_zone_id = 0;
        }
        if ($refresh != 'true') {
        // -Country-State Selector

// Admin edit any customer address begin
        if (strlen($customers_firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
          $error = true;
          $customers_firstname_error = true;
        } else {
          $customers_firstname_error = false;
        }

        if (strlen($customers_lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
          $error = true;
          $customers_lastname_error = true;
        } else {
          $customers_lastname_error = false;
        }

        if (strlen($entry_firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
          $error = true;
          $entry_firstname_error = true;
        } else {
          $entry_firstname_error = false;
        }

        if (strlen($entry_lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
          $error = true;
          $entry_lastname_error = true;
        } else {
          $entry_lastname_error = false;
        }
// Admin edit any customer address end

        if (ACCOUNT_DOB == 'true') {
          if (checkdate(substr(tep_date_raw($customers_dob), 4, 2), substr(tep_date_raw($customers_dob), 6, 2), substr(tep_date_raw($customers_dob), 0, 4))) {
            $entry_date_of_birth_error = false;
          } else {
            $error = true;
            $entry_date_of_birth_error = true;
          }
        }

        if (strlen($customers_email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
          $error = true;
          $entry_email_address_error = true;
        } else {
          $entry_email_address_error = false;
        }

        if (!tep_validate_email($customers_email_address)) {
          $error = true;
          $entry_email_address_check_error = true;
        } else {
          $entry_email_address_check_error = false;
        }

        if (strlen($entry_street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
          $error = true;
          $entry_street_address_error = true;
        } else {
          $entry_street_address_error = false;
        }

        if (strlen($entry_postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
          $error = true;
          $entry_post_code_error = true;
        } else {
          $entry_post_code_error = false;
        }

        if (strlen($entry_city) < ENTRY_CITY_MIN_LENGTH) {
          $error = true;
          $entry_city_error = true;
        } else {
          $entry_city_error = false;
        }

        if ($entry_country_id == false) {
          $error = true;
          $entry_country_error = true;
        } else {
          $entry_country_error = false;
        }

      if (strlen($customers_telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
        $error = true;
        $entry_telephone_error = true;
      } else {
        $entry_telephone_error = false;
      }
	  
	  if ($_POST['customers_new_password'] != $_POST['customers_repeat_password']) {
        $error = true;
        $entry_password_error = true;
      } else {
        $entry_password_error = false;
      }

      // BOF Customers extra fields
      $extra_fields_query = tep_db_query("select ce.fields_id, ce.fields_input_type, ce.fields_required_status, cei.fields_name, ce.fields_status, ce.fields_input_type, ce.fields_size from " . TABLE_EXTRA_FIELDS . " ce, " . TABLE_EXTRA_FIELDS_INFO . " cei where ce.fields_status=1 and ce.fields_required_status=1 and cei.fields_id=ce.fields_id and cei.languages_id =" . $languages_id);
        while($extra_fields = tep_db_fetch_array($extra_fields_query)){
          if (strlen($_POST['fields_' . $extra_fields['fields_id']])<$extra_fields['fields_size']) {
            $error = true;
            $string_error = sprintf(ENTRY_EXTRA_FIELDS_ERROR, $extra_fields['fields_name'], $extra_fields['fields_size']);
            $messageStack->add($string_error);
          }
        }
      // EOF Customers extra fields

      $check_email = tep_db_query("select customers_email_address from " . TABLE_CUSTOMERS . " where customers_email_address = '" . tep_db_input($customers_email_address) . "' and customers_id != '" . (int)$customers_id . "'");
      if (tep_db_num_rows($check_email)) {
        $error = true;
        $entry_email_address_exists = true;
      } else {
        $entry_email_address_exists = false;
      }
// +Country-State Selector	  
	}  // End if (!$refresh)	
      if (($error == false) && ($refresh != 'true')) {
// -Country-State Selector
        $sql_data_array = array('customers_firstname' => $customers_firstname,
                                'customers_lastname' => $customers_lastname,
                                'customers_email_address' => $customers_email_address,
                                'customers_telephone' => $customers_telephone,
                                'customers_fax' => $customers_fax,
                                'customers_newsletter' => $customers_newsletter,
// BOF: MOD - Separate Pricing per Customer
                                'customers_group_id' => $customers_group_id,
                                'customers_group_ra' => $customers_group_ra,
                                'customers_payment_allowed' => $customers_payment_allowed,
                                'customers_shipment_allowed' => $customers_shipment_allowed);
// EOF: MOD - Separate Pricing per Customer


		if(isset($_POST['setdefault'])) $sql_data_array['customers_default_address_id'] = $default_address_id;
        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $customers_gender;
        if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($customers_dob);

        tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" . (int)$customers_id . "'");

        tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now() where customers_info_id = '" . (int)$customers_id . "'");

        if ($entry_zone_id > 0) $entry_state = '';

        $sql_data_array = array('entry_firstname' => $entry_firstname,
                                'entry_lastname' => $entry_lastname,
                                'entry_street_address' => $entry_street_address,
                                'entry_postcode' => $entry_postcode,
                                'entry_city' => $entry_city,
                                'entry_country_id' => $entry_country_id);
// BOF: MOD - Separate Pricing per Customer
//      if  (ACCOUNT_COMPANY  ==  'true')  $sql_data_array['entry_company']  =  $entry_company;
        if (ACCOUNT_COMPANY == 'true') {
        $sql_data_array['entry_company'] = $entry_company;
         $sql_data_array['entry_company_tax_id'] = $entry_company_tax_id;
        }
// EOF: MOD - Separate Pricing per Customer
        if (ACCOUNT_SUBURB == 'true') $sql_data_array['entry_suburb'] = $entry_suburb;

        if (ACCOUNT_STATE == 'true') {
          if ($entry_zone_id > 0) {
            $sql_data_array['entry_zone_id'] = $entry_zone_id;
            $sql_data_array['entry_state'] = '';
          } else {
            $sql_data_array['entry_zone_id'] = '0';
            $sql_data_array['entry_state'] = $entry_state;
          }
        }

        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "customers_id = '" . (int)$customers_id . "' and address_book_id = '" . (int)$default_address_id . "'");
		
		// BOF Customers extra fields
        tep_db_query("delete from " . TABLE_CUSTOMERS_TO_EXTRA_FIELDS . " where customers_id=" . (int)$customers_id);
   	  	  $extra_fields_query = tep_db_query("select ce.fields_id from " . TABLE_EXTRA_FIELDS . " ce where ce.fields_status=1 ");
    	  while ($extra_fields = tep_db_fetch_array($extra_fields_query)) {
		    if(isset($_POST['fields_' . $extra_fields['fields_id']])) {
              $sql_data_array = array('customers_id' => (int)$customers_id,
                                      'fields_id' => $extra_fields['fields_id'],
                                      'value' => $_POST['fields_' . $extra_fields['fields_id']]);
       		} else {
			  $sql_data_array = array('customers_id' => (int)$customers_id,
                                      'fields_id' => $extra_fields['fields_id'],
                                      'value' => '');
			  $is_add = false;
			  for ($i = 1; $i <= $_POST['fields_' . $extra_fields['fields_id'] . '_total']; $i++) {
			    if(isset($_POST['fields_' . $extra_fields['fields_id'] . '_' . $i])) {
				  if($is_add) {
                    $sql_data_array['value'] .= "\n";
				  } else {
                    $is_add = true;
				  }
              	$sql_data_array['value'] .= $_POST['fields_' . $extra_fields['fields_id'] . '_' . $i];
				}
			  }
			}
			tep_db_perform(TABLE_CUSTOMERS_TO_EXTRA_FIELDS, $sql_data_array);
      	  }
         // EOF Customers extra fields
		
		// Password update fields
		if ($_POST['customers_new_password'] == $_POST['customers_repeat_password'] && $_POST['customers_new_password'] != '') {
		  tep_db_query("update " . TABLE_CUSTOMERS . " set customers_password='" . tep_encrypt_password(tep_db_prepare_input($_POST['customers_new_password'])) . "' WHERE customers_id='" . (int)$customers_id . "'");  
        }
		
        //tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action', 'add_id')) . 'cID=' . $customers_id));
		
		tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action')) . 'action=edit'));

        } else if ($error == true) {
          $cInfo = new objectInfo($_POST);
          $processed = true;
        // +Country-State Selector
        } else if ($refresh == 'true') {
          $cInfo = new objectInfo($_POST);
        }
        // -Country-State Selector

        break;
      case 'deleteconfirm':
        $customers_id = tep_db_prepare_input($_GET['cID']);

        if (isset($_POST['delete_reviews']) && ($_POST['delete_reviews'] == 'on')) {
          $reviews_query = tep_db_query("select reviews_id from " . TABLE_REVIEWS . " where customers_id = '" . (int)$customers_id . "'");
          while ($reviews = tep_db_fetch_array($reviews_query)) {
            tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . (int)$reviews['reviews_id'] . "'");
          }

          tep_db_query("delete from " . TABLE_REVIEWS . " where customers_id = '" . (int)$customers_id . "'");
        } else {
          tep_db_query("update " . TABLE_REVIEWS . " set customers_id = null where customers_id = '" . (int)$customers_id . "'");
        }
// BOF: MOD - Separate Pricing per Customer
// Once all customers with a specific customers_group_id have been deleted from
// the table customers, the next time a customer is deleted, all entries in the table products_groups
// that have the (now apparently obsolete) customers_group_id will be deleted!
// If you don't want that, leave this section out, or comment it out
// Note that when customers groups are deleted from the table customers_groups, all the
// customers with that specific customer_group_id will be changed to customer_group_id = '0' (default/Retail)
        $multiple_groups_query = tep_db_query("select customers_group_id from " . TABLE_CUSTOMERS_GROUPS . " ");
        while ($group_ids = tep_db_fetch_array($multiple_groups_query)) {
          $multiple_customers_query = tep_db_query("select distinct customers_group_id from " . TABLE_CUSTOMERS . " where customers_group_id = " . $group_ids['customers_group_id'] . " ");
          if (!($multiple_groups = tep_db_fetch_array($multiple_customers_query))) {
            tep_db_query("delete from " . TABLE_PRODUCTS_GROUPS . " where customers_group_id = '" . $group_ids['customers_group_id'] . "'");
          }
        }
// EOF: MOD - Separate Pricing per Customer

        tep_db_query("delete from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customers_id . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customers_id . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . (int)$customers_id . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customers_id . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customers_id . "'");
        tep_db_query("delete from " . TABLE_WHOS_ONLINE . " where customer_id = '" . (int)$customers_id . "'");

        tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action'))));
        break;
      default:
// BOF: MOD - Separate Pricing per Customer
//  old    $customers_query  =  tep_db_query("select  c.customers_id,  c.customers_gender,  c.customers_firstname,  c.customers_lastname,  c.customers_dob,  c.customers_email_address,  a.entry_company,  a.entry_street_address,  a.entry_suburb,  a.entry_postcode,  a.entry_city,  a.entry_state,  a.entry_zone_id,  a.entry_country_id,  c.customers_telephone,  c.customers_fax,  c.customers_newsletter,  c.customers_default_address_id  from  "  .  TABLE_CUSTOMERS  .  "  c  left  join  "  .  TABLE_ADDRESS_BOOK  .  "  a  on  c.customers_default_address_id  =  a.address_book_id  where  a.customers_id  =  c.customers_id  and  c.customers_id  =  '"  .  (int)$_GET['cID']  .  "'");
                $customers_query = tep_db_query("select c.customers_id, c.customers_gender, c.customers_firstname, c.customers_lastname, c.customers_dob, c.customers_email_address, a.entry_firstname, a.entry_lastname, a.entry_company, a.entry_company_tax_id, a.entry_street_address, a.entry_suburb, a.entry_postcode, a.entry_city, a.entry_state, a.entry_zone_id, a.entry_country_id, c.customers_telephone, c.customers_fax, c.customers_newsletter, c.customers_group_id, c.customers_group_ra, c.customers_payment_allowed, c.customers_shipment_allowed, c.customers_default_address_id from " . TABLE_CUSTOMERS . " c left join " . TABLE_ADDRESS_BOOK . " a on a.address_book_id = " . (isset($_GET['add_id']) ? (int)$_GET['add_id'] : 'c.customers_default_address_id') . " where a.customers_id = c.customers_id and c.customers_id = '" . (int)$_GET['cID'] . "'");

//        $customers = tep_db_fetch_array($customers_query);
//        $cInfo = new objectInfo($customers);

        $customer_address_query = tep_db_query("select address_book_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$_GET['cID'] . "'");
        $aid_list = array();
        while ($a = tep_db_fetch_array($customer_address_query))
          { $aid_list[] = $a['address_book_id'];}

        $module_directory = DIR_FS_CATALOG_MODULES . 'payment/';
        $ship_module_directory = DIR_FS_CATALOG_MODULES . 'shipping/';

        $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
        $directory_array = array();
        if ($dir = @dir($module_directory)) {
        while ($file = $dir->read()) {
        if (!is_dir($module_directory . $file)) {
           if (substr($file, strrpos($file, '.')) == $file_extension) {
              $directory_array[] = $file; // array of all the payment modules present in includes/modules/payment
                  }
               }
            }
        sort($directory_array);
        $dir->close();
        }

        $ship_directory_array = array();
        if ($dir = @dir($ship_module_directory)) {
        while ($file = $dir->read()) {
        if (!is_dir($ship_module_directory . $file)) {
           if (substr($file, strrpos($file, '.')) == $file_extension) {
              $ship_directory_array[] = $file; // array of all shipping modules present in includes/modules/shipping
                }
              }
            }
            sort($ship_directory_array);
            $dir->close();
        }
        $existing_customers_query = tep_db_query("select customers_group_id, customers_group_name from " . TABLE_CUSTOMERS_GROUPS . " order by customers_group_id ");
// EOF: MOD - Separate Pricing per Customer
        $customers = tep_db_fetch_array($customers_query);
        $cInfo = new objectInfo($customers);
// BOF: MOD - Separate Pricing per Customer
//      $shipment_allowed = explode (";",$cInfo->customers_shipment_allowed);
// EOF: MOD - Separate Pricing per Customer
    }
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/javascript/jquery-ui-1.8.2.custom.css">
<script type="text/javascript" src="includes/general.js"></script>
<?php
  // +Country-State Selector
  if ($refresh == 'true') {
		$entry_state = '';
		$cInfo->entry_state = '';
  }
  // -Country-State Selector
  if ($action == 'edit' || $action == 'update') {
?>
<script type="text/javascript"><!--

function check_form() {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";

  var customers_firstname = document.customers.customers_firstname.value;
  var customers_lastname = document.customers.customers_lastname.value;
/* Admin edit any customer address */
  var entry_firstname = document.customers.entry_firstname.value;
  var entry_lastname = document.customers.entry_lastname.value;
/* Admin edit any customer address */
<?php if (ACCOUNT_COMPANY == 'true') echo 'var entry_company = document.customers.entry_company.value;' . "\n"; ?>
<?php if (ACCOUNT_DOB == 'true') echo 'var customers_dob = document.customers.customers_dob.value;' . "\n"; ?>
  var customers_email_address = document.customers.customers_email_address.value;
  var entry_street_address = document.customers.entry_street_address.value;
  var entry_postcode = document.customers.entry_postcode.value;
  var entry_city = document.customers.entry_city.value;
  var customers_telephone = document.customers.customers_telephone.value;

<?php if (ACCOUNT_GENDER == 'true') { ?>
  if (document.customers.customers_gender[0].checked || document.customers.customers_gender[1].checked) {
  } else {
    error_message = error_message + "<?php echo JS_GENDER; ?>";
    error = 1;
  }
<?php } ?>

  if (customers_firstname.length < <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_FIRST_NAME; ?>";
    error = 1;
  }

  if (customers_lastname.length < <?php echo ENTRY_LAST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_LAST_NAME; ?>";
    error = 1;
  }
  
/* Admin edit any customer address */
  if (entry_firstname.length < <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_FIRST_NAME; ?>";
    error = 1;
  }

  if (entry_lastname.length < <?php echo ENTRY_LAST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_LAST_NAME; ?>";
    error = 1;
  }
/* Admin edit any customer address */

<?php if (ACCOUNT_DOB == 'true') { ?>
  if (customers_dob.length < <?php echo ENTRY_DOB_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_DOB; ?>";
    error = 1;
  }
<?php } ?>

  if (customers_email_address.length < <?php echo ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_EMAIL_ADDRESS; ?>";
    error = 1;
  }
  
  if (document.customers.customers_new_password.value != document.customers.customers_repeat_password.value) {
    error_message = error_message + "<?php echo JS_PASSWORD_DONT_MATCH; ?>";
    error = 1;
  }

  if (entry_street_address.length < <?php echo ENTRY_STREET_ADDRESS_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_ADDRESS; ?>";
    error = 1;
  }

  if (entry_postcode.length < <?php echo ENTRY_POSTCODE_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_POST_CODE; ?>";
    error = 1;
  }

  if (entry_city.length < <?php echo ENTRY_CITY_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_CITY; ?>";
    error = 1;
  }

<?php
  if (ACCOUNT_STATE == 'true') {
?>
  if (document.customers.elements['entry_state'].type != "hidden") {
    if (document.customers.entry_state.value.length < <?php echo ENTRY_STATE_MIN_LENGTH; ?>) {
       error_message = error_message + "<?php echo JS_STATE; ?>";
       error = 1;
    }
  }
<?php
  }
?>

  if (document.customers.elements['entry_country_id'].type != "hidden") {
    if (document.customers.entry_country_id.value == 0) {
      error_message = error_message + "<?php echo JS_COUNTRY; ?>";
      error = 1;
    }
  }

  if (customers_telephone.length < <?php echo ENTRY_TELEPHONE_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_TELEPHONE; ?>";
    error = 1;
  }

  if (error == 1) {
    alert(error_message);
    return false;
  } else {
    return true;
  }
}
//+ Country-State Selector
function refresh_form(form_name) {
   form_name.refresh.value = 'true';
   form_name.submit();
   return true;
}
 //- Country-State Selector
//--></script>
<?php
  }
?>
</head>
<body onLoad="document.search_customers.search.focus(); SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top">
      <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
      <!-- left_navigation //-->
      <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      <!-- left_navigation_eof //-->
      </table>
    </td>
<!-- body_text //-->
    <td width="100%" valign="top">
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ($action == 'edit' || $action == 'update') {
    $newsletter_array = array(array('id' => '1', 'text' => ENTRY_NEWSLETTER_YES),
                              array('id' => '0', 'text' => ENTRY_NEWSLETTER_NO));
?>

        <tr>
          <td><?php echo tep_draw_form('customers', FILENAME_CUSTOMERS, tep_get_all_get_params(array('action')) . 'action=update', 'post', 'onSubmit="return check_form();"') . tep_draw_hidden_field('default_address_id', $cInfo->customers_default_address_id); ?>
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                <td class="pageHeading" align="right">&nbsp;</td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr><?php echo tep_draw_form('customers', FILENAME_CUSTOMERS, tep_get_all_get_params(array('action')) . 'action=update', 'post', 'onSubmit="return check_form();"') . tep_draw_hidden_field('default_address_id', $cInfo->customers_default_address_id); ?>
         <?php
          // +Country-State Selector
         echo tep_draw_hidden_field('refresh','false'); 
         // -Country-State Selector
         ?>
                <td class="formAreaTitle"><?php echo CATEGORY_PERSONAL; ?></td>
              </tr>
              <tr>
                <td class="formArea">
                  <table border="0" cellspacing="2" cellpadding="2">
<?php
    if (ACCOUNT_GENDER == 'true') {
?>
              <tr>
                <td class="main" width="150"><?php echo ENTRY_GENDER; ?></td>
                <td class="main">
<?php
    if ($error == true) {
      if ($entry_gender_error == true) {
        echo tep_draw_radio_field('customers_gender', 'm', false, $cInfo->customers_gender) . '&nbsp;&nbsp;' . MALE . '&nbsp;&nbsp;' . tep_draw_radio_field('customers_gender', 'f', false, $cInfo->customers_gender) . '&nbsp;&nbsp;' . FEMALE . '&nbsp;' . ENTRY_GENDER_ERROR;
      } else {
        echo ($cInfo->customers_gender == 'm') ? MALE : FEMALE;
        echo tep_draw_hidden_field('customers_gender');
      }
    } else {
      echo tep_draw_radio_field('customers_gender', 'm', false, $cInfo->customers_gender) . '&nbsp;&nbsp;' . MALE . '&nbsp;&nbsp;' . tep_draw_radio_field('customers_gender', 'f', false, $cInfo->customers_gender) . '&nbsp;&nbsp;' . FEMALE;
    }
?></td>
              </tr>
<?php
    }
?>
              <tr>
                <td class="main" width="150"><?php echo ENTRY_FIRST_NAME; ?></td>
                <td class="main">
<?php
  if ($error == true) {
    if ($entry_firstname_error == true) {
      echo tep_draw_input_field('customers_firstname', $cInfo->customers_firstname, 'maxlength="32"') . '&nbsp;' . ENTRY_FIRST_NAME_ERROR;
    } else {
      echo $cInfo->customers_firstname . tep_draw_hidden_field('customers_firstname');
    }
  } else {
    echo tep_draw_input_field('customers_firstname', $cInfo->customers_firstname, 'maxlength="32"', true);
  }
?></td>
              </tr>
              <tr>
                <td class="main" width="150"><?php echo ENTRY_LAST_NAME; ?></td>
                <td class="main">
<?php
  if ($error == true) {
    if ($entry_lastname_error == true) {
      echo tep_draw_input_field('customers_lastname', $cInfo->customers_lastname, 'maxlength="32"') . '&nbsp;' . ENTRY_LAST_NAME_ERROR;
    } else {
      echo $cInfo->customers_lastname . tep_draw_hidden_field('customers_lastname');
    }
  } else {
    echo tep_draw_input_field('customers_lastname', $cInfo->customers_lastname, 'maxlength="32"', true);
  }
?></td>
              </tr>
<?php
    if (ACCOUNT_DOB == 'true') {
?>
              <tr>
                <td class="main" width="150"><?php echo ENTRY_DATE_OF_BIRTH; ?></td>
                <td class="main">

<?php
    if ($error == true) {
      if ($entry_date_of_birth_error == true) {
        echo tep_draw_input_field('customers_dob', tep_date_short($cInfo->customers_dob), 'maxlength="10"') . '&nbsp;' . ENTRY_DATE_OF_BIRTH_ERROR;
      } else {
        echo $cInfo->customers_dob . tep_draw_hidden_field('customers_dob');
      }
    } else {
      echo tep_draw_input_field('customers_dob', tep_date_short($cInfo->customers_dob), 'maxlength="10"', true);
    }
?></td>
              </tr>
<?php
    }
?>
              <tr>
                <td class="main" width="150"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
                <td class="main">
<?php
  if ($error == true) {
    if ($entry_email_address_error == true) {
      echo tep_draw_input_field('customers_email_address', $cInfo->customers_email_address, 'maxlength="96"') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR;
    } elseif ($entry_email_address_check_error == true) {
      echo tep_draw_input_field('customers_email_address', $cInfo->customers_email_address, 'maxlength="96"') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
    } elseif ($entry_email_address_exists == true) {
      echo tep_draw_input_field('customers_email_address', $cInfo->customers_email_address, 'maxlength="96"') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
    } else {
      echo $customers_email_address . tep_draw_hidden_field('customers_email_address');
    }
  } else {
    echo tep_draw_input_field('customers_email_address', $cInfo->customers_email_address, 'maxlength="96"', true);
  }
?></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td class="formAreaTitle"><?php echo CATEGORY_PASSWORD; ?></td>
        </tr>
        <tr>
          <td class="formArea">
            <table border="0" cellspacing="2" cellpadding="2"> 
              <tr>
                <td class="main" width="150"><?php echo ENTRY_PASSWORD; ?></td>
                <td class="main"><?php echo tep_draw_input_field('customers_new_password', ''); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo ENTRY_PASSWORD_CONFIRMATION; ?></td>
                <td class="main"><?php echo tep_draw_input_field('customers_repeat_password', ''); ?></td>
              </tr>
            </table>
          </td>
        </tr>
<?php
    if (ACCOUNT_COMPANY == 'true') {
?>
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td class="formAreaTitle"><?php echo CATEGORY_COMPANY; ?></td>
        </tr>
        <tr>
          <td class="formArea">
            <table border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td class="main" width="150"><?php echo ENTRY_COMPANY; ?></td>
                <td class="main">
<?php
    if ($error == true) {
      if ($entry_company_error == true) {
        echo tep_draw_input_field('entry_company', $cInfo->entry_company, 'maxlength="32"') . '&nbsp;' . ENTRY_COMPANY_ERROR;
      } else {
        echo $cInfo->entry_company . tep_draw_hidden_field('entry_company');
      }
    } else {
      echo tep_draw_input_field('entry_company', $cInfo->entry_company, 'maxlength="32"');
    }
?></td>
<?php // BOF: MOD - Separate Pricing per Customer ?>
              </tr>
              <tr>
                <td class="main" width="150"><?php echo ENTRY_COMPANY_TAX_ID; ?></td>
                <td class="main">
<?php
    if ($error == true) {
      if ($entry_company_tax_id_error == true) {
        echo tep_draw_input_field('entry_company_tax_id', $cInfo->entry_company_tax_id, 'maxlength="32"') . '&nbsp;' . ENTRY_COMPANY_TAX_ID_ERROR;
      } else {
        echo $cInfo->entry_company . tep_draw_hidden_field('entry_company_tax_id');
      }
    } else {
      echo tep_draw_input_field('entry_company_tax_id', $cInfo->entry_company_tax_id, 'maxlength="32"');
      }
?></td>
              </tr>
              <tr>
                <td class="main" width="150"><?php echo ENTRY_CUSTOMERS_GROUP_REQUEST_AUTHENTICATION; ?></td>
                <td class="main">
<?php
    if ($error == true) {
      if ($customers_group_ra_error == true) {
        echo tep_draw_radio_field('customers_group_ra', '0', false, $cInfo->customers_group_ra) . '&nbsp;&nbsp;' . ENTRY_CUSTOMERS_GROUP_RA_NO . '&nbsp;&nbsp;' . tep_draw_radio_field('customers_group_ra', '1', false, $cInfo->customers_group_ra) . '&nbsp;&nbsp;' . ENTRY_CUSTOMERS_GROUP_RA_YES . '&nbsp;' . ENTRY_CUSTOMERS_GROUP_RA_ERROR;
      } else {
        echo ($cInfo->customers_group_ra == '0') ? ENTRY_CUSTOMERS_GROUP_RA_NO : ENTRY_CUSTOMERS_GROUP_RA_YES;
        echo tep_draw_hidden_field('customers_group_ra');
      }
    } else {
      echo tep_draw_radio_field('customers_group_ra', '0', false, $cInfo->customers_group_ra) . '&nbsp;&nbsp;' . ENTRY_CUSTOMERS_GROUP_RA_NO . '&nbsp;&nbsp;' . tep_draw_radio_field('customers_group_ra', '1', false, $cInfo->customers_group_ra) . '&nbsp;&nbsp;' . ENTRY_CUSTOMERS_GROUP_RA_YES;
    }
?></td>
              </tr>
<?php // EOF: MOD - Separate Pricing per Customer ?>
            </table>
          </td>
        </tr>
<?php
    }
?>
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>        
        <tr>
          <td>
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
              <tr>
                <td class="formAreaTitle" valign="bottom"><?php echo CATEGORY_ADDRESS; ?></td>
                <td class="main" align="right">
                  <?php      
  //Admin edit any customer address begin
  if ($action != 'update') { // only display if no update entry error
?>

<form>
<?php

$afl = array();
foreach ($aid_list as $a) {
  $afl[] = array('id' => tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('add_id')) . 'add_id=' . $a), 'text' => $a);
}

  echo SELECT_ADDRESS . tep_draw_pull_down_menu('add_id', $afl, tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('add_id')) . 'add_id=' . (isset($_GET['add_id']) ? $_GET['add_id'] : $cInfo->customers_default_address_id)), 'ONCHANGE="location = this.options[this.selectedIndex].value;"'); ?>
</form>

<?php
if (isset($_GET['add_id']) && ($_GET['add_id'] != $cInfo->customers_default_address_id)) { // if not default address
  echo '<a href="' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=deladdress') . '">' . tep_image_button('button_delete.gif', DELETE_ADDRESS) . '</a>';
} 
?>

<?php
  }
//Admin edit any customer address end
?>

				<?php 
				echo '<a href="' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&amp;action=addaddress') . '">' . tep_image_button('button_new.gif', IMAGE_NEW) . '</a>'; 
				?>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td class="formArea">
            <table border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td class="main"><?php echo ENTRY_FIRST_NAME; ?></td>
                <td class="main">

<?php
  if ($error == true) {
    if ($entry_firstname_error == true) {
      echo tep_draw_input_field('entry_firstname', $cInfo->entry_firstname, 'maxlength="32"') . '&nbsp;' . ENTRY_FIRST_NAME_ERROR;
    } else {
      echo $cInfo->entry_firstname . tep_draw_hidden_field('entry_firstname');
    }
  } else {
    echo tep_draw_input_field('entry_firstname', $cInfo->entry_firstname, 'maxlength="32"', true);
  }
?>
			    </td>
              </tr>
              <tr>
                <td class="main"><?php echo ENTRY_LAST_NAME; ?></td>
                <td class="main">

<?php
  if ($error == true) {
    if ($entry_lastname_error == true) {
      echo tep_draw_input_field('entry_lastname', $cInfo->entry_lastname, 'maxlength="32"') . '&nbsp;' . ENTRY_LAST_NAME_ERROR;
    } else {
      echo $cInfo->entry_lastname . tep_draw_hidden_field('entry_lastname');
    }
  } else {
    echo tep_draw_input_field('entry_lastname', $cInfo->entry_lastname, 'maxlength="32"', true);
  }
?></td>

              </tr>
              <tr>
                <td class="main" width="150"><?php echo ENTRY_STREET_ADDRESS; ?></td>
                <td class="main">
<?php
  if ($error == true) {
    if ($entry_street_address_error == true) {
      echo tep_draw_input_field('entry_street_address', $cInfo->entry_street_address, 'maxlength="64"') . '&nbsp;' . ENTRY_STREET_ADDRESS_ERROR;
    } else {
      echo $cInfo->entry_street_address . tep_draw_hidden_field('entry_street_address');
    }
  } else {
    echo tep_draw_input_field('entry_street_address', $cInfo->entry_street_address, 'maxlength="64"', true);
  }
?></td>
              </tr>
<?php
    if (ACCOUNT_SUBURB == 'true') {
?>
              <tr>
                <td class="main" width="150"><?php echo ENTRY_SUBURB; ?></td>
                <td class="main">
<?php
    if ($error == true) {
      if ($entry_suburb_error == true) {
        echo tep_draw_input_field('suburb', $cInfo->entry_suburb, 'maxlength="32"') . '&nbsp;' . ENTRY_SUBURB_ERROR;
      } else {
        echo $cInfo->entry_suburb . tep_draw_hidden_field('entry_suburb');
      }
    } else {
      echo tep_draw_input_field('entry_suburb', $cInfo->entry_suburb, 'maxlength="32"');
    }
?></td>
              </tr>
<?php
    }
?>
              <tr>
                <td class="main" width="150"><?php echo ENTRY_POST_CODE; ?></td>
                <td class="main">
<?php
  if ($error == true) {
    if ($entry_post_code_error == true) {
      echo tep_draw_input_field('entry_postcode', $cInfo->entry_postcode, 'maxlength="8"') . '&nbsp;' . ENTRY_POST_CODE_ERROR;
    } else {
      echo $cInfo->entry_postcode . tep_draw_hidden_field('entry_postcode');
    }
  } else {
    echo tep_draw_input_field('entry_postcode', $cInfo->entry_postcode, 'maxlength="8"', true);
  }
?></td>
              </tr>
              <tr>
                <td class="main" width="150"><?php echo ENTRY_CITY; ?></td>
                <td class="main">
<?php
  if ($error == true) {
    if ($entry_city_error == true) {
      echo tep_draw_input_field('entry_city', $cInfo->entry_city, 'maxlength="32"') . '&nbsp;' . ENTRY_CITY_ERROR;
    } else {
      echo $cInfo->entry_city . tep_draw_hidden_field('entry_city');
    }
  } else {
    echo tep_draw_input_field('entry_city', $cInfo->entry_city, 'maxlength="32"', true);
  }
?></td>
              </tr>
<?php
    if (ACCOUNT_STATE == 'true') {
?>
              <tr>
                <td class="main" width="150"><?php echo ENTRY_STATE; ?></td>
                <td class="main">
<?php
     // +Country-State Selector
     $entry_state = tep_get_zone_name($cInfo->entry_country_id, $cInfo->entry_zone_id, $cInfo->entry_state);
     $zones_array = array();
     $zones_query = tep_db_query("select zone_name, zone_id from " . TABLE_ZONES . " where zone_country_id = '" . (int)$cInfo->entry_country_id . "' order by zone_name");
     while ($zones_values = tep_db_fetch_array($zones_query)) {
        $zones_array[] = array('id' => $zones_values['zone_id'], 'text' => $zones_values['zone_name']);
     }
       if (count($zones_array) > 0) {
         echo tep_draw_pull_down_menu('entry_zone_id', $zones_array, $cInfo->entry_zone_id);
         echo tep_draw_hidden_field('entry_state', '');
      } else {
         echo tep_draw_input_field('entry_state', $entry_state);
      }
      // -Country-State Selector
?></td>
              </tr>
<?php
    }
?>
              <tr>
                <td class="main" width="150"><?php echo ENTRY_COUNTRY; ?></td>
                <td class="main">
<?php
// +Country-State Selector
echo css_get_country_list('entry_country_id',  $cInfo->entry_country_id,'onChange="return refresh_form(customers);"');
// -Country-State Selector
?></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td class="formAreaTitle"><?php echo CATEGORY_CONTACT; ?></td>
        </tr>
        <tr>
          <td class="formArea">
            <table border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td class="main" width="150"><?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
                <td class="main">
<?php
  if ($error == true) {
    if ($entry_telephone_error == true) {
      echo tep_draw_input_field('customers_telephone', $cInfo->customers_telephone, 'maxlength="32"') . '&nbsp;' . ENTRY_TELEPHONE_NUMBER_ERROR;
    } else {
      echo $cInfo->customers_telephone . tep_draw_hidden_field('customers_telephone');
    }
  } else {
    echo tep_draw_input_field('customers_telephone', $cInfo->customers_telephone, 'maxlength="32"', true);
  }
?></td>
              </tr>
              <tr>
                <td class="main" width="150"><?php echo ENTRY_FAX_NUMBER; ?></td>
                <td class="main">
<?php
  if ($processed == true) {
    echo $cInfo->customers_fax . tep_draw_hidden_field('customers_fax');
  } else {
    echo tep_draw_input_field('customers_fax', $cInfo->customers_fax, 'maxlength="32"');
  }
?></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <!-- // BOF Customers extra fields -->
        <?php echo tep_get_extra_fields($cInfo->customers_id, $languages_id, $cInfo->customers_group_id); ?>
        <!-- // EOF Customers extra fields -->
        <tr>
          <td class="formAreaTitle"><?php echo CATEGORY_OPTIONS; ?></td>
        </tr>
        <tr>
          <td class="formArea">
            <table border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td class="main" width="150"><?php echo ENTRY_NEWSLETTER; ?></td>
                <td class="main">
<?php
  if ($processed == true) {
    if ($cInfo->customers_newsletter == '1') {
      echo ENTRY_NEWSLETTER_YES;
    } else {
      echo ENTRY_NEWSLETTER_NO;
    }
    echo tep_draw_hidden_field('customers_newsletter');
  } else {
    echo tep_draw_pull_down_menu('customers_newsletter', $newsletter_array, (($cInfo->customers_newsletter == '1') ? '1' : '0'));
  }
?></td>
              </tr>
<?php // BOF: MOD - Separate Pricing per Customer ?>
              <tr>
                <td class="main" width="150"><?php echo ENTRY_CUSTOMERS_GROUP_NAME; ?></td>
<?php
  if ($processed != true) {
  $index = 0;
  while ($existing_customers =  tep_db_fetch_array($existing_customers_query)) {
 $existing_customers_array[] = array("id" => $existing_customers['customers_group_id'], "text" => "&#160;".$existing_customers['customers_group_name']."&#160;");
    ++$index;
  }
  } // end if ($processed != true )
?>
                <td class="main">
<?php if ($processed == true) {
        echo $cInfo->customers_group_id . tep_draw_hidden_field('customers_group_id');
      } else {
        echo tep_draw_pull_down_menu('customers_group_id', $existing_customers_array, $cInfo->customers_group_id);
      } ?></td>
              </tr>
<?php // EOF: MOD - Separate Pricing per Customer ?>
            </table>
          </td>
        </tr>
<?php // BOF: MOD - Separate Pricing per Customer ?>
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td class="formAreaTitle"><?php include_once(DIR_WS_LANGUAGES . $language . '/modules.php');
          echo HEADING_TITLE_MODULES_PAYMENT; ?></td>
        </tr>
        <tr>
          <td class="formArea">
            <table border="0" cellspacing="2" cellpadding="2">
              <tr bgcolor="#DEE4E8">
                <td class="main" colspan="2">
				<?php
                if ($processed == true) {
                  if ($cInfo->customers_payment_settings == '1') {
                    echo ENTRY_CUSTOMERS_PAYMENT_SET ;
                    echo ' : ';
                  } else {
                    echo ENTRY_CUSTOMERS_PAYMENT_DEFAULT;
                  }
                echo tep_draw_hidden_field('customers_payment_settings');
                } else { // $processed != true
                echo tep_draw_radio_field('customers_payment_settings', '1', false, (tep_not_null($cInfo->customers_payment_allowed)? '1' : '0' )) . '&nbsp;&nbsp;' . ENTRY_CUSTOMERS_PAYMENT_SET . '&nbsp;&nbsp;' . tep_draw_radio_field('customers_payment_settings', '0', false, (tep_not_null($cInfo->customers_payment_allowed)? '1' : '0' )) . '&nbsp;&nbsp;' . ENTRY_CUSTOMERS_PAYMENT_DEFAULT ; } ?></td>
              </tr>
<?php if ($processed != true) {
    $payments_allowed = explode (";",$cInfo->customers_payment_allowed);
    $module_active = explode (";",MODULE_PAYMENT_INSTALLED);
    $installed_modules = array();
    for ($i = 0, $n = sizeof($directory_array); $i < $n; $i++) {
    $file = $directory_array[$i];
    if (in_array ($directory_array[$i], $module_active)) {
      include(DIR_FS_CATALOG_LANGUAGES . $language . '/' . $file);
      include($module_directory . $file);

     $class = substr($file, 0, strrpos($file, '.'));
     if (tep_class_exists($class)) {
       $module = new $class;
       if ($module->check() > 0) {
         $installed_modules[] = $file;
       }
     } // end if (tep_class_exists($class))
?>
              <tr>
                <td class="main" colspan="2"><?php echo tep_draw_checkbox_field('payment_allowed[' . $i . ']', $module->code.".php" , (in_array ($module->code.".php", $payments_allowed)) ?  1 : 0); ?>&#160;&#160;<?php echo $module->title; ?></td>
              </tr>
<?php
  } // end if (in_array ($directory_array[$i], $module_active))
 } // end for ($i = 0, $n = sizeof($directory_array); $i < $n; $i++)
?>
              <tr>
                <td class="main" colspan="2" style="padding-left: 30px; padding-right: 10px; padding-top: 10px;"><?php echo ENTRY_CUSTOMERS_PAYMENT_SET_EXPLAIN ?></td>
              </tr>
<?php
   } else { // end if ($processed != true)
?>
              <tr>
                <td class="main" colspan="2">
				<?php 
				if ($cInfo->customers_payment_settings == '1') {
                  echo $customers_payment_allowed;
                } else {
                  echo ENTRY_CUSTOMERS_PAYMENT_DEFAULT;
                }
                echo tep_draw_hidden_field('customers_payment_allowed'); ?></td>
              </tr>
<?php
 } // end else: $processed == true
?>
            </table>
          </td>
        </tr>
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td class="formAreaTitle"><?php echo HEADING_TITLE_MODULES_SHIPPING; ?></td>
        </tr>
        <tr>
          <td class="formArea">
            <table border="0" cellspacing="2" cellpadding="2">
              <tr bgcolor="#DEE4E8">
                <td class="main" colspan="2">
				<?php 
				if ($processed == true) {
                  if ($cInfo->customers_shipment_settings == '1') {
                    echo ENTRY_CUSTOMERS_SHIPPING_SET ;
                    echo ' : ';
                  } else {
                   echo ENTRY_CUSTOMERS_SHIPPING_DEFAULT;
                  }
                echo tep_draw_hidden_field('customers_shipment_settings');
                } else { // $processed != true
                echo tep_draw_radio_field('customers_shipment_settings', '1', false, (tep_not_null($cInfo->customers_shipment_allowed)? '1' : '0' )) . '&nbsp;&nbsp;' . ENTRY_CUSTOMERS_SHIPPING_SET . '&nbsp;&nbsp;' . tep_draw_radio_field('customers_shipment_settings', '0', false, (tep_not_null($cInfo->customers_shipment_allowed)? '1' : '0' )) . '&nbsp;&nbsp;' . ENTRY_CUSTOMERS_SHIPPING_DEFAULT ; } ?></td>
              </tr>
<?php if ($processed != true) {
    $shipment_allowed = explode (";",$cInfo->customers_shipment_allowed);
    $ship_module_active = explode (";",MODULE_SHIPPING_INSTALLED);
    $installed_shipping_modules = array();
    for ($i = 0, $n = sizeof($ship_directory_array); $i < $n; $i++) {
    $file = $ship_directory_array[$i];
    if (in_array ($ship_directory_array[$i], $ship_module_active)) {
      include(DIR_FS_CATALOG_LANGUAGES . $language . '/' . $file);
      include($ship_module_directory . $file);

     $ship_class = substr($file, 0, strrpos($file, '.'));
     if (tep_class_exists($ship_class)) {
       $ship_module = new $ship_class;
       if ($ship_module->check() > 0) {
         $installed_shipping_modules[] = $file;
       }
     } // end if (tep_class_exists($ship_class))
?>
              <tr>
                <td class="main" colspan="2"><?php echo tep_draw_checkbox_field('shipping_allowed[' . $i . ']', $ship_module->code.".php" , (in_array ($ship_module->code.".php", $shipment_allowed)) ?  1 : 0); ?>&#160;&#160;<?php echo $ship_module->title; ?></td>
              </tr>
<?php
  } // end if (in_array ($ship_directory_array[$i], $ship_module_active))
 } // end for ($i = 0, $n = sizeof($ship_directory_array); $i < $n; $i++)
        ?>
              <tr>
                <td class="main" colspan="2" style="padding-left: 30px; padding-right: 10px; padding-top: 10px;"><?php echo ENTRY_CUSTOMERS_SHIPPING_SET_EXPLAIN ?></td>
              </tr>
<?php
   } else { // end if ($processed != true)
?>
              <tr>
                <td class="main" colspan="2">
				<?php 
				if ($cInfo->customers_shipment_settings == '1') {
                  echo $customers_shipment_allowed;
                } else {
                  echo ENTRY_CUSTOMERS_SHIPPING_DEFAULT;
                }
                echo tep_draw_hidden_field('customers_shipment_allowed'); ?></td>
              </tr>
<?php
 } // end else: $processed == true
?>
            </table>
          </td>
        </tr>
<?php // EOF: MOD - Separate Pricing per Customer ?>
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td align="right" class="main"><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE) . ' <a href="' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action', 'add_id'))) .'">' . tep_image_button('button_back.gif', IMAGE_CANCEL) . '</a>'; ?></td>
        </tr></form>
      </table>
      
<?php
  } else {
?>
        <tr>
          <td><?php echo tep_draw_form('search', FILENAME_CUSTOMERS, '', 'get'); ?>
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                <td class="pageHeading" align="right">&nbsp;</td>
                <td class="smallText" align="right"><?php // echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('search'); ?></td>
              </tr>
            </table><?php echo tep_hide_session_id(); ?></form>
          </td>
        </tr>
        <tr>
          <td>
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
<?php // BOF: MOD - customer_sort_admin_v1 adapted for Separate Pricing Per Customer

		  $listing = (isset($_GET['listing']) ? $_GET['listing'] : '');

          switch ($listing) {
              case "id-asc":
              $order = "c.customers_id";
                break;
                case "cg_name":
              $order = "cg.customers_group_name, c.customers_lastname";
                break;
              case "cg_name-desc":
              $order = "cg.customers_group_name DESC, c.customers_lastname";
              break;
              case "firstname":
              $order = "c.customers_firstname";
              break;
              case "firstname-desc":
              $order = "c.customers_firstname DESC";
              break;
              case "company":
              $order = "a.entry_company, c.customers_lastname";
              break;
              case "company-desc":
              $order = "a.entry_company DESC,c .customers_lastname DESC";
              break;
              case "ra":
              $order = "c.customers_group_ra DESC, c.customers_id DESC";
              break;
              case "ra-desc":
              $order = "c.customers_group_ra, c.customers_id DESC";
              break;
              case "lastname":
              $order = "c.customers_lastname, c.customers_firstname";
              break;
              case "lastname-desc":
              $order = "c.customers_lastname DESC, c.customers_firstname";
              break;
              default:
              $order = "c.customers_id DESC";
          }
// EOF: MOD - customer_sort_admin_v1 adapted for Separate Pricing Per Customer ?>
              <td valign="top">
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" valign="top"><a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('listing')) . 'listing=company'); ?>"><?php echo tep_image_button('ic_up.gif', ' Sort ' . ENTRY_COMPANY . ' --> A-B-C From Top '); ?></a>&nbsp;<a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('listing')) . 'listing=company-desc'); ?>"><?php echo tep_image_button('ic_down.gif', ' Sort ' . ENTRY_COMPANY . ' --> Z-X-Y From Top '); ?></a><br><?php echo ENTRY_COMPANY; ?></td>
                    <td class="dataTableHeadingContent" valign="top"><a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('listing')) . 'listing=lastname'); ?>"><?php echo tep_image_button('ic_up.gif', ' Sort ' . TABLE_HEADING_LASTNAME . ' --> A-B-C From Top '); ?></a>&nbsp;<a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('listing')) . 'listing=lastname-desc'); ?>"><?php echo tep_image_button('ic_down.gif', ' Sort ' . TABLE_HEADING_LASTNAME . ' --> Z-X-Y From Top '); ?></a><br><?php echo TABLE_HEADING_LASTNAME; ?></td>
                    <td class="dataTableHeadingContent" valign="top"><a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('listing')) . 'listing=firstname'); ?>"><?php echo tep_image_button('ic_up.gif', ' Sort ' . TABLE_HEADING_FIRSTNAME . ' --> A-B-C From Top '); ?></a>&nbsp;<a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('listing')) . 'listing=firstname-desc'); ?>"><?php echo tep_image_button('ic_down.gif', ' Sort ' . TABLE_HEADING_FIRSTNAME . ' --> Z-X-Y From Top '); ?></a><br><?php echo TABLE_HEADING_FIRSTNAME; ?></td>
                    <td class="dataTableHeadingContent" valign="top"><a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('listing')) . 'listing=cg_name'); ?>"><?php echo tep_image_button('ic_up.gif', ' Sort ' . TABLE_HEADING_CUSTOMERS_GROUPS . ' --> A-B-C From Top '); ?></a>&nbsp;<a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('listing')) . 'listing=cg_name-desc'); ?>"><?php echo tep_image_button('ic_down.gif', ' Sort ' . TABLE_HEADING_CUSTOMERS_GROUPS . ' --> Z-X-Y From Top '); ?></a><br><?php echo TABLE_HEADING_CUSTOMERS_GROUPS; ?></td>
                    <td class="dataTableHeadingContent" align="right" valign="top"><a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('listing')) . 'listing=id-asc'); ?>"><?php echo tep_image_button('ic_up.gif', ' Sort ' . TABLE_HEADING_ACCOUNT_CREATED . ' --> 1-2-3 From Top '); ?></a>&nbsp;<a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('listing')) . 'listing=id-desc'); ?>"><?php echo tep_image_button('ic_down.gif', ' Sort ' . TABLE_HEADING_ACCOUNT_CREATED . ' --> 3-2-1 From Top '); ?></a><br><?php echo TABLE_HEADING_ACCOUNT_CREATED; ?></td>
                    <td class="dataTableHeadingContent" align="center" valign="top"><a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('listing')) . 'listing=ra'); ?>"><?php echo tep_image_button('ic_up.gif', ' Sort ' . TABLE_HEADING_REQUEST_AUTHENTICATION . ' --> RA first (to Top) '); ?></a>&nbsp;<a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('listing')) . 'listing=ra-desc'); ?>"><?php echo tep_image_button('ic_down.gif', ' Sort ' . TABLE_HEADING_REQUEST_AUTHENTICATION . ' --> RA last (to Bottom)'); ?></a><br><?php echo TABLE_HEADING_REQUEST_AUTHENTICATION; ?>&nbsp;</td>
                    <td class="dataTableHeadingContent" align="right" valign="top"><?php echo tep_draw_separator('pixel_trans.gif', '11', '12'); ?>&nbsp;<br><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
<?php // EOF: MOD - customer_sort_admin_v1 adapted for Separate Pricing Per Customer ?>
              </tr>
<?php
    $search = '';
    if (isset($_GET['search']) && tep_not_null($_GET['search'])) {
      $keywords = tep_db_input(tep_db_prepare_input($_GET['search']));
      $search = "where c.customers_lastname like '%" . $keywords . "%' or c.customers_firstname like '%" . $keywords . "%' or c.customers_email_address like '%" . $keywords . "%'";
    }
// LINE CHANGED: MOD - customer_sort_admin_v1 adapted for Separate Pricing Per Customer
//  $customers_query_raw = "select c.customers_id, c.customers_lastname, c.customers_firstname, c.customers_email_address, a.entry_country_id from " . TABLE_CUSTOMERS . " c left join " . TABLE_ADDRESS_BOOK . " a on c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id " . $search . " order by c.customers_lastname, c.customers_firstname";
    $customers_query_raw = "select c.customers_id, c.customers_lastname, c.customers_firstname, c.customers_email_address, c.customers_group_id, c.customers_group_ra, a.entry_country_id, a.entry_company, cg.customers_group_name from " . TABLE_CUSTOMERS . " c left join " . TABLE_ADDRESS_BOOK . " a on c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id left join customers_groups cg on c.customers_group_id = cg.customers_group_id " . $search . " order by $order";
    $customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $customers_query_raw, $customers_query_numrows);
    $customers_query = tep_db_query($customers_query_raw);
    while ($customers = tep_db_fetch_array($customers_query)) {
      $info_query = tep_db_query("select customers_info_date_account_created as date_account_created, customers_info_date_account_last_modified as date_account_last_modified, customers_info_date_of_last_logon as date_last_logon, customers_info_number_of_logons as number_of_logons from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . $customers['customers_id'] . "'");
      $info = tep_db_fetch_array($info_query);

      if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $customers['customers_id']))) && !isset($cInfo)) {
        $country_query = tep_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$customers['entry_country_id'] . "'");
        $country = tep_db_fetch_array($country_query);

        $reviews_query = tep_db_query("select count(*) as number_of_reviews from " . TABLE_REVIEWS . " where customers_id = '" . (int)$customers['customers_id'] . "'");
        $reviews = tep_db_fetch_array($reviews_query);

        $customer_info = array_merge($country, $info, $reviews);

        $cInfo_array = array_merge($customers, $customer_info);
        $cInfo = new objectInfo($cInfo_array);
      }

      if (isset($cInfo) && is_object($cInfo) && ($customers['customers_id'] == $cInfo->customers_id)) {
        echo '          <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=edit') . '\'">' . "\n";
      } else {
        echo '          <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID')) . 'cID=' . $customers['customers_id']) . '\'">' . "\n";
      }
	  ### begin customer notes by tabsl v0.1|2007 ###
	  $ias_notes_marker = false;
	  $ias_notes_mark = tep_db_query("SELECT customers_notes_id FROM customers_notes WHERE customers_id = ".$customers['customers_id']);
	  $font = "";
	  $font_end = "";
	  if(tep_db_num_rows($ias_notes_mark)) {
		$ias_notes_marker = true;
		$font = "<font color=red>";
		$font_end = "</font>";
	  };
	  ### end customer notes by tabsl v0.1|2007 ###
// BOF: MOD - customer_sort_admin_v1 adapted for Separate Pricing Per Customer ?>
                <td class="dataTableContent"><?php
				echo $font;
      if (strlen($customers['entry_company']) > 16 ) {
        print ("<acronym title=\"".$customers['entry_company']."\">".substr($customers['entry_company'], 0, 16)."&#160;</acronym>");
      } else {
                echo $customers['entry_company']; }
				echo '&nbsp;';
				echo $font_end;
				?></td>
                <td class="dataTableContent"><?php
				echo $font;
      if (strlen($customers['customers_lastname']) > 15 ) {
        print ("<acronym title=\"".$customers['customers_lastname']."\">".substr($customers['customers_lastname'], 0, 15)."&#160;</acronym>");
      } else {
                echo $customers['customers_lastname']; }
				echo $font_end;
		?></td>
                <td class="dataTableContent"><?php
				echo $font;
      if (strlen($customers['customers_firstname']) > 15 ) {
        print ("<acronym title=\"".$customers['customers_firstname']."\">".substr($customers['customers_firstname'], 0, 15)."&#160;</acronym>");
      } else {
            echo $customers['customers_firstname']; }
				echo $font_end;
		?></td>
                <td class="dataTableContent"><?php
      if (strlen($customers['customers_group_name']) > 17 ) {
        print ("<acronym title=\"".$customers['customers_group_name']."\"> ".substr($customers['customers_group_name'], 0, 17)."&#160;</acronym>");
      } else {
        echo $customers['customers_group_name'] ;
      }
// EOF: MOD - customer_sort_admin_v1 adapted for Separate Pricing Per Customer ?></td>
                <td class="dataTableContent" align="right"><?php echo tep_date_short($info['date_account_created']); ?></td>
<?php // BOF: MOD - Customer Group ?>
                <td class="dataTableContent" align="center">
<?php
      if ($customers['customers_group_ra'] == '1') {
        echo tep_image(DIR_WS_ICONS . 'icon_status_red.gif', IMAGE_ICON_STATUS_GREEN, 10, 10);
      } else {
        echo tep_draw_separator('pixel_trans.gif', '10', '10');
      } ?></td>
<?php // EOF: MOD - Customer Group ?>
                <td class="dataTableContent" align="right"><?php if (isset($cInfo) && is_object($cInfo) && ($customers['customers_id'] == $cInfo->customers_id)) { echo tep_image(DIR_WS_ICONS . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID')) . 'cID=' . $customers['customers_id']) . '">' . tep_image(DIR_WS_ICONS . 'information.png', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
<?php // LINE CHANGED: MOD customer_sort_admin_v1 adapted for Separate Pricing Per Customer colspan 4 to 7 ?>
                <td colspan="7">
                  <table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr>
                      <td class="smallText" valign="top"><?php echo $customers_split->display_count($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                      <td class="smallText" align="right"><?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></td>
                    </tr>
<?php
    if (isset($_GET['search']) && tep_not_null($_GET['search'])) {
?>
                    <tr>
                      <td align="right" colspan="2"><?php echo '<a href="' . tep_href_link(FILENAME_CUSTOMERS) . '">' . tep_image_button('button_reset.gif', IMAGE_RESET) . '</a>'; ?></td>
                    </tr>
<?php
    } else {
?>
                    <tr>
                      <td align="right" colspan="2" class="smallText"><?php echo '<a href="' . tep_href_link(FILENAME_CREATE_ACCOUNT) . '">' . tep_image_button('button_insert.gif', IMAGE_INSERT) . '</a>'; ?></td>
                    </tr>
<?php	
	}
?>
                  </table>
                </td>
              </tr>
<?php // BOF: MOD - Separate Pricing Per Customer: show numbers of customers in each customers group
  if (!isset($_GET['search'])) {
  $customers_groups_query = tep_db_query("select customers_group_id, customers_group_name from " . TABLE_CUSTOMERS_GROUPS . " order by customers_group_id ");
  while ($existing_customers_groups =  tep_db_fetch_array($customers_groups_query)) {
    $existing_customers_groups_array[] = array("id" => $existing_customers_groups['customers_group_id'], "text" => $existing_customers_groups['customers_group_name']);
    }
    $count_groups_query = tep_db_query("select customers_group_id, count(*) as count from " . TABLE_CUSTOMERS . " group by customers_group_id order by count desc");
    while ($count_groups = tep_db_fetch_array($count_groups_query)) {
  for ($n = 0; $n < sizeof($existing_customers_groups_array); $n++) {
    if ($count_groups['customers_group_id'] == $existing_customers_groups_array[$n]['id']) {
      $count_groups['customers_group_name'] = $existing_customers_groups_array[$n]['text'];
    }
  } // end for ($n = 0; $n < sizeof($existing_customers_groups_array); $n++)
  $count_groups_array[] = array("id" => $count_groups['customers_group_id'], "number_in_group" => $count_groups['count'], "name" => $count_groups['customers_group_name']);
  }
?>
     <tr>
       <td style="padding-top: 10px;" align="center" colspan="7">
         <table border="0" cellspacing="0" cellpadding="2" style="border: 1px solid #c9c9c9">
           <tr class="dataTableHeadingRow">
             <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMERS_GROUPS ?></td>
             <td class="dataTableHeadingContent">&#160;</td>
             <td class="dataTableHeadingContent" align="right">No.</td>
           </tr>
<?php $c = '0'; // variable used for background coloring of rows
  for ($z = 0; $z < sizeof($count_groups_array); $z++) {
    $bgcolor = ($c++ & 1) ? ' class="dataTableRow"' : '';
?>
           <tr<?php echo $bgcolor; ?>>
             <td class="dataTableContent"><?php echo $count_groups_array[$z]['name']; ?></td>
             <td class="dataTableContent">&#160;</td>
             <td class="dataTableContent" align="right"><?php echo $count_groups_array[$z]['number_in_group'] ?></td>
           </tr>
<?php
   } // end for ($z = 0; $z < sizeof($count_groups_array); $z++)
?>
         </table>
       </td>
     </tr>
<?php
  } // end if (!isset($_GET['search']))
// EOF: MOD - Separate Pricing Per Customer: show numbers of customers in each customers group ?>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'confirm':
// LINE CHANGED: MOD - Separate Pricing Per Customer: dark grey field with customer name higher
//    $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CUSTOMER . '</b>');
      $heading[] = array('text' => ''. tep_draw_separator('pixel_trans.gif', '11', '12') .'&nbsp;<br><b>' . TEXT_INFO_HEADING_DELETE_CUSTOMER . '</b>');
      $contents = array('form' => tep_draw_form('customers', FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_DELETE_INTRO . '<br><br><b>' . $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname . '</b>');
      if (isset($cInfo->number_of_reviews) && ($cInfo->number_of_reviews) > 0) $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('delete_reviews', 'on', true) . ' ' . sprintf(TEXT_DELETE_REVIEWS, $cInfo->number_of_reviews));
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:  
      if (isset($cInfo) && is_object($cInfo)) {
// LINE CHANGED: MOD - Separate Pricing Per Customer: dark grey field with customer name higher
//      $heading[] = array('text' => '<b>' . $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname . '</b>');
        $heading[] = array('text' => ''. tep_draw_separator('pixel_trans.gif', '11', '12') .'&nbsp;<br><b>' . $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname . '</b>');
		if (isset($sub_list_size) || isset($unsub_list_size)) {
		$contents[] = array('align' => 'center', 'text' => '<table width="100%"><tr><td class="messageStackSuccess">MailChimp Sync Complete.<br>Subscribed:<b>' . $sub_list_size . '</b>&nbsp;&nbsp;Unsubscribed:<b>' . $unsub_list_size . '</b></td></tr></table>');	
		}
        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=confirm') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS, 'cID=' . $cInfo->customers_id) . '">' . tep_image_button('button_orders.gif', IMAGE_ORDERS) . '</a> <a href="' . tep_href_link(FILENAME_MAIL, 'selected_box=tools&customer=' . $cInfo->customers_email_address) . '">' . tep_image_button('button_email.gif', IMAGE_EMAIL) . '</a>');
		
// Adds phone order functionality		
		if (ENABLE_SSL_CATALOG == 'true') {
		  $po_string = '<form action="' . HTTPS_CATALOG_SERVER . DIR_WS_CATALOG . 'login.php" method="POST" target="_blank">';
		} else {
		  $po_string = '<form action="' . HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'login.php" method="POST" target="_blank">';
		}
		$po_string .= '<input type="hidden" name="email_address" value="' . $cInfo->customers_email_address . '"><input type="hidden" name="action" value="process"><input type="hidden" name="phoneorder" value="order"><input type="hidden" name="admin" value="' . str_replace(DIR_FS_CATALOG, "", DIR_FS_ADMIN) . '">' . tep_image_submit('button_login.gif', IMAGE_BUTTON_LOGIN_AS) . '</form>';
		
		$contents[] = array('align' => 'center', 'text' => ' <a href="' . tep_href_link(FILENAME_CREATE_ORDER, tep_get_all_get_params(array('cID', 'action', 'page')) . 'Customer_nr=' . $cInfo->customers_id) . '">' . tep_image_button('button_create_order2.gif', IMAGE_ORDERS) . '</a> ' . $po_string);
		if (MAILCHIMP_ENABLE == true) {
		$contents[] = array('align' => 'center', 'text' => ' <a href="' . tep_href_link(FILENAME_CUSTOMERS, 'action=mailchimp') . '">' . tep_image_button('button_mc_sync.gif', IMAGE_MC_SYNC) . '</a> ');
		}
        $contents[] = array('text' => '<br>' . TEXT_DATE_ACCOUNT_CREATED . ' ' . tep_date_short($cInfo->date_account_created));
        $contents[] = array('text' => '<br>' . TEXT_DATE_ACCOUNT_LAST_MODIFIED . ' ' . tep_date_short($cInfo->date_account_last_modified));
        $contents[] = array('text' => '<br>' . TEXT_INFO_DATE_LAST_LOGON . ' '  . tep_date_short($cInfo->date_last_logon));
        $contents[] = array('text' => '<br>' . TEXT_INFO_NUMBER_OF_LOGONS . ' ' . $cInfo->number_of_logons);
        $contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY . ' ' . $cInfo->countries_name);
        $contents[] = array('text' => '<br>' . TEXT_INFO_NUMBER_OF_REVIEWS . ' ' . $cInfo->number_of_reviews);

// BOF : PGM EDITS CUSTOMER NOTES

        $contents[] = array('text' => '<br><p class="main"><b>' . TEXT_COMMENTS .'</b></p>');

$ias_notes_query = tep_db_query("SELECT DISTINCT customers_id, customers_notes_id, customers_notes_message, customers_notes_editor, customers_notes_date FROM customers_notes WHERE customers_id = " . (isset($_GET['cID']) ? $_GET['cID'] : $cInfo->customers_id));
	if(tep_db_num_rows($ias_notes_query) == 0) { // No Comments Available
		$contents[] = array('text' => '');
	} else {
		function notedate($fdate) {
					list($year, $month, $day) = explode("-", $fdate);
					return sprintf("%02d-%02d-%04d", $month, $day, $year);
		} // end function

		$comment_table_string = '';

		while ($ias_notes = tep_db_fetch_array($ias_notes_query)) {

        $comment_table_string .= '<table width="100%">';
		$comment_table_string .= '  <tr>';
		$comment_table_string .= '    <td>';
		$comment_table_string .= '      <table width="100%" cellpadding="2" cellspacing="0" border="0" style="background-color:#ffffff">';
        $comment_table_string .= '        <tr>';
		$comment_table_string .= '          <td colspan="3" class="smallText">' . $ias_notes["customers_notes_message"] . '</td>';
		$comment_table_string .= '        </tr>';
		$comment_table_string .= '        <tr>';
		$comment_table_string .= '          <td class="smallText"><b>' . $ias_notes["customers_notes_editor"] . '</b></td>';
		$comment_table_string .= '          <td class="smallText" align="center" width="80">' . notedate($ias_notes["customers_notes_date"]) . '</td>';
		$comment_table_string .= '          <td class="smallText" align="right" width="16"><a href="' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=deletenotes&notesid='.$ias_notes["customers_notes_id"]) . '">' . tep_image_button('delete.png', IMAGE_DELETE) . '</a></td>';																																																																													 		$comment_table_string .= '      </tr>';
		$comment_table_string .= '    </table>';
		$comment_table_string .= '  </td>';
		$comment_table_string .= '</tr>';
		$comment_table_string .= '</table>';



		} // end while
		$contents[] = array('text' => $comment_table_string);
	} // end else

        $notes_table_string = '';

		$notes_table_string .= '<table width="100%">';
		$notes_table_string .= '  <tr>';
		$notes_table_string .= '    <td><form action="' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=newnotes') . '" method="post" name="notes">';
		$notes_table_string .= '      <table width="100%" cellpadding="0" cellspacing="0" border="0">';
		$notes_table_string .= '        <tr>';
		$notes_table_string .= '          <td class="smallText" colspan="3"><b>' . TEXT_ADD_A_COMMENT . '</b></td>';
		$notes_table_string .= '    	   </tr>';
		$notes_table_string .= '        <tr>';
		$notes_table_string .= '          <td class="smallText" valign="top">' . TEXT_NOTES . '</td>';
		$notes_table_string .= '          <td colspan="2"><textarea name="message" cols="30" rows="6"></textarea></td>';
		$notes_table_string .= '        </tr>';
		$notes_table_string .= '        <tr>';
		$notes_table_string .= '          <td class="smallText">' . TEXT_AUTHOR . '</td>';
		$notes_table_string .= '          <td><input type="text" name="editor" size="12" value=""></td>';
		$notes_table_string .= '          <td class="smallText" valign="top" align="right">' . tep_image_submit('button_add_comment.gif', IMAGE_BUTTON_ADD_COMMENT) . '&nbsp;&nbsp;&nbsp;</td>';
		$notes_table_string .= '        </tr>';
		$notes_table_string .= '      </table>';
		$notes_table_string .= '    </form></td>';
		$notes_table_string .= '  </tr>';
		$notes_table_string .= '</table>';
		
		$contents[] = array('text' => $notes_table_string);
		
// EOF : PGM EDITS CUSTOMER NOTES
      } // end if
      break;
  } // end case

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
<?php
  }
?>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>