<?php
/*
$Id: Interface_Cache.php 978 2011-01-06 01:22:29Z michael.oscmax@gmail.com $

  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/
 /**
 *
 * ULTIMATE Seo Urls 5
 *
 * 
 * @package Ultimate Seo Urls 5
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU Public License
 * @link http://www.fwrmedia.co.uk
 * @copyright Copyright 2008-2009 FWR Media
 * @copyright Portions Copyright 2005 Bobby Easland
 * @author Robert Fisher, FWR Media, http://www.fwrmedia.co.uk 
 * @lastdev $Author:: michael.oscmax@gmail.com                         $:  Author of last commit
 * @lastmod $Date:: 2011-01-05 18:22:29 -0700 (Wed, 05 Jan 2011)       $:  Date of last commit
 * @version $Rev:: 978                                                 $:  Revision of last commit
 */

  interface Interface_Cache {
    
    public function store();
    
    public function retrieve();
    
    public function gc();
  }
?>