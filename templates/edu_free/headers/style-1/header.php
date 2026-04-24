<?php
/**
 * 1. Horizontal Left
 */

defined ('_JEXEC') or die('Restricted Access');

use Joomla\CMS\Helper\ModuleHelper;
use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Language\Text;

$data = $displayData;
$params = $data->params; // Cache params for reuse
$attrs_sticky = [];

$navbar_search = $params->get( 'search_position' );

$feature_folder_path = JPATH_THEMES . '/' . $data->template->template . '/features';
$canvas_folder_path = JPATH_THEMES . '/' . $data->template->template . '/offcanvas/';

$mobile_breakpoint_options     = $params->get( 'mobile_breakpoint_options', 'm' );
$toolbar_visibility     = $params->get( 'toolbar_visibility' );

include_once $feature_folder_path . '/contact.php';
include_once $feature_folder_path . '/cookie.php';
include_once $feature_folder_path . '/logo.php';
include_once $feature_folder_path . '/menu.php';
include_once $feature_folder_path . '/mobile.php';
include_once $feature_folder_path . '/search.php';
include_once $feature_folder_path . '/social.php';
include_once $feature_folder_path . '/toolbar.php';

$header_outside     = $params->get( 'boxed_layout' ) && $params->get( 'boxed_header_outside' );
$transparent_header = $params->get( 'transparent_header' );
$header_wrap[]      = '';
$header_wrap[]      = 'tm-header uk-visible@' . $mobile_breakpoint_options . ' header-' . $params->get( 'header_style' );
$header_outside ? $header_wrap[] = 'tm-outside' : '';
// Navbar Container
$navbar_container[] = 'uk-navbar-container';

$sticky = $params->get( 'header_navbar' );

if ( $sticky ) {

	$attrs_sticky[] = 'sel-target: .uk-navbar-container';
	$attrs_sticky[] = 'cls-active: uk-navbar-sticky';
	$attrs_sticky[] = 'media: @' . $mobile_breakpoint_options;

	if ( '2' === $sticky ) {
		$attrs_sticky[] = 'animation: uk-animation-slide-top';
		$attrs_sticky[] = 'show-on-up: true';
	}
}

if ( $header_outside && $transparent_header ) {

	$header_wrap[] = 'tm-header-transparent';

	if ( $sticky ) {
		$attrs_sticky[] = 'cls-inactive: uk-navbar-transparent uk-' . $transparent_header;
		$attrs_sticky[] = 'top: 300';
		if ( '1' === $sticky ) {
			$attrs_sticky[] = 'animation: uk-animation-slide-top';
		}
	} else {
		$navbar_container[] = 'uk-navbar-transparent uk-' . $transparent_header;
	}
}

$transparent_header_cls = ( $transparent_header && $header_outside ) ? ' uk-navbar-transparent uk-' . $transparent_header : '';

$header_container = $params->get( 'header_maxwidth', 'default' );

// Width Container
$header_container_cls[] = '';

if ( $header_outside ) {
	$header_container_cls[]  = $header_container === 'expand' ? 'uk-container uk-container-expand' : 'container tm-page-width';
} else {
	$header_container_cls[]  = $header_container != 'default' ? 'uk-container uk-container-' . $header_container : 'container';
}

$social_pos               = $params->get( 'social_pos' );
$contact_pos              = $params->get( 'contact_pos' );

$remove_logo_padding    = $params->get( 'remove_logo_padding', '0' );
$header_container_cls[] = $header_container === 'expand' && $remove_logo_padding ? 'uk-padding-remove-left' : '';
$header_menu_style      = $params->get( 'header_menu_options' ) ? ' uk-navbar-primary' : '';
$header_wrap            = implode( ' ', array_filter( $header_wrap ) );
$attrs_sticky           = ' uk-sticky="' . implode( '; ', array_filter( $attrs_sticky ) ) . '"';
$header_container_cls   = implode( ' ', array_filter( $header_container_cls ) );
$navbar_container       = implode( ' ', array_filter( $navbar_container ) );

/**
 * Helper classes for-
 * social icons, contact info, site logo, Menu header, toolbar, cookie, search.
 */

$contact = new HelixUltimateFeatureContact( $params );
$cookie  = new HelixUltimateFeatureCookie( $params );
$logo    = new HelixUltimateFeatureLogo( $params );
$menu    = new HelixUltimateFeatureMenu( $params );
$mobile    = new HelixUltimateFeatureMobile( $params );
$search  = new HelixUltimateFeatureSearch( $params );
$social  = new HelixUltimateFeatureSocial( $params );
$toolbar  = new HelixUltimateFeatureToolbar( $params );

$isheaderMenu = $params->get('header_enable_menu', 0);

$class_sfx  = $subnav_cls = '';
$menu_style_cls = $params->get('header_menu_options') ? 'primary' : 'default';

$menuStyle = $params->get('header_menu_style', 'navbar', 'STRING');
$subnav_cls .= $params->get('hd_subnav_divider') ? ' uk-subnav-divider' : '';
$subnav_cls .= $params->get('hd_subnav_pill') ? ' uk-subnav-pill' : '';

$class_sfx = $menuStyle == 'subnav' ? ' uk-subnav'.$subnav_cls : '';

$menuType = $params->get('header_menu', 'mainmenu', 'STRING');
$maxLevel = $params->get('hd_menu_max_level', 0, 'INT');

$headerMenuModule = Helper::createModule('mod_menu', [
	'title' => 'Main Menu',
	'params' => '{"menutype":"' . $menuType . '","base":"","startLevel":"1","endLevel":"' . $maxLevel . '","showAllChildren":"1","tag_id":"","class_sfx":"'.$class_sfx.'","window_open":"","layout":"_:'.$menuStyle.'","moduleclass_sfx":"","cache":"1","cache_time":"900","cachemode":"itemid","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}',
	'name' => 'menu'
]);

// Uikit Navbar style
$hasModMenu = array_search('mod_menu', array_column(ModuleHelper::getModules('menu'), 'module'));
$navbarMenuType = $params->get('navbar_menu', 'mainmenu', 'STRING');
$nav_cls = $params->get('nav_style') ? $params->get('nav_style') : 'navbar';
$navbarMenuModule = Helper::createModule('mod_menu', [
	'title' => 'Main Menu',
	'params' => '{"menutype":"' . $navbarMenuType . '","base":"","startLevel":1,"endLevel":0,"showAllChildren":1,"tag_id":"","class_sfx":"","window_open":"","layout":"_:'.$nav_cls.'","moduleclass_sfx":"","cache":"1","cache_time":"900","cachemode":"itemid","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}',
	'name' => 'menu'
]);

?>

<?php echo $cookie->renderFeature(); ?>

<?php if ( ! empty($toolbar_visibility) ) : ?>
	<?php echo $mobile->renderFeature(); ?>
<?php endif; ?>

<?php if ( ! $params->get('toolbar_transparent') ) : ?>
  <?php echo $toolbar->renderFeature(); ?>
<?php endif; ?>

<?php if ( empty($toolbar_visibility) ) : ?>
	<?php echo $mobile->renderFeature(); ?>
<?php endif; ?>

<div class="<?php echo $header_wrap; ?>" uk-header>

	<?php if ( $params->get('toolbar_transparent') ) : ?>
		<?php echo $toolbar->renderFeature(); ?>
	<?php endif; ?>

	<?php if ( $sticky ) : ?>
		<div<?php echo $attrs_sticky; ?>>
	<?php endif; ?>

<div class="<?php echo $navbar_container; echo $header_menu_style; ?>">

<div class="<?php echo $header_container_cls; ?>">

<nav class="uk-navbar" uk-navbar>

	<div class="uk-navbar-left">

		<?php echo $logo->renderFeature(); ?>

		<?php if ( ModuleHelper::getModules('logo') ) : ?>
			<jdoc:include type="modules" name="logo" style="warp_xhtml" />
		<?php endif; ?>

		<?php if ( $params->get('offcanvas_style') && $params->get('dialog_toggle') == 'navbar-start' ) : ?>
			<a class="uk-navbar-toggle uk-navbar-toggle-animate" href="#" uk-toggle="target: #tm-dialog;">
				<?php if ( $params->get('dialog_toggle_text') ) : ?>
					<span class="uk-margin-small-right uk-text-middle"><?php echo Text::_('TPL_HELIX_ULTIMATE_MENU_HEADER'); ?></span>
				<?php endif; ?>
				<div uk-navbar-toggle-icon></div>
			</a>
		<?php endif; ?>

		<?php if ( ! $params->get('navbar_menu_style') ) : ?>
			<?php echo $menu->renderFeature(); ?>
		<?php else: ?>
			<?php if ($hasModMenu === false): ?>
				<?php echo ModuleHelper::renderModule($navbarMenuModule, ['style' => 'navbar_xhtml']); ?>
			<?php else: ?>
				<jdoc:include type="modules" name="menu" style="navbar_xhtml" />
			<?php endif ?>
		<?php endif; ?>

		<?php if ( ModuleHelper::getModules( 'navbar' ) ) : ?>
			<jdoc:include type="modules" name="navbar" style="warp_xhtml" />
		<?php endif; ?>

		<?php if ( $navbar_search === 'navbar' ) : ?>
			<div class="uk-navbar-item">
				<?php echo $search->renderFeature(); ?>
			</div>
		<?php endif; ?>

		<?php if ( $social_pos === 'navbar' ) : ?>
			<div class="uk-navbar-item">
				<?php echo $social->renderFeature(); ?>
			</div>
		<?php endif; ?>

		<?php if ( $contact_pos === 'navbar' ) : ?>
			<div class="uk-navbar-item">
				<?php echo $contact->renderFeature(); ?>
			</div>
		<?php endif; ?>

		<?php if ( $params->get('offcanvas_style') && $params->get('dialog_toggle') == 'navbar-end' ) : ?>
			<a class="uk-navbar-toggle uk-navbar-toggle-animate" href="#" uk-toggle="target: #tm-dialog;">
				<?php if ( $params->get('dialog_toggle_text') ) : ?>
					<span class="uk-margin-small-right uk-text-middle"><?php echo Text::_('TPL_HELIX_ULTIMATE_MENU_HEADER'); ?></span>
				<?php endif; ?>
				<div uk-navbar-toggle-icon></div>
			</a>
		<?php endif; ?>

	</div>

	<?php if ( ModuleHelper::getModules( 'header' ) || $navbar_search === 'header' || $social_pos === 'header' || $contact_pos === 'header' || $isheaderMenu || ($params->get('offcanvas_style') && ($params->get('dialog_toggle') == 'header-start' || $params->get('dialog_toggle') == 'header-end')) ) : ?>

		<div class="uk-navbar-right">

		<?php if ( $params->get('offcanvas_style') && $params->get('dialog_toggle') == 'header-start' ) : ?>
			<a class="uk-navbar-toggle uk-navbar-toggle-animate" href="#" uk-toggle="target: #tm-dialog;">
				<?php if ( $params->get('dialog_toggle_text') ) : ?>
					<span class="uk-margin-small-right uk-text-middle"><?php echo Text::_('TPL_HELIX_ULTIMATE_MENU_HEADER'); ?></span>
				<?php endif; ?>
				<div uk-navbar-toggle-icon></div>
			</a>
		<?php endif; ?>

			<?php if ($isheaderMenu) : ?>
				<?php if ($menuStyle != 'navbar'): ?>
					<div class="uk-navbar-item">
				<?php endif; ?>
					<?php echo ModuleHelper::renderModule($headerMenuModule); ?>
				<?php if ($menuStyle != 'navbar'): ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( ModuleHelper::getModules( 'header' ) ) : ?>
				<jdoc:include type="modules" name="header" style="warp_xhtml" />
			<?php endif; ?>

			<?php if ( $navbar_search === 'header' ) : ?>
				<div class="uk-navbar-item">
					<?php echo $search->renderFeature(); ?>
				</div>
			<?php endif; ?>

			<?php if ( $social_pos === 'header' ) : ?>
				<div class="uk-navbar-item">
					<?php echo $social->renderFeature(); ?>
				</div>
			<?php endif; ?>

			<?php if ( $contact_pos === 'header' ) : ?>
				<div class="uk-navbar-item">
					<?php echo $contact->renderFeature(); ?>
				</div>
			<?php endif; ?>

			<?php if ( $params->get('offcanvas_style') && $params->get('dialog_toggle') == 'header-end' ) : ?>
				<a class="uk-navbar-toggle uk-navbar-toggle-animate" href="#" uk-toggle="target: #tm-dialog;">
				<?php if ( $params->get('dialog_toggle_text') ) : ?>
					<span class="uk-margin-small-right uk-text-middle"><?php echo Text::_('TPL_HELIX_ULTIMATE_MENU_HEADER'); ?></span>
				<?php endif; ?>
				<div uk-navbar-toggle-icon></div>
				</a>
			<?php endif; ?>

		</div>

	<?php endif; ?>

</nav>

</div>

</div>

<?php if ($params->get('offcanvas_style') == 'style-3'): ?>
	<?php include_once $canvas_folder_path . 'style-3/canvas.php'; ?>
<?php endif; ?>

<?php if ( $sticky ) : ?>
	</div>
<?php endif; ?>

</div>