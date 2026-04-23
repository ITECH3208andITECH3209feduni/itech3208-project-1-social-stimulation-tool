<?php
/**
 * CBLib, Community Builder Library(TM)
 *
 * @version       $Id: 5/13/14 5:26 PM $
 * @package       ${NAMESPACE}
 * @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license       http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */
namespace CBLib\Cms;

use CBLib\Application\ApplicationContainerInterface;
use CBLib\Input\InputInterface;
use CBLib\Registry\Registry;
use Joomla\CMS\Mail\Mail;
use JSession;

/**
 * CBLib\Cms Class implementation
 *
 */
interface CmsInterface
{
	/**
	 * @param  string   $info  Informwation to return ('release' php-style version)
	 * @return string
	 */
	public function getCmsVersion( $info = 'release' );

	/**
	 * @param  ApplicationContainerInterface  $di
	 * @param  string                         $type    'Web' or 'Cli'
	 * @param  array|InputInterface           $input
	 * @return InputInterface
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getInput( ApplicationContainerInterface $di, $type, $input );

	/**
	 * Returns client id (0 = front, 1 = admin)
	 * @deprecated 2.5.0, removed in 3.0
	 * @see        isClient
	 *
	 * @return int
	 */
	public function getClientId( );

	/**
	 * Returns the language name
	 *
	 * @return string
	 */
	public function getLanguageName( );

	/**
	 * Returns the language tag
	 *
	 * @return string
	 */
	public function getLanguageTag( );

	/**
	 * Returns extension name being executed (e.g. com_comprofiler or mod_cblogin)
	 *
	 * @return string
	 */
	public function getExtensionName( );

	/**
	 * Get the CBLib's interface class to the CMS User
	 *
	 * @param  int|array|null $userIdOrCriteria  [optional] default: NULL: viewing user, int: User-id (0: guest), array: Criteria, e.g. array( 'username' => 'uniqueUsername' ) or array( 'email' => 'uniqueEmail' )
	 * @return CmsUserInterface
	 */
	public function getCmsUser( $userIdOrCriteria );

	/**
	 * Gets the folder with path for the $clientId (0 = front, 1 = admin)
	 *
	 * @param $clientId
	 * @return string
	 *
	 * @throws \UnexpectedValueException
	 */
	public function getFolderPath( $clientId );

	/**
	 * Registers a handler to filter the final output
	 *
	 * @param  callable  $handler  A function( $body ) { return $bodyChanged; }
	 * @return self                To allow chaining.
	 */
	public function registerOnAfterRenderBodyFilter( $handler );

	/**
	 * Registers a handler to a particular CMS event
	 *
	 * @param  string    $event    The event name:
	 * @param  callable  $handler  The handler, a function or an instance of a event object.
	 * @return self                To allow chaining.
	 */
	public function registerEvent( $event, $handler );

	/**
	 * Executes a particular CMS event and fetch back an array with their return values.
	 *
	 * @param   string  $event  The event (trigger) name, e.g. onBeforeScratchMyEar
	 * @param   array   $data   A hash array of data sent to the plugins as part of the trigger
	 * @return  array  A simple array containing the results of the plugins triggered
	*/
	public function triggerEvent( $event, $data );

	/**
	 * Prepares the HTML $htmlText with triggering CMS Content Plugins
	 *
	 * @param  string $htmlText
	 * @param  string $context
	 * @param  int    $userId
	 * @return string
	 */
	public function prepareHtmlContentPlugins( $htmlText, $context = 'text', $userId = 0 );

	/**
	 * Get CMS Database object
	 * @return object|\JDatabase|\JDatabaseDriver
	 */
	public function getCmsDatabaseDriver( );

	/**
	 * Gets menu params
	 *
	 * @return Registry
	 */
	public function getActiveMenuWithParams( );

	/**
	 * Returns the current active CMS (menu) page classes
	 *
	 * @return null|string
	 * @since 2.5.0
	 */
	public function getPageCssClasses();

	/**
	 * Display the CMS editor area.
	 *
	 * @param  string  $name       Control name.
	 * @param  string  $content    Content of the text area.
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
	 * @throws \Exception
	 * @since   2.5.0
	 */
	public function displayCmsEditor( $name, $content, $width, $height, $columns, $rows, $buttons = true, $id = null, $asset = null, $author = null, $params = array() );

	/**
	 * Logs CMS action log entries
	 *
	 * @param string      $message
	 * @param array       $data
	 * @param null|string $context
	 * @param null|int    $userId
	 */
	public function logUserAction( $message, $data = array(), $context = null, $userId = null );

	/**
	 * Get the current visitor's IP address
	 *
	 * @return null|string
	 */
	public function getIpAddress();

	/**
	 * Returns Joomla CMS phpmailer instance
	 *
	 * @return Mail|\JMail
	 */
	public function getMailer();

	/**
	 * Returns CSRF token for the user session
	 *
	 * @param bool $new
	 * @return string
	 */
	public function getFormToken( bool $new = false ): string;

	/**
	 * Validates the CSRF token for the user session
	 *
	 * @param string $method
	 * @return bool
	 */
	public function checkFormToken( string $method = 'post' ): bool;
}
