<?php
/**
 * Joomla! 1.5-205 component CBJUICE2
 *
 * @version $Id: controller.php 2010-12-25 12:20:17 svn $ * @author Paul Jacobson and Stephen Thompson
 * @copyright Paul Jacobson and Stephen Thompson
 * @package Joomla
 * @subpackage CBJUICE2
 * @license GNU/GPL

 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'  );
global $_CB_framework,$_CB_database, $ueConfig;
/** @global string $_CB_adminpath
 *  @global array $ueConfig
 */
global $_CB_Admin_Done, $_CB_adminpath, $ueConfig, $mainframe;

if ( defined( 'JPATH_ADMINISTRATOR' ) ) {
	$_CB_adminpath		=	JPATH_ADMINISTRATOR . '/components/com_comprofiler';
	include_once $_CB_adminpath . '/plugin.foundation.php';
	include_once $_CB_adminpath . '/ue_config.php';
	include_once $_CB_adminpath . '/plugin.class.php';
	include_once $_CB_adminpath . '/comprofiler.class.php';

} else {
	$_CB_adminpath		=	$mainframe->getCfg( 'absolute_path' ). '/administrator/components/com_comprofiler';
	include_once $_CB_adminpath . '/plugin.foundation.php';
	include_once $_CB_adminpath . '/ue_config.php';
	include_once $_CB_adminpath . '/plugin.class.php';
	include_once $_CB_adminpath . '/comprofiler.class.php';
}
function cbj2_saveusers($delimiter){
	global $_CB_framework,$_CB_database, $ueConfig;
	/** @global string $_CB_adminpath
	 *  @global array $ueConfig
	 */
	global $_CB_Admin_Done, $_CB_adminpath, $ueConfig, $mainframe;

	if ( defined( 'JPATH_ADMINISTRATOR' ) ) {
		$_CB_adminpath		=	JPATH_ADMINISTRATOR . '/components/com_comprofiler';
		include_once $_CB_adminpath . '/ue_config.php';
		include_once $_CB_adminpath . '/plugin.class.php';
		include_once $_CB_adminpath . '/comprofiler.class.php';

	} else {
		$_CB_adminpath		=	$mainframe->getCfg( 'absolute_path' ). '/administrator/components/com_comprofiler';
		include_once $_CB_adminpath . '/ue_config.php';
		include_once $_CB_adminpath . '/plugin.class.php';
		include_once $_CB_adminpath . '/comprofiler.class.php';
	}
	//	echo "Starting to save<br>";
	$cbj_debug=FALSE;
	cbimport( 'cb.database' );
	cbimport( 'cb.html' );
	jimport('joomla.user.helper');
	if (strlen($delimiter)<=0){
		// now issue warning messages and exit.
		echo "<b><H1>".JText::_('CBJ_WARNING1'). "</H1></b><br>",
		"<b><h2>".JTEXT::_('CBJ_WARNING2')."</h2></b><br><br><br>";
		return false;
	}
	$jver16=FALSE;
	$versionstuff=new JVersion;
	$thisversion=$versionstuff->getShortVersion();
	if((strcasecmp( substr($thisversion,0,3), '1.6' ) >= 0)){
		$jver16=TRUE;
	}
	/*define a set of fields never to save
	 * this avoids users doing things they should not.
	 */
	$never_save=array("id","gid","usertype");
	$extra_excluded=trim(cbGetParam($_REQUEST, 'cbjuice_excluded_fields'));
	$extra_excluded=str_replace(" ","",$extra_excluded);
	$extra_fields_exclude=explode($delimiter,$extra_excluded);
	$never_save=array_merge($never_save,$extra_fields_exclude);
	/* first retrieve the fields for both the user table anc comprofiler table.
	 * the keys are the field names  usertype is there because it is specially processed
	 *
	 */
	$full_field_names=$_CB_database->getTableFields(array('#__users','#__comprofiler'),TRUE);
	$users_fields=$full_field_names['#__users'];
	$comprofiler_fields=$full_field_names['#__comprofiler'];
	$users_keys=array_keys($users_fields);
	$comprofiler_keys=array_keys($comprofiler_fields);
	$users_fields_edited=array_diff($users_keys, $never_save);
	//	echo "User fields to save<br>";
	//	print_r($users_fields_edited);
	//	echo "<br>";
	$comprofiler_fields_edited=array_diff($comprofiler_keys,$never_save);
	//	echo "Comprofiler fields to save<br>";
	//	print_r($comprofiler_fields_edited);
	//	echo "<br>";
	/* Build up the query
	 * u for the user fields and c for the comprofiler fields
	 */
	$query3= " FROM #__users u  LEFT JOIN #__comprofiler c on (u.id=c.user_id)";
	$query1="SELECT ";
	$query2a="";
	$fieldcount=0;
	$numbfields=count($users_fields_edited);
	foreach($users_fields_edited as $thisfield){
		if($fieldcount>0 and $fieldcount<($numbfields)){
			$query2a=$query2a." ,";
		}
		$query2a=$query2a."u.".$thisfield;
		$fieldcount++;
	}
	//add the id field so that we can get the user groups.
	if ($query2a)
	{
		$query2a="u.id,".$query2a;
	}
	else {
		$query2a="u.id";
	}

	$fieldcount=0;
	$query2b="";
	$numbfields=count($comprofiler_fields_edited);
	if($numbfields>0)
	foreach($comprofiler_fields_edited as $thisfield){
		if($fieldcount>0 and $fieldcount<($numbfields)){
			$query2b=$query2b." ,";
		}
		$query2b=$query2b."c.".$thisfield;
		$fieldcount++;
	}
	//	echo "Query2a=",$query2a,"<br>";
	//	echo "Query2b=",$query2b,"<br>";
	$query2=$query2a.",".$query2b;
	$query=$query1.$query2.$query3;
	//	echo "Trying query = $query <br>";
	$_CB_database->setQuery($query);
	$result=$_CB_database->query();
	if(!$result){
		echo "Error retrieving the data serious error<br>";
		echo $_CB_database->getErrorMSG()."<br>";
		return FALSE;
	}
	$numbusers=$_CB_database->getNumRows();
	$fulldata=$_CB_database->loadAssocList();
	//	var_dump($fulldata);
	$namesarray=array_merge($users_fields_edited,$comprofiler_fields_edited,array("usertype"));
	/*
	 * Write the page header
	 *
	 */
	$output_csv_name="users_".date( 'YmdHiS', $_CB_framework->now() ).".csv";
	echo "$output_csv_name <br>";
	if(!$cbj_debug){
		while( @ob_end_clean() );
		ob_start();
		$browser="IE";
		header('Content-Type: '.(($browser=='IE' || $browser=='OPERA')?
        'text/plain':'text/plain'));
		header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');

		if($browser=='IE') {
			header('Content-Disposition: attachment; filename="'.$output_csv_name.'"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header('Content-Disposition: attachment; filename="'.$output_csv_name.'"');
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: no-cache');
		}
	}
	// write header array
	$outputrow= "\"" . implode("\"$delimiter\"", $namesarray) . "\"" ."\r\n";
	print $outputrow;
	$usercount=0;
	Foreach($fulldata as $thisuser){
		if($cbj_debug){
			print_r($thisuser);
			echo "<br>";
		}

		$thisuser1=$thisuser;
		$thisuser_id=$thisuser['id'];
		//		echo "user id is $thisuser_id <br>";
		$thisone=new JUser();
		$result=$thisone->load($thisuser_id);
		if(!$result){
			"Echo serious problems retrieving $thisuser_id <br>";
			return;
		}
		if($jver16){
		$thisusers_groups=$thisone->groups;
		$thisusers_groupnames=array();
			foreach ($thisusers_groups as $group_number)
				{
					$thisusers_groupnames[]=$_CB_framework->acl->get_group_name($group_number);
				}
		if(count($thisusers_groupnames)>0){
				$thisones_types=implode("|",($thisusers_groupnames));
				//	$thisones_types=implode("|",array_keys($thisusers_groups));
			}
		} else {
			$thisones_types=$thisone->usertype;
		}

		// now we have to escape each field
		$thisuser2=array();
		// alternative string to use is \0..\37\177..\377
		// need to skip the first 1 to eliminiate userid;
		$counter=0;
		foreach($thisuser as $thiscell){
			$newcell=str_replace("\"","\"\"",$thiscell);
			$newcell=addcslashes($newcell, "\0..\37");
			if($counter>0){
				$thisuser2[]=$newcell;
			}
			$counter++;
		}
		$thisuser2[]=$thisones_types;
		$outputrow= "\"" . implode("\"$delimiter\"", $thisuser2) . "\"" ."\r\n";

		if(!$cbj_debug){
			print($outputrow);
		} else {
			print_r($outputrow);
			echo "<br>";
		}

	}
	if(!$cbj_debug){
		ob_end_flush();
	}
	exit;
}

?>
