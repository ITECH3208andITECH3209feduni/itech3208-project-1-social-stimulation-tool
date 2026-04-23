<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Plugin\Core\Field\JoomlaField;
use CBLib\Core\AutoLoader;
use CB\Plugin\Core\Field\AudioField;
use CB\Plugin\Core\Field\CheckboxField;
use CB\Plugin\Core\Field\ColorField;
use CB\Plugin\Core\Field\ConnectionsField;
use CB\Plugin\Core\Field\CounterField;
use CB\Plugin\Core\Field\DateField;
use CB\Plugin\Core\Field\DateTimeField;
use CB\Plugin\Core\Field\EditorField;
use CB\Plugin\Core\Field\EmailField;
use CB\Plugin\Core\Field\FileField;
use CB\Plugin\Core\Field\FormatNameField;
use CB\Plugin\Core\Field\HtmlField;
use CB\Plugin\Core\Field\ImageField;
use CB\Plugin\Core\Field\PmField;
use CB\Plugin\Core\Field\PointsField;
use CB\Plugin\Core\Field\RatingField;
use CB\Plugin\Core\Field\StatusField;
use CB\Plugin\Core\Field\TermsField;
use CB\Plugin\Core\Field\TimeField;
use CB\Plugin\Core\Field\FloatField;
use CB\Plugin\Core\Field\IntegerField;
use CB\Plugin\Core\Field\MultiCheckboxField;
use CB\Plugin\Core\Field\MultiSelectField;
use CB\Plugin\Core\Field\PasswordField;
use CB\Plugin\Core\Field\PredefinedField;
use CB\Plugin\Core\Field\RadioField;
use CB\Plugin\Core\Field\SelectField;
use CB\Plugin\Core\Field\TagField;
use CB\Plugin\Core\Field\TextareaField;
use CB\Plugin\Core\Field\TextField;
use CB\Plugin\Core\Field\UserParamsField;
use CB\Plugin\Core\Field\VideoField;
use CB\Plugin\Core\Field\WebAddressField;
use CB\Plugin\Core\Tab\AvatarTab;
use CB\Plugin\Core\Tab\CanvasTab;
use CB\Plugin\Core\Tab\ContactTab;
use CB\Plugin\Core\Tab\StatsTab;
use CB\Plugin\Core\Tab\TitleTab;

defined( 'CBLIB' ) or die();

global $_PLUGINS;

AutoLoader::registerExactMap( '%^CB/Plugin/Core/(.+)%i', __DIR__ . '/library/$1.php' );

class_alias( TextField::class, 'CBfield_text' );
class_alias( TextareaField::class, 'CBfield_textarea' );
class_alias( PredefinedField::class, 'CBfield_predefined' );
class_alias( PasswordField::class, 'CBfield_password' );
class_alias( SelectField::class, 'CBfield_select_multi_radio' );
class_alias( CheckboxField::class, 'CBfield_checkbox' );
class_alias( IntegerField::class, 'CBfield_integer' );
class_alias( DateField::class, 'CBfield_date' );
class_alias( EditorField::class, 'CBfield_editorta' );
class_alias( EmailField::class, 'CBfield_email' );
class_alias( WebAddressField::class, 'CBfield_webaddress' );
class_alias( PmField::class, 'CBfield_pm' );
class_alias( ImageField::class, 'CBfield_image' );
class_alias( StatusField::class, 'CBfield_status' );
class_alias( CounterField::class, 'CBfield_counter' );
class_alias( ConnectionsField::class, 'CBfield_connections' );
class_alias( FormatNameField::class, 'CBfield_formatname' );
class_alias( HtmlField::class, 'CBfield_delimiter' );
class_alias( UserParamsField::class, 'CBfield_userparams' );
class_alias( FileField::class, 'CBfield_file' );
class_alias( VideoField::class, 'CBfield_video' );
class_alias( AudioField::class, 'CBfield_audio' );
class_alias( RatingField::class, 'CBfield_rating' );
class_alias( PointsField::class, 'CBfield_points' );
class_alias( TermsField::class, 'CBfield_terms' );
class_alias( ColorField::class, 'CBfield_color' );

class_alias( StatsTab::class, 'getStatsTab' );
class_alias( CanvasTab::class, 'getCanvasTab' );
class_alias( TitleTab::class, 'getPageTitleTab' );
class_alias( AvatarTab::class, 'getPortraitTab' );
class_alias( ContactTab::class, 'getContactTab' );

$_PLUGINS->registerFunction( 'onBeforeDeleteUser', 'onBeforeDeleteUser', ImageField::class );
$_PLUGINS->registerFunction( 'onBeforeDeleteUser', 'deleteFiles', FileField::class );
$_PLUGINS->registerFunction( 'onBeforeDeleteUser', 'deleteFiles', VideoField::class );
$_PLUGINS->registerFunction( 'onBeforeDeleteUser', 'deleteFiles', AudioField::class );

$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( [ 	'checkbox'				=>	CheckboxField::class,
										'multicheckbox'			=>	MultiCheckboxField::class,
										'date'					=>	DateField::class,
										'time'					=>	TimeField::class,
										'datetime'				=>	DateTimeField::class,
										'select'				=>	SelectField::class,
										'multiselect'			=>	MultiSelectField::class,
										'tag'					=>	TagField::class,
										'emailaddress'			=>	EmailField::class,
										'primaryemailaddress'	=>	EmailField::class,
										'editorta'				=>	EditorField::class,
										'textarea'				=>	TextareaField::class,
										'text'					=>	TextField::class,
										'integer'				=>	IntegerField::class,
										'float'					=>	FloatField::class,
										'radio'					=>	RadioField::class,
										'webaddress'			=>	WebAddressField::class,
										'pm'					=>	PmField::class,
										'image'					=>	ImageField::class,
										'status'				=>	StatusField::class,
										'formatname'			=>	FormatNameField::class,
										'predefined'			=>	PredefinedField::class,
										'counter'				=>	CounterField::class,
										'connections'			=>	ConnectionsField::class,
										'password'				=>	PasswordField::class,
										'hidden'				=>	TextField::class,
										'delimiter'				=>	HtmlField::class,
										'userparams'			=>	UserParamsField::class,
										'file'					=>	FileField::class,
										'video'					=>	VideoField::class,
										'audio'					=>	AudioField::class,
										'rating'				=>	RatingField::class,
										'points'				=>	PointsField::class,
										'terms'					=>	TermsField::class,
										'color'					=>	ColorField::class,
									]);

if ( checkJversion( '4.0+' ) ) {
$_PLUGINS->registerUserFieldTypes( [ 	'joomla'				=>	JoomlaField::class,
									]);
}

/**
 * Commented CBT calls for language parser pickup
 * CBTxt::T( '_UE_ADDITIONAL_INFO_HEADER', 'Additional Information' )
 * CBTxt::T( '_UE_Website', 'Web site' )
 * CBTxt::T( '_UE_Location', 'Location' )
 * CBTxt::T( '_UE_Occupation', 'Occupation' )
 * CBTxt::T( '_UE_Interests', 'Interests' )
 * CBTxt::T( '_UE_Company', 'Company' )
 * CBTxt::T( '_UE_City', 'City' )
 * CBTxt::T( '_UE_State', 'State' )
 * CBTxt::T( '_UE_ZipCode', 'Zip Code' )
 * CBTxt::T( '_UE_Country', 'Country' )
 * CBTxt::T( '_UE_Address', 'Address' )
 * CBTxt::T( '_UE_PHONE', 'Phone #' )
 * CBTxt::T( '_UE_FAX', 'Fax #' )
 */
