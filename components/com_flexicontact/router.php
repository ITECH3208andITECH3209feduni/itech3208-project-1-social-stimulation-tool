<?php
/********************************************************************
Product		: Flexicontact
Date		: 2 May 2022
Copyright	: Les Arbres Design 2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactRouter extends JComponentRouterBase
{

public function build(&$query)
{
	unset($query['view']);
	return array();
}

public function parse(&$segments)
{
	return array();
}

} 