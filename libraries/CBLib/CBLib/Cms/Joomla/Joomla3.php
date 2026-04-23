<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 09.06.13 01:15 $
* @package CBLib\Cms
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CBLib\Cms\Joomla;

use ActionlogsModelActionlog;

defined( 'CBLIB') or die();

/**
 * CBLib\Cms\Joomla\Joomla3 Class implementation
 *
 */
class Joomla3 extends Joomla4
{
	/**
	 * Executes a particular CMS event and fetch back an array with their return values.
	 *
	 * @param   string  $event  The event (trigger) name, e.g. onBeforeScratchMyEar
	 * @param   array   $data   A hash array of data sent to the plugins as part of the trigger
	 * @return  array  A simple array containing the results of the plugins triggered
	 */
	public function triggerEvent( $event, $data )
	{
		if ( class_exists( 'JEventDispatcher' ) )
		{
			return \JEventDispatcher::getInstance()->trigger( $event, $data );
		}

		return array();
	}

	/**
	 * Display the CMS editor area.
	 *
	 * @param  string  $name       Control name.
	 * @param  string  $content    Contents of the text area.
	 * @param  string  $width      Width of the text area (px or %).
	 * @param  string  $height     Height of the text area (px or %).
	 * @param  integer $columns    Number of columns for the textarea.
	 * @param  integer $rows       Number of rows for the textarea.
	 * @param  boolean|array $buttons  True and the editor buttons will be displayed, or array.
	 * @param  string  $id         An optional ID for the textarea. If not supplied the name is used.
	 * @param  string  $asset      The object asset
	 * @param  object  $author     The author.
	 * @param  array   $params     Associative array of editor parameters.
	 *                             boolean 'autofocus': Autofocus request for the form field to automatically focus on document load
	 *                             boolean 'readonly':  Readonly state for the form field.  If true then the field will be readonly
	 *                             string  'syntax':    Syntax of the field
	 * @return string
	 *
	 * @since   2.5.0
	 */
	public function displayCmsEditor( $name, $content, $width, $height, $columns, $rows, $buttons = true, $id = null, $asset = null, $author = null, $params = array() )
	{
		$content			=	(string) $content;

		$editor 			=	\JFactory::getConfig()->get('editor');
		$editor				=	\Joomla\CMS\Editor\Editor::getInstance( $editor );

		if ( ! isset( $params['html_height'] ) ) {
			$params['html_height']	=	$height;
		}

		return $editor->display( $name, htmlspecialchars( $content, ENT_COMPAT, 'UTF-8' ), $width, $height, $columns, $rows, $buttons, $id, $asset, $author, $params );
	}

	/**
	 * Logs CMS action log entries
	 *
	 * @param string      $message
	 * @param array       $data
	 * @param null|string $context
	 * @param null|int    $userId
	 */
	public function logUserAction( $message, $data = array(), $context = null, $userId = null )
	{
		\JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_actionlogs/models', 'ActionlogsModel' );

		/** @var ActionlogsModelActionlog $model */
		$model	=	\JModelLegacy::getInstance( 'Actionlog', 'ActionlogsModel' );

		$model->addLog( array( $data ), $message, 'com_comprofiler' . ( $context ? '.' . $context : null ), $userId );
	}
}
