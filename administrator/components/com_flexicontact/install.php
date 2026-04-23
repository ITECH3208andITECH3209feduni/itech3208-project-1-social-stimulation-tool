<?php
/********************************************************************
Product		: Flexicontact
Date		: 25 February 2023
Copyright	: Les Arbres Design 2009-2023
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class com_flexicontactInstallerScript
{
var $app;
var $_db;
var $previous_component_version;

public function preflight($type, $parent) 
{
    if (defined('JVERSION'))
        $joomla_version = JVERSION;         // get the Joomla version (JVERSION did not exist before Joomla 2.5)
    else
        $joomla_version = '1.x';
        
    $this->app = JFactory::getApplication();

	if (version_compare($joomla_version,"3.7.0","<"))
		{
        $this->app->enqueueMessage("Flexicontact requires at least Joomla 3.7.0. This is $joomla_version ", 'error');
		return false;
		}
		
	$dbtype = $this->app->get('dbtype');
	if (!strstr($dbtype,'mysql'))
		{
        $this->app->enqueueMessage("Flexicontact currently only supports MYSQL databases. It cannot run with $dbtype", 'error');
		return false;
		}

	$this->_db = JFactory::getDBO();
    $db_version = $this->ladb_loadResult('select version()');
	if (version_compare($db_version,"5.5.3","<"))
		{
        $this->app->enqueueMessage("Flexicontact requires at least MySql 5.5.3. Your version is $db_version", 'error');
		return false;
		}

	if (version_compare(PHP_VERSION,"5.3.0","<"))
		{
        $app->enqueueMessage("Flexicontact requires at least PHP 5.3.0. Your version is ".PHP_VERSION, 'error');
		return false;
		}

	if (!function_exists('mb_substr'))
		{
        $this->app->enqueueMessage("Flexicontact cannot run on this server because it does not support the PHP Multibyte String Functions", 'error');
		return false;
		}

// get the previously installed version, if any

	if (file_exists(JPATH_ADMINISTRATOR.'/components/com_flexicontact/flexicontact.xml'))
		{
		$xml_array = JInstaller::parseXMLInstallFile(JPATH_ADMINISTRATOR.'/components/com_flexicontact/flexicontact.xml');
		$this->previous_component_version = $xml_array['version'];
		}
        
// At version 12.00 we moved the assets directory from the front end of the component to the media directory
// We rename the old assets directory because otherwise Joomla will delete it during the install,
// and it's possible that the user had customised the CSS or had some additional images

	if (file_exists(JPATH_SITE.'/components/com_flexicontact/assets'))
		@rename(JPATH_SITE.'/components/com_flexicontact/assets', JPATH_SITE.'/components/com_flexicontact/old_assets');

	return true;
}

public function uninstall($parent)
{ 
	$text = "You uninstalled the Flexicontact component. If you want to remove the Flexicontact data, execute this query in phpMyAdmin:
	         <br><br>DROP TABLE #__flexicontact_log;
             <br><br>If you DO NOT execute the query, you can install Flexicontact again without losing your data.
             <br>Please note that you don't have to uninstall Flexicontact to install a new version. Simply install the new version without uninstalling the current version.";
    $this->app = JFactory::getApplication();
	$dbprefix = $this->app->get('dbprefix');
	$text = str_replace('#__', $dbprefix, $text);
    $this->app->enqueueMessage($text, 'notice');
	return true;
}

//-------------------------------------------------------------------------------
// The main install function
//
public function postflight($type, $parent)
{		
	if ($type == 'uninstall')
		return;

// we don't support the Hathor template

	$template = $this->app->getTemplate();
    if ($template == 'hathor')
        $this->app->enqueueMessage("Flexicontact does not support the Hathor administrative template. Please use a different template.", 'notice');

// check the Joomla version

	if (substr(JVERSION,0,1) > "4")				// if > 4
        $this->app->enqueueMessage("This version of Flexicontact has not been tested on this version of Joomla.", 'notice');
		
// get the component version from the component manifest xml file		

    $component_version = $parent->getManifest()->version;

// delete redundant files from older versions

	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/admin.flexicontact.php');
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/toolbar.flexicontact.html.php'); 
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/toolbar.flexicontact.php'); 
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/admin.flexicontact.html.php');
	@unlink(JPATH_SITE.'/components/com_flexicontact/flexicontact.html.php');
	@unlink(JPATH_SITE.'/components/com_flexicontact/RL_flexicontact.html.php');
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/joomla15.xml');
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/joomla16.xml');
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/install.flexicontact.php');
	@unlink(JPATH_SITE.'/components/com_flexicontact/views/responsive/view.html.php');  // leave the rest of the view so that old menu items still work
    @unlink(JPATH_SITE.'/components/com_flexicontact/error_log.txt');
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/helpers/db_helper.php');
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/assets/com_flexicontact.css');
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/forms/image.php');
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/assets/captcha2.png');
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/assets/captcha3.png');
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/assets/captcha6.png');
   	@unlink(JPATH_SITE.'/components/com_flexicontact/views/contact/metadata.xml');
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/latest_version.xml');			// re-created by the about view

	self::recurse_delete(JPATH_SITE."/administrator/components/com_flexicontact/forms");

    self::deleteAdminViews(array('help','log_list','log_detail','config_list','config_general','config_fields','config_text','config_template',
        'config_captcha','config_confirm','config_images','config_css'));

// clean any old .gif files from the admin assets directory (we only use .png images now)

    foreach (glob(JPATH_SITE.'/administrator/components/com_flexicontact/assets/*.gif') as $filename)
        @unlink($filename);
        
// we no longer install the view named 'responsive', but if it is present, copy the default.xml from 'contact' to 'responsive'

    if (file_exists(JPATH_SITE.'/components/com_flexicontact/views/responsive/tmpl/default.xml'))
        copy(JPATH_SITE.'/components/com_flexicontact/views/contact/tmpl/default.xml', JPATH_SITE.'/components/com_flexicontact/views/responsive/tmpl/default.xml');

// remove redundant http update sites, if present

	$this->_db = JFactory::getDBO();
	$this->ladb_execute_ignore("DELETE FROM `#__update_sites` WHERE `name`= 'Flexicontact' AND SUBSTRING(`location`,1,5) = 'http:'");

// we now install language files in the component directories, so must remove them from the system-wide directories, since those would take precedence

    $dirs = glob(JPATH_ADMINISTRATOR.'/language/*',GLOB_ONLYDIR);
    foreach ($dirs as $dir)
        {
        $sub_dir = basename($dir);
    	@unlink($dir.'/'.$sub_dir.'.com_flexicontact.ini');
    	@unlink($dir.'/'.$sub_dir.'.com_flexicontact.sys.ini');
        }

    $dirs = glob(JPATH_SITE.'/language/*',GLOB_ONLYDIR);
    foreach ($dirs as $dir)
        {
        $sub_dir = basename($dir);
    	@unlink($dir.'/'.$sub_dir.'.com_flexicontact.ini');
        }

// remove the original 20 GIF images

	$image_path = JPATH_SITE.'/media/com_flexicontact/images/';
	for ($i=1; $i<=20; $i++)
		@unlink($image_path.sprintf('%03u.gif',$i));

// create or upgrade the log table

	$this->create_log_table();
	$this->ladb_execute_ignore("ALTER TABLE `#__flexicontact_log` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
	$this->add_column('#__flexicontact_log', 'admin_email', "VARCHAR(60) NOT NULL DEFAULT '' AFTER `email`");
	$this->add_column('#__flexicontact_log', 'list_choice', "VARCHAR(60) DEFAULT NULL AFTER `browser_string`");
	$this->add_column('#__flexicontact_log', 'admin_from_email', "VARCHAR(60) DEFAULT NULL AFTER `admin_email`");                   // 8.08
	$this->add_column('#__flexicontact_log', 'user_from_email', "VARCHAR(60) NOT NULL DEFAULT '' AFTER `admin_from_email`");		// 8.08
	$this->add_column('#__flexicontact_log', 'admin_reply_to_email', "VARCHAR(60) NOT NULL DEFAULT '' AFTER `user_from_email`");	// 8.08
	$this->add_column('#__flexicontact_log', 'config_show_copy', "TINYINT(4) NOT NULL DEFAULT 0 AFTER `message`");		            // 8.08
	$this->add_column('#__flexicontact_log', 'show_copy', "TINYINT(4) NOT NULL DEFAULT 0 AFTER `config_show_copy`");                // 8.08
	$this->add_column('#__flexicontact_log', 'agreement_check', "TINYINT(4) NOT NULL DEFAULT 0 AFTER `field5`");                    // 12.01
	$this->add_column('#__flexicontact_log', 'admin_email_subject', "varchar(255) NOT NULL DEFAULT ''");		        	        // 12.13
	$this->add_column('#__flexicontact_log', 'user_email_subject', "varchar(255) NOT NULL DEFAULT ''");		        	            // 12.13
	$this->add_column('#__flexicontact_log', 'admin_email_body', "text NOT NULL");                                                  // 13.03
	$this->add_column('#__flexicontact_log', 'user_email_body', "text NOT NULL");                                                   // 13.03

	$this->ladb_execute_ignore("ALTER TABLE `#__flexicontact_log` CHANGE `list_choice` `list_choice` VARCHAR(255) NOT NULL DEFAULT '' ");   // 12.13
	$this->ladb_execute_ignore("ALTER TABLE `#__flexicontact_log` CHANGE `field1` `field1` VARCHAR(255) NOT NULL DEFAULT '' ");             // 12.13
	$this->ladb_execute_ignore("ALTER TABLE `#__flexicontact_log` CHANGE `field2` `field2` VARCHAR(255) NOT NULL DEFAULT '' ");             // 12.13
	$this->ladb_execute_ignore("ALTER TABLE `#__flexicontact_log` CHANGE `field3` `field3` VARCHAR(255) NOT NULL DEFAULT '' ");             // 12.13
	$this->ladb_execute_ignore("ALTER TABLE `#__flexicontact_log` CHANGE `field4` `field4` VARCHAR(255) NOT NULL DEFAULT '' ");             // 12.13
	$this->ladb_execute_ignore("ALTER TABLE `#__flexicontact_log` CHANGE `field5` `field5` VARCHAR(255) NOT NULL DEFAULT '' ");             // 12.13
	$this->ladb_execute_ignore("ALTER TABLE `#__flexicontact_log` CHANGE `ip` `ip` VARCHAR(45) NOT NULL DEFAULT '' ");                      // 12.15
	$this->ladb_execute_ignore("ALTER TABLE `#__flexicontact_log` CHANGE `config_show_copy` `config_show_copy` TINYINT(4) NOT NULL DEFAULT 0"); // 13.03
	$this->ladb_execute_ignore("ALTER TABLE `#__flexicontact_log` CHANGE `show_copy` `show_copy` TINYINT(4) NOT NULL DEFAULT 0");               // 13.03

// upgrade the component configuration as necessary

	$this->upgrade_config();

// check the language files for errors - some older files contain errors

    $this->check_language_files('com_flexicontact',true,false);
    
// if upgrading from a version older than 12.00, show an extra message

	if (isset($this->previous_component_version) && (version_compare($this->previous_component_version,"12.00", "<")) )
        $this->app->enqueueMessage("If you use the image captcha system, please install one of the (free) image packs. The original 20 images are no longer included.", 'message');

// copy default.css to com_flexicontact.css if it doesn't exist

	if (!file_exists(JPATH_ROOT.'/media/com_flexicontact/css/com_flexicontact.css'))
		copy(JPATH_ROOT.'/media/com_flexicontact/css/default.css', JPATH_ROOT.'/media/com_flexicontact/css/com_flexicontact.css');

// we are done, show the update or install message

	if (isset($this->previous_component_version) && version_compare($this->previous_component_version,$component_version,"<"))
		{
		$url = 'https://www.lesarbresdesign.info/version-history/flexicontact';
		$link = JHtml::link($url, $url, 'target="_blank"');
        $this->app->enqueueMessage("Flexicontact updated to version $component_version. Here's what changed: $link", 'message');
		}
    else
	    $this->app->enqueueMessage("Flexicontact version $component_version installed.", 'message');
	return true;
}

//---------------------------------------------------------------
// Create the log table if it doesn't exist
//
function create_log_table()
{
	$query = "CREATE TABLE IF NOT EXISTS `#__flexicontact_log` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `datetime` datetime NOT NULL,
				  `name` varchar(60) NOT NULL DEFAULT '',
				  `email` varchar(60) NOT NULL DEFAULT '',
				  `admin_email` varchar(60) NOT NULL DEFAULT '',
                  `admin_from_email` varchar(60) NOT NULL DEFAULT '',
                  `user_from_email` varchar(60) NOT NULL DEFAULT '',
                  `admin_reply_to_email` varchar(60) NOT NULL DEFAULT '',
				  `subject` varchar(100) NOT NULL DEFAULT '',
				  `message` text NOT NULL,
				  `config_show_copy` TINYINT(4) NOT NULL DEFAULT 0,
				  `show_copy` TINYINT(4) NOT NULL DEFAULT 0,
				  `status_main` varchar(255) NOT NULL DEFAULT '',
				  `status_copy` varchar(255) NOT NULL DEFAULT '',
				  `ip` varchar(45) NOT NULL DEFAULT '',
				  `browser_id` tinyint(4) NOT NULL DEFAULT 0,
				  `browser_string` varchar(20) NOT NULL DEFAULT '',
				  `list_choice` varchar(255) NOT NULL DEFAULT '',
				  `field1` varchar(255) NOT NULL DEFAULT '',
				  `field2` varchar(255) NOT NULL DEFAULT '',
				  `field3` varchar(255) NOT NULL DEFAULT '',
				  `field4` varchar(255) NOT NULL DEFAULT '',
				  `field5` varchar(255) NOT NULL DEFAULT '',
				  `agreement_check` TINYINT(4) NOT NULL DEFAULT 0,
				  `admin_email_subject` varchar(255) NOT NULL DEFAULT '',
				  `user_email_subject` varchar(255) NOT NULL DEFAULT '',
				  `admin_email_body` text NOT NULL,
				  `user_email_body` text NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `DATETIME` (`datetime`)
				) DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";
	return $this->ladb_execute($query);
}

//-------------------------------------------------------------------------------
// Various upgrades to the component configuration parameters
//
function upgrade_config()
{	
	$query = "SELECT `params` from `#__extensions` WHERE `type` = 'component' AND `element` = 'com_flexicontact'";
	$params = $this->ladb_loadResult($query);
	if (($params === false) || (empty($params)) || ($params == '{}'))
        return;             							// there are no component parameters yet

	$config_data = json_decode($params);
        
	if (!empty($config_data->agreement_name))			// 12.08 - removed the agreement_name parameter and moved it to the agreement_prompt
		{
		if (empty($config_data->agreement_prompt))
			$config_data->agreement_prompt = '';
		$config_data->agreement_prompt .= ' %'.$config_data->agreement_name.'%';
		unset($config_data->agreement_name);
		}

	if (empty($config_data->copyme_prompt))				// 12.14 - added copyme_prompt
		$config_data->copyme_prompt = '';

	if (empty($config_data->error_class))				// 12.14 - added error_class
		$config_data->error_class = '';

// save the config data

    $query = "UPDATE `#__extensions` SET `params` = ".$this->_db->quote(json_encode($config_data))." WHERE `type` = 'component' AND `element` = 'com_flexicontact'";
    $this->ladb_execute($query);
}

//-------------------------------------------------------------------------------
// Check whether a column exists in a table. Returns TRUE if exists, FALSE if it doesn't
//
function column_exists($table, $column)
{
	$fields = $this->_db->getTableColumns($table);
		
	if ($fields === null)
		return false;
		
	if (array_key_exists($column,$fields))
		return true;
	else
		return false;
}

//-------------------------------------------------------------------------------
// Add a column if it doesn't exist (the table must exist)
//
function add_column($table, $column, $details)
{
	if ($this->column_exists($table, $column))
		return;
	$query = 'ALTER TABLE `'.$table.'` ADD `'.$column.'` '.$details;;
	return $this->ladb_execute($query);
}

//-------------------------------------------------------------------------------
// Execute a SQL query and return true if it worked, false if it failed
//
function ladb_execute($query)
{
	try
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
		}
	catch (RuntimeException $e)
		{
        $message = $e->getMessage();
        $this->app->enqueueMessage($message, 'error');
		return false;
		}
	return true;
}

//-------------------------------------------------------------------------------
// Execute a SQL query ignoring any errors
//
function ladb_execute_ignore($query)
{
	try
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
		}
	catch (RuntimeException $e)
		{
		return;
		}
	return;
}

//-------------------------------------------------------------------------------
// Get a single value from the database as an object and return it, or false if it failed
//
function ladb_loadResult($query)
{
	try
		{
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		}
	catch (RuntimeException $e)
		{
        $message = $e->getMessage();
        $this->app->enqueueMessage($message, 'error');
		return false;
		}
	return $result;
}

//-------------------------------------------------------------------------------
// Delete one or more back end views
//
static function deleteAdminViews($views)
{
    foreach ($views as $view)
		self::recurse_delete(JPATH_SITE."/administrator/components/com_flexicontact/views/$view");
}

//-------------------------------------------------------------------------------
// Recursively delete a folder and all its contents
//
static function recurse_delete($dir)
{ 
	if (!file_exists($dir))
		return;
    $files = array_diff(scandir($dir), array('.','..')); 
    foreach ($files as $file)
        if (is_dir($dir.'/'.$file))
            self::recurse_delete($dir.'/'.$file);
        else
            unlink($dir.'/'.$file); 
    rmdir($dir); 
}

// -------------------------------------------------------------------------------
// Check all the language file for errors
//
function check_language_files($component,$admin,$site)
{
	$errors = array();
    $admin_dirs = array();
    $site_dirs = array();
    if ($admin)
        $admin_dirs = glob(JPATH_ADMINISTRATOR.'/components/'.$component.'/language/*',GLOB_ONLYDIR);
    if ($site)
        $site_dirs = glob(JPATH_SITE.'/components/'.$component.'/language/*',GLOB_ONLYDIR);
    $all_dirs = array_merge($admin_dirs, $site_dirs);
    foreach ($all_dirs as $dir)
        {
        $sub_dir = basename($dir);
        $files = glob($dir.'/*.ini');
        foreach ($files as $file_name)
            if (!self::parseIniFile($file_name))
                $errors[] = 'Language file has formatting errors: '.str_replace(JPATH_SITE, '', $file_name);;
        }

	if (!empty($errors))
        $this->app->enqueueMessage(implode('<br>',$errors), 'notice');
}

// -------------------------------------------------------------------------------
// Check a language file for correct syntax
//
static function parseIniFile($filename)
{
    if (!is_file($filename))
        return false;
    $contents = file_get_contents($filename);
    $contents = str_replace('_QQ_', '"\""', $contents);
    $strings  = @parse_ini_string($contents);
    if ($strings === false)
        return false;
    return true;
}

}