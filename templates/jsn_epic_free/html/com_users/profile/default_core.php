<?php
/**
 * @version		$Id: default_core.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

jimport('joomla.user.helper');
?>

<fieldset id="users-profile-core">
	<legend>
		<?php echo JText::_('COM_USERS_PROFILE_CORE_LEGEND'); ?>
	</legend>
	<div class="jsn-formRow clearafter">
		<div class="jsn-formRow-lable">
			<?php echo JText::_('COM_USERS_PROFILE_NAME_LABEL'); ?>
		</div>
		<div class="jsn-formRow-input">
			<?php echo $this->data->name; ?>
		</div>
	</div>
	<div class="jsn-formRow clearafter">
		<div class="jsn-formRow-lable">
			<?php echo JText::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?>
		</div>
		<div class="jsn-formRow-input">
			<?php echo $this->data->username; ?>
		</div>
	</div>
	<div class="jsn-formRow clearafter">
		<div class="jsn-formRow-lable">
			<?php echo JText::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?>
		</div>
		<div class="jsn-formRow-input">
			<?php echo JHTML::_('date',$this->data->registerDate); ?>
		</div>
	</div>
	<div class="jsn-formRow clearafter">
		<div class="jsn-formRow-lable">
			<?php echo JText::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?>
		</div>

		<?php if ($this->data->lastvisitDate != '0000-00-00 00:00:00'){?>
			<div class="jsn-formRow-input">
				<?php echo JHTML::_('date',$this->data->lastvisitDate); ?>
			</div>
		<?php }
		else {?>
			<div class="jsn-formRow-input">
				<?php echo JText::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
			</div>
		<?php } ?>
	</div>
		
</fieldset>
