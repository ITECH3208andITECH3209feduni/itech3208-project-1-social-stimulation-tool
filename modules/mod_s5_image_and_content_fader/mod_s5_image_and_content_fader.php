<?php
/**
@version 1.0: mod_s5_image_and_content_fader
Author: Shape 5 - Professional Template Community
Available for download at www.shape5.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$jslibrary = $params->get( 'jslibrary' );
$s5_dropdowntext = $params->get( 's5_dropdowntext' );
$s5_hidecar = $params->get( 's5_hidecar' );
$s5_hidebut = $params->get( 's5_hidebut' );
$s5_hidetext = $params->get( 's5_hidetext' );
$s5_delay = $params->get( 's5_delay' );
if ($s5_hidecar == "falsee") {$s5_hidecar = "false";}
if ($s5_hidecar == "truee") {$s5_hidecar = "true";}
if ($s5_hidebut == "falsee") {$s5_hidebut = "false";}
if ($s5_hidebut == "truee") {$s5_hidebut = "true";}
if ($s5_hidetext == "falsee") {$s5_hidetext = "false";}
if ($s5_hidetext == "truee") {$s5_hidetext = "true";}
$jseffect = $params->get( 'jseffect' );
$background_s5_iacf		= $params->get( 'background' );
$pretext_s5_iacf		= $params->get( 'pretext' );
$tween_time_s5_iacf	    = "0.5";
$height_s5_iacf		    = $params->get( 'height' );
$width_s5_iacf   		= $params->get( 'width' );
$picture1_s5_iacf		= $params->get( 'picture1' );
$picture1link_s5_iacf		= $params->get( 'picture1link' );
$picture1target_s5_iacf		= $params->get( 'picturetarget' );
$picture2_s5_iacf		= $params->get( 'picture2' );
$picture2link_s5_iacf		= $params->get( 'picture2link' );
$picture2target_s5_iacf		= $params->get( 'picturetarget' );
$picture3_s5_iacf		= $params->get( 'picture3' );
$picture3link_s5_iacf		= $params->get( 'picture3link' );
$picture3target_s5_iacf		= $params->get( 'picturetarget' );
$picture4_s5_iacf		= $params->get( 'picture4' );
$picture4link_s5_iacf		= $params->get( 'picture4link' );
$picture4target_s5_iacf		= $params->get( 'picturetarget' );
$picture5_s5_iacf		= $params->get( 'picture5' );
$picture5link_s5_iacf		= $params->get( 'picture5link' );
$picture5target_s5_iacf		= $params->get( 'picturetarget' );
$picture6_s5_iacf		= $params->get( 'picture6' );
$picture6link_s5_iacf		= $params->get( 'picture6link' );
$picture6target_s5_iacf		= $params->get( 'picturetarget' );
$picture7_s5_iacf		= $params->get( 'picture7' );
$picture7link_s5_iacf		= $params->get( 'picture7link' );
$picture7target_s5_iacf		= $params->get( 'picturetarget' );
$picture8_s5_iacf		= $params->get( 'picture8' );
$picture8link_s5_iacf		= $params->get( 'picture8link' );
$picture8target_s5_iacf		= $params->get( 'picturetarget' );
$picture9_s5_iacf		= $params->get( 'picture9' );
$picture9link_s5_iacf		= $params->get( 'picture9link' );
$picture9target_s5_iacf		= $params->get( 'picturetarget' );
$picture10_s5_iacf		= $params->get( 'picture10' );
$picture10link_s5_iacf		= $params->get( 'picture10link' );
$picture10target_s5_iacf	= $params->get( 'picturetarget' );
$display_time_s5_iacf   	= "10";
$s5stretchimage = $params->get( 's5stretchimage' );
$s5pixelwidth = $params->get( 's5pixelwidth' );


$title1	= $params->get( 'title1' );
$title2	= $params->get( 'title2' );
$title3	= $params->get( 'title3' );
$title4	= $params->get( 'title4' );
$title5	= $params->get( 'title5' );
$title6	= $params->get( 'title6' );
$title7	= $params->get( 'title7' );
$title8	= $params->get( 'title8' );
$title9	= $params->get( 'title9' );
$title10 = $params->get( 'title10' );




$picture1text_s5_iacf   	= $params->get( 'picture1text' );
$picture1spacing_s5_iacf   	= "12px";
$picture1textsize_s5_iacf   	= $params->get( 'picture1textsize' );
$picture1textcolor_s5_iacf   	= "ffffff";
$picture1textbg_s5_iacf   	= "333333";
$picture2text_s5_iacf   	= $params->get( 'picture2text' );
$picture2spacing_s5_iacf   	= "12px";
$picture2textsize_s5_iacf   	= $params->get( 'picture2textsize' );
$picture2textcolor_s5_iacf   	= "ffffff";
$picture2textbg_s5_iacf   	= "333333";
$picture3text_s5_iacf   	= $params->get( 'picture3text' );
$picture3spacing_s5_iacf   	= "12px";
$picture3textsize_s5_iacf   	= $params->get( 'picture3textsize' );
$picture3textcolor_s5_iacf   	= "ffffff";
$picture3textbg_s5_iacf   	= "333333";
$picture4text_s5_iacf   	= $params->get( 'picture4text' );
$picture4spacing_s5_iacf   	= "12px";
$picture4textsize_s5_iacf   	= $params->get( 'picture4textsize' );
$picture4textcolor_s5_iacf   	= "ffffff";
$picture4textbg_s5_iacf   	= "333333";
$picture5text_s5_iacf   	= $params->get( 'picture5text' );
$picture5spacing_s5_iacf   	= "12px";
$picture5textsize_s5_iacf   	= $params->get( 'picture5textsize' );
$picture5textcolor_s5_iacf   	= "ffffff";
$picture5textbg_s5_iacf   	= "333333";
$picture6text_s5_iacf   	= $params->get( 'picture6text' );
$picture6spacing_s5_iacf   	= "12px";
$picture6textsize_s5_iacf   	= $params->get( 'picture6textsize' );
$picture6textcolor_s5_iacf   	= "ffffff";
$picture6textbg_s5_iacf   	= "333333";
$picture7text_s5_iacf   	= $params->get( 'picture7text' );
$picture7spacing_s5_iacf   	= "12px";
$picture7textsize_s5_iacf   	= $params->get( 'picture7textsize' );
$picture7textcolor_s5_iacf   	= "ffffff";
$picture7textbg_s5_iacf   	= "333333";
$picture8text_s5_iacf   	= $params->get( 'picture8text' );
$picture8spacing_s5_iacf   	= "12px";
$picture8textsize_s5_iacf   	= $params->get( 'picture8textsize' );
$picture8textcolor_s5_iacf   	= "ffffff";
$picture8textbg_s5_iacf   	= "333333";
$picture9text_s5_iacf   	= $params->get( 'picture9text' );
$picture9spacing_s5_iacf   	= "12px";
$picture9textsize_s5_iacf   	= $params->get( 'picture9textsize' );
$picture9textcolor_s5_iacf   	= "ffffff";
$picture9textbg_s5_iacf   	= "333333";
$picture10text_s5_iacf   	= $params->get( 'picture10text' );
$picture10spacing_s5_iacf   	= "12px";
$picture10textsize_s5_iacf   	= $params->get( 'picture10textsize' );
$picture10textcolor_s5_iacf   	= "ffffff";
$picture10textbg_s5_iacf   	= "333333";

$picture1textweight_s5_iacf   	= "normal";
$picture2textweight_s5_iacf   	= "normal";
$picture3textweight_s5_iacf   	= "normal";
$picture4textweight_s5_iacf   	= "normal";
$picture5textweight_s5_iacf   	= "normal";
$picture6textweight_s5_iacf   	= "normal";
$picture7textweight_s5_iacf   	= "normal";
$picture8textweight_s5_iacf   	= "normal";
$picture9textweight_s5_iacf   	= "normal";
$picture10textweight_s5_iacf   	= "normal";

$picture1textopac_s5_iacf   	= "25";
$picture2textopac_s5_iacf   	= "25";
$picture3textopac_s5_iacf   	= "25";
$picture4textopac_s5_iacf   	= "25";
$picture5textopac_s5_iacf   	= "25";
$picture6textopac_s5_iacf   	= "25";
$picture7textopac_s5_iacf   	= "25";
$picture8textopac_s5_iacf   	= "25";
$picture9textopac_s5_iacf   	= "25";
$picture10textopac_s5_iacf   	= "25";

$non_ie_picture1textopac_s5_iacf = 55/ 100;
$non_ie_picture2textopac_s5_iacf = 55 / 100;
$non_ie_picture3textopac_s5_iacf = 55 / 100;
$non_ie_picture4textopac_s5_iacf = 55 / 100;
$non_ie_picture5textopac_s5_iacf = 55 / 100;
$non_ie_picture6textopac_s5_iacf = 55 / 100;
$non_ie_picture7textopac_s5_iacf = 55 / 100;
$non_ie_picture8textopac_s5_iacf = 55 / 100;
$non_ie_picture9textopac_s5_iacf = 55 / 100;
$non_ie_picture10textopac_s5_iacf = 55 / 100;


$tween_time_s5_iacf = $tween_time_s5_iacf*1000;
$display_time_s5_iacf = $display_time_s5_iacf*1000;

$text_display_effect = $tween_time_s5_iacf * 0.75;
$text_display_time_s5_iacf = $display_time_s5_iacf - $text_display_effect;


if ($picture1target_s5_iacf == "1") {
$picture1target_s5_iacf = "_blank"; }
if ($picture1target_s5_iacf == "0") {
$picture1target_s5_iacf = "_top"; }
if ($picture2target_s5_iacf == "1") {
$picture2target_s5_iacf = "_blank"; }
if ($picture2target_s5_iacf == "0") {
$picture2target_s5_iacf = "_top"; }
if ($picture3target_s5_iacf == "1") {
$picture3target_s5_iacf = "_blank"; }
if ($picture3target_s5_iacf == "0") {
$picture3target_s5_iacf = "_top"; }
if ($picture4target_s5_iacf == "1") {
$picture4target_s5_iacf = "_blank"; }
if ($picture4target_s5_iacf == "0") {
$picture4target_s5_iacf = "_top"; }
if ($picture5target_s5_iacf == "1") {
$picture5target_s5_iacf = "_blank"; }
if ($picture5target_s5_iacf == "0") {
$picture5target_s5_iacf = "_top"; }
if ($picture6target_s5_iacf == "1") {
$picture6target_s5_iacf = "_blank"; }
if ($picture6target_s5_iacf == "0") {
$picture6target_s5_iacf = "_top"; }
if ($picture7target_s5_iacf == "1") {
$picture7target_s5_iacf = "_blank"; }
if ($picture7target_s5_iacf == "0") {
$picture7target_s5_iacf = "_top"; }
if ($picture8target_s5_iacf == "1") {
$picture8target_s5_iacf = "_blank"; }
if ($picture8target_s5_iacf == "0") {
$picture8target_s5_iacf = "_top"; }
if ($picture9target_s5_iacf == "1") {
$picture9target_s5_iacf = "_blank"; }
if ($picture9target_s5_iacf == "0") {
$picture9target_s5_iacf = "_top"; }
if ($picture10target_s5_iacf == "1") {
$picture10target_s5_iacf = "_blank"; }
if ($picture10target_s5_iacf == "0") {
$picture10target_s5_iacf = "_top"; }

if ($s5stretchimage == "") {
$s5stretchimage = "actualsize";}
if ($s5pixelwidth == "") {
$s5pixelwidth = "null";}

$version = new JVersion();
/*
if($version->RELEASE <= '2.5'){
JHTML::_('behavior.mootools');
}
 */
//if($version->RELEASE >= '3.0'){
JHtml::_('jquery.framework');
//}
require(JModuleHelper::getLayoutPath('mod_s5_image_and_content_fader'));
?>
<?php if ($s5stretchimage  == "stretch") { ?>
<script type="text/javascript">//<![CDATA[
    document.write('<style>.jdGallery .slideElement {background-size:100% auto;}<?php if ($s5pixelwidth  != "") { ?>@media screen and (max-width: <?php echo $s5pixelwidth; ?>) {#myGallery { height:<?php echo $height_s5_iacf ?> !important; } .jdGallery .slideElement {background-size:auto auto !important;}}<?php } ?></style>');
//]]></script>
<?php } ?>





