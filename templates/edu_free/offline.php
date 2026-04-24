<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\AuthenticationHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

// Get two-factor authentication methods
$twofactormethods = AuthenticationHelper::getTwoFactorMethods();
$doc = Factory::getDocument();
$app = Factory::getApplication();

// Start output buffering for login form
ob_start();
?>

<form action="<?php echo Route::_('index.php', true); ?>" method="post" id="form-login" class="uk-grid-small" uk-grid>
    <div>
        <div class="uk-inline">
            <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: user"></span>
            <label for="username" class="visually-hidden"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
            <input name="username" type="text" class="uk-input uk-width-1-1" id="username" placeholder="<?php echo Text::_('JGLOBAL_USERNAME'); ?>" autocomplete="username" required aria-required="true">
        </div>
    </div>

    <div>
        <div class="uk-inline">
            <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: lock"></span>
            <label for="password" class="visually-hidden"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
            <input name="password" type="password" class="uk-input" id="password" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>" autocomplete="current-password" required aria-required="true">
        </div>
    </div>

    <?php if (count($twofactormethods) > 1) : ?>
        <div>
            <div class="uk-inline">
                <span class="uk-form-icon uk-form-icon-flip uk-icon" uk-icon="icon: lock"></span>
                <label for="secretkey" class="visually-hidden"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
                <input name="secretkey" type="text" class="uk-input" id="secretkey" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>" autocomplete="one-time-code" aria-describedby="secretkey-desc">
                <small id="secretkey-desc" class="uk-text-muted uk-display-block uk-margin-small-top">
                    <?php echo Text::_('JGLOBAL_SECRETKEY_HELP'); ?>
                </small>
            </div>
        </div>
    <?php endif; ?>

    <div>
        <button type="submit" name="Submit" class="uk-button uk-button-primary">
            <?php echo Text::_('JLOGIN'); ?>
        </button>
        
        <input type="hidden" name="option" value="com_users">
        <input type="hidden" name="task" value="user.login">
        <input type="hidden" name="return" value="<?php echo base64_encode(Uri::base()); ?>">
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>

<?php
$login_form = ob_get_clean();

// Render coming soon layout
echo LayoutHelper::render('comingsoon', [
    'language' => $this->language,
    'direction' => $this->direction,
    'params' => $this->params,
    'login' => true,
    'isOffline' => true,
    'login_form' => $login_form
]);