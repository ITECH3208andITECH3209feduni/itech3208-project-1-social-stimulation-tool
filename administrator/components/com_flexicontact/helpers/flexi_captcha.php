<?php
/********************************************************************
Product		: Flexicontact
Date		: 25 February 2023
Copyright	: Les Arbres Design 2010-2023
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class Flexi_captcha
{

// -------------------------------------------------------------------------------
// Display the image captcha
// Builds a structure containing information about the test and stores it in the session
// Returns the description of the target image
//
static function show_image_captcha($config_data, $error)
{
// initialise the captcha information structure

    $image_list = array();
	$app = JFactory::getApplication();
	$captcha_info = new stdClass();
	$captcha_info->num_images = $config_data->num_images;
	$captcha_info->images = array();
    $captcha_info->target = -1;
        
// get list of images in images directory

    if (!file_exists(LAFC_SITE_IMAGES_PATH))
        FC_trace::trace("Image captcha configured but image directory not present");
		
    $handle = @opendir(LAFC_SITE_IMAGES_PATH);
	if (!$handle)
        FC_trace::trace("Image captcha configured but unable to open image directory");
    else
		{
		while (($filename = @readdir($handle)) != false)
			{
			if ($filename == '.' or $filename == '..')
				continue;
			$imageInfo = @getimagesize(LAFC_SITE_IMAGES_PATH.'/'.$filename);
			if ($imageInfo === false)
				continue;				            // not an image
			if ($imageInfo[2] > IMAGETYPE_PNG)      // only support gif, jpg or png
				continue;
			if ($imageInfo[0] > 150)                // if X size > 150 pixels ..
				continue;                           // .. it's too big so skip it
			$image = array();
			$image['filename'] = $filename;
			$image['width'] = $imageInfo[0];
			$image['height'] = $imageInfo[1];
			$image['type'] = $imageInfo[2];
			$image_list[] = $image;
			}
		@closedir($handle);
		}
	$imageCount = count($image_list);
	if ($imageCount < $config_data->num_images)
        {
        FC_trace::trace('Not enough captcha images. Config: '.$config_data->num_images.' Found: '.$imageCount.' - image captcha disabled');
    	$app->setUserState(LAFC_COMPONENT."_captcha_info", $captcha_info);
		return '';
        }

// choose the images
	
	$i = 0;
	$randoms = array();
	while ($i < $config_data->num_images)
		{
		$imageNum = rand(0,$imageCount - 1);	// get a random number
		if (in_array($imageNum,$randoms))		// if already chosen
			continue;							// try again
		$randoms[] = $imageNum;					// add to random number array
		$i ++;									// got one more
		}

// build the captcha information structure

	for ($i = 0; $i < $config_data->num_images; $i++)
		{
		$j = $randoms[$i];						// index of the next chosen image
		$image = $image_list[$j];				// point to image info
		$captcha_info->images[$i] = $image;		// copy the image info array into the captcha_info structure
		}
		
// choose the target image and store it in the captcha_info structure
	
	$captcha_info->target = rand(0, $config_data->num_images - 1);
	$target_filename = $captcha_info->images[$captcha_info->target]['filename'];
	FC_trace::trace("Created captcha_info structure: ".print_r($captcha_info,true));	

// store the captcha_info structure in the session, and check it got stored correctly

	$app->setUserState(LAFC_COMPONENT."_captcha_info", $captcha_info);
    $captcha_info_check = $app->getUserState(LAFC_COMPONENT."_captcha_info",'');
    if ($captcha_info_check == '')
        {
    	FC_trace::trace("Captcha info session check failed");	
        return 'Cannot use image captcha because the Joomla session is not working correctly';
        }
	$retry_count = $app->getUserState(LAFC_COMPONENT."_captcha_retry_count",0);
    FC_trace::trace("Captcha retry_count = $retry_count");	
    
// get the installed themes and load the relevant language files   
    
    $theme_info_array = self::get_themes();
    self::load_language_files($theme_info_array);
        
// get the description of the target image and make the user prompt
	
	$target_text = JText::_('COM_FLEXICONTACT_IMAGE_'.strtoupper($target_filename));
	$select_image_text = JText::_('COM_FLEXICONTACT_SELECT_IMAGE');
	if (strstr($select_image_text,"%s"))
        $text = JText::sprintf('COM_FLEXICONTACT_SELECT_IMAGE',$target_text);
	else
		$text = JText::_('COM_FLEXICONTACT_SELECT_IMAGE').' '.$target_text;

// draw the chosen images
        
	$langObj = JFactory::getLanguage();
	$language = $langObj->get('tag');			// get the current site language
	$lang = substr($language,0,2);
	$html = '<div class="fc_line fc_images">';
	$html .= '<label class="fc_image_text">';
	$html .= '<span class="fc_image_desc">'.$text.'</span>';
	$html .= '</label>';
	$html .= '<div class="fc_image_inner">';
	foreach ($captcha_info->images as $index => $image)
		{
        $rand = mt_rand(1001,9999);
		if ($config_data->raw_images)
			$src = LAFC_SITE_IMAGES_URL.$image['filename'];
		else
			$src = JURI::root(true).'/index.php?option='.LAFC_COMPONENT.'&amp;tmpl=component&amp;format=raw&amp;lang='.$lang.'&amp;task=image&amp;n='.$index.'&amp;r='.$rand;
		$html .= '<img id="fci_'.$index.'" src="'.$src.'" width="'.$image['width'].'" height="'.$image['height'].'" 
			class="fc_imcap fc_inactive" alt="Captcha image '.$rand.'">';
		}
	$html .= '</div>';
    
// do we have an error message?

	if ($error != '')
		$html .= $error;
		
	$html .= '<input type="hidden" name="picselected" id="fc_picselected" value="">';
	$html .= '</div>';     // class="fc_line fc_images"
	return $html;
}

// -------------------------------------------------------------------------------
// Serve an image to the browser
//
static function show_image()
{
	$app = JFactory::getApplication();
	$captcha_info = $app->getUserState(LAFC_COMPONENT."_captcha_info",'');
	if ($captcha_info == NULL)
        {
       	FC_trace::trace("Unable to serve image because there is no session data");
        header('Warning: Session data not found');
        http_response_code(400);    // Bad request
		return;
        }        
	
	$jinput = JFactory::getApplication()->input;
	$image_number = $jinput->get('n', 0, 'INT');
	$filename = $captcha_info->images[$image_number]['filename'];
	$filepath = LAFC_SITE_IMAGES_PATH.'/'.$filename;
   	FC_trace::trace("Serving image $image_number = $filepath");	

// set the mime type

	switch ($captcha_info->images[$image_number]['type']) 
		{
		case 1:  
			$mimetype = 'image/gif';
			break;
		case 2:
			$mimetype = 'image/jpeg';
			break;
		case 3:
			$mimetype = 'image/png';
			break;
		default: return;
		}
		
	while (@ob_end_clean());
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s") . "GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header("Content-Type: $mimetype");
	echo readfile($filepath);
	echo self::random_data();
	exit;
}

// -------------------------------------------------------------------------------
// Make some random data to vary the image file length
//
static function random_data()
{
	$str = '';
	$length = rand(0,200);
	for ($i = 0; $i < $length; $i++)
		$str .= chr(rand(0,255));
	return $str;
}

// -------------------------------------------------------------------------------
// Check if the user picked the correct image
// returns 0 for yes, the user picked the correct image
//         1 for no, the user picked the wrong image
//         2 if the user picked the wrong image too many times
//
static function check($pic_selected, $num_images)
{
	$app = JFactory::getApplication();
	$captcha_info = $app->getUserState(LAFC_COMPONENT."_captcha_info",'');
	if ($captcha_info == '')
        {
    	FC_trace::trace("Flexi_captcha::check() session data not found so FAIL");
		return 1;
        }
    if ($captcha_info->target == -1)	// 12.03 this must come before the pic_selected check
        {
    	FC_trace::trace("Flexi_captcha::check() target = -1 (not enough images) so PASS");
		return 0;
        }
        
	if ($pic_selected == '')
		{
       	FC_trace::trace("Flexi_captcha::check() no picture selected so FAIL");
		return 1;
		}

    FC_trace::trace("Flexi_captcha::check() captcha_info: ".print_r($captcha_info,true));
	FC_trace::trace("Flexi_captcha::check() pic_requested = ".$captcha_info->target.", pic_selected = $pic_selected");

	if ($pic_selected != $captcha_info->target)
		{
		$retry_count = $app->getUserState(LAFC_COMPONENT."_captcha_retry_count",0);
		$retry_count ++;
        $retry_limit = min(($num_images - 1), 3);
		$app->setUserState(LAFC_COMPONENT."_captcha_retry_count", $retry_count);
       	FC_trace::trace("Flexi_captcha::check() wrong picture selected so FAIL, retry count is $retry_count, retry limit is $retry_limit");
		if ($retry_count <  $retry_limit)
			return 1;
		else
			return 2;
		}
   	FC_trace::trace("Flexi_captcha::check() correct image chosen so PASS");
	$app->setUserState(LAFC_COMPONENT."_captcha_info", '');	// destroy the session captcha info
	return 0;
}

// -------------------------------------------------------------------------------
// load language files for the themes installed
//
static function load_language_files($theme_info_array)
{
// load the additional language file provided by our old image packs, if it is present
// - it's also the one recommended in the user guide for user's own personal captcha images
// if not, load the front end Flexicontact language file for the current language (it has names for the default images)

	$lang = JFactory::getLanguage();
	$language = $lang->get('tag');
	if (file_exists(JPATH_SITE.'/language/'.$language.'/'.$language.'.com_flexicontact_captcha.ini'))
        {
        FC_trace::trace("Loading language file: ".JPATH_SITE.'/language/'.$language.'/'.$language.'.com_flexicontact_captcha.ini');
		$lang->load('com_flexicontact_captcha', JPATH_SITE);
        }
	else
        {
        FC_trace::trace("Loading language file: ".JPATH_SITE.'/components/com_flexicontact/'.$language.'/'.$language.'.com_flexicontact.ini');
		$lang->load('com_flexicontact', JPATH_SITE.'/components/com_flexicontact');
        }
        
// load any additional language files provided by the new xml image packs

    foreach ($theme_info_array as $short_name => $theme_info)
        {
        if ($short_name == 'all')                               // not a real theme so no language file for this one
            continue;
        if ($short_name == 'standard')                          // there is no separate language file for this one
            continue;
        if ($theme_info_array[$short_name]['count'] == 0)       // don't bother if there are no images for this pack
            continue;
        $lang_file_name = 'com_flexicontact_theme_'.$short_name;
        $lang_file_path = JPATH_SITE.'/components/com_flexicontact/language/'.$language.'/'.$language.'.'.$lang_file_name.'.ini';
        FC_trace::trace("Checking for: $lang_file_path");
    	if (file_exists($lang_file_path))
            {
            FC_trace::trace("Loading: $lang_file_path");
            $lang->load($lang_file_name, JPATH_SITE.'/components/com_flexicontact');
            }
        else
            {
            $lang_file_path = JPATH_SITE.'/components/com_flexicontact/language/en-GB/en-GB.'.$lang_file_name.'.ini';
            FC_trace::trace("Checking for: $lang_file_path");
        	if (file_exists($lang_file_path))
                {
                FC_trace::trace("Loading: $lang_file_path");
                $lang->load($lang_file_name, JPATH_SITE.'/components/com_flexicontact', 'en-GB');
                }
            }
        }
}

// -------------------------------------------------------------------------------
// make an array of themes installed
//
static function get_themes()
{
    $theme_info_array = array();
    $theme_info_array['all']['list_name'] = JText::_('JALL');
    $theme_info_array['all']['regex'] = '.*';
    $theme_info_array['standard']['list_name'] = 'Original';        // in case anyone has the original antiques
    $theme_info_array['standard']['regex'] = '^\d*.gif$';
    
// search for any new themes (defined by an XML file)

    if (is_dir(LAFC_SITE_IMAGES_PATH))
        {
        $xml_files = glob(JPATH_SITE.'/media/com_flexicontact/*.xml');
        foreach ($xml_files as $xml_file)
            {
            FC_trace::trace("Loading theme from $xml_file");
            $xml = self::getXML($xml_file,true);
            if (!isset($xml->theme_info))
                continue;
            $short_name = (string) $xml->theme_info->short_name;
            $theme_info_array[$short_name] = array();
       		$theme_info_array[$short_name]['list_name'] = (string) $xml->theme_info->list_name;
       		$theme_info_array[$short_name]['regex'] = (string) $xml->theme_info->regex;
       		$theme_info_array[$short_name]['extension_name'] = strtolower($xml->name);
            }
        }
        
    foreach ($theme_info_array as $short_name => $theme_info)
        {
        $imageFiles = self::get_image_files(LAFC_SITE_IMAGES_PATH, $theme_info['regex']);
        $theme_info_array[$short_name]['count'] = count($imageFiles);
        }
        
    return $theme_info_array;
}

// -------------------------------------------------------------------------------
// get an array of image files matching a regular expression (glob() can't do this)
//
static function get_image_files($image_path, $regex)
{
    if (!is_dir($image_path))
        return array();
    $handle = opendir($image_path);
	if (!$handle)
		return array();

    $files = array();
    $regex = '/'.$regex.'/';
	while (($filename = readdir($handle)) != false)
		{
        if ($filename == '.' or $filename == '..')
            continue;
        if (preg_match($regex, $filename) != 1)
            continue;
        $imageInfo = @getimagesize($image_path.'/'.$filename);
        if ($imageInfo === false)
            continue;					// not an image
        if ($imageInfo[2] > 3)			// we only support gif, jpg or png
            continue;
        if ($imageInfo[0] > 150)		// if X size > 150 pixels ..
            continue;					// .. it's too big so skip it
        $files[] = $filename;
   		}
    closedir($handle);
    return $files;
}

// -------------------------------------------------------------------------------
// JFactory::getXML() was removed in Joomla 4.0 so it's here now
//
static function getXml($data, $isFile = true)
{
    $class = 'SimpleXMLElement';
    if (class_exists('JXMLElement'))
        $class = 'JXMLElement';
    
    libxml_use_internal_errors(true);       // Disable libxml errors and allow to fetch error information as needed

    if ($isFile)
        $xml = simplexml_load_file($data, $class);
    else
        $xml = simplexml_load_string($data, $class);

    if ($xml === false)
        {
        FC_trace::trace("Error loading xml file $data");
        foreach (libxml_get_errors() as $error)
            FC_trace::trace($error->message);
        }

    return $xml;
}

}
