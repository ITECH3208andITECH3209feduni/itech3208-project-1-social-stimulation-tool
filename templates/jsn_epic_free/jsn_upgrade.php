<?php
/**
 * @author    JoomlaShine.com http://www.joomlashine.com
 * @copyright Copyright (C) 2008 - 2012 JoomlaShine.com. All rights reserved.
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 * @version   $Id: jsn_upgrade.php 15780 2012-09-04 09:10:18Z ngocpm $
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once('includes'.DS.'jsn_defines.php');
require_once('includes'.DS.'lib'.DS.'jsn_utils.php');
require_once('includes'.DS.'lib'.DS.'jsn_readxmlfile.php');
require_once('includes'.DS.'lib'.DS.'jsn_proupgrade_helper.php');

$jsnUtils   = new JSNUtils();
$jsnReadXml = new JSNReadXMLFile();

$joomlaVersion    = $jsnUtils->getJoomlaVersion(true);
$templateManifest = $jsnReadXml->getTemplateManifestFileInformation();
$templateLowName  = strtolower($templateManifest['full_name']);

$jsnUpgradeHelper = new JSNProUpgradeHelper($templateManifest, $joomlaVersion);

$task             = JRequest::getVar('task', '');
$manualUpgrade    = JRequest::getVar('manual', 0);
$autoUpgradeTried = JRequest::getVar('auto_tried', 0, 'GET');
$templateStyleId  = JRequest::getInt('template_style_id', 0, 'GET');

$frontIndexPath = JURI::root() . $jsnUtils->determineFrontendIndex();

/* Session variables */
$session = JFactory::getSession();
$sessionTemp = array();

$sessionTemp['upgrader']         = md5('jsn_upgrader_' . $templateLowName);
$sessionTemp['joomla_login']     = md5('joomla_login_' . $templateLowName);
$sessionTemp['jsn_login']        = md5('jsn_login_' . $templateLowName);
$sessionTemp['multiple_edition'] = md5('multiple_edition_' . $templateLowName);
$sessionTemp['customer_post']    = md5('customer_post_' . $templateLowName);
$sessionTemp['upgrade_edition']  = md5('upgrade_edition_' . $templateLowName);
$sessionTemp['new_package_file'] = md5('pro_template_package_' . $templateLowName);

if ($autoUpgradeTried == 1)
{
	/* Change from auto to manual upgrade, bypass the intro page */
	$session->set($sessionTemp['upgrader'], true, 'jsntemplatesession');
}

if (!$jsnUtils->cURLCheckFunctions() && !$jsnUtils->fOPENCheck() && !$jsnUtils->fsocketopenCheck())
{
	$manualUpgrade = 1;
}

$isAjax = false;

switch ($task)
{
	case 'upgrade_proceeded':
		$session->set($sessionTemp['upgrader'], true, 'jsntemplatesession');
		break;

	case 'manual_upgrade':
		$uploadedFile = $jsnUpgradeHelper->getPackageFromUpload();
		if ($uploadedFile)
		{
			$manualUpgrade = 1;
			$session->set($sessionTemp['jsn_login'], true, 'jsntemplatesession');
			$session->set($sessionTemp['new_package_file'], (string) $uploadedFile, 'jsntemplatesession');
		}
		break;

	case 'joomla_login':
		JRequest::checkToken() or jexit('Invalid Token');
		$options = array();
		$credentials['username'] = JRequest::getVar('username', '', 'post', 'username');
		$credentials['password'] = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);

		jimport('joomla.user.helper');
		$app  = JFactory::getApplication();
		/* Perform the login action */
		$error = $app->login($credentials, $options);

		/* Check User permission */
		$canDo = new JObject;
		$user = JFactory::getUser();
		$actions = array('core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete');
		foreach ($actions as $action)
		{
			$canDo->set($action, $user->authorise($action, 'com_templates'));
		}

		if (!JError::isError($error) && $error && $canDo->get('core.manage'))
		{
			$session->set($sessionTemp['joomla_login'], true, 'jsntemplatesession');
		}

		break;

	case 'jsn_login':
		JRequest::checkToken() or jexit('Invalid Token');
		$result = $jsnUpgradeHelper->authenticateCustomerInfo();
		$isAjax = true;

		if (!$result->error){
			$session->set($sessionTemp['jsn_login'], true, 'jsntemplatesession');
			if (count($result->editions) > 1)
			{
				$session->set($sessionTemp['multiple_edition'], json_encode($result->editions), 'jsntemplatesession');
				echo json_encode(array('authenticated' => true,'multiple' => true, 'editions' => $result->editions));
			}
			else
			{
				$session->set($sessionTemp['multiple_edition'], false, 'jsntemplatesession');
				$session->set($sessionTemp['upgrade_edition'], $result->editions[0], 'jsntemplatesession');
				echo json_encode(array('authenticated' => true, 'multiple' => false));
			}

			$session->set($sessionTemp['customer_post'], json_encode($result->post), 'jsntemplatesession');
		}
		else
		{
			echo json_encode(array('authenticated' => false, 'message' => $result->message));
		}

		break;

	case 'edition_select':
		JRequest::checkToken() or jexit('Invalid Token');
		$selectedEdition = JRequest::getVar('jsn_upgrade_edition');

		if ($selectedEdition != '')
		{
			$session->set($sessionTemp['multiple_edition'], false, 'jsntemplatesession');
			$session->set($sessionTemp['upgrade_edition'], $selectedEdition, 'jsntemplatesession');
		}
		else
		{
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('JSN_UPGRADE_NO_EDITION_SELECTED'));
		}
		break;

	case 'ajax_download_package':
		if ($session->get($sessionTemp['jsn_login'], false, 'jsntemplatesession'))
		{
			$isAjax = true;

			$selectedEdition = $session->get($sessionTemp['upgrade_edition'], '', 'jsntemplatesession');
			$customerPost = json_decode($session->get($sessionTemp['customer_post'], false, 'jsntemplatesession'));

			$result = $jsnUpgradeHelper->downloadTemplatePackage($customerPost, $selectedEdition);
			if ($result)
			{
				$errorCode = strtolower((string) $result);
				switch ($errorCode)
				{
					case 'err00':
						$message = '<span class="jsn-red-message">Invalid Parameters! Can not verify your product information</span>';
						break;
					case 'err01':
						$message = '<span class="jsn-red-message">Invalid username or password. Please enter JoomlaShine customer account you created when were purchasing the product</span>';
						break;
					case 'err02':
						$message = '<span class="jsn-red-message">Installation is not authorized. We could not find the product in your order list. Seems like you did not purchase it yet..</span>';
						break;
					case 'err03':
						$message = '<span class="jsn-red-message">Requested file is not found on server</span>';
						break;
					default:
						$message = '';
						break;
				}
				if ($message != '')
				{
					echo json_encode(array('download' => false, 'file_name' => '', 'connection' => true, 'message' => $message, 'manual' => false));
					$jsnUpgradeHelper->destroyUpgradeSession($sessionTemp);
				}
				else
				{
					$session->set($sessionTemp['new_package_file'], (string) $result, 'jsntemplatesession');
					echo json_encode(array('download' => true, 'file_name' => (string) $result, 'connection' => true, 'message'=>'', 'manual' => false));
				}
			}
			else
			{
				echo json_encode(array('download' => false, 'file_name' => '', 'message' => JText::_('JSN_UPGRADE_FAILED_DOWNLOAD'), 'connection' => false, 'manual' => true));
			}
		}
		break;

	case 'ajax_install_pro':
		if ($session->get($sessionTemp['jsn_login'], false, 'jsntemplatesession'))
		{
			$isAjax = true;

			$packageFile = $session->get($sessionTemp['new_package_file'], '', 'jsntemplatesession');
			$packagePath = JPATH_ROOT.DS.'tmp'.DS.$packageFile;

			jimport('joomla.installer.helper');
			$unpack = JInstallerHelper::unpack($packagePath);
			if ($unpack)
			{
				$installer = JInstaller::getInstance();
				if (!$installer->install($unpack['dir']))
				{
					echo json_encode(array('install' => false, 'message' => JText::_('JSN_UPGRADE_FAILED_INSTALL'), 'manual' => true));
					$jsnUpgradeHelper->destroyUpgradeSession($sessionTemp);
				}
				else
				{
					echo json_encode(array('install' => true, 'message' => '', 'manual' => false));
				}
			}
			else
			{
				echo json_encode(array('install' => false, 'message' => JText::_('JSN_UPGRADE_FAILED_INSTALL_UNPACK'), 'manual' => true));
				$jsnUpgradeHelper->destroyUpgradeSession($sessionTemp);
			}

			JInstallerHelper::cleanupInstall($packageFile, $unpack['dir']);
		}
		break;

	case 'ajax_migrate_settings':
		if ($session->get($sessionTemp['jsn_login'], false, 'jsntemplatesession'))
		{
			$isAjax = true;

			$result = $jsnUpgradeHelper->migrateSettings();
			if ($result !== true)
			{
				echo json_encode(array('migrate' => false, 'new_template_style_id' => ''));
			}
			else
			{
				$newTemplateStyleId = $jsnUpgradeHelper->getProTemplateStyleId();
			 	echo json_encode(array('migrate' => true, 'message' => '', 'new_template_style_id' => $newTemplateStyleId));
			}

			$jsnUpgradeHelper->destroyUpgradeSession($sessionTemp);
		}
		break;

	case 'ajax_destroy_sesison':
		$isAjax = true;
		$jsnUpgradeHelper->destroyUpgradeSession($sessionTemp);
		echo json_encode(array('sessionclear' => true));
		break;

	default:
		break;
}

if ($isAjax) {
	jexit();
}

/* Begin to include appropriate HTML content */
include('elements/upgrader/jsn_head.php');

/* Intro page to show benefits of using PRO edition */
if (!$session->get($sessionTemp['upgrader'], false, 'jsntemplatesession'))
{
	$buyLink = JSN_BASE_BUY_LINK . str_replace('_', '-', $jsnUpgradeHelper->_name) . '-buy-now.html';
	include('elements/upgrader/jsn_upgradeinfo.php');
}
else
{
	if (!$session->get($sessionTemp['joomla_login'], false, 'jsntemplatesession')) 
	{
		$session->set($sessionTemp['joomla_login'], false, 'jsntemplatesession');
		/* Require login with Joomla Super Administrator account */
		include('elements/upgrader/jsn_joomlaloginform.php');
	}
	else
	{
		if ($manualUpgrade)
		{
			if ($session->get($sessionTemp['new_package_file'], false, 'jsntemplatesession'))
			{
				$manualUpgrade = true;
				$upgradeEdition = 'PRO';
				include('elements/upgrader/jsn_doupgrade.php');
			}
			else
			{
				include('elements/upgrader/jsn_manual.php');
			}
		}
		else
		{
			if (!$session->get($sessionTemp['jsn_login'], false, 'jsntemplatesession'))
			{
				$session->set($sessionTemp['jsn_login'], false, 'jsntemplatesession');

				/* Joomla Login Successful. Change to JSN Customer Login */
				include('elements/upgrader/jsn_loginform.php');
			}
			else
			{
				/* Change to Upgrade view, start upgrade process */
				$upgradeEdition = strtoupper($session->get($sessionTemp['upgrade_edition'], '', 'jsntemplatesession'));
				include('elements/upgrader/jsn_doupgrade.php');
			}
		}
	}
}

include('elements/upgrader/jsn_foot.php');
