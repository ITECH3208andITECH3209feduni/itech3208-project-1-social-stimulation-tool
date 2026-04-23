<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_breadcrumbs
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

?>
<span class="breadcrumbs pathway clearafter <?php echo $moduleclass_sfx; ?>">
<?php if ($params->get('showHere', 1))
	{
		echo '<span class="showHere">' .JText::_('MOD_BREADCRUMBS_HERE').'</span>';
	}
?>
<?php for ($i = 0; $i < $count; $i ++) :
	// If not the last item in the breadcrumbs add the separator
	if ($i < $count-1) {
		if(!empty($list[$i]->link)) {
			echo '<a href="'.$list[$i]->link.'"'.($i==0?' class="first">':'>').$list[$i]->name.'</a>';
		} else {
			echo '<span>'.$list[$i]->name.'</span>';
		}
	}  else if ($params->get('showLast', 1)) { // when $i == $count -1 and 'showLast' is true
	    echo '<span class="current">'.$list[$i]->name.'</span>';
	}
endfor; ?>
</span>