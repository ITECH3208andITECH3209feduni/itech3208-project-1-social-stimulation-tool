<?php
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
class Tablespidervideoplayer_theme extends JTable
{
var $id = null;
var $title = null;
var $appWidth = null;
var $appHeight = null;
var $playlistWidth = null;
var $startWithLib = null;
var $autoPlay = null;
var $autoNext = null;
var $autoNextAlbum = null;
var $defaultVol = null;
var $defaultRepeat = null;
var $defaultShuffle = null;
var $autohideTime = null;
var $centerBtnAlpha = null;
var $loadinAnimType = null;
var $keepAspectRatio = null;
var $clickOnVid = null;
var $spaceOnVid = null;
var $mouseWheel = null;
var $ctrlsPos = null;
var $ctrlsStack = null;
var $ctrlsOverVid = null;
var $ctrlsSlideOut = null;
var $watermarkUrl = null;

var $watermarkPos = null;
var $watermarkSize = null;
var $watermarkSpacing = null;
var $watermarkAlpha = null;
var $playlistPos = null;
var $playlistOverVid = null;
var $playlistAutoHide = null;
var $playlistTextSize = null;
var $libCols = null;
var $libRows = null;
var $libListTextSize = null;
var $libDetailsTextSize = null;

var $appBgColor = null;
var $vidBgColor = null;
var $framesBgColor = null;
var $ctrlsMainColor = null;
var $ctrlsMainHoverColor = null;
var $slideColor = null;
var $itemBgHoverColor = null;
var $itemBgSelectedColor = null;
var $textColor = null;
var $textHoverColor = null;
var $textSelectedColor = null;
var $framesBgAlpha = null;
var $ctrlsMainAlpha = null;
var $show_trackid = null;
var $openPlaylistAtStart = null;


function __construct(&$db)
{
parent::__construct('#__spidervideoplayer_theme','id',$db);
}
}
?>