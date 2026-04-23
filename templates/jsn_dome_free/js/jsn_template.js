/**
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2008 - 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
*/

	var JSNTemplate = {
		_templateParams:		{},

		initOnDomReady: function()
		{
			// Setup HTML code for typography
			JSNUtils.createGridLayout("DIV", "grid-layout", "grid-col", "grid-lastcol");
			JSNUtils.createExtList("list-number-", "span", "jsn-listbullet", true);
			JSNUtils.createExtList("list-icon", "span", "jsn-listbullet", false);
			
		},
		
		initOnLoad: function()
		{
			// Stick positions layout setup
			JSNUtils.setVerticalPosition("jsn-pos-stick-leftmiddle", 'middle');
			JSNUtils.setVerticalPosition("jsn-pos-stick-rightmiddle", 'middle');
		},

		initTemplate: function(templateParams)
		{
			// Store template parameters
			_templateParams = templateParams;
			
			// Init template on "domready" event
			window.addEvent('domready', JSNTemplate.initOnDomReady);
	
			// Init template on "load" event
			JSNUtils.addEvent(window, 'load', JSNTemplate.initOnLoad);
		}
	}; // must have ; to prevent syntax error when compress
