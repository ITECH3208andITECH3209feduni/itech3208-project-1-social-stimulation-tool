<?php
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
class Tablespidervideoplayer_video extends JTable
{
var $id = null;
var $type = null;
var $url = null;
var $urlHD = null;
var $fmsUrl = null;
var $thumb = null;
var $trackId = null;
var $title = null;
var $published = null;
var $params = null;
function __construct(&$db)
{
parent::__construct('#__spidervideoplayer_video','id',$db);
}
}
?>