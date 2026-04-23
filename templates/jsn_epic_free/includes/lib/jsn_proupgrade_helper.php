<?php
/**
 * @author    JoomlaShine.com http://www.joomlashine.com
 * @copyright Copyright (C) 2008 - 2012 JoomlaShine.com. All rights reserved.
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');

include_once(dirname(__FILE__).DS.'jsn_updater_helper.php');
include_once(dirname(__FILE__).DS.'jsn_httpsocket.php');
include_once(dirname(__FILE__).DS.'jsn_downloadtemplatepackage.php');

class JSNProUpgradeHelper extends JSNUpdaterHelper
{
	var $_template_version      = '';
	var $_edition               = '';
	var $_identified_name       = '';
	var $_joomla_version        = '';
	var $_full_name             = '';
	var $_name                  = '';

	function JSNProUpgradeHelper($templateInfo, $joomlaVersion)
	{
		parent::JSNUpdaterHelper();

		$this->_template_version      = $templateInfo['version'];
		$this->_identified_name       = str_replace('jsn', 'tpl', strtolower($templateInfo['name']));
		$this->_joomla_version        = $joomlaVersion;
		$this->_full_name             = strtolower($templateInfo['full_name']);
		$this->_name                  = strtolower($templateInfo['name']);
	}

	function getPackageFromUpload()
	{
		$install_file = JRequest::getVar('package', null, 'files', 'array');

		if (!(bool) ini_get('file_uploads'))
		{
			$msg 	= 'File upload function is disabled, please enable it in file "php.ini"';
			JError::raiseWarning('SOME_ERROR_CODE', JText::_($msg));
			return false;
		}
		if (!extension_loaded('zlib'))
		{
			$msg = 'Zlib library is disabled, please enable it in file "php.ini"';
			JError::raiseWarning('SOME_ERROR_CODE', JText::_($msg));
			return false;
		}
		if ($install_file['name'] == '')
		{
			$msg 	= 'Installation package is not selected, please download and select it';
			JError::raiseWarning('SOME_ERROR_CODE', JText::_($msg));
			return false;
		}
		if (JFile::getExt($install_file['name']) != 'zip')
		{
			$msg = 'The package has incorrect format, please use exactly the file you downloaded';
			JError::raiseWarning('SOME_ERROR_CODE', JText::_($msg));
			return false;
		}

		$tmp_dest 	= JPATH_ROOT.DS.'tmp'.DS.$install_file['name'];
		$tmp_src	= $install_file['tmp_name'];

		if (!JFile::upload($tmp_src, $tmp_dest))
		{
			$msg = 'Folder "tmp" is Unwritable, please set it to Writable (chmod 777). You can set the folder back to Unwritable after sample data installation';
			JError::raiseWarning('SOME_ERROR_CODE', JText::_($msg));
			return false;
		}

		return $install_file['name'];
	}

	/**
	 * This function will authenticate the user as JoomlaShine customer and
	 * return the array of versions of template that the user purchased
	 * @return [type] [description]
	 */
	function authenticateCustomerInfo()
	{
		$post						= JRequest::get('post');
		$post['customer_password'] 	= JRequest::getString('customer_password', '', 'post', JREQUEST_ALLOWRAW);
		$link						= JSN_TEMPLATE_AUTOUPDATE_URL.'&identified_name='.urlencode($this->_identified_name).'&joomla_version='.urlencode($this->_joomla_version).'&username='.urlencode($post['customer_username']).'&password='.urlencode($post['customer_password']).'&upgrade=no';
		$objHTTPSocket 				= new JSNHTTPSocket($link, null, null, 'get');
		$result    					= $objHTTPSocket->socketDownload();

		$errorCode = strtolower((string) $result);
		$hasError = true;

		$rel = new stdClass();
		if ($result)
		{
			switch ($errorCode)
			{
				case 'err00':
					$message = 'Invalid Parameters! Cannot verify your product information.';
					break;
				case 'err01':
					$message = 'Invalid username or password. Please enter JoomlaShine customer account you created when you purchased the product.';
					break;
				case 'err02':
					$message = 'Installation is not authorized. We could not find the product in your order list. Seems like you did not purchase it yet.';
					break;
				case 'err03':
					$message = 'Requested file could not be found on server.';
					break;
				default:
					$hasError = false;
					break;
			}

			if ($hasError === true)
			{
				$rel->error = true;
				$rel->message = $message;
			}
			else
			{
				/* Standardize the returned array */
				$result = json_decode($result, true);
				$editionArray = array();
				foreach ($result['editions'] as $value) {
					if (!in_array($value, $editionArray))
					{
						$editionArray[] = $value;
					}
				}

				$rel->error                     = false;
				$rel->editions                  = $editionArray;
				$rel->post['customer_username'] = $post['customer_username'];
				$rel->post['customer_password'] = $post['customer_password'];
			}
		}
		else
		{
			$rel->error = true;
			$rel->message = 'Can not authorize your Customer account! Your server does not allow connection to Joomlashine server.';
		}

		return $rel;
	}

	function downloadTemplatePackage($post, $edition)
	{
		$link					= JSN_TEMPLATE_AUTOUPDATE_URL
			.'&identified_name='.urlencode($this->_identified_name)
			.'&edition='.urlencode($edition)
			.'&joomla_version='.urlencode($this->_joomla_version)
			.'&username='.urlencode($post->customer_username)
			.'&password='.urlencode($post->customer_password)
			.'&product_version='.urlencode($this->_template_version)
			.'&upgrade=yes';
		$tmpName				= strtolower($this->_name.'_'.str_replace(' ', '_', $edition).'.zip');
		$objJSNDownloadPackage  = new JSNDownloadTemplatePackage($link, $tmpName);

		return $objJSNDownloadPackage->download();
	}

	function migrateSettings()
	{
		$db = JFactory::getDbo();

		$query = 'SELECT params FROM #__extensions WHERE type = "template"'
					. ' AND element = "' . $this->_full_name . '"';
		$db->setQuery($query);
		$defaultFreeParams = $db->loadResult();
		if ($db->getErrorMsg()) {
			return $db->getErrorMsg();
		}

		$query = 'SELECT params FROM #__template_styles WHERE client_id = 0'
					. ' AND template = "' . $this->_full_name . '"';
		$db->setQuery($query);
		$customFreeParams = $db->loadResult();
		if ($db->getErrorMsg()) {
			return $db->getErrorMsg();
		}

		$query = 'SELECT params FROM #__template_styles WHERE client_id = 0'
					. ' AND template = "' . $this->_name . '_pro' . '"';
		$db->setQuery($query);
		$newProParams = $db->loadResult();
		if ($db->getErrorMsg()) {
			return $db->getErrorMsg();
		}

		if ($defaultFreeParams && $customFreeParams && $newProParams)
		{
			$defaultFreeParams = json_decode($defaultFreeParams, true);
			$customFreeParams  = json_decode($customFreeParams, true);
			$newProParams      = json_decode($newProParams, true);

			foreach ($customFreeParams as $key => $val)
			{
				if (isset($defaultFreeParams[$key])
						&& isset($newProParams[$key])
						&& $defaultFreeParams[$key] != $val) {
					$newProParams[$key] = $val;
				}
			}
		}

		/* Reset default template */
		$query = 'UPDATE #__template_styles SET home = 0 WHERE client_id = 0';
		$db->setQuery($query);
		$db->query();

		$query = 'UPDATE #__template_styles'
					. ' SET params = ' . $db->quote(json_encode($newProParams))
					. ', home = 1'
					. ' WHERE template = ' . $db->quote($this->_name . '_pro')
					. ' AND client_id = 0';

		$db->setQuery($query);
		$db->query();
		if ($db->getErrorMsg()) {
			return $db->getErrorMsg();
		}

		return true;
	}

	function getProTemplateStyleId()
	{
		$db = JFactory::getDbo();
		$query = 'SELECT id FROM #__template_styles'
					. ' WHERE client_id = 0'
					. ' AND template = "' . $this->_name . '_pro"';
		$db->setQuery($query);

		return $db->loadResult();
	}

	function destroyUpgradeSession($sessionNameArray, $noJoomlaLogin = false)
	{
		if (is_array($sessionNameArray))
		{
			$session = JFactory::getSession();
			if ($noJoomlaLogin === true)
			{
				foreach ($sessionNameArray as $sessionKey => $sessionName) {
					if ($sessionKey != 'joomla_login')
					{
						$session->clear($sessionName, 'jsntemplatesession');
					}
				}
			}
			else
			{
				foreach ($sessionNameArray as $sessionName) {
					$session->clear($sessionName, 'jsntemplatesession');
				}
			}
		}
	}
}
