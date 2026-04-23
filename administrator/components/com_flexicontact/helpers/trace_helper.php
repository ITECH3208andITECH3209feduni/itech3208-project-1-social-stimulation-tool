<?php
/********************************************************************
Product		: Flexicontact
Date		: 10 October 2022
Copyright	: Les Arbres Design 2010-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

define("LAFC_TRACE_FILE_NAME", 'trace.txt');
define("LAFC_TRACE_FILE_PATH", JPATH_ROOT.'/components/com_flexicontact/trace.txt');
define("LAFC_TRACE_FILE_URL", JURI::root().'components/'.LAFC_COMPONENT.'/trace.txt');
define("LAFC_MAX_TRACE_SIZE", 1000000);	// about 1Mb
define("LAFC_MAX_TRACE_AGE",   21600);		// maximum trace file age in seconds (6 hours)
define("LAFC_UTF8_HEADER",     "\xEF\xBB\xBF");	// UTF8 file header

if (class_exists("FC_trace"))
	return;

class FC_trace
{

//-------------------------------------------------------------------------------
// Write an entry to the trace file
// Tracing is ON if the trace file exists
// if $no_time is true, the date time is not added
//
static function trace($data)
{
	if (@!file_exists(LAFC_TRACE_FILE_PATH))
		return;
	if (filesize(LAFC_TRACE_FILE_PATH) > LAFC_MAX_TRACE_SIZE)
		{
		@unlink(LAFC_TRACE_FILE_PATH);
		@file_put_contents(LAFC_TRACE_FILE_PATH, LAFC_UTF8_HEADER.date("d/m/y H:i").' New trace file created'."\n");
		}
	@file_put_contents(LAFC_TRACE_FILE_PATH, $data."\n",FILE_APPEND);
}

//-------------------------------------------------------------------------------
// Start a new trace file
//
static function init_trace()
{
	self::delete_trace_file();
	@file_put_contents(LAFC_TRACE_FILE_PATH, LAFC_UTF8_HEADER.date("d/m/y H:i").' Tracing Initialised'."\n");
	
	$locale = setlocale(LC_ALL,0);
	$locale_string = print_r($locale, true);
	$langObj = JFactory::getLanguage();
	$app = JFactory::getApplication();
	$mailfrom = $app->get('mailfrom');
	$fromname = $app->get('fromname');
    if (empty($mailfrom))
        $mailfrom = "***** Global Config mailfrom is BLANK *****";
    if (empty($fromname))
        $fromname = "***** Global Config fromname is BLANK *****";

	$xml_array = JInstaller::parseXMLInstallFile(JPATH_ADMINISTRATOR.'/components/com_flexicontact/flexicontact.xml');
	$component_version = $xml_array['version'];
        
	self::trace("Component version: ".$component_version);
	self::trace("PHP version      : ".phpversion());
	self::trace("PHP Locale       : ".$locale_string);
	self::trace("Server           : ".PHP_OS);
	self::trace("Joomla Version   : ".JVERSION);
	self::trace("Joomla Language  : ".$langObj->get('tag'));
	self::trace("Session Lifetime : ".$app->get('lifetime').' minutes');
	self::trace("JURI::root()     : ".JURI::root());
	self::trace("JPATH_SITE       : ".JPATH_SITE);
	self::trace("Joomla Live_site : ".$app->get('live_site'));
	self::trace("Joomla Caching   : ".$app->get('caching'));
	self::trace("Joomla Mailer    : ".$app->get('mailer'));
	self::trace("Joomla Mail From : ".$mailfrom);
	self::trace("Joomla From Name : ".$fromname);
	self::trace("Joomla Session   : ".$app->get('session_handler'));
	if (JPluginHelper::isEnabled('system', 'cache'))
		self::trace("Sys Cache Plugin : Enabled");
	else
		self::trace("Sys Cache Plugin : Not enabled");
}

//-------------------------------------------------------------------------------
// Trace an entry point
// Tracing is ON if the trace file exists
//
static function trace_entry_point($front=false)
{
	if (@!file_exists(LAFC_TRACE_FILE_PATH))
		return;
		
// if the trace file is more than 6 hours old, delete it, which will switch tracing off
//  - we don't want trace to be left on accidentally

	$filetime = @filemtime(LAFC_TRACE_FILE_PATH);
	if (time() > ($filetime + LAFC_MAX_TRACE_AGE))
		{
		self::delete_trace_file();
		return;
		}
		
	$date_time = date("d/m/y H:i").' ';	
	
	if ($front)
		self::trace("\n".$date_time.'================================ [Front Entry Point] ================================');
	else
		self::trace("\n".$date_time.'================================ [Admin Entry Point] ================================');
		
	if ($front)
		{
		if (isset($_SERVER["REMOTE_ADDR"]))
			$ip_address = '('.$_SERVER["REMOTE_ADDR"].')';
		else
			$ip_address = '';

		if (isset($_SERVER["HTTP_USER_AGENT"]))
			$user_agent = $_SERVER["HTTP_USER_AGENT"];
		else
			$user_agent = '';

		$method = $_SERVER['REQUEST_METHOD'];
		
		self::trace("$method from $ip_address $user_agent");

		if (isset($_SERVER["CONTENT_TYPE"]))
			self::trace('Content-Type: '.$_SERVER["CONTENT_TYPE"], true);

		if (isset($_SERVER["HTTP_REFERER"]))
			self::trace('Referer: '.$_SERVER["HTTP_REFERER"], true);
		}

	if (!empty($_POST))
		self::trace("Post data: ".print_r($_POST,true));
	if (!empty($_GET))
		self::trace("Get data: ".print_r($_GET,true));
}

//-------------------------------------------------------------------------------
// Delete the trace file
//
static function delete_trace_file()
{
	if (@file_exists(LAFC_TRACE_FILE_PATH))
		@unlink(LAFC_TRACE_FILE_PATH);
}

//-------------------------------------------------------------------------------
// Return true if tracing is currently active
//
static function tracing()
{
	if (@file_exists(LAFC_TRACE_FILE_PATH))
		return true;
	else
		return false;
}

}