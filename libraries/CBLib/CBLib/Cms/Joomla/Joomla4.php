<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 08.06.13 22:32 $
* @package ${NAMESPACE}
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CBLib\Cms\Joomla;

use CBLib\Application\Application;
use CBLib\Cms\CmsInterface;
use CBLib\Cms\CmsUserInterface;
use CBLib\Cms\Joomla\Joomla3\CmsUser;
use CBLib\Application\ApplicationContainerInterface;
use CBLib\Cms\Joomla\Joomla3\CmsEventHandler;
use CBLib\Input\InputInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Mail\Mail;
use Joomla\Utilities\IpHelper;
use JSession;

defined('CBLIB') or die();

/**
 * CBLib\Cms\Joomla\Joomla4 Class implementation
 *
 */
class Joomla4 implements CmsInterface
{
	/**
	 * Constructor. Must define DI for CmsPermissions and RouterInterface
	 *
	 * @param  ApplicationContainerInterface  $di
	 */
	public function __construct( ApplicationContainerInterface $di )
	{
		$di->set( 'CBLib\Cms\CmsPermissionsInterface', 'CBLib\Cms\Joomla\Joomla3\CmsPermissions', true )
			->alias( 'CBLib\Cms\CmsPermissionsInterface', 'CmsPermissions' );

		$di->set( 'CBLib\Controller\RouterInterface', 'CBLib\Cms\Joomla\Joomla3\CmsRouter', true )
			->alias( 'CBLib\Controller\RouterInterface', 'Router' );

		/* This one is to work-around a bug in Joomla 3.3.6- that prevents using closures in observer objects:
		 * ( https://github.com/joomla/joomla-cms/pull/4865 )
		 */
		$di->set( 'CBLib\Cms\Joomla\Joomla3\CmsEventsRegistry', 'CBLib\Registry\Registry', true );
	}

	/**
	 * @param  string   $info  Informwation to return ('release' php-style version)
	 * @return string
	 */
	public function getCmsVersion( $info = 'release' )
	{
		switch ( $info ) {
			case 'release':
				return JVERSION;
			default:
				trigger_error( __CLASS__ . '::'. __FUNCTION__ . ': info not supported', E_USER_WARNING );
				return null;
		}
	}

	/**
	 * @param  ApplicationContainerInterface  $di
	 * @param  string                         $type    'Web' or 'Cli'
	 * @param  array|InputInterface           $input
	 * @return InputInterface
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getInput( ApplicationContainerInterface $di, $type, $input )
	{
		// Standalone case without input:
		$srcGpc				=	( $input === null && $type == 'Web' );

		if ( $srcGpc ) {
			//TODO Check here how we could use in the future JInput (which has buggy getArray()).
			// $_REQUEST is needed here because of Joomla's SEF populating only $_REQUEST:
			$input 			=   array_merge( $_GET, $_POST, $_REQUEST );
		}

		// Standalone case continued or array for input:
		if ( is_array( $input ) ) {
			/** @see \CBLib\Input\Input::__construct() */
			return $di->get( 'CBLib\Input\Input', array( 'source' => $input, 'srcGp' => $srcGpc ) )
				->setNamespaceRegistry( 'get', $di->get( 'CBLib\Input\Input', array( 'source' => $_GET, 'srcGp' => $srcGpc ) ) )
				->setNamespaceRegistry( 'post', $di->get( 'CBLib\Input\Input', array( 'source' => $_POST, 'srcGp' => $srcGpc ) ) )
				->setNamespaceRegistry( 'files', $di->get( 'CBLib\Input\Input', array( 'source' => $_FILES, 'srcGp' => $srcGpc ) ) )
				->setNamespaceRegistry( 'cookie', $di->get( 'CBLib\Input\Input', array( 'source' => $_COOKIE, 'srcGp' => $srcGpc ) ) )
				->setNamespaceRegistry( 'server', $di->get( 'CBLib\Input\Input', array( 'source' => $_SERVER, 'srcGp' => $srcGpc ) ) )
				->setNamespaceRegistry( 'env', $di->get( 'CBLib\Input\Input', array( 'source' => $_ENV, 'srcGp' => $srcGpc ) ) );
		}

		// From now on it can only be an object:
		if ( ! is_object( $input ) ) {
			throw new \InvalidArgumentException('Invalid input argument in CBLib SetMainInput');
		}

		// Already InputInterface:
		if ( $input instanceof InputInterface ) {
			return $input;
		}

		/** This could be a way to get all inputs from Joomla, but it is not fast because no way to get just the keys or data:
		 *	if ( ! $input ) {
		 *		// This is not usable and filter is buggy in Joomla 3.3 unfortunately, so can't use:		$input		=	\JFactory::getApplication()->input->getArray();
		 *		$inputKeys		=	array_keys( \JFactory::getApplication()->input->getArray() );
		 *		$input			=	array();
		 *		foreach ( $inputKeys as $k ) {
		 *			$input[$k]	=	\JFactory::getApplication()->input->get( $k, null, 'raw' );
		 *		}
		 *	}
		 */

		/** @see \CBLib\Input\Input::__construct() */
		/** @var \JInput|\Traversable $input */
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $di->get( 'CBLib\Input\Input', array('source' => $input ) );
	}

	/**
	 * Returns client id (0 = front, 1 = admin)
	 * @deprecated 2.5.0
	 * @see Application::isClient() : Use instead: Application::Application()->isClient( 'administrator' )
	 *
	 * @return int
	 */
	public function getClientId( )
	{
		return ( Application::Application()->isClient( 'administrator' ) ? 1 : 0 );
	}

	/**
	 * Returns language name
	 *
	 * @return int
	 */
	public function getLanguageName( )
	{
		return strtolower( preg_replace( '/^(\w+).*$/i', '\1', \JFactory::getLanguage()->getName() ) );
	}

	/**
	 * Returns language tags
	 *
	 * @return int
	 */
	public function getLanguageTag( )
	{
		return \JFactory::getLanguage()->getTag();
	}

	/**
	 * Returns extension name being executed (e.g. com_comprofiler or mod_cblogin)
	 *
	 * @return string
	 */
	public function getExtensionName( )
	{
		return Application::Input()->get( 'option', null, GetterInterface::COMMAND );

	}

	/**
	 * Get the CBLib's interface class to the CMS User
	 *
	 * @param  int|array|null $userIdOrCriteria  [optional] default: NULL: viewing user, int: User-id (0: guest), array: Criteria, e.g. array( 'username' => 'uniqueUsername' ) or array( 'email' => 'uniqueEmail' )
	 * @return CmsUserInterface|boolean           Boolean FALSE if user does not exist (and userId = 0)
	 */
	public function getCmsUser( $userIdOrCriteria )
	{
		return CmsUser::getInstance( $userIdOrCriteria );
	}

	/**
	 * Gets the folder with path for the $clientId (0 = front, 1 = admin)
	 *
	 * @param $clientId
	 * @return string
	 *
	 * @throws \UnexpectedValueException
	 */
	public function getFolderPath( $clientId )
	{
		$optionCleaned	=	$this->getExtensionName();

		if ( $clientId == 0 )
		{
			return JPATH_ROOT . '/components/' . $optionCleaned;
		}
		elseif ( $clientId == 1 )
		{
			return JPATH_ADMINISTRATOR . '/components/' . $optionCleaned;
		}
		throw new \UnexpectedValueException( 'Unexpected client id' );
	}

	/**
	 * Registers a handler to filter the final output
	 *
	 * @param  callable  $handler  A function( $body ) { return $bodyChanged; }
	 * @return self                To allow chaining.
	 */
	public function registerOnAfterRenderBodyFilter( $handler )
	{
		$this->registerEvent(
			'onAfterRender',
			function( ) use ( $handler ) {
				$app	=	\JFactory::getApplication();
				$app->setBody( $handler( $app->getBody() ) );
			}
		);

		return $this;
	}

	/**
	 * Registers a handler to a particular CMS event
	 *
	 * @param  string    $event    The event name:
	 * @param  callable  $handler  The handler, a function or an instance of a event object.
	 * @return self                To allow chaining.
	 */
	public function registerEvent( $event, $handler )
	{
		/** This line (and the class:
		 * @see CmsEventHandler
		 * is to work-around a bug in Joomla 3.3.6- that prevents using closures in observer objects:
		 * ( https://github.com/joomla/joomla-cms/pull/4865 )
		 */
		/** @noinspection PhpDeprecationInspection */
		CmsEventHandler::register( $event, $handler );

		/*
		 * This is the simple way of implementing this but does not work if $handler is a closure or a callable array( $class, $method ) where $class has closure variables:
		 * but because of https://github.com/joomla/joomla-cms/pull/4865 this can not be done:
		 *
		 *	\JFactory::getApplication()
		 *		->registerEvent( $event, $handler );
		 */

		return $this;
	}

	/**
	 * Executes a particular CMS event and fetch back an array with their return values.
	 *
	 * @param   string  $event  The event (trigger) name, e.g. onBeforeScratchMyEar
	 * @param   array   $data   A hash array of data sent to the plugins as part of the trigger
	 * @return  array  A simple array containing the results of the plugins triggered
	*/
	public function triggerEvent( $event, $data )
	{
		try
		{
			$app = \JFactory::getApplication();
		}
		catch ( \Exception $e ) {
			// If I can't get JApplication I cannot run the plugins.
			return array();
		}
		return $app->triggerEvent( $event, $data );
	}

	/**
	 * Prepares the HTML $htmlText with triggering CMS Content Plugins
	 *
	 * @param  string $htmlText
	 * @param  string $context
	 * @param  int    $userId
	 * @return string
	 */
	public function prepareHtmlContentPlugins( $htmlText, $context = 'text', $userId = 0 )
	{
		$previousDocType		=	\JFactory::getDocument()->getType();
		\JFactory::getDocument()->setType( 'html' );

		$content				=	new \stdClass();
		$content->text			=	$htmlText;
		$content->created_by	=	(int) $userId;

		$params					=	new \Joomla\Registry\Registry();

		\JPluginHelper::importPlugin( 'content' );

		// For our sanity, we ignore errors in third party content plugins, unless we are in Joomla debug mode:
		if ( \JFactory::getConfig()->get( 'debug' ) ) {
			$this->triggerEvent( 'onContentPrepare', array('com_comprofiler' . ( $context ? '.' . $context : null ), &$content, &$params, 0) );
		} else {
			try
			{
				$this->triggerEvent( 'onContentPrepare', array( 'com_comprofiler' . ( $context ? '.' . $context : null ), &$content, &$params, 0 ) );
			}
			catch ( \Exception $e ) { }
		}

		\JFactory::getDocument()->setType( $previousDocType );

		return $content->text;
	}

	/**
	 * Get CMS Database object
	 * @return object|\JDatabase|\JDatabaseDriver
	 */
	public function getCmsDatabaseDriver( )
	{
		return \JFactory::getDbo();
	}

	/**
	 * Gets menu settings, with its params ( ->get( 'params/NAME' )
	 *
	 * @return Registry
	 */
	public function getActiveMenuWithParams()
	{
		static $cache	=	null;

		if ( $cache !== null ) {
			return $cache;
		}

		$params			=	array();
		$menu			=	\JFactory::getApplication()->getMenu()->getActive();
		$menuR			=	new Registry( $menu );

		if ( $menu && isset( $menu->id ) ) {
			if ( is_callable( array( $menu, 'getParams' ) ) ) {
				// Joomla 3.7+:
				$params	=	$menu->getParams();
			} elseif ( isset( $menu->params ) ) {
				$params	=	$menu->params;
			}

			if ( $params instanceof \Joomla\Registry\Registry ) {
				$params	=	$params->toArray();
			}
		}

		// Convert \Joomla\Registry to CBLib Registry:
		$cache			=	$menuR->setNamespaceRegistry( 'params', new Registry( $params ) );

		return $cache;
	}

	/**
	 * Returns the current active CMS (menu) page classes
	 *
	 * @return null|string
	 * @since 2.5.0
	 */
	public function getPageCssClasses()
	{
		static $cache	=	null;

		if ( $cache === null ) {
			$menuParams	=	$this->getActiveMenuWithParams();
			if ( $menuParams ) {
				$cache	=	trim( $menuParams->getString( 'params/pageclass_sfx', '' ) );
			}
		}

		return $cache;
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
	 * @throws \Exception
	 * @since   2.5.0
	 */
	public function displayCmsEditor( $name, $content, $width, $height, $columns, $rows, $buttons = true, $id = null, $asset = null, $author = null, $params = array() )
	{
		$content = (string) $content;

		$editor = \Joomla\CMS\Factory::getApplication()->get('editor');
		$editor = \Joomla\CMS\Editor\Editor::getInstance( $editor );

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
	public function logUserAction( $message, $data = [], $context = null, $userId = null )
	{
		Factory::getApplication()
			   ->bootComponent( 'com_actionlogs' )
			   ->getMVCFactory()
			   ->createModel( 'Actionlog', 'Administrator', [ 'ignore_request' => true ] )
			   ->addLog( [ $data ], $message, 'com_comprofiler' . ( $context ? '.' . $context : null ), $userId );
	}

	/**
	 * Get the current visitor's IP address
	 *
	 * @return null|string
	 */
	public function getIpAddress()
	{
		return IpHelper::getIp();
	}

	/**
	 * Returns Joomla CMS phpmailer instance
	 *
	 * @return Mail|\JMail
	 */
	public function getMailer()
	{
		return Factory::getMailer();
	}

	/**
	 * Returns CSRF token for the user session
	 *
	 * @param bool $new
	 * @return string
	 */
	public function getFormToken( bool $new = false ): string
	{
		return JSession::getFormToken( $new );
	}

	/**
	 * Validates the CSRF token for the user session
	 *
	 * @param string $method
	 * @return bool
	 */
	public function checkFormToken( string $method = 'post' ): bool
	{
		return JSession::checkToken( $method );
	}
}
