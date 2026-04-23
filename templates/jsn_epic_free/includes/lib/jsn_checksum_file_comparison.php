<?php
/**
 * @author    JoomlaShine.com http://www.joomlashine.com
 * @copyright Copyright (C) 2008 - 2011 JoomlaShine.com. All rights reserved.
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 * @version   $Id: jsn_checksum_file_comparison.php 10887 2012-01-18 09:13:22Z giangnd $
 */
defined('_JEXEC') or die('Restricted access');
include_once dirname(__FILE__).DS.'jsn_checksum.php';
class JSNChecksumFileComparison extends JSNChecksum
{
	var $_comparedFile 	= '';
	var $_comparingFile	= '';

	function JSNChecksumFileComparison($comparedFilePath)
	{
		parent::JSNChecksum();
		$this->_comparedFile 	= $comparedFilePath.DS.$this->_checksum_file_name;
		$this->_comparingFile 	= $this->_template_folder_path.DS.$this->_checksum_file_name;
	}

	function compareFileContent()
	{
		$comparedContentFile 		= $this->_getFileContent($this->_comparedFile);
		$comparingContentFile 		= $this->_getFileContent($this->_comparingFile);
		return $this->compare($comparedContentFile, $comparingContentFile);
	}
}