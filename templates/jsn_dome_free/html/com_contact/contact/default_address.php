<?php

/**
 * @version		$Id: default_address.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/* marker_class: Class based on the selection of text, none, or icons
 * jicon-text, jicon-none, jicon-icon
 */
?>
<?php if (($this->params->get('address_check') > 0) &&  ($this->contact->address || $this->contact->suburb  || $this->contact->state || $this->contact->country || $this->contact->postcode)) : ?>
	<div class="contact-address clearafter">
	<?php if ($this->params->get('address_check') > 0) : ?>
		<span class="<?php echo $this->params->get('marker_class'); ?>">
			<?php echo $this->params->get('marker_address'); ?>
		</span>
	<?php endif; ?>
	<?php if ($this->contact->address && $this->params->get('show_street_address')) : ?>
		<p class="contact-street">
			<?php echo nl2br($this->contact->address); ?>
		</p>
	<?php endif; ?>
	<?php if ($this->contact->suburb && $this->params->get('show_suburb')) : ?>
		<p class="contact-suburb">
			<?php echo $this->contact->suburb; ?>
		</p>
	<?php endif; ?>
	<?php if ($this->contact->state && $this->params->get('show_state')) : ?>
		<p class="contact-state">
			<?php echo $this->contact->state; ?>
		</p>
	<?php endif; ?>
	<?php if ($this->contact->postcode && $this->params->get('show_postcode')) : ?>
		<p class="contact-postcode">
			<?php echo $this->contact->postcode; ?>
		</p>
	<?php endif; ?>
	<?php if ($this->contact->country && $this->params->get('show_country')) : ?>
		<p class="contact-country">
			<?php echo $this->contact->country; ?>
		</p>
	<?php endif; ?>
<?php endif; ?>

<?php if ($this->params->get('address_check') > 0) : ?>
	</div>
<?php endif; ?>

<?php if($this->params->get('show_email') || $this->params->get('show_telephone')||$this->params->get('show_fax')||$this->params->get('show_mobile')|| $this->params->get('show_webpage') ) : ?>
	<div class="contact-contactinfo clearafter">
<?php endif; ?>
<?php if ($this->contact->email_to && $this->params->get('show_email')) : ?>
	<p class="contact-emailto">
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_email'); ?>
		</span>
		<?php echo $this->contact->email_to; ?>
	</p>
<?php endif; ?>

<?php if ($this->contact->telephone && $this->params->get('show_telephone')) : ?>
	<p class="contact-telephone">
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_telephone'); ?>
		</span>
		<?php echo nl2br($this->contact->telephone); ?>
	</p>
<?php endif; ?>
<?php if ($this->contact->fax && $this->params->get('show_fax')) : ?>
	<p class="contact-fax">
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_fax'); ?>
		</span>
		<?php echo nl2br($this->contact->fax); ?>
	</p>
<?php endif; ?>
<?php if ($this->contact->mobile && $this->params->get('show_mobile')) :?>
	<p class="contact-mobile">
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_mobile'); ?>
		</span>
		<?php echo nl2br($this->contact->mobile); ?>
	</p>
<?php endif; ?>
<?php if ($this->contact->webpage && $this->params->get('show_webpage')) : ?>
	<p class="contact-webpage">
		<span class="<?php echo $this->params->get('marker_class'); ?>">
		</span>
		<a href="<?php echo $this->contact->webpage; ?>" target="_blank">
		<?php echo $this->contact->webpage; ?></a>
	</p>
<?php endif; ?>
<?php if($this->params->get('show_email') || $this->params->get('show_telephone')||$this->params->get('show_fax')||$this->params->get('show_mobile')|| $this->params->get('show_webpage') ) : ?>
	</div>
<?php endif; ?>