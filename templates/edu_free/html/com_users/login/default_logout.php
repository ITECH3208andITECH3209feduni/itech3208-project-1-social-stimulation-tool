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

/** @var \Joomla\Component\Users\Site\View\Login\HtmlView $this */
?>
<div class="com-users-logout logout">
    <div class="uk-flex uk-flex-center">
    <div class="uk-width-large uk-background-muted uk-padding">
    <?php if ($this->params->get('show_page_heading')) : ?>
    <div class="page-header">
        <h1 class="el-title uk-h3 uk-margin-small-top uk-margin-remove-bottom">
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
    </div>
    <?php endif; ?>

    <?php if (($this->params->get('logoutdescription_show') == 1 && str_replace(' ', '', $this->params->get('logout_description', '')) != '') || $this->params->get('logout_image') != '') : ?>
        <div class="com-users-logout__description logout-description">
    <?php endif; ?>

    <?php if ($this->params->get('logoutdescription_show') == 1) : ?>
        <?php echo $this->params->get('logout_description'); ?>
    <?php endif; ?>

    <?php if ($this->params->get('logout_image') != '') : ?>
        <?php echo HTMLHelper::_('image', $this->params->get('logout_image'), empty($this->params->get('logout_image_alt')) && empty($this->params->get('logout_image_alt_empty')) ? false : $this->params->get('logout_image_alt'), ['class' => 'com-users-logout__image thumbnail float-end logout-image']); ?>
    <?php endif; ?>

    <?php if (($this->params->get('logoutdescription_show') == 1 && str_replace(' ', '', $this->params->get('logout_description', '')) != '') || $this->params->get('logout_image') != '') : ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo Route::_('index.php?task=user.logout'); ?>" method="post" class="com-users-logout__form form-horizontal well">
        <div class="com-users-logout__submit">
            <div class="uk-panel">
                <button type="submit" class="uk-button uk-button-primary uk-width-1-1">
                    <span class="icon-backward-2 icon-white" aria-hidden="true"></span>
                    <?php echo Text::_('JLOGOUT'); ?>
                </button>
            </div>
        </div>
        <?php if ($this->params->get('logout_redirect_url')) : ?>
            <input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('logout_redirect_url', $this->form->getValue('return', null, ''))); ?>">
        <?php else : ?>
            <input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('logout_redirect_menuitem', $this->form->getValue('return', null, ''))); ?>">
        <?php endif; ?>
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>
</div>
</div>
