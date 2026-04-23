<?php
 /**
 * @package Spider Video Player Lite
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class TOOLBAR_spidervideoplayer_video {

	function _NEW() {
		JToolBarHelper::title( JText::_( 'Add Video' ), 'generic.png' );
		JToolBarHelper::apply('apply_video');
		JToolBarHelper::save('save_video');
		
		JToolBarHelper::cancel('cancel_video');		
	}

	function _DEFAULT() {
		//JToolBarHelper::preview('index.php?option=com_spidervideoplayer');
		JToolBarHelper::title( JText::_( 'Videos' ), 'generic.png' );
		JToolBarHelper::addNew('add_video');
		JToolBarHelper::editList('edit_video');
		JToolBarHelper::publishList('publish_video');
		JToolBarHelper::unpublishList('unpublish_video');
		JToolBarHelper::deleteList('Are you sure you want to delete? ', 'remove_video');
		
		
	}
}

class TOOLBAR_spidervideoplayer_playlist {
	function _NEW() {
		JToolBarHelper::title( JText::_( 'Add Playlist' ), 'generic.png' );
		JToolBarHelper::apply('apply_playlist');
		JToolBarHelper::save('save_playlist');
		
		  JToolBarHelper::cancel('cancel_playlist');		
	}
	function _DEFAULT() {
		//JToolBarHelper::preview('index.php?option=com_spidervideoplayer');
		JToolBarHelper::title( JText::_( 'Playlists' ), 'generic.png' );
		JToolBarHelper::addNew('add_playlist');
		JToolBarHelper::editList('edit_playlist');
		JToolBarHelper::publishList('publish_playlist');
		JToolBarHelper::unpublishList('unpublish_playlist');
		JToolBarHelper::deleteList('Are you sure you want to delete? ', 'remove_playlist');
		
		
	}
}

class TOOLBAR_spidervideoplayer_theme {
	function _NEW() {
		JToolBarHelper::title( JText::_( 'Add Theme' ), 'generic.png' );
			JToolBarHelper::apply('apply_theme');
		JToolBarHelper::save('save_theme');
	
		JToolBarHelper::cancel('cancel_theme');		
	}

	function _DEFAULT() {
	JToolBarHelper::addNew('add_theme');
		JToolBarHelper::editList('edit_theme');
		JToolBarHelper::makeDefault();
		
		JToolBarHelper::title( JText::_( 'Themes' ), 'thememanager.png' );
		JToolBarHelper::deleteList('Are you sure you want to delete? ', 'remove_theme');
		
		
	}
}

class TOOLBAR_spidervideoplayer_tag {

	function _NEW() {
	JToolBarHelper::title( JText::_( 'Add Tag' ), 'generic.png' );
	JToolBarHelper::apply('apply_tag');
		JToolBarHelper::save('save_tag');
		
		JToolBarHelper::cancel('cancel_tag');		
	}

	function _DEFAULT() {

		JToolBarHelper::title( JText::_( 'Tags' ), 'generic.png' );
		JToolBarHelper::addNew('add_tag');
		JToolBarHelper::editList('edit_tag');
		JToolBarHelper::publishList('publish_tag');
		JToolBarHelper::unpublishList('unpublish_tag');
		JToolBarHelper::deleteList('Are you sure you want to delete? ', 'remove_tag');
		
		
	}
}
?>