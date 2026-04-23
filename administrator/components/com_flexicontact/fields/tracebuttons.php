<?php
/********************************************************************
Product		: Multiple Products
Date		: 28 November 2022
Copyright	: Les Arbres Design 2010-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class JFormFieldTracebuttons extends JFormField
{
protected $type = 'tracebuttons';

protected function getInput()
{    
    $controls = ' <button type="button" class="btn btn-primary ladj-click-submit" data-task="trace_on">On</button>';
    $controls .= ' <button type="button" class="btn btn-primary ladj-click-submit" data-task="trace_off">Off</button>';
	if (file_exists(LAFC_TRACE_FILE_PATH))
		$controls .= ' <span><a href="'.LAFC_TRACE_FILE_URL.'" target="_blank"> Trace File</a></span>';
	else
		$controls .= ' <span>Tracing is currently OFF</span>';
    return $controls;
}

}