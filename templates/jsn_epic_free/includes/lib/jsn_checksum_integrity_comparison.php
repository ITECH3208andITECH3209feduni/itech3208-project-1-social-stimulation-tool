<?php
/**
 * @author    JoomlaShine.com http://www.joomlashine.com
 * @copyright Copyright (C) 2008 - 2011 JoomlaShine.com. All rights reserved.
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 * @version   $Id: jsn_checksum_integrity_comparison.php 11056 2012-02-06 10:56:00Z giangnd $
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
include_once dirname(__FILE__).DS.'jsn_checksum.php';
class JSNChecksumIntegrityComparison extends JSNChecksum
{
	function JSNChecksumIntegrityComparison()
	{
		parent::JSNChecksum();
	}

	function getFileList()
	{
		$files 		= array ();
		$basePath	= $this->_template_folder_path;
		//Get the list of files from given folder
		$fileList 	= JFolder::files($basePath, '.', true, true, array('.checksum', '.svn', 'CVS', 'language'));
		if ($fileList !== false)
		{
			foreach ($fileList as $file)
			{
				$absolute_path			= str_replace('/', DS, $file);
				$relative_path 			= str_replace(DS, '/', str_replace($basePath.DS, '',  $absolute_path));
				$files[$relative_path] 	= md5_file($absolute_path);
			}
			//unset($files[@$this->_checksum_file_name]);
			unset($files['template.checksum']);
			unset($files['templateDetails.xml']);
		}
		return $files;
	}

	function compareIntegrity()
	{
		$comparedContent 	= $this->getFileList();
		$comparingContent	= $this->_getFileContent($this->_template_folder_path.DS.$this->_checksum_file_name, array('template.checksum'));
		return $this->compare($comparedContent, $comparingContent);
	}
}