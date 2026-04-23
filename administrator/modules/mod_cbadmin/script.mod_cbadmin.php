<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/
if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class mod_cbadminInstallerScript
{

	private function purge()
	{
		// Purge cb version cache:
		$version						=	JPATH_SITE . '/cache/cblatestversion.xml';

		if ( file_exists( $version ) ) {
			@unlink( $version );
		}

		// Purge news feed cache:
		$feed							=	JPATH_SITE . '/cache/cbnewsfeed.xml';

		if ( file_exists( $feed ) ) {
			@unlink( $feed );
		}
	}

	/**
	 * Adapts a Joomla 4 default param to Joomla 3 if needed
	 *
	 * @param  string  $moduleParams
	 * @return void
	 */
	private function adaptForJoomla3IfNeeded( &$moduleParams )
	{
		if ( ! ( defined( 'JVersion::MAJOR_VERSION' ) && ( JVersion::MAJOR_VERSION > 3 ) ) ) {
			// Joomla 3.x:  (MAJOR_VERSION defined only since J3.8):
			$moduleParams	=	str_replace( '"bootstrap_size":"12"', '"bootstrap_size":"6"', $moduleParams );
		}
	}

	/**
	 * Upgrades existing installs on Joomla 4 with strictly default params to new bootstrap default width from 6 of 12
	 *
	 * @param  string  $moduleParams
	 * @return void
	 */
	private function upgradeDefaultWidth( $moduleParams )
	{
		if ( defined( 'JVersion::MAJOR_VERSION' ) && ( JVersion::MAJOR_VERSION > 3 ) ) {
			// Joomla 4:  (MAJOR_VERSION defined only since J3.8):
			/** @var $module JTableModule */
			$module							=	JTable::getInstance( 'module' );
			if ( $module->load( array( 'module' => 'mod_cbadmin', 'position' => 'cpanel', 'params' => $moduleParams ) ) ) {
				$module->set( 'params', str_replace( '"bootstrap_size":"6"', '"bootstrap_size":"12"', $moduleParams ) );
				$module->store();
			}
		}
	}

	/**
	 * Runs parameters upgrades (for now in Joomla 4 only, to fix bootstrap-size from 6 to 12).
	 *
	 * @return void
	 */
	private function runParametersUpgrades( )
	{
		// These 3 default params are the ones of the CB 2.6.x on Joomla 3 (or CB < 2.6.3 on Joomla 4) that should be upgraded on Joomla 4's default bootstrap width of 12:
		$moduleParamsNews    = '{"mode":"3","menu_cb":"1","menu_plugins":"1","menu_compact":"1","feed_entries":"5","feed_duration":"12","modal_display":"1","modal_width":"800","modal_height":"500","module_tag":"div","bootstrap_size":"6","header_tag":"h3","header_class":"","style":"0"}';
		$moduleParamsUpdates = '{"mode":"4","menu_cb":"1","menu_plugins":"1","menu_compact":"1","feed_entries":"5","feed_duration":"12","modal_display":"1","modal_width":"800","modal_height":"500","module_tag":"div","bootstrap_size":"6","header_tag":"h3","header_class":"","style":"0"}';
		$moduleParamsVersion = '{"mode":"5","menu_cb":"1","menu_plugins":"1","menu_compact":"1","feed_entries":"5","feed_duration":"12","modal_display":"1","modal_width":"800","modal_height":"500","module_tag":"div","bootstrap_size":"6","header_tag":"h3","header_class":"","style":"0"}';
		$this->upgradeDefaultWidth( $moduleParamsNews );
		$this->upgradeDefaultWidth( $moduleParamsUpdates );
		$this->upgradeDefaultWidth( $moduleParamsVersion );
	}

	public function install( $adapter )
	{
		$this->purge();

		$db								=	JFactory::getDbo();

		// Check if old admin module exists and if it does remove it:
		/** @var $extension JTableExtension */
		$extension						=	JTable::getInstance( 'extension' );

		if ( $extension->load( array( 'element' => 'mod_cb_adminnav' ) ) ) {
			$query						=	'SELECT ' . $db->quoteName( 'id' )
										.	"\n FROM " . $db->quoteName( '#__modules' )
										.	"\n WHERE " . $db->quoteName( 'module' ) . " = " . $db->quote( 'mod_cb_adminnav' );
			$db->setQuery( $query );
			$modules					=	$db->loadColumn();

			if ( $modules ) {
				foreach ( $modules as $moduleId ) {
					/** @var $module JTableModule */
					$module				=	JTable::getInstance( 'module' );

					if ( $module->load( array( 'id' => (int) $moduleId ) ) ) {
						$moduleParams	=	new JRegistry;

						$moduleParams->loadString( $module->get( 'params' ) );

						if ( $moduleParams->get( 'cb_adminnav_display', 1 ) == 1 ) {
							$moduleParams->set( 'mode', 2 );
						} else {
							$moduleParams->set( 'mode', 1 );
						}

						$moduleParams->set( 'menu_cb', $moduleParams->get( 'cb_adminnav_cb', 1 ) );
						$moduleParams->set( 'menu_plugins', ( $moduleParams->get( 'cb_adminnav_plugins', 0 ) || $moduleParams->get( 'cb_adminnav_gj', 0 ) || $moduleParams->get( 'cb_adminnav_cbsubs', 0 ) ) );
						$moduleParams->set( 'menu_compact', 1 );

						$module->set( 'module', 'mod_cbadmin' );
						$module->set( 'params', $moduleParams->toString() );

						$module->store();
					}
				}
			}

			$installer					=	new JInstaller();

			try {
				$installer->uninstall( 'module', $extension->get( 'extension_id' ) );
			} catch ( RuntimeException $e ) {}
		}

		// Check if dropdown module exists and if not lets create it:
		/** @var $module JTableModule */
		$module							=	JTable::getInstance( 'module' );

		if ( ! $module->load( array( 'module' => 'mod_cbadmin', 'position' => 'menu' ) ) ) {
			// Load the first empty module on initial install or create a new module:
			$module->load( array( 'module' => 'mod_cbadmin', 'position' => '' ) );

			$module->set( 'title', 'CB Admin Dropdown Menu' );
			$module->set( 'ordering', '99' );
			$module->set( 'position', 'menu' );
			$module->set( 'published', '1' );
			$module->set( 'module', 'mod_cbadmin' );
			$module->set( 'access', '1' );
			$module->set( 'showtitle', '0' );
			$module->set( 'params', '{"mode":"1","menu_cb":"1","menu_plugins":"1","menu_compact":"1","feed_entries":"5","feed_duration":"12","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}' );
			$module->set( 'client_id', '1' );
			$module->set( 'language', '*' );

			if ( $module->store() ) {
				$moduleId				=	$module->get( 'id' );

				if ( $moduleId ) {
					$db->setQuery( 'INSERT IGNORE INTO `#__modules_menu` ( `moduleid`, `menuid` ) VALUES ( ' . (int) $moduleId . ', 0 )' );

					try {
						$db->execute();
					} catch ( RuntimeException $e ) {}
				}
			}
		}

		// Check if feed modules exist and if not lets create them:
		/** @var $module JTableModule */
		$module							=	JTable::getInstance( 'module' );

		if ( $module->load( array( 'module' => 'mod_cbadmin', 'position' => 'cpanel' ) ) ) {
			$this->runParametersUpgrades();
		} else {
			// These are the default params for new installations for Joomla 4 (bootstrap_size of 12):
			$moduleParamsNews    = '{"mode":"3","menu_cb":"1","menu_plugins":"1","menu_compact":"1","feed_entries":"5","feed_duration":"12","modal_display":"1","modal_width":"800","modal_height":"500","module_tag":"div","bootstrap_size":"12","header_tag":"h3","header_class":"","style":"0"}';
			$moduleParamsUpdates = '{"mode":"4","menu_cb":"1","menu_plugins":"1","menu_compact":"1","feed_entries":"5","feed_duration":"12","modal_display":"1","modal_width":"800","modal_height":"500","module_tag":"div","bootstrap_size":"12","header_tag":"h3","header_class":"","style":"0"}';
			$moduleParamsVersion = '{"mode":"5","menu_cb":"1","menu_plugins":"1","menu_compact":"1","feed_entries":"5","feed_duration":"12","modal_display":"1","modal_width":"800","modal_height":"500","module_tag":"div","bootstrap_size":"12","header_tag":"h3","header_class":"","style":"0"}';
			$this->adaptForJoomla3IfNeeded( $moduleParamsNews );
			$this->adaptForJoomla3IfNeeded( $moduleParamsUpdates );
			$this->adaptForJoomla3IfNeeded( $moduleParamsVersion );

			// Load the first empty module on initial install or create a new module:
			$module->load( array( 'module' => 'mod_cbadmin', 'position' => '' ) );

			// News feed:
			$module->set( 'title', 'Community Builder News' );
			$module->set( 'ordering', '99' );
			$module->set( 'position', 'cpanel' );
			$module->set( 'published', '1' );
			$module->set( 'module', 'mod_cbadmin' );
			$module->set( 'access', '1' );
			$module->set( 'showtitle', '1' );
			$module->set( 'params', $moduleParamsNews );
			$module->set( 'client_id', '1' );
			$module->set( 'language', '*' );

			if ( $module->store() ) {
				$moduleId				=	$module->get( 'id' );

				if ( $moduleId ) {
					$db->setQuery( 'INSERT IGNORE INTO `#__modules_menu` ( `moduleid`, `menuid` ) VALUES ( ' . (int) $moduleId . ', 0 )' );

					try {
						$db->execute();
					} catch ( RuntimeException $e ) {}
				}
			}

			// Update feed:
			/** @var $module JTableModule */
			$module						=	JTable::getInstance( 'module' );

			$module->set( 'title', 'Community Builder Updates' );
			$module->set( 'ordering', '99' );
			$module->set( 'position', 'cpanel' );
			$module->set( 'published', '1' );
			$module->set( 'module', 'mod_cbadmin' );
			$module->set( 'access', '1' );
			$module->set( 'showtitle', '1' );
			$module->set( 'params', $moduleParamsUpdates );
			$module->set( 'client_id', '1' );
			$module->set( 'language', '*' );

			if ( $module->store() ) {
				$moduleId				=	$module->get( 'id' );

				if ( $moduleId ) {
					$db->setQuery( 'INSERT IGNORE INTO `#__modules_menu` ( `moduleid`, `menuid` ) VALUES ( ' . (int) $moduleId . ', 0 )' );

					try {
						$db->execute();
					} catch ( RuntimeException $e ) {}
				}
			}

			// Version checker:
			/** @var $module JTableModule */
			$module						=	JTable::getInstance( 'module' );

			$module->set( 'title', 'CB Admin Version Checker' );
			$module->set( 'ordering', '99' );
			$module->set( 'position', 'cpanel' );
			$module->set( 'published', '1' );
			$module->set( 'module', 'mod_cbadmin' );
			$module->set( 'access', '1' );
			$module->set( 'showtitle', '0' );
			$module->set( 'params', $moduleParamsVersion );
			$module->set( 'client_id', '1' );
			$module->set( 'language', '*' );

			if ( $module->store() ) {
				$moduleId				=	$module->get( 'id' );

				if ( $moduleId ) {
					$db->setQuery( 'INSERT IGNORE INTO `#__modules_menu` ( `moduleid`, `menuid` ) VALUES ( ' . (int) $moduleId . ', 0 )' );

					try {
						$db->execute();
					} catch ( RuntimeException $e ) {}
				}
			}
		}
	}

	public function discover_install( $adapter )
	{
		$this->install( $adapter );
	}

	public function update( $adapter )
	{
		$this->purge();
		$this->runParametersUpgrades();
	}
}
