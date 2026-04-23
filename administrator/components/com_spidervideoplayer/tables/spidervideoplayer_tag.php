<?php
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
class Tablespidervideoplayer_tag extends JTable
{
var $id = null;
var $name = null;
var $required = null;
var $ordering = null;
var $published = null;
function __construct(&$db)
{
parent::__construct('#__spidervideoplayer_tag','id',$db);
}
}
?>