<?php
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
class Tablespidervideoplayer_album extends JTable
{
var $id = null;
var $title = null;
var $artist = null;
var $year = null;
var $thumb = null;
var $published = null;
function __construct(&$db)
{
parent::__construct('#__spidervideoplayer_album','id',$db);
}
}
?>