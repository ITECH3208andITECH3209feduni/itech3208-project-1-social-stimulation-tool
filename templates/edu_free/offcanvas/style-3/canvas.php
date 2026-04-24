<?php
/**
 * Dialog Dropbar
 */

defined('_JEXEC') or die('Restricted Access');

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

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

$dialog_dropbar_animation = $params->get('dialog_dropbar_animation') ? ' animation:' . $params->get('dialog_dropbar_animation') . ';' : '';

$dialog_dropbar_content_width = $params->get('dialog_dropbar_content_width', '');
$dropbar_content_width = '';

if (in_array($dialog_dropbar_content_width, ['medium', 'large', 'xlarge', '2xlarge'])) {
    $dropbar_content_width = ' uk-width-' . $dialog_dropbar_content_width . ' uk-margin-auto';
} elseif ($dialog_dropbar_content_width === 'container') {
    $dropbar_content_width = ' container uk-margin-auto';
}

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

$dropbarTarget = $params->get('header_style') === '.tm-headerbar-bottom' ? '.tm-headerbar-bottom' : '.uk-navbar-container';

?>

<div id="tm-dialog" class="uk-dropbar uk-dropbar-large uk-dropbar-top" uk-drop="clsDrop:uk-dropbar; flip:false; container:.tm-header; target-y:.tm-header <?php echo $dropbarTarget; ?>; mode:click; target-x:.tm-header <?php echo $dropbarTarget; ?>; stretch:true; bgScroll:false;<?php echo $dialog_dropbar_animation; ?> animateOut:true; duration:300; toggle:false">

    <div class="tm-height-min-1-1 uk-flex uk-flex-column<?php echo $dropbar_content_width; ?>">

        <?php if ($centerVertical): ?>
            <div class="uk-margin-auto-vertical<?php echo $dialog_menu_horizontally; ?>">
        <?php else: ?>
            <div class="uk-margin-auto-bottom<?php echo $dialog_menu_horizontally; ?>">
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