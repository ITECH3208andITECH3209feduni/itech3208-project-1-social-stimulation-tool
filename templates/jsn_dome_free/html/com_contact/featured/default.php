<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');

// If the page class is defined, add to class as suffix.
// It will be a separate class if the user starts it with a space
?>

<div class="com-contact <?php echo $this->pageclass_sfx; ?>">
	<div class="contact-category<?php echo $this->pageclass_sfx;?>">
		<?php if ($this->params->def('show_page_heading', 1)) : ?>
		<h2 class="componentheading"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h2>
		<?php endif; ?>
		<?php echo $this->loadTemplate('items'); ?>
		<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->get('pages.total') > 1)) : ?>
		<div class="jsn-pagination-container"> <?php echo $this->pagination->getPagesLinks(); ?>
			<?php if ($this->params->def('show_pagination_results', 1)) : ?>
			<p class="jsn-pageinfo"> <?php echo $this->pagination->getPagesCounter(); ?> </p>
			<?php  endif; ?>
		</div>
		<?php endif; ?>
	</div>
</div>