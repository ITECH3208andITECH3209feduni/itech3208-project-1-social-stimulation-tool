<?php
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
class Tablespidervideoplayer_playlist extends JTable
{
var $id = null;
var $published = null;
var $title = null;
var $thumb = null;
var $videos = null;

function __construct(&$db)
{
parent::__construct('#__spidervideoplayer_playlist','id',$db);
}
}
?>