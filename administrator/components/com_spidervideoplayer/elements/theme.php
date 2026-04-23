<?php 
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class JFormFieldTheme extends JFormFieldText {

	function getInput() {
		$db	=& JFactory::getDBO();
		$name = $this->name;
		$value = $this->value;
		$control_name = $this->options['title'];

		$fieldName	= $control_name.'['.$name.']';

		$query = 'SELECT id, title FROM #__spidervideoplayer_theme';
		$db->setQuery( $query );
		$array2 = $db->loadObjectList();
		if($db->getErrorNum()){
			echo $db->stderr();
			return false;
		}
		
		$query = 'SELECT id FROM #__spidervideoplayer_theme WHERE `default`=1 LIMIT 1';
		$db->setQuery( $query );
		$def = $db->loadResult();
		if($db->getErrorNum()){
			echo $db->stderr();
			return false;
		}
		
		$query = 'SELECT id FROM #__spidervideoplayer_theme WHERE id='.$value.' LIMIT 1';
		$db->setQuery( $query );
		$is = $db->loadResult();
		if($db->getErrorNum()){
			echo $db->stderr();
			return false;
		}
		
		if(!$is)
			$value=$def;
		
		$array1[]=JHTML::_('select.option', $id = '-1', $title= JText::_( 'Select Theme' ), 'id', 'title', $disable=true );
		$rows = array_merge($array1, $array2);
		return  JHTML::_('select.genericlist', $rows, $name,'', 'id', 'title', $value);
		
	}
}
?>