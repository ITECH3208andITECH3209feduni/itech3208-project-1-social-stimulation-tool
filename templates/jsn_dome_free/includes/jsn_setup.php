<?php
/**
 * @author    JoomlaShine.com http://www.joomlashine.com
 * @copyright Copyright (C) 2008 - 2011 JoomlaShine.com. All rights reserved.
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

	// No direct access
	defined('_JEXEC') or die('Restricted access');

	// Include MooTools libs
	//JHTML::_('behavior.mootools');
	$version = new JVersion();
	if($version->RELEASE <= '2.5'){
		JHTML::_('behavior.mootools');
	}
	if($version->RELEASE >= '3.0'){
		JHtml::_('jquery.framework');
	}
	JHTML::_('behavior.modal');

	require_once 'jsn_document.php';
	// Include JSN Utils
	$jsnutils = JSNUtils::getInstance();

	$app 		= JFactory::getApplication();

	/****************************************************************/
	/* PUBLIC TEMPLATE PARAMETERS */
	/****************************************************************/

	// Path to logo image starting from the Joomla! root folder (! without preceding slash !)
	$enable_colored_logo = ($this->params->get("enableColoredLogo", 0) == 1)?true:false;

	// Logo Path
	$logo_path = $this->params->get("logoPath", "");
	if ($logo_path != "")
	{
		$logo_path = $this->baseurl.'/'.htmlspecialchars($logo_path);
	}

	/* URL where logo image should link to (! without preceding slash !)
	   Leave this box empty if you want your logo to be clickable. */
	$logo_link = $this->params->get("logoLink", "");
	if (strpos($logo_link, "http")=== false && $logo_link != '')
	{
		$logo_link = $jsnutils->trimPreceddingSlash($logo_link);
		$logo_link = $this->baseurl."/".$logo_link;
	}

	// Slogan text to be attached to the logo image ALT text for SEO purpose.
	$logo_slogan = $this->params->get("logoSlogan", "");

	// Overall template width.
	$template_width = $this->params->get("templateWidth", "narrow");

	// Define custom width for template in narrow mode
	$narrow_width = intval($this->params->get("narrowWidth", "960"));

	// Define custom width for template in wide mode
	$wide_width = intval($this->params->get("wideWidth", "1150"));

	// Define custom width for template in float mode
	$float_width = intval($this->params->get("floatWidth", "90"));
	$float_width = ($float_width > 100)?100:$float_width;

	/* Promo left column width specified in percentage.
	   Only whole number is allowed, for example 25% - correct, 25.5% - incorrect */
	$promo_left_width = intval($this->params->get("promoLeftWidth", "23"));

	/* Promo right column width specified in percentage.
	   Only whole number is allowed, for example 25% - correct, 25.5% - incorrect */
	$promo_right_width = intval($this->params->get("promoRightWidth", "23"));

	/* Left column width specified in percentage.
	   Only whole number is allowed, for example 25% - correct, 25.5% - incorrect */
	$left_width = intval($this->params->get("leftWidth", "23"));

	/* Right column width specified in percentage.
	   Only whole number is allowed, for example 25% - correct, 25.5% - incorrect */
	$right_width = intval($this->params->get("rightWidth", "23"));

	/* InnerLeft column width specified in percentage.
	   Only whole number is allowed, for example 25% - correct, 25.5% - incorrect */
	$innerleft_width = intval($this->params->get("innerleftWidth", "28"));

	/* InnerRight column width specified in percentage.
	   Only whole number is allowed, for example 25% - correct, 25.5% - incorrect */
	$innerright_width = intval($this->params->get("innerrightWidth", "28"));

	// Definition whether to show mainbody on frontpage page or not
	$show_frontpage = ($this->params->get("showFrontpage", 1) == 1)?true:false;

	// Template color: orange | red | cyan | green | yellow | pink
	$template_color = $this->params->get("templateColor", "red");

	/* Template text style:
	   1 - Business / Corporation
	   2 - Personal / Blog
	   3 - News / Magazines */
	$template_textstyle = $this->params->get("templateTextStyle", "business");

	// Template text size
	$template_textsize = $this->params->get("templateTextSize", "medium");

	/****************************************************************/
	/* PRIVATE TEMPLATE PARAMETERS */
	/****************************************************************/

	// Get browser info
	$brower_info 		= $jsnutils->getBrowserInfo(null);
	$is_ie				= (@$brower_info['browser'] == 'msie');
	$is_ie7				= (@$brower_info['browser'] == 'msie' && (int) @$brower_info['version'] == 7);
	$ieoffset 			= ($is_ie7)?0.1:0;

	// Get template details
	$template_details   	= json_decode($jsnutils->getTemplateManifestCache());

	$template_prefix 		= $template_details->name . '-';
	$template_path 			= $this->baseurl.'/templates/'.$this->template;
	$template_direction 	= $this->direction;
	$has_right				= $this->countModules('right');
	$has_left				= $this->countModules('left');
	$has_promo 				= $this->countModules('promo');
	$has_promoleft 			= $this->countModules('promo-left');
	$has_promoright 		= $this->countModules('promo-right');
	$has_innerleft 			= $this->countModules('innerleft');
	$has_innerright 		= $this->countModules('innerright');

	$pageclass 		= '';
	$not_homepage 	= true;
	$menus 			= $app->getMenu();
	$menu 			= $menus->getActive();
	if (is_object($menu)) {
		// Set page class suffix
		$params = JMenu::getInstance('site')->getParams( $menu->id );
		$pageclass = $params->get( 'pageclass_sfx', '');
		
		// Set homepage flag
		$lang = JFactory::getLanguage();
		$default_menu = $menus->getDefault($lang->getTag());
		if (is_object($default_menu)) {
			$not_homepage = ($menu->id != $default_menu->id);
		}
	}

	// Define to show main body on homepage or not
	if($show_frontpage == false) {
		$show_frontpage = $not_homepage;
	}

	// Check template attributes to override settings
	$tattrs = $jsnutils->getTemplateAttributes($jsn_template_attrs, $template_prefix, $pageclass);
	if ($tattrs['width'] != null) $template_width = $tattrs['width'];
	if ($tattrs['textstyle'] != null) $template_textstyle = $tattrs['textstyle'];
	if ($tattrs['textsize'] != null) $template_textsize = $tattrs['textsize'];
	if ($tattrs['color'] != null) $template_color = $tattrs['color'];
	if ($tattrs['direction'] != null) $template_direction = $tattrs['direction'];
	if ($tattrs['promoleftwidth'] != null) $promo_left_width = $tattrs['promoleftwidth'];
	if ($tattrs['promorightwidth'] != null) $promo_right_width = $tattrs['promorightwidth'];
	if ($tattrs['leftwidth'] != null) $left_width = $tattrs['leftwidth'];
	if ($tattrs['rightwidth'] != null) $right_width = $tattrs['rightwidth'];
	if ($tattrs['innerleftwidth'] != null) $innerleft_width = $tattrs['innerleftwidth'];
	if ($tattrs['innerrightwidth'] != null) $innerright_width = $tattrs['innerrightwidth'];
	// Custom template JS declarations
	$javascript_params = '
			var templateParams					= {};
			templateParams.templatePrefix		= "'.$template_prefix.'";
			templateParams.templatePath			= "'.$template_path.'";
			templateParams.enableRTL			= '.(($template_direction == "rtl")?'true':'false').';

			JSNTemplate.initTemplate(templateParams);
	';
?>