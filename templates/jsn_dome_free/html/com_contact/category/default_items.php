<?php
/**
 * @version		$Id: default_items.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach($this->items as $i => $item) : ?>
<?php if ($this->items[$i]->published == 0) : ?>

<tr class="system-unpublished sectiontableentry<?php echo $i % 2 +1; ?>">
	<?php else: ?>
<tr class="sectiontableentry<?php echo $i % 2 +1; ?>">
	<?php endif; ?>
	<td class="jsn-table-column-order" width="10" align="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
	<td class="jsn-table-column-name"><a class="category" href="<?php echo JRoute::_(ContactHelperRoute::getContactRoute($item->slug, $item->catid)); ?>"> <?php echo $item->name; ?></a></td>
	<?php if ($this->params->get('show_country_headings')) : ?>
	<td class="jsn-table-column-country"><?php echo $item->country; ?></td>
	<?php endif; ?>
	<?php if ($this->params->get('show_state_headings')) : ?>
	<td class="jsn-table-column-state"><?php echo $item->state; ?></td>
	<?php endif; ?>
	<?php if ($this->params->get('show_suburb_headings')) : ?>
	<td class="jsn-table-column-suburb"><?php echo $item->suburb; ?></td>
	<?php endif; ?>
	<?php if ($this->params->get('show_email_headings')) : ?>
	<td class="jsn-table-column-email"><?php echo $item->email_to; ?></td>
	<?php endif; ?>
	<?php if ($this->params->get('show_telephone_headings')) : ?>
	<td class="jsn-table-column-telephone"><?php echo $item->telephone; ?></td>
	<?php endif; ?>
	<?php if ($this->params->get('show_mobile_headings')) : ?>
	<td class="jsn-table-column-mobile"><?php echo $item->mobile; ?></td>
	<?php endif; ?>
	<?php if ($this->params->get('show_fax_headings')) : ?>
	<td class="jsn-table-column-fax"><?php echo $item->fax; ?></td>
	<?php endif; ?>
	<?php if ($this->params->get('show_position_headings')) : ?>
	<td class="jsn-table-column-position"><?php echo $item->con_position; ?></td>
	<?php endif; ?>
	</ul>
</tr>
<?php endforeach; ?>