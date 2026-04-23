<?php
/**
* @Copyright Copyright (C) 2012 Alfred Bösch
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// No direct access.
defined('_JEXEC') or die;

// Include the mod_bo_resetarticlehits functions only once.
require_once dirname(__FILE__).'/helper.php';

// Get module data.
$list = modBoResetArticleHitsHelper::getList($params);

// Render the module
require JModuleHelper::getLayoutPath('mod_bo_resetarticlehits', $params->get('layout', 'default'));
