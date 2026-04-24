<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Users\Site\View\Profile\HtmlView $this */
?>
<div class="uk-flex uk-flex-middle uk-margin">
	<div>
		<h3><?php echo Text::_('COM_USERS_PROFILE_CORE_LEGEND'); ?></h3>
	</div>
	<div class="uk-margin-auto-left">
		<?php if ($this->getCurrentUser()->id == $this->data->id) : ?>
			<a class="uk-button uk-button-primary" href="<?php echo Route::_('index.php?option=com_users&task=profile.edit&user_id=' . (int) $this->data->id); ?>">
				<span class="icon-user-edit" aria-hidden="true"></span> <?php echo Text::_('COM_USERS_EDIT_PROFILE'); ?>
			</a>
		<?php endif; ?>			
	</div>
</div>
<fieldset id="users-profile-core" class="com-users-profile__core">
    <dl class="dl-horizontal">
        <dt>
            <?php echo Text::_('COM_USERS_PROFILE_NAME_LABEL'); ?>
        </dt>
        <dd>
            <?php echo $this->escape($this->data->name); ?>
        </dd>
        <dt>
            <?php echo Text::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?>
        </dt>
        <dd>
            <?php echo $this->escape($this->data->username); ?>
        </dd>
        <dt>
            <?php echo Text::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?>
        </dt>
        <dd>
            <?php echo HTMLHelper::_('date', $this->data->registerDate, Text::_('DATE_FORMAT_LC1')); ?>
        </dd>
        <dt>
            <?php echo Text::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?>
        </dt>
        <?php if ($this->data->lastvisitDate !== null) : ?>
            <dd>
                <?php echo HTMLHelper::_('date', $this->data->lastvisitDate, Text::_('DATE_FORMAT_LC1')); ?>
            </dd>
        <?php else : ?>
            <dd>
                <?php echo Text::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
            </dd>
        <?php endif; ?>
    </dl>
</fieldset>
