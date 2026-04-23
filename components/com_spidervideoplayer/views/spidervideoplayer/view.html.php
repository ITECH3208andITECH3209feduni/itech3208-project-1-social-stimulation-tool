<?php
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view'); 

class spidervideoplayerViewspidervideoplayer extends JViewLegacy

{

    function display($tpl = null)

		{
		$model = $this->getModel();
		$result = $model->getParams();
        $this->assignRef( 'params',		$result[0] );
        $this->assignRef( 'theme',		$result[1] );
        $this->assignRef( 'playlist',	$result[2] );
		$this->assignRef( 'priority',	$result[3] );
		$this->assignRef( 'typeselect',	$result[4] );
		$this->assignRef( 'track',	$result[5] );       
		parent::display($tpl);

		}

}

?>