<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

use CB\Database\Table\UserTable;
use CBLib\Application\Application;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var UserTable       $user
 * @var string          $regLayout
 * @var int             $secureForm
 *
 * @var CBframework     $_CB_framework
 * @var cbPluginHandler $_PLUGINS
 */

require_once $_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/comprofiler.html.php';

$_CB_framework->document->outputToHeadCollectionStart();

HTML_comprofiler::registerForm( 'com_comprofiler', Application::Config()->getInt( 'emailpass', 0 ), $user, [], null, false, 'error', true, $regLayout, (bool) $secureForm );

$_CB_framework->getAllJsPageCodes();

if ( ( Application::Input()->getInt( 'no_html', 0 ) !== 1 ) || ( ! in_array( Application::Input()->getString( 'format', '' ), [ 'raw', 'json' ], true ) ) ) {
	echo $_CB_framework->document->outputToHead();
}