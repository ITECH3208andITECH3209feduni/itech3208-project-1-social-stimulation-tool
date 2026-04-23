<?php
/**
 * @author    JoomlaShine.com http://www.joomlashine.com
 * @copyright Copyright (C) 2008 - 2011 JoomlaShine.com. All rights reserved.
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

	// Include CSS files
	$this->addStylesheet($this->baseurl."/templates/system/css/system.css");
	$this->addStylesheet($this->baseurl."/templates/system/css/general.css");
	$this->addStylesheet($template_path."/css/template.css");

	// Load specific CSS file for template color
	$this->addStylesheet($template_path."/css/template_".$template_color.".css");
	if($template_direction == "rtl") { $this->addStylesheet($template_path."/css/jsn_rtl.css"); }

	// apply K2 style
	if ($jsnutils->checkK2()) {
		$this->addStylesheet($template_path."/ext/k2/jsn_ext_k2.css");
	}
?>

	<!--[if IE 7]>
	<link href="<?php echo $template_path; ?>/css/jsn_fixie7.css" rel="stylesheet" type="text/css" />
	<![endif]-->

<?php
	// Inline CSS declaration for template styling
	echo '<style type="text/css">';
	// Template desktop layout
	// Setup template width parameter
	$twidth = 0;
	switch ($template_width) {
		case 'narrow':
			$twidth = $narrow_width;
			break;
		case 'wide':
			$twidth = $wide_width;
			break;
		case 'float':
			$twidth = $float_width;
			break;
	}

	if ($twidth > 100) {
		echo '
	#jsn-page {
		width: '.$twidth.'px;
	}
		';
	} else {
		echo '
	#jsn-page {
		width: '.$twidth.'%;
	}
		';
	}

	// Setup width of promo area
	$tw = 100;
	echo '
	#jsn-pos-promo-left {
		float: left;
		width: '.$promo_left_width.'%;
		left: -'.($tw-$ieoffset).'%;
	}
	#jsn-pos-promo {
		width: '.($tw-$ieoffset).'%;
		left: '.(($has_promoleft)?$promo_left_width.'%':0).';
	}
	#jsn-pos-promo-right {
		float: right;
		width: '.$promo_right_width.'%;
	}
	';
	if ($has_promoright) {
		$tw -= $promo_right_width;
		echo '
	#jsn-pos-promo {
		float: left;
		width: '.($tw-$ieoffset).'%;
	}
		';
	}
	if ($has_promoleft) {
		$tw -= $promo_left_width;
		echo '
	#jsn-pos-promo {
		width: '.($tw-$ieoffset).'%;
		float: right;
		left: auto;
	}
	#jsn-pos-promo-left {
		left: auto;
	}
		';
	}
	if ($has_promoleft && $has_promoright) {
		$tw -= $promo_left_width;
		echo '
	#jsn-pos-promo {
		float: left;
		left: '.(($has_promoleft)?$promo_left_width.'%':0).';
	}
	#jsn-pos-promo-left {
		left: -'.($tw+$promo_left_width).'%;
	}
		';
	}
	if (!$has_promo) {
		echo '
	#jsn-pos-promo-left {
		left: auto;
		display: auto;
	}
		';
	}
	
	// Setup width of content area
	$tw = 100;
	if ($has_left) {
		$tw -= $left_width;
	}
	if ($has_right) {
		$tw -= $right_width;
	}

	echo '
	#jsn-leftsidecontent {
		float: left;
		width: '.$left_width.'%;
		left: -'.($tw-$ieoffset).'%;
	}
	#jsn-maincontent {
		float: left;
		width: '.($tw-$ieoffset).'%;
		left: '.(($has_left)?$left_width.'%':0).';
	}
	#jsn-rightsidecontent {
		float: right;
		width: '.$right_width.'%;
	}
	';

	$tw = 100;
	if ($has_innerleft) {
		$tw -= $innerleft_width;
		echo '
	#jsn-maincontent_inner1 {
		right: '.(100 - $innerleft_width).'%;
	}
	#jsn-maincontent_inner2 {
		left: '.(100 - $innerleft_width).'%;
	}
		';
	}
	if ($has_innerright) {
		$tw -= $innerright_width;
		echo '
	#jsn-maincontent_inner3 {
		left: '.(100 - $innerright_width).'%;
	}
	#jsn-maincontent_inner4 {
		right: '.(100 - $innerright_width).'%;
	}
		';
	}

	echo '
	#jsn-pos-innerleft {
		float: left;
		width: '.$innerleft_width.'%;
		left: -'.($tw-$ieoffset).'%;
	}
	#jsn-centercol {
		float: left;
		width: '.($tw-$ieoffset).'%;
		left: '.(($has_innerleft)?$innerleft_width.'%':0).';
	}
	#jsn-pos-innerright {
		float: right;
		width: '.$innerright_width.'%;
	}
	';
	// Setup font regular text
	echo '
		body.jsn-textstyle-'.$template_textstyle.' {
			font-family: '.$jsn_textstyles_config[$template_textstyle]['font-a'].';
		}
		';

	// Setup font heading and menu text
	$elements_length = count($jsn_font_b_elements);
	for($i=0;$i<$elements_length;$i++){
		echo '
		body.jsn-textstyle-'.$template_textstyle.' '.$jsn_font_b_elements[$i].(($i < $elements_length-1)?",":' {');
	}
		echo "
				font-family: ".$jsn_textstyles_config[$template_textstyle]['font-b'].";
			}
		";

	// Setup font size
	echo '
		body.jsn-textstyle-'.$template_textstyle.'.jsn-textsize-'.$template_textsize.' {
			font-size: '.$jsn_textstyles_config[$template_textstyle]["size-$template_textsize"].';
		}
	';

	// Include CSS3 support for IE browser
	if($is_ie) {
		echo '
		.text-box,
		.text-box-highlight,
		.text-box-highlight:hover {
			behavior: url('.JURI::root(true).'/templates/'.strtolower($this->template).'/includes/PIE.htc);
		}
		.link-button {
			zoom: 1;
			position: relative;
			behavior: url('.JURI::root(true).'/templates/'.strtolower($this->template).'/includes/PIE.htc);
		}
		';
	}

	echo '</style>';

	// Include Javascript files
	$this->addScriptDeclaration($javascript_params);
	$this->addScript($template_path.'/js/jsn_noconflict.js');
	$this->addScript($template_path.'/js/jsn_utils.js');
	$this->addScript($template_path.'/js/jsn_template.js');
?>