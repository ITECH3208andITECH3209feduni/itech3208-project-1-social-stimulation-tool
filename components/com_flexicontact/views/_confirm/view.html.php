<?php
/********************************************************************
Product		: Flexicontact
Date		: 23 July 2017
Copyright	: Les Arbres Design 2009-2017
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactView_Confirm extends JViewLegacy
{
function display($tpl = null)
	{
	echo "\n".'<div class="fc_conf">'.$this->message."</div>";
	}
}
