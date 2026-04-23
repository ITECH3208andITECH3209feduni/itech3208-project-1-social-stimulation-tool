<?php
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\String\StringHelper;

require_once( JApplicationHelper::getPath( 'admin_html' ) );
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_spidervideoplayer'.DS.'tables');
$task	= JRequest::getCmd('task'); 
$id = JRequest::getVar('id', 0, 'get', 'int');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
// checks the $task variable and 
// choose an appropiate function
switch($task){
	case 'default':
		setdefault();
		break;
		
	case 'preview':
		preview_spidervideoplayer();
		break;
		
	case 'preview_playlist':
		preview_playlist();
		break;
		
	case 'preview_settings':
		preview_settings();
		break;
		
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
///////////////////////////////////////////////////////// V I D E O //////////////////////////////////////////////////////////////////////////////////////////				
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
	case 'media_manager_video':
		media_manager_video();
		break;
		
	case 'media_manager_image':
		media_manager_image();
		break;
		
	case 'video':
		show_video();
		JSubMenuHelper::addEntry(JText::_('Tags'), 'index.php?option=com_spidervideoplayer&amp;task=tag' );
		JSubMenuHelper::addEntry(JText::_('Videos'), 'index.php?option=com_spidervideoplayer&amp;task=video', true );
		JSubMenuHelper::addEntry(JText::_('Playlists'), 'index.php?option=com_spidervideoplayer&amp;task=playlist');
		JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_spidervideoplayer&amp;task=theme'  );
		break;
		
	case 'add_video':
		add_video();
		break;
		
	case 'cancel_video';		
		cancel_video();
		break;
	case 'apply_video':	
	case 'save_video';		
		save_video($task);
		break;
		
	case 'edit_video':
    		edit_video();
    		break;	
		
	case 'remove_video':
		remove_video();
		break;
		
 	 case 'publish_video':
   		change_video(1 );
    		break;	 
	case 'unpublish_video':
	   	change_video(0 );
	    	break;
				
	case 'select_video':
	   	select_video();
	    	break;	
			
	case 'select_video_menu':
	   	select_video_menu();
	    	break;			

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
///////////////////////////////////////////////////////// P L A Y L I S T //////////////////////////////////////////////////////////////////////////////////////////				
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
	case 'playlist':
		show_playlist();
		JSubMenuHelper::addEntry(JText::_('Tags'), 'index.php?option=com_spidervideoplayer&amp;task=tag' );
		JSubMenuHelper::addEntry(JText::_('Videos'), 'index.php?option=com_spidervideoplayer&amp;task=video' );
		JSubMenuHelper::addEntry(JText::_('Playlists'), 'index.php?option=com_spidervideoplayer&amp;task=playlist', true);
		JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_spidervideoplayer&amp;task=theme'  );
		break;
		
	case 'add_playlist':
		add_playlist();
		break;
		
	case 'cancel_playlist';		
		cancel_playlist();
		break;
	case 'apply_playlist':	
	case 'save_playlist';		
		save_playlist($task);
		break;
		
	case 'edit_playlist':
    		edit_playlist();
    		break;	
		
	case 'remove_playlist':
		remove_playlist();
		break;
		
 	 case 'publish_playlist':
   		change_playlist(1 );
    		break;	 
	case 'unpublish_playlist':
	   	change_playlist(0 );
	    	break;	
			
	case 'select_playlist':
	   	select_playlist();
	    	break;	

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
///////////////////////////////////////////////////////// T H E M E //////////////////////////////////////////////////////////////////////////////////////////				
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
	case 'theme':
		show_theme();
		JSubMenuHelper::addEntry(JText::_('Tags'), 'index.php?option=com_spidervideoplayer&amp;task=tag' );
		JSubMenuHelper::addEntry(JText::_('Videos'), 'index.php?option=com_spidervideoplayer&amp;task=video' );
		JSubMenuHelper::addEntry(JText::_('Playlists'), 'index.php?option=com_spidervideoplayer&amp;task=playlist');
		JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_spidervideoplayer&amp;task=theme', true  );
		break;
		
	case 'add_theme':
		add_theme();
		break;
		
	case 'cancel_theme';		
		cancel_theme();
		break;
	case 'apply_theme':	
	case 'save_theme';		
		save_theme($task);
		break;
		
	case 'edit_theme':
    		edit_theme();
    		break;	
		
	case 'remove_theme':
		remove_theme();
		break;


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
///////////////////////////////////////////////////////// T A G S //////////////////////////////////////////////////////////////////////////////////////////				
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
	case 'tag':
		show_tag();
		JSubMenuHelper::addEntry(JText::_('Tags'), 'index.php?option=com_spidervideoplayer&amp;task=tag', true );
		JSubMenuHelper::addEntry(JText::_('Videos'), 'index.php?option=com_spidervideoplayer&amp;task=video' );
		JSubMenuHelper::addEntry(JText::_('Playlists'), 'index.php?option=com_spidervideoplayer&amp;task=playlist' );
		JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_spidervideoplayer&amp;task=theme' );
		break;
		
	case 'add_tag':
		add_tag();
		break;
		
	case 'cancel_tag';		
		cancel_tag();
		break;
	case 'apply_tag':	
	case 'save_tag';		
		save_tag($task);
		break;
		
	case 'saveorder';		
		saveorder($task);
		break;
		
	case 'orderup' :
		ordertag(-1);
		break;

	case 'orderdown' :
		ordertag(1);
		break;

	case 'edit_tag':
    		edit_tag();
    		break;	
		
	case 'remove_tag':
		remove_tag();
		break;
		
 	case 'publish_tag':
   		change_tag(1 );
    		break;	 
	case 'unpublish_tag':
	   	change_tag(0 );
	    	break;	
			
 	case 'required_tag':
   		required_tag(1 );
    		break;	 
	case 'unrequired_tag':
	   	required_tag(0 );
	    	break;	
			
	default:
	   	show_playlist();
		JSubMenuHelper::addEntry(JText::_('Tags'), 'index.php?option=com_spidervideoplayer&amp;task=tag' );
		JSubMenuHelper::addEntry(JText::_('Videos'), 'index.php?option=com_spidervideoplayer&amp;task=video' );
		JSubMenuHelper::addEntry(JText::_('Playlists'), 'index.php?option=com_spidervideoplayer&amp;task=playlist', true );
		JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_spidervideoplayer&amp;task=theme' );
	    	break;	
}


	
			$exist="0";

    $db =JFactory::getDBO();
	$query = "SHOW columns FROM #__spidervideoplayer_video";
	$db->setQuery($query);
	$fields = $db->loadObjectList();
	for($i=0; $i<count($fields); $i++){
	if($fields[$i]->Field=="urlHdHtml5" )
	$exist="1";
	}
	
	if($exist!="1")
	{
	$query = "ALTER TABLE #__spidervideoplayer_video  ADD urlHdHtml5 varchar(255) AFTER thumb, ADD urlHtml5 varchar(255) AFTER urlHD;";
	$db->setQuery($query);
	$db->Query($query);
	}
	
	
		$exist="0";


	$query = "SHOW columns FROM #__spidervideoplayer_theme";
	$db->setQuery($query);
	$fields = $db->loadObjectList();
	for($i=0; $i<count($fields); $i++){
	if($fields[$i]->Field=="openPlaylistAtStart" )
	$exist="1";
	
	}
	if($exist!="1")
	{
	$query = "ALTER TABLE #__spidervideoplayer_theme  ADD openPlaylistAtStart varchar(255)";
	$db->setQuery($query);
	$db->Query($query);
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
///////////////////////////////////////////////////////// V I D E O //////////////////////////////////////////////////////////////////////////////////////////				
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
function add_video(){
	$lists['published'] = JHTML::_('select.booleanlist', 'published' , 'class="inputbox"', 1);
    $db =& JFactory::getDBO();
	
	$query = "SELECT * FROM #__spidervideoplayer_tag order by ordering";
	$db->setQuery($query);
	$tags = $db->loadObjectList();
	


// display function
	HTML_contact::add_video($lists, $tags );
}

function show_video(){
	$option	= JRequest::getVar('option'); 
	$mainframe = &JFactory::getApplication();
    $db =& JFactory::getDBO();
	$filter_order= $mainframe-> getUserStateFromRequest( $option.'filter_order_video', 'filter_order_video','id','cmd' );
	//$filter_order='id';
	$filter_order_Dir= $mainframe-> getUserStateFromRequest( $option.'filter_order_Dir_video', 'filter_order_Dir_video','desc','word' );
	$filter_state = $mainframe-> getUserStateFromRequest( $option.'filter_state', 'filter_state', '','word' );
	$search_video = $mainframe-> getUserStateFromRequest( $option.'search_video', 'search_video','','string' );
	$filter_playlistid	= $mainframe->getUserStateFromRequest( $option.'filter_playlistid',	'filter_playlistid',	-1,	'int' );
	//$search_video = JString::strtolower( $search_video );
	$search_video = StringHelper::strtolower( $search_video );
	$limit= $mainframe-> getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart= $mainframe-> getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
	$where = array();
	if ($filter_playlistid > 0) {
			$query = "SELECT id FROM #__spidervideoplayer_playlist WHERE id=".(int) $filter_playlistid;
			$db->setQuery( $query );
			$playlist_ex = $db->loadResult();
			if(!$playlist_ex)
				$filter_playlistid=0;
			
		}

	if ($filter_playlistid > 0) {
		
			$query = "SELECT videos FROM #__spidervideoplayer_playlist WHERE id=".(int) $filter_playlistid;
			$db->setQuery( $query );
			$videos = $db->loadResult();
			$where[] = 'id in ('.$videos.'0)';
		}
		
	
	if ( $search_video ) {
		$where[] = 'title LIKE "%'.$db->getEscaped($search_video).'%"';
	}	
	
	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'id'){
		$orderby 	= ' ORDER BY id '.$filter_order_Dir;
	} else {
		$orderby 	= ' ORDER BY '. 
         $filter_order .' '. $filter_order_Dir .', id';
	}	
	
	
	// get the total number of records
	$query = "SELECT COUNT(*) FROM #__spidervideoplayer_video ". $where;
	//$query = 'SELECT COUNT(*)'.' FROM #__spidervideoplayer_video'. $where;
	$db->setQuery( $query );
	$total = $db->loadResult();
	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );	
	
	$query = "SELECT * FROM #__spidervideoplayer_video ". $where. $orderby;
	//$query = "SELECT * FROM #__spidervideoplayer_video". $where. $orderby;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $db->loadObjectList();
	if($db->getErrorNum()){
		echo $db->stderr();
		return false;
	}
	
	// table ordering
		// get list of Playlists for dropdown filter
	$query = "SELECT id, title FROM #__spidervideoplayer_playlist";
	$playlists[] = JHTML::_('select.option', '0', '- '.JText::_('Select Playlist').' -', 'id', 'title');
	$db->setQuery($query);
	$playlists = array_merge($playlists, $db->loadObjectList());
	$lists['filter_playlistid'] = JHTML::_('select.genericlist',  $playlists, 'filter_playlistid', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $filter_playlistid);

	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;	
	// search_video filter	
        $lists['search_video']= $search_video;	
	
    // display function
	HTML_contact::show_video($rows, $pageNav, $lists);
}

function save_video($task){
	$mainframe = &JFactory::getApplication();
	$row =& JTable::getInstance('spidervideoplayer_video', 'Table');
	if(!$row->bind(JRequest::get('post')))
	{
		JError::raiseError(500, $row->getError() );
	}
	
	$type=JRequest::getVar('type');
	switch($type){
	case 'http':
	
	$row->url=JRequest::getVar('url_http');
	break;
	case 'youtube':
	$row->url=JRequest::getVar('url_youtube');
	$row->urlHtml5='';
	$row->urlHdHtml5='';
	break;
	case 'rtmp':
	$row->url=JRequest::getVar('url_rtmp');
	$row->urlHD=JRequest::getVar('urlHD_rtmp');
	$row->urlHtml5='';
	$row->urlHdHtml5='';
	break;
	}
	
	
	$params = JRequest::getVar('params', null, 'params', 'array');
	$s='';
	if(isset($params))
	foreach($params as $key=> $param )
	{
		$s=$s.$key.'#===#'.$param.'#***#';
	}
	$row->params=$s;
	if(!$row->store()){
		JError::raiseError(500, $row->getError() );
	}

	switch ($task)
	{
		case 'apply_video':
		$msg ='Changes to Video saved';		
		$link ='index.php?option=com_spidervideoplayer&task=edit_video&cid[]='.$row->id;
		break;
		case 'save_video':
		default:
		$msg = 'Video Saved';
		$link = 'index.php?option=com_spidervideoplayer&task=video';
		break;
	}	$mainframe->redirect($link, $msg);
}

function edit_video(){
	$db		=& JFactory::getDBO();
	$cid 	= JRequest::getVar('cid', array(0), '', 'array');
	JArrayHelper::toInteger($cid, array(0));
	
	
	$query = "SELECT * FROM #__spidervideoplayer_tag  order by ordering";
	$db->setQuery($query);
	$tags = $db->loadObjectList();
	

	$id 	= $cid[0];
	$row =& JTable::getInstance('spidervideoplayer_video', 'Table');
	// load the row from the db table
	$row->load( $id);
	
	$lists = array();
	$lists['published'] = JHTML::_('select.booleanlist', 'published' , 'class="inputbox"', $row->published);
	// display function 
	HTML_contact::edit_video($lists, $row, $tags);
}

function remove_video(){
  $mainframe = &JFactory::getApplication();
  // Initialize variables	
  $db =& JFactory::getDBO();
  // Define cid array variable
  $cid = JRequest::getVar( 'cid' , array() , '' , 'array' );
  // Make sure cid array variable content integer format
  JArrayHelper::toInteger($cid);
  // If any item selected
  if (count( $cid )) {
    // Prepare sql statement, if cid array more than one, 
    // will be "cid1, cid2, ..."
    $cids = implode( ',', $cid );
    // Create sql statement
    $query = 'DELETE FROM #__spidervideoplayer_video WHERE id IN ( '. $cids .' )';
    // Execute query
    $db->setQuery( $query );
    if (!$db->query()) {
      echo "<script> alert('".$db->getErrorMsg(true)."'); 
      window.history.go(-1); </script>\n";
    }
	
  }
  
  	$query = "SELECT * FROM #__spidervideoplayer_playlist ";
	
	$db->setQuery( $query);
	$playlists = $db->loadObjectList();
	if($db->getErrorNum()){
		echo $db->stderr();
		return false;
	}
	
	foreach($playlists as $playlist)
	{
		$viedos_temp=array();
		$videos_id=explode(',',$playlist->videos);
		$videos_id= array_slice($videos_id,0, count($videos_id)-1);
		$new_videos='';   
		foreach($videos_id as $video_id)
		{
			if(!in_array($video_id,$cid))
				$new_videos=$new_videos.$video_id.',';
		}
  	$query = "UPDATE #__spidervideoplayer_playlist SET videos = '".$new_videos."' WHERE id=".$playlist->id;
	$db->setQuery( $query);
    if (!$db->query()) {
      echo $db->getErrorMsg(true);
		}
	}

  // After all, redirect again to frontpage
  $mainframe->redirect( "index.php?option=com_spidervideoplayer&task=video" );
}

function change_video( $state=0 ){
  $mainframe = &JFactory::getApplication();
  // Initialize variables
  $db 	=& JFactory::getDBO();
  // define variable $cid from GET
  $cid = JRequest::getVar( 'cid' , array() , '' , 'array' );	
  JArrayHelper::toInteger($cid);
  // Check there is/are item that will be changed. 
  //If not, show the error.
  if (count( $cid ) < 1) {
    $action = $state ? 'publish_video' : 'unpublish_video';
    JError::raiseError(500, JText::_( 'Select an item to' .$action, true ) );
  }
  // Prepare sql statement, if cid more than one, 
  // it will be "cid1, cid2, cid3, ..."
  $cids = implode( ',', $cid );
  $query = 'UPDATE #__spidervideoplayer_video'
  . ' SET published = ' . (int) $state
  . ' WHERE id IN ( '. $cids .' )'
  ;
  // Execute query
  $db->setQuery( $query );
  if (!$db->query()) {
    JError::raiseError(500, $db->getErrorMsg() );
  }
  if (count( $cid ) == 1) {
    $row =& JTable::getInstance('spidervideoplayer_video', 'Table');
    $row->checkin( intval( $cid[0] ) );
  }
  // After all, redirect to front page
  $mainframe->redirect( 'index.php?option=com_spidervideoplayer&task=video' );
}

function cancel_video(){
  $mainframe = &JFactory::getApplication();
  $mainframe->redirect( 'index.php?option=com_spidervideoplayer&task=video' );
}

function select_video(){
	$option	= JRequest::getVar('option'); 
	$mainframe = &JFactory::getApplication();
    $db =& JFactory::getDBO();
	///////////////////////////////////////order
	
	$filter_order= $mainframe-> getUserStateFromRequest( $option.'filter_order_video', 'filter_order_video','id','cmd' );
	$filter_order_Dir= $mainframe-> getUserStateFromRequest( $option.'filter_order_Dir_video', 'filter_order_Dir_video','desc','word' );
	
	//////search title
	$search_video = $mainframe-> getUserStateFromRequest( $option.'search_video', 'search_video','','string' );
	$filter_playlistid	= -1;
	//$search_video = JString::strtolower( $search_video );
	$search_video = StringHelper::strtolower( $search_video );
	
	/////limit
	$limit= $mainframe-> getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart= $mainframe-> getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
	
	$where = array();
	if ($filter_playlistid > 0) {
		
			$query = "SELECT videos FROM #__spidervideoplayer_playlist WHERE id=".(int) $filter_playlistid;
			$db->setQuery( $query );
			$videos = $db->loadResult();
			$where[] = 'id in ('. $videos.'0)';
		}
		
	
	if ( $search_video ) {
		$where[] = 'title LIKE "%'.$db->getEscaped($search_video).'%"';
	}	
	
	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'id'){
		$orderby 	= ' ORDER BY id '.$filter_order_Dir;
	} else {
		$orderby 	= ' ORDER BY '. 
         $filter_order .' '. $filter_order_Dir .', id';
	}	
	
	
	// get the total number of records
	$query = "SELECT COUNT(*) FROM #__spidervideoplayer_video ". $where;

	//$query = 'SELECT COUNT(*)'.' FROM #__spidervideoplayer_video'. $where;
	$db->setQuery( $query );
	$total = $db->loadResult();
	
	$query = "SELECT * FROM #__spidervideoplayer_video ". $where. $orderby;
	//$query = "SELECT * FROM #__spidervideoplayer_video". $where. $orderby;
	$db->setQuery( $query);
	$rows = $db->loadObjectList();
	if($db->getErrorNum()){
		echo $db->stderr();
		return false;
	}
	
	// table ordering
		// get list of Playlists for dropdown filter
	$query = "SELECT id, title FROM #__spidervideoplayer_playlist";
	$playlists[] = JHTML::_('select.option', '0', '- '.JText::_('Select Playlist').' -', 'id', 'title');
	$db->setQuery($query);
	$playlists = array_merge($playlists, $db->loadObjectList());
	$lists['filter_playlistid'] = JHTML::_('select.genericlist',  $playlists, 'filter_playlistid', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $filter_playlistid);

	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;	
	// search_video filter	
    $lists['search_video']= $search_video;	
	
	///////////////tags
	$query = "SELECT * FROM #__spidervideoplayer_tag WHERE published=1 ORDER BY ordering";
	$db->setQuery($query);
	$tags =$db->loadObjectList();
	$search_tags=array();
	
	foreach($tags as $tag)
	{
		$search_tags[$tag->id] = $mainframe-> getUserStateFromRequest( $option.'param_'.$tag->id, 'param_'.$tag->id,'','string' );
		//$search_tags[$tag->id] = JString::strtolower( $search_tags[$tag->id] );
		$search_tags[$tag->id] = StringHelper::strtolower( $search_tags[$tag->id] );
		$lists[$tag->id]= $search_tags[$tag->id];	

	}
	
	$param= array( array ());
	foreach($rows as $row)
	{
		$params=explode('#***#', $row->params);
		$params= array_slice($params,0, count($params)-1);   
		foreach ($params as $param_temp)
		{
			$param_temp							= explode('#===#', $param_temp);
			//$param[$row->id][$param_temp[0]]	= JString::strtolower($param_temp[1]);
			$param[$row->id][$param_temp[0]]	= StringHelper::strtolower($param_temp[1]);
		}
	}
	$new_rows=array();
	foreach($rows as $row)
	{
		$t=true;
		foreach($search_tags as $key =>$search_tag)
		{
			if($search_tag)
			if(isset($param[$row->id][$key]))
			{
				if(!is_numeric(strpos($param[$row->id][$key], $search_tag)))
					$t=false;
			}
			else
					$t=false;
		}
		
		if($t)
			$new_rows[]=$row;
	}
	
	
	jimport('joomla.html.pagination');
	$pageNav = new JPagination( count($new_rows), $limitstart, $limit );	
	//$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	
	$new_rows= array_slice($new_rows,$pageNav->limitstart, $pageNav->limit);  

    // display function
	HTML_contact::select_video($new_rows, $pageNav, $lists, $tags);
}

function select_video_menu(){
	$option	= JRequest::getVar('option'); 
	$mainframe = &JFactory::getApplication();
    $db =& JFactory::getDBO();
	///////////////////////////////////////order
	
	$filter_order= $mainframe-> getUserStateFromRequest( $option.'filter_order_video', 'filter_order_video','id','cmd' );
	$filter_order_Dir= $mainframe-> getUserStateFromRequest( $option.'filter_order_Dir_video', 'filter_order_Dir_video','desc','word' );
	
	//////search title
	$search_video = $mainframe-> getUserStateFromRequest( $option.'search_video', 'search_video','','string' );
	$filter_playlistid	= -1;
	//$search_video = JString::strtolower( $search_video );
	$search_video = StringHelper::strtolower( $search_video );
	
	/////limit
	$limit= $mainframe-> getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart= $mainframe-> getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
	
	$where = array();
	if ($filter_playlistid > 0) {
		
			$query = "SELECT videos FROM #__spidervideoplayer_playlist WHERE id=".(int) $filter_playlistid;
			$db->setQuery( $query );
			$videos = $db->loadResult();
			$where[] = 'id in ('. $videos.'0)';
		}
		
	
	if ( $search_video ) {
		$where[] = 'title LIKE "%'.$db->getEscaped($search_video).'%"';
	}	
	
	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'id'){
		$orderby 	= ' ORDER BY id '.$filter_order_Dir;
	} else {
		$orderby 	= ' ORDER BY '. 
         $filter_order .' '. $filter_order_Dir .', id';
	}	
	
	
	// get the total number of records
	$query = "SELECT COUNT(*) FROM #__spidervideoplayer_video ". $where;

	//$query = 'SELECT COUNT(*)'.' FROM #__spidervideoplayer_video'. $where;
	$db->setQuery( $query );
	$total = $db->loadResult();
	
	$query = "SELECT * FROM #__spidervideoplayer_video ". $where. $orderby;
	//$query = "SELECT * FROM #__spidervideoplayer_video". $where. $orderby;
	$db->setQuery( $query);
	$rows = $db->loadObjectList();
	if($db->getErrorNum()){
		echo $db->stderr();
		return false;
	}
	
	// table ordering
		// get list of Playlists for dropdown filter
	$query = "SELECT id, title FROM #__spidervideoplayer_playlist";
	$playlists[] = JHTML::_('select.option', '0', '- '.JText::_('Select Playlist').' -', 'id', 'title');
	$db->setQuery($query);
	$playlists = array_merge($playlists, $db->loadObjectList());
	$lists['filter_playlistid'] = JHTML::_('select.genericlist',  $playlists, 'filter_playlistid', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $filter_playlistid);

	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;	
	// search_video filter	
    $lists['search_video']= $search_video;	
	
	///////////////tags
	$query = "SELECT * FROM #__spidervideoplayer_tag WHERE published=1 ORDER BY ordering";
	$db->setQuery($query);
	$tags =$db->loadObjectList();
	$search_tags=array();
	
	foreach($tags as $tag)
	{
		$search_tags[$tag->id] = $mainframe-> getUserStateFromRequest( $option.'param_'.$tag->id, 'param_'.$tag->id,'','string' );
		//$search_tags[$tag->id] = JString::strtolower( $search_tags[$tag->id] );
		$search_tags[$tag->id] = StringHelper::strtolower( $search_tags[$tag->id] );
		$lists[$tag->id]= $search_tags[$tag->id];	

	}
	
	$param= array( array ());
	foreach($rows as $row)
	{
		$params=explode('#***#', $row->params);
		$params= array_slice($params,0, count($params)-1);   
		foreach ($params as $param_temp)
		{
			$param_temp							= explode('#===#', $param_temp);
			//$param[$row->id][$param_temp[0]]	= JString::strtolower($param_temp[1]);
			$param[$row->id][$param_temp[0]]	= StringHelper::strtolower($param_temp[1]);
		}
	}
	$new_rows=array();
	foreach($rows as $row)
	{
		$t=true;
		foreach($search_tags as $key =>$search_tag)
		{
			if($search_tag)
			if(isset($param[$row->id][$key]))
			{
				if(!is_numeric(strpos($param[$row->id][$key], $search_tag)))
					$t=false;
			}
			else
					$t=false;
		}
		
		if($t)
			$new_rows[]=$row;
	}
	
	
	jimport('joomla.html.pagination');
	$pageNav = new JPagination( count($new_rows), $limitstart, $limit );	
	//$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	
	$new_rows= array_slice($new_rows,$pageNav->limitstart, $pageNav->limit);  

    // display function
	HTML_contact::select_video_menu($new_rows, $pageNav, $lists, $tags);
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
///////////////////////////////////////////////////////// P L A Y L I S T //////////////////////////////////////////////////////////////////////////////////////////				
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
function add_playlist(){
	$lists['published'] = JHTML::_('select.booleanlist', 'published' , 'class="inputbox"', 1);
		
// display function
	HTML_contact::add_playlist($lists);
}

function show_playlist(){
	$option	= JRequest::getVar('option'); 
	$mainframe = &JFactory::getApplication();
    $db =& JFactory::getDBO();
	$filter_order= $mainframe-> getUserStateFromRequest( $option.'filter_order_playlist', 'filter_order_playlist','id','cmd' );
	//$filter_order='id';
	$filter_order_Dir= $mainframe-> getUserStateFromRequest( $option.'filter_order_Dir_playlist', 'filter_order_Dir_playlist','desc','word' );
	$filter_state = $mainframe-> getUserStateFromRequest( $option.'filter_state', 'filter_state', '','word' );
	$search_playlist = $mainframe-> getUserStateFromRequest( $option.'search_playlist', 'search_playlist','','string' );
	$filter_playlistid	= $mainframe->getUserStateFromRequest( $option.'filter_playlistid',	'filter_playlistid',	-1,	'int' );
	//$search_playlist = JString::strtolower( $search_playlist );
	$search_playlist = StringHelper::strtolower( $search_playlist );
	$limit= $mainframe-> getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart= $mainframe-> getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
	$where = array();
		
	if ( $search_playlist ) {
		$where[] = 'title LIKE "%'.$db->getEscaped($search_playlist).'%"';
	}	
	
	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'id' or $filter_order == 'number_of_vids'){
		$orderby 	= ' ORDER BY id '.$filter_order_Dir;
	} else {
		$orderby 	= ' ORDER BY '. 
         $filter_order .' '. $filter_order_Dir .', id';
		 
	}	
	
	
	// get the total number of records
	$query = "SELECT COUNT(*) FROM #__spidervideoplayer_playlist". $where;

	//$query = 'SELECT COUNT(*)'.' FROM #__spidervideoplayer_playlist'. $where;
	$db->setQuery( $query );
	$total = $db->loadResult();
	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );	
	
	$query = "SELECT * FROM #__spidervideoplayer_playlist". $where. $orderby;
if($filter_order == 'number_of_vids')
	$query = 'SELECT *, (LENGTH(  `videos` ) - LENGTH( REPLACE(  `videos` ,  ",",  "" ) )) AS video_count FROM #__spidervideoplayer_playlist'. $where. ' ORDER BY  `video_count`'.$filter_order_Dir ;
	
	//$query = "SELECT * FROM #__spidervideoplayer_playlist". $where. $orderby;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $db->loadObjectList();
	if($db->getErrorNum()){
		echo $db->stderr();
		return false;
	}
	
	// table ordering

	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;	
	// search_playlist filter	
        $lists['search_playlist']= $search_playlist;	
	
    // display function
	HTML_contact::show_playlist($rows, $pageNav, $lists);
}

function save_playlist($task){
	$mainframe = &JFactory::getApplication();
	$row =& JTable::getInstance('spidervideoplayer_playlist', 'Table');
	if(!$row->bind(JRequest::get('post')))
	{
		JError::raiseError(500, $row->getError() );
	}
	
	if(!$row->store()){
		JError::raiseError(500, $row->getError() );
	}
	switch ($task)
	{
		case 'apply_playlist':
		$msg ='Changes to playlist saved';		
		$link ='index.php?option=com_spidervideoplayer&task=edit_playlist&cid[]='.$row->id;
		break;
		case 'save_playlist':
		default:
		$msg = 'playlist Saved';
		$link = 'index.php?option=com_spidervideoplayer&task=playlist';
		break;
	}	$mainframe->redirect($link, $msg);
}

function edit_playlist(){
	$db		=& JFactory::getDBO();
	$cid 	= JRequest::getVar('cid', array(0), '', 'array');
	JArrayHelper::toInteger($cid, array(0));
	
	$id 	= $cid[0];
	$row =& JTable::getInstance('spidervideoplayer_playlist', 'Table');
	// load the row from the db table
	$row->load( $id);
	
	$viedos=array();
	$videos_id=explode(',',$row->videos);
	$videos_id= array_slice($videos_id,0, count($videos_id)-1);   
	foreach($videos_id as $video_id)
	{
		$query ="SELECT * FROM #__spidervideoplayer_video WHERE id =".$video_id ;
		$db->setQuery($query); 
		$viedos[] = $db->loadObject();
	}
	
	$lists = array();
	$lists['published'] = JHTML::_('select.booleanlist', 'published' , 'class="inputbox"', $row->published);
	// display function 
	HTML_contact::edit_playlist($lists, $row,$viedos);
}

function remove_playlist(){
  $mainframe = &JFactory::getApplication();
  // Initialize variables	
  $db =& JFactory::getDBO();
  // Define cid array variable
  $cid = JRequest::getVar( 'cid' , array() , '' , 'array' );
  // Make sure cid array variable content integer format
  JArrayHelper::toInteger($cid);
  // If any item selected
  if (count( $cid )) {
    // Prepare sql statement, if cid array more than one, 
    // will be "cid1, cid2, ..."
    $cids = implode( ',', $cid );
    // Create sql statement
    $query = 'DELETE FROM #__spidervideoplayer_playlist'
    . ' WHERE id IN ( '. $cids .' )'
    ;
    // Execute query
    $db->setQuery( $query );
    if (!$db->query()) {
      echo "<script> alert('".$db->getErrorMsg(true)."'); 
      window.history.go(-1); </script>\n";
    }
	
  }
  // After all, redirect again to frontpage
  $mainframe->redirect( "index.php?option=com_spidervideoplayer&task=playlist" );
}

function change_playlist( $state=0 ){
  $mainframe = &JFactory::getApplication();
  // Initialize variables
  $db 	=& JFactory::getDBO();
  // define variable $cid from GET
  $cid = JRequest::getVar( 'cid' , array() , '' , 'array' );	
  JArrayHelper::toInteger($cid);
  // Check there is/are item that will be changed. 
  //If not, show the error.
  if (count( $cid ) < 1) {
    $action = $state ? 'publish_playlist' : 'unpublish_playlist';
    JError::raiseError(500, JText::_( 'Select an item to' .$action, true ) );
  }
  // Prepare sql statement, if cid more than one, 
  // it will be "cid1, cid2, cid3, ..."
  $cids = implode( ',', $cid );
  $query = 'UPDATE #__spidervideoplayer_playlist'
  . ' SET published = ' . (int) $state
  . ' WHERE id IN ( '. $cids .' )'
  ;
  // Execute query
  $db->setQuery( $query );
  if (!$db->query()) {
    JError::raiseError(500, $db->getErrorMsg() );
  }
  if (count( $cid ) == 1) {
    $row =& JTable::getInstance('spidervideoplayer_playlist', 'Table');
    $row->checkin( intval( $cid[0] ) );
  }
  // After all, redirect to front page
  $mainframe->redirect( 'index.php?option=com_spidervideoplayer&task=playlist' );
}

function cancel_playlist(){
  $mainframe = &JFactory::getApplication();
  $mainframe->redirect( 'index.php?option=com_spidervideoplayer&task=playlist' );
}

function select_playlist(){
	$option	= JRequest::getVar('option'); 
	$mainframe = &JFactory::getApplication();
    $db =& JFactory::getDBO();
	$filter_order= $mainframe-> getUserStateFromRequest( $option.'filter_order_playlist', 'filter_order_playlist','id','cmd' );
	//$filter_order='id';
	$filter_order_Dir= $mainframe-> getUserStateFromRequest( $option.'filter_order_Dir_playlist', 'filter_order_Dir_playlist','desc','word' );
	$filter_state = $mainframe-> getUserStateFromRequest( $option.'filter_state', 'filter_state', '','word' );
	$search_playlist = $mainframe-> getUserStateFromRequest( $option.'search_playlist', 'search_playlist','','string' );
	$filter_playlistid	= $mainframe->getUserStateFromRequest( $option.'filter_playlistid',	'filter_playlistid',	-1,	'int' );
	//$search_playlist = JString::strtolower( $search_playlist );
	$search_playlist = StringHelper::strtolower( $search_playlist );
	$limit= $mainframe-> getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart= $mainframe-> getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
	$where = array();
		
	if ( $search_playlist ) {
		$where[] = 'title LIKE "%'.$db->getEscaped($search_playlist).'%"';
	}	
	
	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'id' or $filter_order == 'number_of_vids'){
		$orderby 	= ' ORDER BY id '.$filter_order_Dir;
	} else {
		$orderby 	= ' ORDER BY '. 
         $filter_order .' '. $filter_order_Dir .', id';
		 
	}	
	
	
	// get the total number of records
	$query = "SELECT COUNT(*) FROM #__spidervideoplayer_playlist". $where;

	//$query = 'SELECT COUNT(*)'.' FROM #__spidervideoplayer_playlist'. $where;
	$db->setQuery( $query );
	$total = $db->loadResult();
	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );	
	
	$query = "SELECT * FROM #__spidervideoplayer_playlist". $where. $orderby;
if($filter_order == 'number_of_vids')
	$query = 'SELECT *, (LENGTH(  `videos` ) - LENGTH( REPLACE(  `videos` ,  ",",  "" ) )) AS video_count FROM #__spidervideoplayer_playlist'. $where. ' ORDER BY  `video_count`'.$filter_order_Dir ;
	
	//$query = "SELECT * FROM #__spidervideoplayer_playlist". $where. $orderby;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $db->loadObjectList();
	if($db->getErrorNum()){
		echo $db->stderr();
		return false;
	}
	
	// table ordering

	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;	
	// search_playlist filter	
        $lists['search_playlist']= $search_playlist;	
	
    // display function
	HTML_contact::select_playlist($rows, $pageNav, $lists);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
///////////////////////////////////////////////////////// T H E M E //////////////////////////////////////////////////////////////////////////////////////////				
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	
function add_theme(){
	//$lists['published'] = JHTML::_('select.booleanlist', 'published' , 'class="inputbox"', 1);
		
	$db		=& JFactory::getDBO();
	$query = "SELECT * FROM #__spidervideoplayer_theme where `default`=1";
	$db->setQuery($query);
	$def_theme = $db->loadObject();
// display function
	HTML_contact::add_theme($def_theme);
}

function show_theme(){
	$option	= JRequest::getVar('option'); 
	$mainframe = &JFactory::getApplication();
    $db =& JFactory::getDBO();
	$filter_order= $mainframe-> getUserStateFromRequest( $option.'filter_order_theme', 'filter_order_theme','id','cmd' );
	//$filter_order='id';
	$filter_order_Dir= $mainframe-> getUserStateFromRequest( $option.'filter_order_Dir_theme', 'filter_order_Dir_theme','desc','word' );
	$filter_state = $mainframe-> getUserStateFromRequest( $option.'filter_state', 'filter_state', '','word' );
	$search_theme = $mainframe-> getUserStateFromRequest( $option.'search_theme', 'search_theme','','string' );
	//$filter_albumid	= $mainframe->getUserStateFromRequest( $option.'filter_albumid',	'filter_albumid',	-1,	'int' );
	//$search_theme = JString::strtolower( $search_theme );
	$search_theme = StringHelper::strtolower( $search_theme );
	$limit= $mainframe-> getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart= $mainframe-> getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
	$where = array();
	/*if ($filter_albumid > 0) {
			$where[] = 'album_id = ' . (int) $filter_albumid;
		}
		
	if ($filter_albumid == 0) {
			$where[] = "#__spidervideoplayer_album.title is null";
		}

	*/
	if ( $search_theme ) {
		$where[] = '#__spidervideoplayer_theme.title LIKE "%'.$db->getEscaped($search_theme).'%"';
	}	
	
	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'id'){
		$orderby 	= ' ORDER BY id '.$filter_order_Dir;
	} else {
		$orderby 	= ' ORDER BY '. 
         $filter_order .' '. $filter_order_Dir .', id';
	}	
	
	
	// get the total number of records
	$query = "SELECT COUNT(*) FROM #__spidervideoplayer_theme". $where;

	//$query = 'SELECT COUNT(*)'.' FROM #__spidervideoplayer_theme'. $where;
	$db->setQuery( $query );
	$total = $db->loadResult();
	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );	
	
	$query = "SELECT * FROM #__spidervideoplayer_theme". $where. $orderby;
	//$query = "SELECT * FROM #__spidervideoplayer_theme". $where. $orderby;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $db->loadObjectList();
	if($db->getErrorNum()){
		echo $db->stderr();
		return false;
	}
	
	// table ordering
		// get list of Albums for dropdown filter
	/*$query = "SELECT id, title FROM #__spidervideoplayer_album";
	$albums[] = JHTML::_('select.option', '-1', '- '.JText::_('Select Album').' -', 'id', 'title');
	$albums[] = JHTML::_('select.option', '0', JText::_('None'), 'id', 'title');
	$db->setQuery($query);
	$albums = array_merge($albums, $db->loadObjectList());
	$lists['filter_albumid'] = JHTML::_('select.genericlist',  $albums, 'filter_albumid', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $filter_albumid);
*/
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;	
	// search_theme filter	
        $lists['search_theme']= $search_theme;	
	
    // display function
	HTML_contact::show_theme($rows, $pageNav, $lists);
}

function save_theme($task){
	$mainframe = &JFactory::getApplication();
	$row =& JTable::getInstance('spidervideoplayer_theme', 'Table');
	if(!$row->bind(JRequest::get('post')))
	{
		JError::raiseError(500, $row->getError() );
	}
	
	if(!$row->store()){
		JError::raiseError(500, $row->getError() );
	}
	switch ($task)
	{
		case 'apply_theme':
		$msg ='Changes to theme saved';		
		$link ='index.php?option=com_spidervideoplayer&task=edit_theme&cid[]='.$row->id;
		break;
		case 'save_theme':
		default:
		$msg = 'theme Saved';
		$link = 'index.php?option=com_spidervideoplayer&task=theme';
		break;
	}	$mainframe->redirect($link, $msg);
}

function edit_theme(){
	$db		=& JFactory::getDBO();
	$cid 	= JRequest::getVar('cid', array(0), '', 'array');
	JArrayHelper::toInteger($cid, array(0));

	$id 	= $cid[0];
	$row =& JTable::getInstance('spidervideoplayer_theme', 'Table');
	// load the row from the db table
	$row->load( $id);
	
	// display function 
	HTML_contact::edit_theme( $row);
}

function remove_theme(){
  $mainframe = &JFactory::getApplication();
  // Initialize variables	
  $db =& JFactory::getDBO();
  // Define cid array variable
  $cid = JRequest::getVar( 'cid' , array() , '' , 'array' );
  // Make sure cid array variable content integer format
  JArrayHelper::toInteger($cid);
  $query = 'SELECT id FROM #__spidervideoplayer_theme WHERE `default`=1 LIMIT 1';
  $db->setQuery( $query );
  $def = $db->loadResult();
  if($db->getErrorNum()){
	  echo $db->stderr();
	  return false;
  }
  $msg='';
  $k=array_search($def, $cid);
  if ($k>0)
  {
	  $cid[$k]=0;
	  $msg="You can't delete default theme";
  }
  
  if ($cid[0]==$def)
  {
	  $cid[0]=0;
	  $msg="You can't delete default theme";
  }
  
  // If any item selected
  if (count( $cid )) {
    // Prepare sql statement, if cid array more than one, 
    // will be "cid1, cid2, ..."
    $cids = implode( ',', $cid );
    // Create sql statement

    $query = 'DELETE FROM #__spidervideoplayer_theme'.' WHERE id IN ( '. $cids .' )';
    // Execute query
    $db->setQuery( $query );
    if (!$db->query()) {
      echo "<script> alert('".$db->getErrorMsg(true)."'); 
      window.history.go(-1); </script>\n";
    }
	
  }
  // After all, redirect again to frontpage
  if($msg)
  $mainframe->redirect( "index.php?option=com_spidervideoplayer&task=theme",  $msg);
  else
  $mainframe->redirect( "index.php?option=com_spidervideoplayer&task=theme");
}

function change_theme( $state=0 ){
  $mainframe = &JFactory::getApplication();
  // Initialize variables
  $db 	=& JFactory::getDBO();
  // define variable $cid from GET
  $cid = JRequest::getVar( 'cid' , array() , '' , 'array' );	
  JArrayHelper::toInteger($cid);
  // Check there is/are item that will be changed. 
  //If not, show the error.
  if (count( $cid ) < 1) {
    $action = $state ? 'publish_theme' : 'unpublish_theme';
    JError::raiseError(500, JText::_( 'Select an item to' .$action, true ) );
  }
  // Prepare sql statement, if cid more than one, 
  // it will be "cid1, cid2, cid3, ..."
  $cids = implode( ',', $cid );
  $query = 'UPDATE #__spidervideoplayer_theme'
  . ' SET published = ' . (int) $state
  . ' WHERE id IN ( '. $cids .' )'
  ;
  // Execute query
  $db->setQuery( $query );
  if (!$db->query()) {
    JError::raiseError(500, $db->getErrorMsg() );
  }
  if (count( $cid ) == 1) {
    $row =& JTable::getInstance('spidervideoplayer_theme', 'Table');
    $row->checkin( intval( $cid[0] ) );
  }
  // After all, redirect to front page
  $mainframe->redirect( 'index.php?option=com_spidervideoplayer&task=theme' );
}

function cancel_theme(){
  $mainframe = &JFactory::getApplication();
  $mainframe->redirect( 'index.php?option=com_spidervideoplayer&task=theme' );
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
///////////////////////////////////////////////////////// T A G //////////////////////////////////////////////////////////////////////////////////////////				
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	
function add_tag(){
	$lists['published'] = JHTML::_('select.booleanlist', 'published' , 'class="inputbox"', 1);
	$lists['required'] = JHTML::_('select.booleanlist', 'required' , 'class="inputbox"', 1);

// display function
	HTML_contact::add_tag($lists );
}
	
function ordertag($direction){
	$mainframe = &JFactory::getApplication();

	// Initialize variables
	$db		= & JFactory::getDBO();

	$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );

	if (isset( $cid[0] ))
	{
		$row =& JTable::getInstance('spidervideoplayer_tag', 'Table');
		$row->load( (int) $cid[0] );
		$row->move($direction, ''  );//. ' AND state >= 0'

		$cache = & JFactory::getCache('com_content');
		$cache->clean();
	}

	$mainframe->redirect('index.php?option=com_spidervideoplayer&task=tag');
}

function saveOrder(){
	$mainframe = &JFactory::getApplication();

	// Initialize variables
	$db			= & JFactory::getDBO();

	$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
	$order		= JRequest::getVar( 'order', array (0), 'post', 'array' );
	$redirect	= -1 ;
	$rettask	= JRequest::getVar( 'returntask', '', 'post', 'cmd' );
	$total		= count($cid);
	$conditions	= array ();

	JArrayHelper::toInteger($cid, array(0));
	JArrayHelper::toInteger($order, array(0));
	// Instantiate an article table object
	$row =& JTable::getInstance('spidervideoplayer_tag', 'Table');

	// Update the ordering for items in the cid array
	for ($i = 0; $i < $total; $i ++)
	{
		$id=((int) $cid[$i]);
		$row->load($id );
		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if (!$row->store()) {
				JError::raiseError( 500, $db->getErrorMsg() );
				return false;
			}
			// remember to updateOrder this group
		}
	}

	// execute updateOrder for each group
	foreach ($conditions as $cond)
	{
		$row->load($cond[0]);
	}

	$cache = & JFactory::getCache('com_content');
	$cache->clean();

	$msg = JText::_('New ordering saved');
	$row->reorder();
	switch ($rettask)
	{
		default :
			$mainframe->redirect('index.php?option=com_spidervideoplayer&task=tag', $msg);
			break;
	}
}

function show_tag(){
	$option	= JRequest::getVar('option'); 
	$mainframe = &JFactory::getApplication();
    $db =& JFactory::getDBO();
	$filter_order= $mainframe-> getUserStateFromRequest( $option.'filter_order_tag', 'filter_order_tag','id','cmd' );
	//$filter_order='id';
	$filter_order_Dir= $mainframe-> getUserStateFromRequest( $option.'filter_order_Dir_tag', 'filter_order_Dir_tag','desc','word' );
	$search_tag = $mainframe-> getUserStateFromRequest( $option.'search_tag', 'search_tag','','string' );
	//$filter_albumid	= $mainframe->getUserStateFromRequest( $option.'filter_albumid',	'filter_albumid',	-1,	'int' );
	//$search_tag = JString::strtolower( $search_tag );
	$search_tag = StringHelper::strtolower( $search_tag );
	$limit= $mainframe-> getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart= $mainframe-> getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
	$where = array();
	/*if ($filter_albumid > 0) {
			$where[] = 'album_id = ' . (int) $filter_albumid;
		}
		
	if ($filter_albumid == 0) {
			$where[] = "#__spidervideoplayer_album.title is null";
		}

	*/
	//$filter_order="name";
	
	if ( $search_tag ) {
		$where[] = 'name LIKE "%'.$db->getEscaped($search_tag).'%"';
	}	
	
	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'id'){
		$orderby 	= ' ORDER BY id '.$filter_order_Dir;
	} else {
		$orderby 	= ' ORDER BY '. 
         $filter_order .' '. $filter_order_Dir .', id';
	}	
	
	
	// get the total number of records
	$query = "SELECT COUNT(*) FROM #__spidervideoplayer_tag". $where;

	//$query = 'SELECT COUNT(*)'.' FROM #__spidervideoplayer_tag'. $where;
	$db->setQuery( $query );
	$total = $db->loadResult();
	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );	
	
	$query = "SELECT * FROM #__spidervideoplayer_tag". $where. $orderby;
	//$query = "SELECT * FROM #__spidervideoplayer_tag". $where. $orderby;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $db->loadObjectList();
	if($db->getErrorNum()){
		echo $db->stderr();
		return false;
	}
	
	// table ordering
		// get list of Albums for dropdown filter
	/*$query = "SELECT id, title FROM #__spidervideoplayer_album";
	$albums[] = JHTML::_('select.option', '-1', '- '.JText::_('Select Album').' -', 'id', 'title');
	$albums[] = JHTML::_('select.option', '0', JText::_('None'), 'id', 'title');
	$db->setQuery($query);
	$albums = array_merge($albums, $db->loadObjectList());
	$lists['filter_albumid'] = JHTML::_('select.genericlist',  $albums, 'filter_albumid', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $filter_albumid);
*/
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;	
	// search_tag filter	
        $lists['search_tag']= $search_tag;	
	
    // display function
	HTML_contact::show_tag($rows, $pageNav, $lists);

}

function save_tag($task){
	$mainframe = &JFactory::getApplication();
	$db		=& JFactory::getDBO();

	$row =& JTable::getInstance('spidervideoplayer_tag', 'Table');
	if(!$row->bind(JRequest::get('post')))
	{
		JError::raiseError(500, $row->getError() );
	}
	
	$query = "SELECT max(ordering) FROM #__spidervideoplayer_tag";

	$db->setQuery( $query );
	$max = $db->loadResult();
	
	$row->ordering=$max+1;
	
	if(!$row->store()){
		JError::raiseError(500, $row->getError() );
	}
	switch ($task)
	{
		case 'apply_tag':
		$msg ='Changes to tag saved';		
		$link ='index.php?option=com_spidervideoplayer&task=edit_tag&cid[]='.$row->id;
		break;
		case 'save_tag':
		default:
		$msg = 'tag Saved';
		$link = 'index.php?option=com_spidervideoplayer&task=tag';
		break;
	}	$mainframe->redirect($link, $msg);
}

function edit_tag(){
	$db		=& JFactory::getDBO();
	$cid 	= JRequest::getVar('cid', array(0), '', 'array');
	JArrayHelper::toInteger($cid, array(0));
	
	$query = "SELECT id, title FROM #__spidervideoplayer_tag";
	$db->setQuery($query);
	$albums = $db->loadObjectList();
	


	$id 	= $cid[0];
	$row =& JTable::getInstance('spidervideoplayer_tag', 'Table');
	// load the row from the db table
	$row->load( $id);
	
	$lists = array();
	$lists['published'] = JHTML::_('select.booleanlist', 'published' , 'class="inputbox"', $row->published);
	$lists['required'] = JHTML::_('select.booleanlist', 'required' , 'class="inputbox"', $row->required);
	// display function 
	HTML_contact::edit_tag($lists, $row,$albums);
}

function remove_tag(){
  $mainframe = &JFactory::getApplication();
  // Initialize variables	
  $db =& JFactory::getDBO();
  // Define cid array variable
  $cid = JRequest::getVar( 'cid' , array() , '' , 'array' );
  // Make sure cid array variable content integer format
  JArrayHelper::toInteger($cid);
  // If any item selected
  if (count( $cid )) {
    // Prepare sql statement, if cid array more than one, 
    // will be "cid1, cid2, ..."
    $cids = implode( ',', $cid );
    // Create sql statement
    $query = 'DELETE FROM #__spidervideoplayer_tag'
    . ' WHERE id IN ( '. $cids .' )'
    ;
    // Execute query
    $db->setQuery( $query );
    if (!$db->query()) {
      echo "<script> alert('".$db->getErrorMsg(true)."'); 
      window.history.go(-1); </script>\n";
    }
	
  }
  // After all, redirect again to frontpage
  $mainframe->redirect( "index.php?option=com_spidervideoplayer&task=tag" );
}

function change_tag( $state=0 ){
  $mainframe = &JFactory::getApplication();
  // Initialize variables
  $db 	=& JFactory::getDBO();
  // define variable $cid from GET
  $cid = JRequest::getVar( 'cid' , array() , '' , 'array' );	
  JArrayHelper::toInteger($cid);
  // Check there is/are item that will be changed. 
  //If not, show the error.
  if (count( $cid ) < 1) {
    $action = $state ? 'publish_tag' : 'unpublish_tag';
    JError::raiseError(500, JText::_( 'Select an item to' .$action, true ) );
  }
  // Prepare sql statement, if cid more than one, 
  // it will be "cid1, cid2, cid3, ..."
  $cids = implode( ',', $cid );
  $query = 'UPDATE #__spidervideoplayer_tag'
  . ' SET published = ' . (int) $state
  . ' WHERE id IN ( '. $cids .' )'
  ;
  // Execute query
  $db->setQuery( $query );
  if (!$db->query()) {
    JError::raiseError(500, $db->getErrorMsg() );
  }
  if (count( $cid ) == 1) {
    $row =& JTable::getInstance('spidervideoplayer_tag', 'Table');
    $row->checkin( intval( $cid[0] ) );
  }
  // After all, redirect to front page
  $mainframe->redirect( 'index.php?option=com_spidervideoplayer&task=tag' );
}

function required_tag( $state=0 ){
  $mainframe = &JFactory::getApplication();
  // Initialize variables
  $db 	=& JFactory::getDBO();
  // define variable $cid from GET
  $cid = JRequest::getVar( 'cid' , array() , '' , 'array' );	
  JArrayHelper::toInteger($cid);
  // Check there is/are item that will be changed. 
  //If not, show the error.
  if (count( $cid ) < 1) {
    $action = $state ? 'required_tag' : 'unrequired_tag';
    JError::raiseError(500, JText::_( 'Select an item to' .$action, true ) );
  }
  // Prepare sql statement, if cid more than one, 
  // it will be "cid1, cid2, cid3, ..."
  $cids = implode( ',', $cid );
  $query = 'UPDATE #__spidervideoplayer_tag'
  . ' SET required = ' . (int) $state
  . ' WHERE id IN ( '. $cids .' )'
  ;
  // Execute query
  $db->setQuery( $query );
  if (!$db->query()) {
    JError::raiseError(500, $db->getErrorMsg() );
  }
  if (count( $cid ) == 1) {
    $row =& JTable::getInstance('spidervideoplayer_tag', 'Table');
    $row->checkin( intval( $cid[0] ) );
  }
  // After all, redirect to front page
  $mainframe->redirect( 'index.php?option=com_spidervideoplayer&task=tag' );
}

function cancel_tag(){
  $mainframe = &JFactory::getApplication();
  $mainframe->redirect( 'index.php?option=com_spidervideoplayer&task=tag' );
}



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		

function media_manager_video()
{
	HTML_contact::media_manager_video();
}

function media_manager_image()
{
	HTML_contact::media_manager_image();
}

function preview_spidervideoplayer()
{
	$getparams=JRequest::get('get');
	HTML_contact::preview_spidervideoplayer(array_slice($getparams,3,count($getparams)));
}

function preview_playlist()
{
	HTML_contact::preview_playlist();
}

function preview_settings()
{	
	$row =& JTable::getInstance('spidervideoplayer_theme', 'Table');
	if(!$row->bind(JRequest::get('get')))
	{
		JError::raiseError(500, $row->getError() );
	}
	
	
	HTML_contact::preview_settings($row);
}


function setdefault()
{
  $mainframe = &JFactory::getApplication();
	$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
	JArrayHelper::toInteger($cid);
	
	if (isset($cid[0]) && $cid[0]) 
		$id = $cid[0];
	else 
	{
		$mainframe->redirect(  'index.php?option=com_spidervideoplayer&task=theme',JText::_('No Items Selected') );
		return false;
	}
	
	$db =& JFactory::getDBO();
echo $id;

	// Clear home field for all other items
	$query = 'UPDATE #__spidervideoplayer_theme SET `default` = 0 WHERE 1';
	$db->setQuery( $query );
	if ( !$db->query() ) {
		$msg =$db->getErrorMsg();
		echo $msg;
		return false;
	}

	// Set the given item to home
	$query = 'UPDATE #__spidervideoplayer_theme SET `default` = 1 WHERE id = '.(int) $id;
	$db->setQuery( $query );
	if ( !$db->query() ) {
		$msg = $db->getErrorMsg();
		return false;
	}
		
	$msg = JText::_( 'Default Theme Selected' );
	$mainframe->redirect( 'index.php?option=com_spidervideoplayer&task=theme' ,$msg);
}
?>

