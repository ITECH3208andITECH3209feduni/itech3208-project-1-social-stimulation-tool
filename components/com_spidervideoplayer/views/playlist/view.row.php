<?php

 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );



jimport( 'joomla.application.component.view');



class spidervideoplayerViewplaylist extends JViewLegacy

{

    function display($tpl = null)

		{

			$model = $this->getModel();

			$result = $model->playlist();

			$this->assignRef( 'playlists',	$result[0] );
			$this->assignRef( 'videos',	$result[1] );
			$this->assignRef( 'show_trackid',	$result[2] );
			
			parent::display($tpl);

		}

}

?>