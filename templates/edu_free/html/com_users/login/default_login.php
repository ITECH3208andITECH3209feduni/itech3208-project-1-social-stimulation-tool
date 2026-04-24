<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Users\Site\View\Login\HtmlView $this */

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

$usersConfig = ComponentHelper::getParams('com_users');

?>
<div class="tm-form-login<?php echo $this->pageclass_sfx; ?>">
	<div class="uk-flex uk-flex-center">
		<div class="uk-width-large uk-background-muted uk-padding">
    <?php if ($this->params->get('show_page_heading')) : ?>
    <div class="page-header">
        <h1 class="el-title uk-h3 uk-margin-small-top uk-margin-remove-bottom">
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
    </div>
    <?php endif; ?>

    <?php if (($this->params->get('logindescription_show') == 1 && trim($this->params->get('login_description', ''))) || $this->params->get('login_image') != '') : ?>
    <div class="com-users-login__description login-description">
    <?php endif; ?>

        <?php if ($this->params->get('logindescription_show') == 1) : ?>
            <?php echo $this->params->get('login_description'); ?>
        <?php endif; ?>

        <?php if ($this->params->get('login_image') != '') : ?>
            <?php echo HTMLHelper::_('image', $this->params->get('login_image'), empty($this->params->get('login_image_alt')) && empty($this->params->get('login_image_alt_empty')) ? false : $this->params->get('login_image_alt'), ['class' => 'com-users-login__image login-image']); ?>
        <?php endif; ?>

    <?php if (($this->params->get('logindescription_show') == 1 && trim($this->params->get('login_description', ''))) || $this->params->get('login_image') != '') : ?>
    </div>
    <?php endif; ?>

    <form action="<?php echo Route::_('index.php?task=user.login'); ?>" method="post" id="com-users-login__form" class="form-validate">

        <fieldset>
            <?php echo $this->form->renderFieldset('credentials', ['class' => 'com-users-login__input']); ?>

				<?php if (PluginHelper::isEnabled('system', 'remember')) : ?>
					<div class="uk-margin">
						<label class="uk-form-label">
							<input class="uk-checkbox" type="checkbox" name="remember" id="remember" value="yes">
							<?php echo Text::_('COM_USERS_LOGIN_REMEMBER_ME') ?>
						</label>
					</div>
				<?php endif; ?>

            <?php foreach ($this->extraButtons as $button) :
                $dataAttributeKeys = array_filter(array_keys($button), function ($key) {
                    return substr($key, 0, 5) == 'data-';
                });
                ?>
                <div class="com-users-login__submit control-group">
                    <div class="controls">
                        <button type="button"
                                class="uk-button uk-button-secondary w-100 <?php echo $button['class'] ?? '' ?>"
                                <?php foreach ($dataAttributeKeys as $key) : ?>
                                    <?php echo $key ?>="<?php echo $button[$key] ?>"
                                <?php endforeach; ?>
                                <?php if ($button['onclick']) : ?>
                                onclick="<?php echo $button['onclick'] ?>"
                                <?php endif; ?>
                                title="<?php echo Text::_($button['label']) ?>"
                                id="<?php echo $button['id'] ?>"
                        >
                            <?php if (!empty($button['icon'])) : ?>
                                <span class="<?php echo $button['icon'] ?>"></span>
                            <?php elseif (!empty($button['image'])) : ?>
                                <?php echo HTMLHelper::_('image', $button['image'], Text::_($button['tooltip'] ?? ''), [
                                    'class' => 'icon',
                                ], true) ?>
                            <?php elseif (!empty($button['svg'])) : ?>
                                <?php echo $button['svg']; ?>
                            <?php endif; ?>
                            <?php echo Text::_($button['label']) ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="uk-margin">
                <button type="submit" class="uk-button uk-button-primary uk-width-1-1">
                    <?php echo Text::_('JLOGIN'); ?>
                </button>
            </div>

            <?php echo $this->form->renderControlFields(); ?>
        </fieldset>
    </form>
    <div class="uk-width-medium uk-text-center uk-margin-auto uk-margin-small-top">
        <a class="uk-link-text uk-text-small" href="<?php echo Route::_('index.php?option=com_users&view=reset'); ?>">
            <?php echo Text::_('COM_USERS_LOGIN_RESET'); ?>
        </a>
        <a class="uk-link-text uk-text-small" href="<?php echo Route::_('index.php?option=com_users&view=remind'); ?>">
            <?php echo Text::_('COM_USERS_LOGIN_REMIND'); ?>
        </a>
        <?php
        if ($usersConfig->get('allowUserRegistration')) :
            $regLinkMenuId = $this->params->get('customRegLinkMenu');
            $regLink = 'index.php?option=com_users&view=registration';

            if ($regLinkMenuId) {
                $menu = Factory::getApplication()->getMenu();
                $item = $menu->getItem($regLinkMenuId);

                if ($item) {
                    $regLink = 'index.php?Itemid=' . $regLinkMenuId;
                }
            }
            ?>
            <a class="uk-link-text uk-text-small" href="<?php echo Route::_($regLink); ?>">
                <?php echo Text::_('COM_USERS_LOGIN_REGISTER'); ?>
            </a>
        <?php endif; ?>
    </div>
</div>
	</div>
</div>
