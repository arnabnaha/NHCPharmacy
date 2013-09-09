<?php
/*
  $Id: banktransfer.php,v 1.3 2002/05/31 19:02:02 thomasamoulton Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_TITLE', 'Bank Transfer Payment');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_DESCRIPTION','An Email Has Been Sent With Details To Transfer Your Total Order Value. For security reasons the bank details are not shown here.<br>
<br>
<br>
  We will not ship your order until we receive payment in the above account.');
  define('MODULE_PAYMENT_BANKTRANSFER_TEXT_EMAIL_FOOTER', "Please use the following details to transfer your total order value:\n\nAccount No.:  " . MODULE_PAYMENT_BANKTRANSFER_ACCNUM . "\nAccount Name: " . MODULE_PAYMENT_BANKTRANSFER_ACCNAM . "\nBank Name:    " . MODULE_PAYMENT_BANKTRANSFER_BANKNAM . "\nBranch Name:    " . MODULE_PAYMENT_BANKTRANSFER_BANK_BRANCH_NAME ."\nIFSC Code:    " . MODULE_PAYMENT_BANKTRANSFER_IFSC ."\n\nThank You for your order. We will process your order once we receive the confirmation of your payment in our bank accounts above. Please do not forget to mention your [Order No: xxx] in the transaction remark when transacting from your Bank Account.");
  define('MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER', 'Sort order of display');
define('MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID', 'Set the order status');
?>
