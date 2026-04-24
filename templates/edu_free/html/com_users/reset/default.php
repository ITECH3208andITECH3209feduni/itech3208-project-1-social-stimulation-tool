<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Users\Site\View\Reset\HtmlView $this */
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

?>
<div class="com-users-reset tm-form-reset">
	<div class="uk-flex uk-flex-center">
		<div class="uk-width-large uk-background-muted uk-padding">    
    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1 class="el-title uk-h3 uk-margin-small-top uk-margin-remove-bottom">
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
        </div>
    <?php endif; ?>
    <form action="<?php echo Route::_('index.php?task=reset.request'); ?>" method="post" id="user-registration" class="com-users-reset__form form-validate">
        <?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
            <fieldset>
                <?php if (isset($fieldset->label)) : ?>
                    <legend><?php echo Text::_($fieldset->label); ?></legend>
                <?php endif; ?>
                <?php echo $this->form->renderFieldset($fieldset->name); ?>
            </fieldset>
        <?php endforeach; ?>
        <div class="com-users-reset__submit control-group">
            <div class="controls">
                <button type="submit" class="uk-button uk-button-primary uk-width-1-1 validate">
                    <?php echo Text::_('JSUBMIT'); ?>
                </button>
            </div>
        </div>

        <?php echo $this->form->renderControlFields(); ?>
    </form>
</div>
</div>
</div>
