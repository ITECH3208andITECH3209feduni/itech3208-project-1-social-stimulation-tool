<?php
/**
 * Joomla! 1.5 component CBJUICE2
 *
 * @version $Id: controller.php 2010-12-25 12:20:17 svn $
 * @author Paul Jacobson and Stephen Thompson
 * @copyright Paul Jacobson and Stephen Thompson
 * @package Joomla
 * @subpackage CBJUICE2
 * @license GNU/GPL
 *
 * This component manages users using a CSV file
 *
 */
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
function cbj2_delete_users($filename){
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
	//	echo "Starting to delete<br>";
	cbimport( 'cb.database' );
	cbimport( 'cb.html' );
	$delimiter=cbGetParam($_REQUEST,'delimiter',null);
	if($delimiter=='TAB'||$delimiter=='tab'){$delimiter='\t';}
	if (strlen($delimiter)<=0){
		// now issue warning messages and exit.
		echo "<b><H1>".JText::_('CBJ_WARNING1'). "</H1></b><br>",
		"<b><h2>".JTEXT::_('CBJ_WARNING2')."</h2></b><br><br><br>";
		return false;
	}
	$key_on_username=TRUE;  //this determines whether email or username may be updated
	if(cbGetParam($_REQUEST,'keytype',null)==2)$key_on_username=FALSE;
	$auditlist=TRUE;  //produce audit trail
	if(!cbGetParam($_REQUEST,'auditlist',0)==1)$auditlist=FALSE;
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
	echo JText::_('CBJ_HEADERVBLS')."<br>";
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
	$email_pointer=Cbjuice2Helper::cbj2_find_pointer('email',$headervariables);
	while ($each_user_record=fgetcsv($csv_filehandle,4096,$delimiter,'"')){
		/*first we clean the data points adding slashes as required
		 *
		 */
		$Records_Imported++;
		$i=0;
		$user_failed=FALSE;
		$this_username=Cbjuice2Helper::cbj2_default_fields($username_pointer, $each_user_record,NULL);
		$this_email=Cbjuice2Helper::cbj2_default_fields($email_pointer, $each_user_record,NULL);
		$this_username=trim($this_username);
		$this_email=trim($this_email);
		//		echo "Prior to delete keys are $this_username $this_email <br>";
		foreach($each_user_record as $each_user_cell){
			$each_user_record[$i]=addcslashes($each_user_cell, "'");
			$i++;
		}
		$this_user     = new moscomprofilerUser( $_CB_database );
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
					echo $this_mail.JTEXT::_('CBJ_NOTFOUND')."<br>";
					$user_failed=TRUE;
				}
			} else {
				$result=$this_user->loadByEmail($this_email);
				if(!$result){
					echo "$this_email <br>";
					$user_failed=TRUE;
				}
			}
			if($this_user->user_id==$_CB_framework->myId()){
				Echo JTEXT::_('CBJ_EDITYOURSELF')."<br>";
				$user_failed=TRUE;
			}
			/* at this point, this user should be successfully retrieved if we can delete it
			 * if we are going to issue a message we do it first while the user is defined.
			 *
			 */
			if(!$user_failed){
				$Records_Used++;
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
					if(!$no_password_hash and $password_pointer>=0 and strlen($this_password)>3){
						$body=cbstr_ireplace('[cbjpassword]', $this_password, $body);
					}
					$this_cbuser=CBUser::getInstance($send_to_id);
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
				/* how we can do the delete
				 *
				 */
				$result=$this_user->delete();
				if(!result){
					echo Jtext::_('CBJ_NODELETE')." $this_user->username <br>";
					$user_failed=true;
				} elseif($auditlist) {
					echo Jtext::_('CBJ_DELETING')." $this_username, $this_email <br>";
				}
			} else {
				echo Jtext::_('CBJ_NODELETE')." $this_username $this_email <br>";
			}
		}
	}
	echo Jtext::_('CBJ_RECORDCOUNT1') .$Records_Imported."<br>";
	echo Jtext::_('CBJ_RECORDCOUNT2') .$Records_Used."<br>";
	return TRUE;
}
?>