<?php
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Sample data field type
 *
 * @package
 * @subpackage
 * @since		1.6
 */
class JFormFieldJSNSampleData extends JFormField
{
	public $type = 'JSNSampleData';
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected function getInput() {

		JHTML::_('behavior.modal', 'a.jsn-modal');
		
		$doc 				= JFactory::getDocument();

		$templateName 		= explode( DS, str_replace( array( '\elements', '/elements' ), '', dirname(__FILE__) ) );
		$templateName 		= $templateName [ count( $templateName ) - 1 ];

		require_once JPATH_ROOT.DS.'templates'.DS.$templateName.DS.'includes'.DS.'lib'.DS.'jsn_utils.php';
		$doc->addScript(JURI::root().'templates/'.$templateName.'/admin/js/jsn_slider.js');
		$doc->addScript(JURI::root().'templates/'.$templateName.'/admin/js/jsn_admin.js');
		$doc->addStyleSheet(JURI::root().'templates/'.$templateName.'/admin/css/jsn_admin.css');
		$doc->addScriptDeclaration('
			var templateLang					= {};
			templateLang.expand_all				= "'.JText::_('EXPAND_ALL').'";
			templateLang.collapse_all			= "'.JText::_('COLLAPSE_ALL').'";
		');

		$jsnUtils 	  		= JSNUtils::getInstance();
		
		$tellMore	    	= '';
		$html 				= '';
		$result 			= $jsnUtils->getTemplateDetails();
		$templateVersion 	= $result->version;
		$templateName	  	= $result->name;
		$templateEdition 	= $result->edition;
		$templateName    	= str_replace('_', ' ', $templateName);
		$templateEdition 	= strtolower($templateEdition);

		// check System Cache - Plugin
		define('JSN_CACHESENSITIVE', $jsnUtils->checkSystemCache());

		$hashCookieIndex = (isset($_COOKIE['HashCookieIndex'])) ? json_decode($_COOKIE['HashCookieIndex']) : null;
		if (empty($hashCookieIndex->selected)) {
			$hashCookieIndex->selected = '0,';
			setcookie('HashCookieIndex', json_encode($hashCookieIndex), 0, '/');
		}

		$jsAccordion 	= "window.addEvent('domready', function(){
								new Accordion($$('.panel h3.jpane-toggler'), $$('.panel div.jpane-slider'), {
									onActive: function(toggler, i) {
										toggler.addClass('jpane-toggler-down');
										toggler.removeClass('jpane-toggler');
									},
									onBackground: function(toggler, i) {
										toggler.addClass('jpane-toggler');
										toggler.removeClass('jpane-toggler-down');
									},
									duration: 300,
									opacity: false,
									alwaysHide: true
								});
							});";
		$doc->addScriptDeclaration($jsAccordion);

		$templateName = preg_replace('/_(free|pro)$/i', '', $result->name);
		$templateDocName = strtolower(str_replace('_', '-', $templateName));
		$templateReadableName = preg_replace('/^JSN_(.*)/ie', '"JSN " . ucfirst(strtolower("\\1"))', $templateName);

		$html  = '<div class="jsn-quickstarted">';
		$html .= '<p>' . JTEXT::sprintf('WELCOME_MESSAGE', $templateReadableName) . '</p>
				<ol id="jsn-quickstart-links">
					<li>
						' . JText::_('INSTALL_SAMPLE_DATA_AS_SEEN_ON_DEMO_WEBSITE') . '
						<a class="jsn-modal btn link-button" rel="{handler: \'iframe\', size: {x: 750, y: 650}, closable: false}" href="../index.php?template='.strtolower($result->name).'&tmpl=jsn_installsampledata&template_style_id='.JRequest::getInt('id').'">'.JText::_('INSTALL_SAMPLE_DATA').'</a>
					</li>
					<li>
						' . JText::_('WATCH_CONFIGURATION_VIDEOS_ON_YOUTUBE') . '
						<a class="btn link-button" href="http://www.joomlashine.com/docs/joomla-templates/template-configuration-videos.html" target="_blank">Watch videos</a>
					</li>
					<li>
						' . JText::_('DOWNLOAD_AND_READ_DOCUMENT_IN_PDF') . '
						<a class="btn link-button" href="http://www.joomlashine.com/joomla-templates/' . $templateDocName . '-docs.zip">Download documentation</a>
					</li>
				</ol>';
		$html .= '</div>';

		return $html;
	}
}