<?php
/*
$Id: billing_address.php 986 2011-01-06 04:32:47Z michael.oscmax@gmail.com $

  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/
?>
<div id="billingAddress"><?php
 if (tep_session_is_registered('customer_id') && ONEPAGE_CHECKOUT_SHOW_ADDRESS_INPUT_FIELDS == 'False'){
	 echo tep_address_label($customer_id, $billto, true, ' ', '<br>');
 }else{
	 if (tep_session_is_registered('onepage')){
		 $billingAddress = $onepage['billing'];
		 $customerAddress = $onepage['customer'];
	 }
	 if(ONEPAGE_ADDR_LAYOUT == 'vertical') {
    include(DIR_WS_INCLUDES.'checkout/billing_address_vertical.php');
 }else
 {
   include(DIR_WS_INCLUDES.'checkout/billing_address_horizontal.php');
 }
 
}
?></div>