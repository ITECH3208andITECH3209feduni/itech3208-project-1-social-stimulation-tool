<?php 
/**
* @package Spider Video Player
* @author Web-Dorado
* @copyright (C) 2012 Web-Dorado. All rights reserved.
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
header('Cache-Control: no-store, no-cache, must-revalidate'); 
header('Cache-Control: post-check=0, pre-check=0',false);
header('Pragma: no-cache');
header ("Content-Type:text/xml"); 


function change_to_str($x)
{
	if($x)
	return 'true';
	return 'false';
}

$playlist = JRequest::getVar('playlist', 0);
$theme = JRequest::getVar('theme', 0);
$typeselect	=	JRequest::getVar('typeselect');
$params=$this->params;

//$new	=str_replace("#", "0x", $params->get( 'textColor'));
$new="";
if($params->get( 'ctrlsStack'))
{
	$ctrls = explode(",", $params->get( 'ctrlsStack'));
	foreach($ctrls as $key =>  $x) 
	{
		$y = explode(":", $x);
		$ctrl	=$y[0];
		$active	=$y[1];
		if($typeselect==1)
		{
			if($ctrl=='playlist')
				$active=0;

			if($ctrl=='lib')
				$active=0;
		}
		if($active==1)
			if($new=="")
		$new=$y[0];
		else
			$new=$new.','.$y[0];
	}
};

$video=	JRequest::getVar('video');
if($typeselect==0)
{
	$startWithLib=$params->get( 'startWithLib');
	$embed_url='index.php?option=com_spidervideoplayer&typeselect='.$typeselect.'&view=spidervideoplayer&playlist='.$playlist.'&theme='.$theme.'&tmpl=component';
}
else
{
	$startWithLib=0;
	$embed_url='index.php?option=com_spidervideoplayer&typeselect='.$typeselect.'&view=spidervideoplayer&video='.$video.'&theme='.$theme.'&tmpl=component';
}

echo  '<settings>
<appWidth>'.$params->get( 'appWidth').'</appWidth>
<appHeight>'.$params->get( 'appHeight').'</appHeight>
<playlistWidth>'.$params->get( 'playlistWidth').'</playlistWidth>
<startWithLib>'.change_to_str($startWithLib).'</startWithLib>
<autoPlay>'.change_to_str($params->get( 'autoPlay')).'</autoPlay>
<autoNext>'.change_to_str($params->get( 'autoNext')).'</autoNext>
<autoNextAlbum>'.change_to_str($params->get( 'autoNextAlbum')).'</autoNextAlbum>
<defaultVol>'.(($params->get( 'defaultVol')+0)/100).'</defaultVol>
<defaultRepeat>'.$params->get( 'defaultRepeat').'</defaultRepeat>
<defaultShuffle>'.$params->get( 'defaultShuffle').'</defaultShuffle>
<autohideTime>'.$params->get( 'autohideTime').'</autohideTime>
<centerBtnAlpha>'.(($params->get( 'centerBtnAlpha')+0)/100).'</centerBtnAlpha>
<loadinAnimType>'.$params->get( 'loadinAnimType').'</loadinAnimType>
<keepAspectRatio>'.change_to_str($params->get( 'keepAspectRatio')).'</keepAspectRatio>
<clickOnVid>'.change_to_str($params->get( 'clickOnVid')).'</clickOnVid>
<spaceOnVid>'.change_to_str($params->get( 'spaceOnVid')).'</spaceOnVid>
<mouseWheel>'.change_to_str($params->get( 'mouseWheel')).'</mouseWheel>
<ctrlsPos>'.$params->get( 'ctrlsPos').'</ctrlsPos>
<ctrlsStack>'.$new.'</ctrlsStack>
<ctrlsOverVid>'.change_to_str($params->get( 'ctrlsOverVid')).'</ctrlsOverVid>
<ctrlsAutoHide>'.change_to_str($params->get( 'ctrlsSlideOut')).'</ctrlsAutoHide>
<watermarkUrl>'.JURI::root().'administrator/'.$params->get( 'watermarkUrl').'</watermarkUrl>
<watermarkPos>'.$params->get( 'watermarkPos').'</watermarkPos>
<watermarkSize>'.$params->get( 'watermarkSize').'</watermarkSize>
<watermarkSpacing>'.$params->get( 'watermarkSpacing').'</watermarkSpacing>
<watermarkAlpha>'.(($params->get( 'watermarkAlpha')+0)/100).'</watermarkAlpha>
<playlistPos>'.$params->get( 'playlistPos').'</playlistPos>
<playlistOverVid>'.change_to_str($params->get( 'playlistOverVid')).'</playlistOverVid>
<playlistAutoHide>'.change_to_str($params->get( 'playlistAutoHide')).'</playlistAutoHide>
<openPlaylistAtStart>'.change_to_str($params->get( 'openPlaylistAtStart')).'</openPlaylistAtStart>
<playlistTextSize>'.$params->get( 'playlistTextSize').'</playlistTextSize>
<libCols>'.$params->get( 'libCols').'</libCols>
<libRows>'.$params->get( 'libRows').'</libRows>
<libListTextSize>'.$params->get( 'libListTextSize').'</libListTextSize>
<libDetailsTextSize>'.$params->get( 'libDetailsTextSize').'</libDetailsTextSize>
<playBtnHint>'.JText::_( strtoupper ('playBtnHint') ).'</playBtnHint>
<pauseBtnHint>'.JText::_( strtoupper ('pauseBtnHint') ).'</pauseBtnHint>
<playPauseBtnHint>'.JText::_( strtoupper ('playPauseBtnHint') ).'</playPauseBtnHint>
<stopBtnHint>'.JText::_( strtoupper ('stopBtnHint') ).'</stopBtnHint>
<playPrevBtnHint>'.JText::_( strtoupper ('playPrevBtnHint') ).'</playPrevBtnHint>
<playNextBtnHint>'.JText::_( strtoupper ('playNextBtnHint') ).'</playNextBtnHint>
<volBtnHint>'.JText::_( strtoupper ('volBtnHint') ).'</volBtnHint>
<repeatBtnHint>'.JText::_( strtoupper ('repeatBtnHint') ).'</repeatBtnHint>
<shuffleBtnHint>'.JText::_( strtoupper ('shuffleBtnHint') ).'</shuffleBtnHint>
<hdBtnHint>'.JText::_( strtoupper ('hdBtnHint') ).'</hdBtnHint>
<playlistBtnHint>'.JText::_( strtoupper ('playlistBtnHint') ).'</playlistBtnHint>
<libOnBtnHint>'.JText::_( strtoupper ('libOnBtnHint') ).'</libOnBtnHint>
<libOffBtnHint>'.JText::_( strtoupper ('libOffBtnHint') ).'</libOffBtnHint>
<fullScreenBtnHint>'.JText::_( strtoupper ('fullScreenBtnHint') ).'</fullScreenBtnHint>
<backBtnHint>'.JText::_( strtoupper ('backBtnHint') ).'</backBtnHint>
<replayBtnText>'.JText::_( strtoupper ('replayBtnText') ).'</replayBtnText>
<nextBtnText>'.JText::_( strtoupper ('nextBtnText') ).'</nextBtnText>
<appBgColor>'."0x".$params->get( 'appBgColor').'</appBgColor>
<vidBgColor>'."0x".$params->get( 'vidBgColor').'</vidBgColor>
<framesBgColor>'."0x".$params->get( 'framesBgColor').'</framesBgColor>
<framesBgAlpha>'.(($params->get( 'framesBgAlpha')+0)/100).'</framesBgAlpha>
<ctrlsMainColor>'."0x".$params->get( 'ctrlsMainColor').'</ctrlsMainColor>
<ctrlsMainHoverColor>'."0x".$params->get( 'ctrlsMainHoverColor').'</ctrlsMainHoverColor>
<ctrlsMainAlpha>'.(($params->get( 'ctrlsMainAlpha')+0)/100).'</ctrlsMainAlpha>
<slideColor>'."0x".$params->get( 'slideColor').'</slideColor>
<itemBgHoverColor>'."0x".$params->get( 'itemBgHoverColor').'</itemBgHoverColor>
<itemBgSelectedColor>'."0x".$params->get( 'itemBgSelectedColor').'</itemBgSelectedColor>
<itemBgAlpha>'.(($params->get( 'framesBgAlpha')+0)/100).'</itemBgAlpha>
<textColor>'."0x".$params->get( 'textColor').'</textColor>
<textHoverColor>'."0x".$params->get( 'textHoverColor').'</textHoverColor>
<textSelectedColor>'."0x".$params->get( 'textSelectedColor').'</textSelectedColor>
<embed>'.JURI::base().$embed_url.'</embed>
</settings>';

?>

