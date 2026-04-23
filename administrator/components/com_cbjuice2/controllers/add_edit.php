<?php
/**
 * Joomla! 1.5 component CBJUICE2
 *
 * @version $Id: controller.php 2011-12-20 $
 * @author Paul Jacobson and Stephen Thompson
 * @copyright Paul Jacobson and Stephen Thompson
 * @package Joomla
 * @subpackage CBJUICE2
 * @license GNU/GPL
 *
 * This component manages users using a CSV file
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'  );
global $_CB_framework,$_CB_database, $ueConfig, $cbj2_opptype;
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



function cbj2_process_users($filename,$cbj2_opptype){
	/* This function does the mainline processing of the CSV file for add-edit
	 * The interface controls whether it is add or edit and whether the activity
	 * keys on the username or email address
	 * In this version of the code, we are trying to use the CB Interface.
	 */
	global $_CB_Admin_Done, $_CB_adminpath, $ueConfig, $_CB_framework, $_CB_database, $mainframe;
	global $_PLUGINS;

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
	//echo "Process users called<br>";
	cbimport( 'cb.database' );
	cbimport( 'cb.html' );
	cbimport('language.front');
	$_CB_framework->cbset( '_ui', 2 );
	// load plugins
	$_PLUGINS->loadPluginGroup('user');
	/* end of setup*/
	/* get parameters and defaults
	 * set a flag for joomla 1.6
	 */
	$jver16=FALSE;
	$versionstuff=new JVersion;
	$thisversion=$versionstuff->getShortVersion();
	if((strcasecmp( substr($thisversion,0,3), '1.6' ) >= 0)){
		$jver16=TRUE;
	}
	/* we have to check the CB version if we are in Joomla 1.6
	 * it must be 1.4 or higher
	 */
	$cb_version=$ueConfig['version'];
	$cb_version_short=substr($cb_version,0,3);
	echo "CB Version =$cb_version_short <br>";
	if($jver16 and $cb_version_short<'1.4'){
		echo "<h1><font color='red'>".JText::_('CBJ_WRONGCB')."</font></h1><br>";
		return FALSE;
	}
	$delimiter=cbGetParam($_REQUEST,'delimiter',null);
	if($delimiter=='TAB'||$delimiter=='tab'){$delimiter='\t';}
	//now do a hard fail if there is a null delimiter
	if (strlen($delimiter)<=0){
		// now issue warning messages and exit.
		echo JText::_('CBJ_WARNING1')."<br>";
		echo JText::_('CBJ_WARNING2')."<br>";
		return false;
	}
	$default_domain=cbGetParam($_REQUEST,'defaultemaildomain','invalid.com');
	$addmode=TRUE;  //add mode determines whether we are going to allow edits of pre-existing records;
	if($cbj2_opptype==2)$addmode=FALSE;
	$key_on_username=TRUE;  //this determines whether email or username may be updated
	if(cbGetParam($_REQUEST,'keytype',null)==2)$key_on_username=FALSE;
	$auditlist=TRUE;  //produce audit trail
	if(!cbGetParam($_REQUEST,'auditlist',0)==1)$auditlist=FALSE;
	/*The set confirmation flag trys to get the conformation message out
	 * This requires that the settings in comprofiler and joomla require confirmation
	 */
	$set_confirmation_flag=FALSE;
	if(cbGetParam($_REQUEST,'confirmed',0)==1)$set_confirmation_flag=TRUE;
	// echo "Confirmation Flag=$set_confirmation_flag <br>";
	$confirmation_email=FALSE;
	if(cbGetParam($_REQUEST,'ConfirmationEmail',0)==1)$confirmation_email=TRUE;
	$confirmation_sender=cbGetParam($_REQUEST, 'cbjuice_ConfirmationSender');
	$confirmation_subject=cbGetParam($_REQUEST,'cbjuice_ConfirmationSubject');
	$confirmation_bcc=cbGetParam($_REQUEST, 'BccEmail');
	if($confirmation_bcc){
		$bcc_email=$confirmation_sender;
	} else {
		$bcc_email=NULL;
	}
	$confirmation_body=cbGetParam($_REQUEST, 'cbjuice_message');
	$no_password_hash=FALSE;
	if(cbGetParam($_REQUEST,'hashedpwinput')==1){
		$no_password_hash=TRUE;
	}
	//	echo "No password hash=$no_password_hash <br>";
	$Records_Imported=0;  // initialize count of Records Read;
	$Records_Used=0;  // initialize count of records that were processed successfully
	/* The next section does the analysis of the header variables in the csv file
	 * The file must have the key field and one other field
	 */
	$csv_filehandle=fopen($filename,'r');
	//  it should not happen because of prechecks but just in case
	if($csv_filehandle==0){
		echo JText::_('CBJ_UNREADABLEFILE')."<br>";
		return false;
	}
	$headervariables=fgetcsv($csv_filehandle,4096,$delimiter,'"');
	$numbheader=count($headervariables);
	// Because people are dumb, we are going to trim the header variables
	$i=0;
	while($i<$numbheader){
		$headervariables[$i]=trim($headervariables[$i]);
		$i++;
	}
	echo JText::_('CBJ_HEADERVBLS')."<BR>";
	print_r($headervariables);
	echo "<BR>";
	/* Duplicate header variables are not allowed
	 *
	 */
	$dup_count_header_vbls=array_count_values($headervariables);
	if(count($dup_count_header_vbls)<>count($headervariables)){
		echo JText::_('CBJ_DUPLICATEVBLS')."<br>";
		print_r($dup_count_header_vbls);
		return FALSE;
	}
	/* Get pointers to the key variables in the header array
	 * This is used to check their existance and to get to them
	 */
	$username_pointer=Cbjuice2Helper::cbj2_find_pointer('username',$headervariables);
	$usertype_pointer=Cbjuice2Helper::cbj2_find_pointer('usertype',$headervariables);
	$email_pointer=Cbjuice2Helper::cbj2_find_pointer('email',$headervariables);
	$password_pointer=Cbjuice2Helper::cbj2_find_pointer('password',$headervariables);
	$middlename_pointer=Cbjuice2Helper::cbj2_find_pointer('middlename',$headervariables);
	$firstname_pointer=Cbjuice2Helper::cbj2_find_pointer('firstname',$headervariables);
	$lastname_pointer=Cbjuice2Helper::cbj2_find_pointer('lastname',$headervariables);
	/* now we have to get the field names for the two key tables to be managed, user and comprofiler
	 * we will establish an exclusion list of field names that cannot touched
	 *
	 */
	$full_field_names=$_CB_database->getTableFields(array('#__users','#__comprofiler'),TRUE);
	$users_fields=$full_field_names['#__users'];
	$comprofiler_fields=$full_field_names['#__comprofiler'];
	//$test=array_key_exists('username',$users_fields);
	/* Now we have to create a pointer array for the header variables that are in comprofiler and users
	 * We test against an array of variables that processed separately using the comprofiler classes
	 * These pointer arrays are used to slam the field values after the basic records are created with comprofiler
	 */
	$this_header_pointer=0;
	$users_pointers=array();
	$comprofiler_pointers=array();
	$comprofiler_names=array();
	$users_names=array();
	$bad_variables=FALSE;
	/* The never update list is for variables that can ever be updated,
	 *
	 */
	$never_update=array("id","user_id","gid");
	/* the excluded variables list removes variables that are processed outside of the field bash routines
	 *
	 */
	/* Define a counter for the key fields defined in the excluded list below.  If there is not more than 1,
	 * there is no requirement to call the update routine
	 */
	$key_vbl_counter=0;
	$excluded_variables=array('username','email','password','usertype');
	$no_update_list=array_merge($never_update,$excluded_variables);
	foreach ($headervariables as $this_header_variable){
		if(in_array($this_header_variable,$excluded_variables)){
			$key_vbl_counter++;
		}
		if(!in_array($this_header_variable,$no_update_list)){
			$in_users=array_key_exists($this_header_variable,$users_fields);
			if($in_users){
				$users_pointers[]=$this_header_pointer;
				$users_names[]=$this_header_variable;
			};
			$in_comprofiler=array_key_exists($this_header_variable,$comprofiler_fields);
			if($in_comprofiler){
				$comprofiler_pointers[]=$this_header_pointer;
				$comprofiler_names[]=$this_header_variable;
			}
			if((!$in_users)and (!$in_comprofiler)){
				echo "<br> $this_header_variable is not valid<br>";
				$bad_variables=True;
			}
		}
		$this_header_pointer++;
	}
	if($bad_variables){
		echo "<br>".JText::_('CBJ_UNDEFHEADERVBLS')."<br>";
		return False;
	}
	/*set up counts of the number of fields to update in each target table
	 * we don't want to call a table update routine if we have nothing to do
	 */
	$numb_users_fields=count($users_names);
	$numb_comprofiler_fields=count($comprofiler_names);
	/* we are now ready to go
	 * we are in a while loop
	 */
	$Records_Imported=0;
	while ($each_user_record=fgetcsv($csv_filehandle,4096,$delimiter,'"')){
		/*first we clean the data points adding slashes as required
		 *
		 */
		$Records_Imported++;

		$i=0;
		foreach($each_user_record as $each_user_cell){
			$each_user_record[$i]=addcslashes($each_user_cell, "'");
			$i++;
		}
		//		print_r($each_user_record);
		//		echo "<br>";
		/*Now we get the key variables
		 * We set them to NULL so that they are not replaced using the comprofiler stuff
		 */
		$defaulted_email=FALSE;
		if($addmode){
			$this_usertype=Cbjuice2Helper::cbj2_default_fields($usertype_pointer,$each_user_record,NULL);
			//		echo "user type is",$this_usertype,"<br>";
			$this_firstname=Cbjuice2Helper::cbj2_default_fields($firstname_pointer,$each_user_record,NULL);
			$this_middlename=Cbjuice2Helper::cbj2_default_fields($middlename_pointer, $each_user_record,NULL);
			$this_lastname=Cbjuice2Helper::cbj2_default_fields($lastname_pointer, $each_user_record,NULL);
			if($this_lastname==NULL){
				$this_lastname='user'.cbj2_RandomPassword(10);
			}
			//		echo "Username Pointer $username_pointer <br>";
			$this_password=Cbjuice2Helper::cbj2_default_fields($password_pointer,$each_user_record,cbj2_RandomPassword(10));
			$this_username=Cbjuice2Helper::cbj2_default_fields($username_pointer, $each_user_record,$this_lastname.cbj2_RandomPassword(6));
			$this_email=Cbjuice2Helper::cbj2_default_fields($email_pointer, $each_user_record,NULL);
			if($this_email==NULL){
				$defaulted_email=TRUE;
				$this_email=$this_lastname.cbj2_randompassword(8).'@'.$default_domain;
			}

		} else {
			/* these are the defaults for edit mode
			 *
			 */
			$this_usertype=Cbjuice2Helper::cbj2_default_fields($usertype_pointer,$each_user_record,NULL);
			$this_firstname=Cbjuice2Helper::cbj2_default_fields($firstname_pointer,$each_user_record,NULL);
			$this_middlename=Cbjuice2Helper::cbj2_default_fields($middlename_pointer, $each_user_record,NULL);
			$this_lastname=Cbjuice2Helper::cbj2_default_fields($lastname_pointer, $each_user_record,NULL);
			$this_password=Cbjuice2Helper::cbj2_default_fields($password_pointer,$each_user_record,NULL);
			$this_username=Cbjuice2Helper::cbj2_default_fields($username_pointer, $each_user_record,NULL);
			$this_email=Cbjuice2Helper::cbj2_default_fields($email_pointer, $each_user_record,NULL);
			//		echo "Prior to edit keys are $this_username $this_email <br>";
			//		echo "Pointers are  user=$username_pointer email=$email_pointer <br>";
		}
		/* Name field is handled in add/edit

		/* trim the name fields and email
		*
		*/
		$this_firstname=trim($this_firstname);
		$this_middlename=trim($this_middlename);
		$this_lastname=trim($this_lastname);
		$this_username=trim($this_username);
		$this_email=trim($this_email);

		/* Sort out confirmation status
		 *
		 */
		$confirmation_status=1;
		if($set_confirmation_flag)$confirmation_status=0;
		// echo "Confirmation status =$confirmation_status <br>";
		$approval_status=0;
		/* now we do the basic checks to see in add mode if the user name and email exists
		 * and the reverse in edit mode
		 *
		 */
		$user_failed=FALSE;
		/* we might as well do the check for passwords right up at the top
		 * if no_password_hash  is true then the passwords must be hashed
		 * at this point $this_password msut be defined
		 */
		if(!($password_pointer===FALSE)){
			$hashedpwd=Cbjuice2Helper::cbj2_checkforhash($this_password);
			//			if($hashedpwd){
			//				echo "$this_password is hashed<br>";
			//			}else{
			//				echo "$this_password is not hashed<br>";
			//			}
			//			echo "Password check $this_password $hashedpwd <br>";
			if($hashedpwd==TRUE and $no_password_hash==FALSE){
				echo $this_username." ".$this_email." ".JText::_('CBJ_PWDHASHED')."<br>";
				$user_failed=TRUE;
			}elseif($hashedpwd==FALSE and $no_password_hash==TRUE ){
				echo $this_username." ".$this_email." ".JText::_('CBJ_PWDNOTHASHED')."<br>";
				$user_failed=TRUE;
			}
		}
		/* first we will check the user type
		 * there are issues if it is a compound user type
		 *
		 */
		$compound_usertype=FALSE;
		$usertype_groupids=array();
		$usertype_array=array();
		if(!($usertype_pointer===FALSE)){
			$usertype_array=array($this_usertype);
		}
		if($usertype_pointer>=0 and $this_usertype!=""){
			$this_usertype=trim($this_usertype);
			if(stripos($this_usertype,"|")>0 or $jver16){
				// This is a compound string
				$dummy_user=new moscomprofilerUser($_CB_database);
				$usertype_array=array_unique(explode("|",$this_usertype));  //uniqueness is a good idea
				$compound_usertype=TRUE;
				if(!$jver16){
					$user_failed=TRUE;
					echo JTEXT::_('CBJ_NOCOMPOUND')."<br>";
				}
				foreach($usertype_array as $each_usertype){
					$test_gid=$_CB_framework->acl->get_group_id($each_usertype,'ARO');
					if($test_gid<=0){
						$user_failed=TRUE;
						echo JText::_('CBJ_INVALIDUSERTYPE')." $this_username $this_email $this_usertype <br>";
					} else{
						$usertype_groupids[]=$test_gid;
					}
				}

				$usertype_groupids=array_unique($usertype_groupids);  // need uniqueness

			} else {
				$dummy_user=new moscomprofilerUser($_CB_database);
				$dummy_user->usertype=$this_usertype;
				//		echo "testing $this_usertype <br>";
				$dummy_user->gid=$_CB_framework->acl->get_group_id( $dummy_user->usertype, 'ARO' );
				$test_gid=$dummy_user->gid;
				if($test_gid<=0){
					$user_failed=TRUE;
					echo JText::_('CBJ_INVALIDUSERTYPE')." $this_username $this_email $this_usertype <br>";
				} else {
					$usertype_groupids=array($test_gid);
					$usertype_array=array($this_usertype);
				}
			}
		}
		if($jver16 and $usertype_pointer>0)$compound_usertype=TRUE;  // we only need to worry about this if usertypes defined.
		$this_user     = new moscomprofilerUser( $_CB_database );
		if($addmode){
			$dummy_user=new moscomprofilerUser($_CB_database);
			$result=$dummy_user->loadbyUsername($this_username);
			if($result){
				echo $this_username." ",JTEXT::_('CBJ_NOTUNIQUE') ,$dummy_user->lastname, "<br>";
				$user_failed=TRUE;
			}
			$dummy_user=new moscomprofilerUser($_CB_database);
			$result=$dummy_user->loadByEmail($this_email);
			if($result){
				echo $this_email." ".JTEXT::_('CBJ_NOTUNIQUE')."<BR>";
				$user_failed=TRUE;
			}
			if(!$user_failed){
				/* now we do the add
				 *
				 */
				$_PLUGINS->trigger( 'onBeforeNewUser', array( &$this_user, &$this_user, false ) );
				$result1=registerUser($this_user, $this_username, $this_firstname, $this_middlename, $this_lastname, $this_email,
				$this_password,$approval_status,$confirmation_status,
				$usertype_groupids,$usertype_array,$no_password_hash);
				$target_user_id=$this_user->id;
				$result2=TRUE;
				$result=$result1 and $result2;

				if(!$result){
					echo "$this_username $this_firstname $this_lastname Email: $this_email Password: $this_password could not be added <br>";
					$user_failed=TRUE;
				}else {
					if($auditlist){
						echo Jtext::_('CBJ_ADDING')." ".$this_user->username." ".$this_user->firstname." ".$this_user->lastname."<br>";
					}
					/*
					 * Now we will try to generate an activation message
					 * using code from kyle
					 */
					if($confirmation_status==1){
						/*now we have to generate the message
						 *
						 */

						$user_interface=$_CB_framework->getUi();
						//                        echo "confirmation is called <br>";
						$activate_result=activateUser($this_user,$user_interface, 'NewUser',FALSE);
						//						print_r($activate_result);
						//						echo "<br>";

					}

					$Records_Used++;
				}
			}
		} else {
			if($key_on_username and $this_username==NULL){
				$user_failed=TRUE;
			}  //if we are editing on username and it is missing bad
			if((!$key_on_username) and $this_email==NULL){
				$user_failed=TRUE;
			}  //
			//echo "edit1 $this_username $this_email $key_on_username $user_failed <br>";
			if(!$user_failed){
				$this_user=new moscomprofilerUser($_CB_database);
				if($key_on_username){
					$result=$this_user->loadByUsername($this_username);
					if(!$result){
						echo $this_username.JTEXT::_('CBJ_NOTFOUND')."<br>";
						$user_failed=TRUE;
					}
				} else {
					$result=$this_user->loadByEmail($this_email);
					if(!$result){
						echo $this_mail.JTEXT::_('CBJ_NOTFOUND')."<br>";
						$user_failed=TRUE;
					}
				}
				if($this_user->user_id==$_CB_framework->myId()){
					Echo JTEXT::_('CBJ_EDITYOURSELF')."<br>";
					$user_failed=TRUE;
				}
				if(!$user_failed){
					// save the current user
					$oldUserComplete= new moscomprofilerUser( $_CB_database );
					$oldUserComplete->load($this_user->user_id);
					$_PLUGINS->trigger( 'onBeforeUpdateUser', array( &$user, &$user, &$oldUserComplete ) );
					if($key_vbl_counter>1){
						$result1=updateUser($this_user, $this_username, $this_firstname, $this_middlename, $this_lastname, $this_email,
						$this_password, $usertype_groupids,$usertype_array,$no_password_hash);
						$target_user_id=$this_user->id;
						$result2=TRUE;
						$result=$result1;
						$result=$result1 and $result2;
						/* now we are going to test things.
						 *
						 */
							
					} else {
						$result=true;  // setting this to true causes the audit list below to show.
					}

					if(!$result){
						echo "$this_username $this_firstname $this_lastname could not be edited <br>";
						$user_failed=TRUE;
					}else {
						if($auditlist){
							echo Jtext::_('CBJ_UPDATING')." ".$this_user->username." ".$this_user->firstname." ".$this_user->lastname."<br>";
						}

						$Records_Used++;
					}
				}
			}

		}
		if(!$user_failed){
			/* now we update the records*/
			//		if($auditlist){
			//			echo "Updating material for $this_username $this_firstname $this_lastname $this_email $this_password <br>";
			//		}
			$target_user_id=$this_user->user_id;
			$target_comprofiler_id=$this_user->id;
			$this_gid=$this_user->gid;
			$current_usertype=$this_user->usertype;
			$current_username=$this_user->username;
			// save the current user
//			$oldUserComplete= new moscomprofilerUser( $_CB_database );
//			$oldUserComplete->load($this_user->user_id);
//			$_PLUGINS->trigger( 'onBeforeUpdateUser', array( &$this_user, &$this_user, &$oldUserComplete ) );
			if($numb_users_fields>0){
				/* now we can update the fields for the user table
				 *
				 */
				$target_user_id=$this_user->user_id;
				$target_comprofiler_id=$this_user->user_id;
				//			echo "Updating user fields $this_username $this_email userid: $target_user_id comprofiler id:$target_comprofiler_id<br>";
				if($this_username==NULL){
					$this_username=$this_user->username;
				}
				if($this_email==NULL){
					$this_email=$this_user->email;
				}
				$result=update_table_fields('#__users',$target_user_id,$users_names,$users_pointers,$each_user_record);
				if(!$result){
					echo Jtext::_('CBJ_ERRORUSERTABLE')." $this_username $this_email <b>";

				}
			}
			if($numb_comprofiler_fields>0){
				/* now we can update the fields for the user table
				 *
				 */
				$target_user_id=$this_user->user_id;
				$target_comprofiler_id=$this_user->user_id;

				if($this_username==NULL){
					$this_username=$this_user->username;
				}
				if($this_email==NULL){
					$this_email=$this_user->email;
				}
				$result=update_table_fields('#__comprofiler',$target_comprofiler_id,$comprofiler_names,$comprofiler_pointers,$each_user_record);
				if(!$result){
					echo Jtext::_('CBJ_ERRORCOMPROFILERTABLE')." $this_username $this_email <b>";
				}
			}

			if($addmode){
				$updated_user     = new moscomprofilerUser( $_CB_database );
				$updated_user->load($this_user->user_id);
				$_PLUGINS->trigger( 'onAfterNewUser', array( &$updated_user, &$updated_user, false, true ) );
			}else{
				//* now we have reload the user to generate the updates
				$updated_user= new moscomprofilerUser( $_CB_database );
				$updated_user->load($this_user->user_id);
				//* now do the trigger
				$_PLUGINS->trigger( 'onAfterUpdateUser', array( &$updated_user, &$updated_user, $oldUserComplete ) );
			}

			//* at this point we can generate the email.
			if($confirmation_email){
				/* use a function to send the mail
				 *
				 */
				$send_to_id=$this_user->user_id;
				$recipient=$this_user->email;
				$subject=$confirmation_subject;
				$body=$confirmation_body;
				$from=$confirmation_sender;
				$fromname=$_CB_framework->getCfg( 'sitename' );
				if(strlen($this_password)>3){
					echo Jtext::_('CBJ_PASSWORDIS')." $this_password <br>";
				}
				if((!$no_password_hash) and strlen($this_password)>3){
					$body=cbstr_ireplace("[CBJPASSWORD]", $this_password, $body);
				} else {
					$body=cbstr_ireplace("[CBJPASSWORD]", "", $body);
				}
				$this_cbuser=CBUser::getInstance($send_to_id);
				$body = cbstr_ireplace("[EMAILADDRESS]", $row->email, $body);
				$body = cbstr_ireplace("[SITEURL]", $_CB_framework->getCfg( 'live_site' ), $body);
				$body=$this_cbuser->replaceUserVars($body);
				//			echo $body."<br>";
				if(!$bcc_email==NULL){
					$bcc=$bcc_email;
				} else {
					$bcc=NULL;
				}
				$result=comprofilerMail($from, $fromname, $recipient, $subject, $body,0,null,$bcc);
				if(!$result){
					echo Jtext::_('CBJ_NOMESSAGESENT')." $recipient <br>";
				}
					
			}
		}
	}
	echo Jtext::_('CBJ_RECORDCOUNT1') .$Records_Imported."<br>";
	echo Jtext::_('CBJ_RECORDCOUNT2') .$Records_Used."<br>";
	return TRUE;
}
/* Support Functions
 * used internally
 *
 */

/* Password Generation Rountine
 *
 */
function cbj2_RandomPassword($PwdLength=8, $PwdType='standard'){
	// $PwdType can be one of these:
	//    test .. .. .. always returns the same password = "test"
	//    any  .. .. .. returns a random password, which can contain strange characters
	//    alphanum . .. returns a random password containing alphanumerics only
	//    standard . .. same as alphanum, but not including l10O (lower L, one, zero, upper O)
	//
	/* this function was found in the following reference
	http://wiki.jumba.com.au/wiki/PHP_Generate_random_password
	Code contributed by Thewebdruid, for the Jumba Wiki
	*/

	$Ranges='';

	if('test'==$PwdType)         return 'test';
	elseif('standard'==$PwdType) $Ranges='65-78,80-90,97-107,109-122,50-57';
	elseif('alphanum'==$PwdType) $Ranges='65-90,97-122,48-57';
	elseif('any'==$PwdType)      $Ranges='40-59,61-91,93-126';

	if($Ranges<>'')
	{
		$Range=explode(',',$Ranges);
		$NumRanges=count($Range);
		mt_srand(); //not required after PHP v4.2.0
		$p='';
		for ($i = 1; $i <= $PwdLength; $i++)
		{
			$r=mt_rand(0,$NumRanges-1);
			list($min,$max)=explode('-',$Range[$r]);
			$p.=chr(mt_rand($min,$max));
		}
		return $p;
	}
}
/* Register User - function to register users
 * $approval and $confirmation should normally be set to zero
 */
function registerUser($user, $username,$firstname,$middlename,$lastname,$email,$password,$approval,$confirmation,
$usertype_groupids,$usertype_array,$dont_hash=FALSE) {
	global $_CB_framework, $_CB_database, $ueConfig;
	global $_PLUGINS;

	$new_usertype=$_CB_framework->getCfg( 'new_usertype' );
	//	echo "type is $new_usertype <br>";
	if($new_usertype==""){
		$new_usertype="Registered";
	}
	$test_gid=0;
	/* we have already tested the gids so the issue is only if they are not supplied
	 *
	 */
	if(count($usertype_groupids)<=0){
		$usertype_groupids[]=$_CB_framework->acl->get_group_id( $new_usertype, 'ARO' );
		$usertype_array[]=$new_usertype;
	}
	if(checkJVersion()<2){
		$user->gid    = $usertype_groupids[0];
		$user->usertype=$usertype_array[0];
	} else {
		$user->gids=$usertype_groupids;
		$user->gid=$usertype_groupids[0];
		$user->usertype=$usertype_array[0];
		//		echo "setting gids<br>";
		//		print_r($usertype_groupids);
		//		echo "<br>";
	}
	$user->sendEmail  = 0;
	$user->registerDate  = date( 'Y-m-d H:i:S', $_CB_framework->now() );
	$user->username   =$username;
	$user->firstname  =$firstname;
	$user->middlename  =$middlename;
	$user->lastname   = $lastname;
	if(!empty($middlename)){
		$user->name=$firstname." ".$middlename." ".$lastname;
	} else{
		$user->name    = $firstname." ".$lastname;
	}
	$user->email   = $email;
	if($dont_hash){
		$user->password=$password;
	} else {
		$user->password   = $user->hashAndSaltPassword($password);
	}
	/*$user->avatar   = _AVATAR_HERE;*/
	$user->registeripaddr = cbGetIPlist();

	if ( $approval == 0 ) {
		$user->approved  = 1;
	} else {
		$user->approved  = 0;
	}

	if ( $confirmation == 0 ) {
		$user->confirmed = 1;
	} else {
		$user->confirmed = 0;
	}

	if ( ( $user->confirmed == 1 ) && ( $user->approved == 1 ) ) {
		$user->block  = 0;
	} else {
		$user->block  = 1;
	}
	if(!isset($user->id)){
		$user->id=0;
	}
	//	$_PLUGINS->trigger( 'onBeforeNewUser', array( &$user, &$user, false ) );
	if ( $user->store() ) {
		if ( ( $user->confirmed == 0 ) && ( $confirmation != 0 ) ) {
			$user->_setActivationCode();

			if ( ! $user->store() ) {
				echo "Error in register store: ",$user->getError(),"<br>";
				return false;
			}
		}
		//		$_PLUGINS->trigger( 'onAfterNewUser', array( &$user, &$user, false, true ) );
		return true;
	}
	echo "Error in register store: ",$user->getError(),"<br>";
	return false;
}
/* Update User manages the update of the key identification and type fields
 *
 */
function updateUser($user, $username,$firstname,$middlename,$lastname,$email,$password,$usertype_groupids=array(),
$usertype_array=array(),$dont_hash=FALSE){
	/*This function updates the key user fields
	 *
	 */
	global $_CB_framework, $_CB_database, $ueConfig;
	global $_PLUGINS;
	/* we need to make sure that the cms object is consistent
	 *
	 */
	$user->_cmsUser	=	$_CB_framework->_getCmsUserObject($user->id);
	$oldUserComplete= new moscomprofilerUser( $_CB_database );
	$oldUserComplete=$user;
	$name_being_edited=$user->username;
	//	echo "Trying to update", $name_being_edited," <br>";
	/* we have already tested the gids so the issue is only if they are not supplied
	 *
	 */
	if(count($usertype_groupids)>=1){
		if(checkJversion()<2){
			$user->gid    = $usertype_groupids[0];
			$user->usertype=$usertype_array[0];
		} else {
			$these_gids=array_combine($usertype_array,$usertype_groupids);
			$these_usertypes=array_combine($usertype_groupids,$usertype_array);
			$max_gid=max($usertype_groupids);
			$user->gid=$max_gid;
			//		echo "updating gids<br>";
			//		print_r($usertype_groupids);
			//		echo "<br>";
			$user->gids=$usertype_groupids;
			$user->usertype=$these_usertypes[$max_gid];
		}
	}
	$user->lastupdatedate  = date( 'Y-m-d H:i:S', $_CB_framework->now() );
	if(!$username==NULL){
		$user->username   =$username;
	}
	if(!$firstname==NULL){
		$user->firstname  =$firstname;
	}
	if(!$middlename==NULL){
		$user->middlename  =$middlename;
	}
	if(!$lastname==NULL){
		$user->lastname   = $lastname;
	}
	if(!$firstname==NULL or !$lastname==NULL){
		if(!empty($middlename)){
			$user->name=$firstname." ".$middlename." ".$lastname;
		} else{
			$user->name    = $firstname." ".$lastname;
		}
	}
	//	echo "Name in update=",$user->name,"<br>";
	if(!$email==NULL){
		$user->email   = $email;
	}
	if(!$password==null){
		if($dont_hash){
			//			echo "we are not hashing the password<cr>";
			$user->password=$password;
		} else {
			$user->password   = $user->hashAndSaltPassword($password);
		}}
		$save_params=$user->params;
		//		$_PLUGINS->trigger( 'onBeforeUpdateUser', array( &$user, &$user, &$oldUserComplete ) );
		if ( ! $user->store() ) {
			echo "Error in update store: ",$user->getError(),"<br>";
			return FALSE;
		} else {
			/* now we have to patch the params
			 * because of a bug in joomla
			 */
			//			$query="UPDATE `#__users` SET `params` = '$save_params' WHERE `id` = ".$user->id;
			//			$_CB_database->setQuery($query);
			//			$result=$_CB_database->query();
			//			if(!$result){
			//				echo "Error updating the user table serious error<br>";
			//				echo $_CB_database->getErrorMSG()."<br>";
			//				return FALSE;
			//			}
			//			$_PLUGINS->trigger( 'onAfterUpdateUser', array( &$user, &$user, $oldUserComplete ) );
			return TRUE;
		}
		return FALSE;
}
/* function to update table fields
 * This takes a generic list of names and value
 *
 */
function update_table_fields($table_name,$target_id,$vbl_names,$vbl_pointers,$each_user_record){
	/*
	 *
	 */
	/*start by building the query
	 */
	Global $_CB_framework, $_CB_database;
	$query1="UPDATE ".$table_name." SET ";
	$query2="";
	$query3=" WHERE `id`  = '".$target_id."'";
	$counter=0;
	foreach($vbl_names as $this_field_name){
		if($counter>=1){
			$query2=$query2." , ";
		}
		$value=$each_user_record[$vbl_pointers[$counter]];
		//$value=stripcslashes($value);
		$query2=$query2."`".$this_field_name."` = '".$value."'";
		$counter++;
	}
	$query=$query1.$query2.$query3;
	// 	echo "Trying query = $query <br>";
	$_CB_database->setQuery($query);
	$result=$_CB_database->query();
	if(!$result){
		echo "Error updating the $table_name table serious error<br>";
		echo $_CB_database->getErrorMSG()."<br>";
	}
	return $result;

}

?>