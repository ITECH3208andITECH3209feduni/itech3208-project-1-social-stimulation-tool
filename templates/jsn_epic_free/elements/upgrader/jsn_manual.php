<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<jdoc:include type="message" />
<form method="POST" action="index.php?template=<?php echo $this->template; ?>&amp;tmpl=jsn_upgrade&amp;template_style_id=<?php echo $templateStyleId; ?>&amp;manual=true" id="frm-manual-upgrade" name="frm_manual_upgrade" autocomplete="off" enctype="multipart/form-data" class="install-sample-data-form">
	<div id="jsn-wrap-installation-content">
		<div id="jsn-sampledata-login">
			<h2><?php echo JText::_('JSN_UPGRADE_MANUAL_HEADING_STEP2'); ?></h2>
			<div class="jsn-message" id="jsn-upgrade-template-message">
				<ol>
					<li><?php echo JText::sprintf('JSN_UPGRADE_MANUAL_UPLOAD_MES1', JSN_TEMPLATE_CUSTOMER_AREA_URL); ?></li>
					<li>
						<?php echo JText::_('JSN_UPGRADE_MANUAL_UPLOAD_MES2'); ?>
						<input type="file" name="package" id="package" size="35" onchange="JSNTemplateUpgraderUtil.setNextButtonState(this.form);"/>
					</li>
				</ol>
			</div>
			<input type="hidden" name="task" value="manual_upgrade" />
			<?php echo JHTML::_('form.token'); ?>
			<hr class="jsn-horizontal-line" />
			<div class="jsn-install-admin-check">
				<div class="jsn-install-admin-navigation">
					<button class="action-submit" type="submit" id="jsn-upgrade-next-step-button" name="next_step_button" disabled="disabled" onclick="JSNTemplateUpgraderUtil.disableNextButtonOnSubmit(this, '<?php echo JText::_('JSN_UPGRADE_SUBMITTING_BUTTON'); ?>');"><?php echo JText::_('JSN_UPGRADE_NEXT_BUTTON'); ?></button>
				</div>
			</div>
		</div>
	</div>
</form>
