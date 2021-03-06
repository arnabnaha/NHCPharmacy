<?php
/*
$Id: recover_cart_sales.php 1857 2012-06-20 01:21:38Z michael.oscmax@gmail.com $

  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/

define('MESSAGE_STACK_CUSTOMER_ID', 'Panier pour client ');
define('MESSAGE_STACK_DELETE_SUCCESS', ' suppression r�ussie');
define('HEADING_TITLE', 'Traiter les paniers non valid�s v2.22');
define('HEADING_EMAIL_SENT', 'rapport par e-mail');
define('EMAIL_TEXT_LOGIN', 'Login to your account here:');
define('EMAIL_SEPARATOR', '------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'Demande de '.  STORE_NAME );
define('EMAIL_TEXT_SALUTATION', 'Dear ' );
define('EMAIL_TEXT_NEWCUST_INTRO', "\n\n" . 'Vous avez choisi ' . STORE_NAME . ' pour vos achats. ');
define('EMAIL_TEXT_CURCUST_INTRO', "\n\n" . 'Nous vous remercions des achats que vous avez d�ja pass�s sur ' . STORE_NAME . '. ');
define('EMAIL_TEXT_BODY_HEADER', 'Nous avons remarqu� que lors d\'une visite sur notre boutique vous avez choisi les articles suivants:' . "\n\n");






define('EMAIL_TEXT_BODY_FOOTER', 'Nous serions int�ress�s pour savoir ce qui s\'est produit et s\'il y avait une raison pour laquelle vous avez d�cid� de ne pas acheter avec ' . STORE_NAME . '.' .
                                 "\n\n" . 'Auriez vous l\'amabilit� de nous expliquer la raison de la non finalisation de votre commande? Toujours soucieux de proposer le meilleur service, votre exp�rience nous permettrait d\'am�liorer nos propositions.' .
                                 "\n\n" . 'Cordialement,' ."\n\n");















define('DAYS_FIELD_PREFIX', 'Montrer depuis les derniers ');
define('DAYS_FIELD_POSTFIX', ' jours ');
define('DAYS_FIELD_BUTTON', 'Go');
define('TABLE_HEADING_DATE', 'DATE');
define('TABLE_HEADING_CONTACT', 'CONTACT');
define('TABLE_HEADING_CUSTOMER', 'NOM');
define('TABLE_HEADING_EMAIL', 'E-MAIL');
define('TABLE_HEADING_PHONE', 'TELEPHONE');
define('TABLE_HEADING_MODEL', 'MODEL');
define('TABLE_HEADING_DESCRIPTION', 'ARTICLE');
define('TABLE_HEADING_QUANTY', 'QTY');
define('TABLE_HEADING_PRICE', 'PRIX');
define('TABLE_HEADING_TOTAL', 'TOTAL');
define('TABLE_GRAND_TOTAL', 'Grand Total: ');
define('TABLE_CART_TOTAL', 'Panier Total: ');
define('TEXT_CURRENT_CUSTOMER', 'CUSTOMER');
define('TEXT_SEND_EMAIL', 'Envoyer e-mail');
define('TEXT_RETURN', '[Retour]');
define('TEXT_NOT_CONTACTED', 'Non contact�');
define('PSMSG', 'facultatif PS Message: ');
define('TEXT_NO_CARTS_FOUND', 'No abandoned carts found for the date range specified.');
?>