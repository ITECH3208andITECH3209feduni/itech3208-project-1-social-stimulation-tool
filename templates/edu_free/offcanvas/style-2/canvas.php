<?php
/**
 * Dialog Modal
 */

defined('_JEXEC') or die('Restricted Access');

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

$doc = Factory::getDocument();

$data = $displayData;
$params = $data->params; // Cache params for reuse

$navbar_search = $params->get('search_position');
$feature_folder_path = JPATH_THEMES . '/' . $data->template->template . '/features';
$dialog_offcanvas_mode = $params->get('dialog_offcanvas_mode', 'slide');
$dialog_offcanvas_overlay = $params->get('dialog_offcanvas_overlay') ? ' overlay: true; ' : '';
$dialog_offcanvas_flip = $params->get('dialog_offcanvas_flip', 0) ? ' flip: true;' : '';
$dialog_menu_horizontally = $params->get('dialog_menu_horizontally', 0) ? ' uk-text-center' : '';

include_once $feature_folder_path . '/contact.php';
include_once $feature_folder_path . '/cookie.php';
include_once $feature_folder_path . '/logo.php';
include_once $feature_folder_path . '/menu.php';
include_once $feature_folder_path . '/mobile.php';
include_once $feature_folder_path . '/search.php';
include_once $feature_folder_path . '/social.php';

$social_pos = $params->get('social_pos');
$contact_pos = $params->get('contact_pos');

/**
 * Helper classes for-
 * social icons, contact info, site logo, Menu header, toolbar, cookie, search.
 */
$contact = new HelixUltimateFeatureContact($params);
$cookie = new HelixUltimateFeatureCookie($params);
$logo = new HelixUltimateFeatureLogo($params);
$menu = new HelixUltimateFeatureMenu($params);
$mobile = new HelixUltimateFeatureMobile($params);
$search = new HelixUltimateFeatureSearch($params);
$social = new HelixUltimateFeatureSocial($params);
$logo_init = $params->get('logo_image') || $params->get('logo_text') || !empty(ModuleHelper::getModules('logo'));

$dialog_menu_style_cls = $params->get('dialog_menu_options') ? 'primary' : 'default';
$dialog_menu_style_cls .= $params->get('dialog_menu_divider', 0) ? ' uk-nav-divider' : '';
$dialog_menu_style_cls .= $params->get('dialog_menu_horizontally', 0) ? ' uk-nav-center' : '';

$dialog_modal_content_width = $params->get('dialog_modal_content_width') ? ' uk-width-' . $params->get('dialog_modal_content_width') : ' uk-width-auto@s';

$dialogmainmenuType = $params->get('dialog_navbar_menu', 'mainmenu', 'STRING');
$dialogmaxLevel = $params->get('dialog_navbar_menu_max_level', 0, 'INT');

$menuModule = Helper::createModule('mod_menu', [
    'title' => 'Main Menu',
    'params' => '{"menutype":"' . $dialogmainmenuType . '","base":"","startLevel":"1","endLevel":"' . $dialogmaxLevel . '","showAllChildren":"1","tag_id":"","class_sfx":"uk-nav uk-nav-' . $dialog_menu_style_cls . ' uk-nav-accordion","window_open":"","layout":"_:nav","moduleclass_sfx":"","cache":"1","cache_time":"900","cachemode":"itemid","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0", "hu_offcanvas": 1}',
    'name' => 'menu'
]);

$searchModule = Helper::getSearchModule();

// Check if dialog has any content
$hasDialogModules = !empty(ModuleHelper::getModules('dialog'));
$hasDialogContent = $hasDialogModules 
    || $params->get('dialog_show_menu') 
    || $params->get('dialog_enable_search') 
    || $params->get('dialog_enable_social') 
    || $params->get('dialog_enable_contact');

$centerVertical = $params->get('dialog_center_vertical');

?>

<div id="tm-dialog" class="uk-modal-full" uk-modal>

    <div class="uk-modal-dialog uk-flex">

        <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>

        <div class="uk-modal-body uk-padding-large uk-margin-auto uk-flex uk-flex-column uk-box-sizing-content<?php echo $dialog_menu_horizontally . $dialog_modal_content_width; ?>" uk-height-viewport>

            <?php if ($centerVertical): ?>
                <div class="uk-margin-auto-vertical">
            <?php else: ?>
                <div class="uk-margin-auto-bottom">
            <?php endif; ?>

            <?php if (!$hasDialogContent): ?>
                <p class="uk-alert uk-alert-warning">
                    <?php echo Text::_('HELIX_ULTIMATE_NO_MODULE_DIALOG'); ?>
                </p>
            <?php endif; ?>

            <?php if ($params->get('dialog_show_menu')): ?>
                <?php echo ModuleHelper::renderModule($menuModule); ?>
            <?php endif; ?>

            <?php if ($hasDialogModules): ?>
                <jdoc:include type="modules" name="dialog" style="offcanvas_xhtml" />
            <?php endif; ?>

            <?php if ($params->get('dialog_enable_search')): ?>
                <div class="uk-margin-top">
                    <?php echo ModuleHelper::renderModule($searchModule, ['style' => 'sp_xhtml']); ?>
                </div>
            <?php endif; ?>

            <?php if ($params->get('dialog_enable_social')): ?>
                <div class="uk-margin-top">
                    <?php echo $social->renderFeature(); ?>
                </div>
            <?php endif; ?>

            <?php if ($params->get('dialog_enable_contact')): ?>
                <div class="uk-margin-top">
                    <?php echo $contact->renderFeature(); ?>
                </div>
            <?php endif; ?>

            </div>

        </div>

    </div>
</div>