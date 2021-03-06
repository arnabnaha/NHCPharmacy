<?php
/*
$Id: sofortueberweisung_direct.php 1692 2012-02-26 01:26:50Z michael.oscmax@gmail.com $

  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/

    define('MODULE_PAYMENT_SOFORTUEBERWEISUNG_DIRECT_TEXT_DESCRIPTION', '<img src="images/icons/icon_popup.gif" border="0" alt="">&nbsp;<a href="https://www.sofortueberweisung.de/cms/index.php?vpartner=21" target="_blank" style="text-decoration: underline; font-weight: bold;">Sofort�berweisung Webseite besuchen</a>&nbsp;<a href="javascript:toggleDivBlock(\'sofortueberweisungInfo\');">(info)</a><span id="sofortueberweisungInfo" style="display: none;"><br><i>Bei Benutzung dieses Links erh&auml;lt osCmax f&uuml;r eine Neukundenvermittlung einen kleinen Bonus.</i></span><br><br>Kontonummer Test Info:<br><br>BLZ#: 88888888');


  define('MODULE_PAYMENT_SOFORTUEBERWEISUNG_DIRECT_TEXT_TITLE', 'Sofort�berweisung Direkt');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNG_DIRECT_TEXT_PUBLIC_TITLE', 'Sofort�berweisung');

  // checkout_payment Informationen via Bild
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNG_DIRECT_TEXT_DESCRIPTION_CHECKOUT_PAYMENT', '
    <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><a href="#" onclick="window.open(\'https://www.sofortueberweisung.de/cms/index.php?vpartner=21\', \'Popup\',\'toolbar=yes,status=no,menubar=no,scrollbars=yes,width=690,height=500\'); return false;">' . tep_image('ext/modules/payment/sofortueberweisung/images/sofortueberweisung.gif', 'Sofort�berweisung ist der kostenlose, T�V-zertifizierte Zahlungsdienst der Payment Network AG. Ihre Vorteile: keine zus�tzliche Registrierung, automatische Abbuchung von Ihrem Online-Bankkonto, h�chste Sicherheitsstandards und sofortiger Versand von Lagerware. F�r die Bezahlung mit Sofort�berweisung ben�tigen Sie Ihre eBanking Zugangsdaten, d.h. Bankverbindung, Kontonummer, PIN und TAN.') . '</a></td>
      </tr>
    </table>');

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNG_DIRECT_TEXT_DESCRIPTION_CHECKOUT_CONFIRMATION', '
    <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="main"><p>Nach Best�tigung der Bestellung werden Sie zum Zahlungssytem von Sofort�berweisung weitergeleitet und k�nnen dort eine Online-�berweisung duchf�hren.</p><p>Hierf�r ben�tigen Sie Ihre eBanking Zugangsdaten, d.h. Bankverbindung, Kontonummer, PIN und TAN. Mehr Informationen finden Sie hier: <a href="#" onclick="window.open(\'https://www.sofortueberweisung.de/cms/index.php?vpartner=21\', \'Popup\',\'toolbar=yes,status=no,menubar=no,scrollbars=yes,width=690,height=500\'); return false;">http://www.sofortueberweisung.de</a>.</p></td>
      </tr>
    </table>');

 // im Verwendungszweck werden folgende Platzhalter ersetzt:
 // {{order_date}} durch Bestelldatum
 // {{customer_id}} durch Kundennummer der Datenbank
 // {{customer_name}}  durch Kundenname
 // {{customer_company}} durch Kundenfirma
 // {{customer_email}} durch Email des Kunden

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNG_DIRECT_TEXT_V_ZWECK_1', STORE_NAME);  // max 27 Zeichen
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNG_DIRECT_TEXT_V_ZWECK_2', 'Nr. {{orderid}} Kd-Nr. {{customer_id}}'); // max 27 Zeichen
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNG_DIRECT_TEXT_EMAIL_FOOTER', '');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNG_DIRECT_TEXT_REDIRECT', 'Sie werden nun zu Sofortueberweisung.de weitergeleitet. Sollte dies nicht geschehen bitte den Button dr�cken');

  define('MODULE_PAYMENT_SOFORTUEBERWEISUNG_DIRECT_TEXT_ERROR_HEADING', 'Folgender Fehler wurde von Sofort�berweisung w�hrend des Prozesses gemeldet:');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNG_DIRECT_TEXT_ERROR_MESSAGE', 'Zahlung via Sofort�berweisung ist leider nicht m�glich, oder wurde auf Kundenwunsch abgebrochen. Bitte w�hlen sie ein andere Zahlungsweise.');
  define('MODULE_PAYMENT_SOFORTUEBERWEISUNG_DIRECT_TEXT_CHECK_ERROR', 'Sofort�berweisungs Transaktionscheck fehlgeschlagen. Bitte manuell �berpr�fen');
?>
