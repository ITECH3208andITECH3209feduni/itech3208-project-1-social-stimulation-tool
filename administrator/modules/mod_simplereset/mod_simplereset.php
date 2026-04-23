<?php
#********************************************************************
#* Simple Reset Module voor Joomla 1.7:
#* @author	Joomill.nl <info@joomill.nl>
#* @link	http://www.joomill.nl
#* @copyright 	Copyright (C) 2011 Joomill.nl  
#* @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL 
#* 
#* Simple Reset Module is free software: you can redistribute it  
#* and/or modify it under the terms of the GNU General Public License 
#* as published by the Free Software Foundation, either version 3 of 
#* the License, or(at your option) any later version.
#*
#* Simple Reset Module is distributed in the hope that it will be 
#* useful, but WITHOUT ANY WARRANTY; without even the implied 
#* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
#* See the GNU General Public License for more details.
#*
#* You should have received a copy of the GNU General Public License
#* along with Simple Reset Module.
#* If not, see <http://www.gnu.org/licenses/>.
#* 
#*******************************************************************

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$msg = '';
$reset = JRequest::getVar('reset', '', '', 'WORD');

if ($reset && ($reset == 'hits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__content SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'revision')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__content SET version=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'bannerimp')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__banners SET impmade=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'bannerclicks')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__banners SET clicks=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}

if ($reset && ($reset == 'weblinkshits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__weblinks SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}

if ($reset && ($reset == 'passreset')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__users SET resetCount=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}

if ($reset && ($reset == 'redirecthits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__redirect_links SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}

if ($reset && ($reset == 'docmanhits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__docman SET dmcounter=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'ipropertyhits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__iproperty SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'fwrehits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__fw_realestate_property SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'fwgalhits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__fwg_files SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'igalcathits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__igallery SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'igalimghits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__igallery_img SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'rokgalviews')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__rokgallery_file_views SET kount=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'rokgalloves')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__rokgallery_file_loves SET kount=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'zoohits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__zoo_item SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'ktwoitemhits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__k2_items SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'ktwoattachhits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__k2_attachments SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'vmcathits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__virtuemart_categories SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'vmproducthits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__virtuemart_products SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'jsprofile')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__community_users SET view=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'jsevents')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__community_events SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'jsphotos')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__community_photos SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'jsalbums')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__photos_albums SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'jsvideos')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__community_videos SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
?>

<div style="margin:1em;">
<form action="<?php echo JRoute::_('index.php'); ?>" method="post">
    <?php echo JText::_('WHATRESET'); ?><br/><br/>
    <div style="font-size:1em; font-weight:bold;"> Joomla Core Components </div>
	<input type="radio" name="reset" value="hits" /> <?php echo JText::_('HITS'); ?><br/>
	<input type="radio" name="reset" value="revision" /> <?php echo JText::_('REVISION'); ?><br/>
	<input type="radio" name="reset" value="bannerimp" /> <?php echo JText::_('BANNERIMP'); ?><br/>
	<input type="radio" name="reset" value="bannerclicks" /> <?php echo JText::_('BANNERCLICKS'); ?><br/>
	<input type="radio" name="reset" value="weblinkshits" /> <?php echo JText::_('WEBLINKSHITS'); ?><br/>
    <input type="radio" name="reset" value="passreset" /> <?php echo JText::_('PASSRESET'); ?><br/>
    <input type="radio" name="reset" value="redirecthits" /> <?php echo JText::_('REDIRECTHITS'); ?><br/><br/>
    <div style="font-size:1em; font-weight:bold;"> 3rd Party Components </div>
    <input type="radio" name="reset" value="docmanhits" /> <?php echo JText::_('Docman Hits'); ?><br/>
    <input type="radio" name="reset" value="ipropertyhits" /> <?php echo JText::_('IProperty Hits'); ?><br/>
    <input type="radio" name="reset" value="fwrehits" /> <?php echo JText::_('Fastw3b Real Estate Hits'); ?><br/>
    <input type="radio" name="reset" value="fwgalhits" /> <?php echo JText::_('Fastw3b Gallery Hits'); ?><br/>
    <input type="radio" name="reset" value="igalcathits" /> <?php echo JText::_('Ignite Gallery Category Hits'); ?><br/>
    <input type="radio" name="reset" value="igalimghits" /> <?php echo JText::_('Ignite Gallery Images Hits'); ?><br/>
    <input type="radio" name="reset" value="rokgalviews" /> <?php echo JText::_('RokGallery Images Views'); ?><br/>
    <input type="radio" name="reset" value="rokgalloves" /> <?php echo JText::_('RokGallery Images Loves'); ?><br/>
    <input type="radio" name="reset" value="zoohits" /> <?php echo JText::_('Yootheme ZOO Items Hits'); ?><br/>
    <input type="radio" name="reset" value="ktwoitemhits" /> <?php echo JText::_('K2 Items Hits'); ?><br/>
    <input type="radio" name="reset" value="ktwoattachhits" /> <?php echo JText::_('K2 Attachments Hits'); ?><br/>
    <input type="radio" name="reset" value="vmcathits" /> <?php echo JText::_('Virtuemart Category Hits'); ?><br/>
   	<input type="radio" name="reset" value="vmproducthits" /> <?php echo JText::_('Virtuemart Product Hits'); ?><br/>   
    <input type="radio" name="reset" value="jsprofile" /> <?php echo JText::_('Jomsocial Profile Views'); ?><br/>
   	<input type="radio" name="reset" value="jsevents" /> <?php echo JText::_('Jomsocial Events Hits'); ?><br/>
    <input type="radio" name="reset" value="jsphotos" /> <?php echo JText::_('Jomsocial Photo Hits'); ?><br/>
    <input type="radio" name="reset" value="jsalbums" /> <?php echo JText::_('Jomsocial Photo Album Hits'); ?><br/>
    <input type="radio" name="reset" value="jsvideos" /> <?php echo JText::_('Jomsocial Video Hits'); ?><br/>
    <br/>
	<input type="submit" value="reset" />
</form>
<?php if ($msg) { ?>
	<div style="color:red; padding-top:1em;"><strong><?php echo $msg; ?></strong></div>
<?php } ?>
</div>
