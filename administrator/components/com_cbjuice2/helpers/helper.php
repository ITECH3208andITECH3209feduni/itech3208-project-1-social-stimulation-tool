<?php
/**
 * Joomla! 1.5 component CBJUICE2
 *
 * @version $Id: helper.php 2011-02026 12:20:17 svn $
 * @author Paul Jacobson and Stephen Thompson
 * @package Joomla
 * @subpackage CBJUICE2
 * @license GNU/GPL
 *
 * This component manages users using a CSV file 
 *
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * CBJUICE2 Helper
 *
 * @package Joomla
 * @subpackage CBJUICE2
 * @since 1.5
 */
class Cbjuice2Helper {
/*  Default Fields manages the loading and defaulting of key fields
  * 
  */
public static function cbj2_default_fields($pointer,$record,$default=NULL){
	$return_value=$default;
	if($pointer>=0){
		$return_value=stripcslashes($record[$pointer]);
		} else
		{
			$return_value=$default;}
	return $return_value;
}
/* Find Pointer routine used to search keys in an array.
 * The routine is required to force false (no match) to be a specific value
 */
public static function cbj2_find_pointer($input_string,$headervbls){
	/*this function is to force array_seach to return negative on missing
	 * 
	 */
	$result=array_search($input_string,$headervbls);
	if($result===FALSE)$result=-1;
	return $result;
}
/* cbj2_checkforhash checks to see if the pattern matches a joomla 1 or 2 hashed password
*  the basic approach is to check that the string is lower case hex 32 characters long
*/
public static function cbj2_checkforhash($input_string){
	$hashedpwd=false;
	if(strlen($input_string)<32){
	return false;
	}elseif(strpos($input_string," ")){
		return false;  // blanks cannot be found in the string
	}else {
	/* how check to see if the first 32 are numeric
	*/
	$hashedpwd=is_numeric("0x".substr($input_string,0,32));
	}
	return $hashedpwd;
	
	
}
}
?>