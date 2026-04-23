<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div id="jsn-wrap-installation-content">
	<div id="jsn-auto-updater">
		<h2><?php echo JText::sprintf('JSN_UPGRADE_HEADING_STEP3', 'FREE', $upgradeEdition); ?></h2>
		<p><?php echo JText::_('JSN_UPGRADE_SEVERAL_STEP'); ?></p>
		<ul>
			<?php if (!$manualUpgrade) : ?>
			<li id="jsn-download-package-li">
				<span id="jsn-download-package-subtitle"><?php echo JText::_('JSN_UPGRADE_DOWNLOAD_PACKAGE'); ?></span>
				<span id="jsn-download-package"></span>
				<span class="jsn-message" id="jsn-download-package-message"></span>
			</li>
		<?php endif; ?>
			<li id="jsn-upgrade-template-li" <?php if (!$manualUpgrade) : ?> class="jsn-updater-display-none" <?php endif; ?>>
				<span id="jsn-upgrade-template-subtitle"><?php echo JText::_('JSN_UPGRADE_INSTALL_TEMPLATE_FILES'); ?></span>
				<span id="jsn-upgrade-template"></span>
				<span class="jsn-message" id="jsn-upgrade-template-message"></span>
			</li>
			<li id="jsn-migrate-settings-li" class="jsn-updater-display-none">
				<span id="jsn-migrate-settings-subtitle"><?php echo JText::_('JSN_UPGRADE_MIGRATE_SETTINGS'); ?></span>
				<span id="jsn-migrate-settings"></span>
				<span class="jsn-message" id="jsn-migrate-settings-message"></span>
			</li>
		</ul>
	</div>
	<div id="jsn-upgrade-succesfully-container" class="jsn-updater-display-none">
		<hr class="jsn-horizontal-line" />
		<h3 class="jsn-green-heading"><?php echo JText::_('JSN_UPGRADE_SUCCESS_TITLE'); ?></h3>
		<p><?php echo JText::sprintf('JSN_UPGRADE_SUCCESS_MES', $upgradeEdition); ?></p>
		<input type="hidden" id="jsn-pro-template-style-id" value="" />
	</div>
	<div id="jsn-upgrade-failed-container" class="jsn-updater-display-none">
		<form method="POST" action="index.php?template=<?php echo $this->template; ?>&amp;tmpl=jsn_upgrade&amp;template_style_id=<?php echo $templateStyleId; ?>" id="frm-manual-upgrade" name="frm_manual_upgrade" autocomplete="off" enctype="multipart/form-data" class="install-sample-data-form">
			<div class="jsn-message" id="jsn-upgrade-template-message">
				<ol>
					<li><?php echo JText::sprintf('JSN_UPGRADE_MANUAL_UPLOAD_MES1', JSN_TEMPLATE_CUSTOMER_AREA_URL); ?></li>
					<li>
						<?php echo JText::_('JSN_UPGRADE_MANUAL_UPLOAD_MES2'); ?>
						<input type="file" name="package" id="package" size="35" onchange="JSNTemplateUpgraderUtil.setNextButtonState(this.form);"/>
					</li>
				</ol>
			</div>
			<hr class="jsn-horizontal-line" />
			<input type="hidden" name="task" value="manual_upgrade" />
			<div style="text-align: center">
				<button class="action-submit" type="button" id="jsn-upgrade-next-step-button" name="next_step_button" onclick="JSNTemplateUpgraderUtil.disableNextButtonOnSubmit(this, '<?php echo JText::_('JSN_UPGRADE_SUBMITTING_BUTTON'); ?>'); this.form.submit();"><?php echo JText::_('JSN_UPGRADE_NEXT_BUTTON'); ?></button>
			</div>
		</form>
	</div>
	<div class="jsn-updater-display-none" id="jsn-upgrade-finish-button-wrapper">
		<hr class="jsn-horizontal-line" />
		<button class="action-submit" type="button" id="jsn-upgrade-finish-button" name="jsn_finish_button" onclick="JSNTemplateUpgraderUtil.disableNextButtonOnSubmit(this);"><?php echo JText::_('JSN_UPGRADE_FINISH_BUTTON'); ?></button>
	</div>
</div>

<script type="text/javascript">
	window.addEvent("domready", function() {
		var upgrader = new JSNTemplateUpgrader('<?php echo $this->template; ?>', '<?php echo $templateStyleId; ?>', '<?php echo $frontIndexPath; ?>');
		<?php if (!$manualUpgrade) : ?>
		upgrader.downloadTemplatePackage();
		<?php else : ?>
		upgrader.installTemplate();
		<?php endif; ?>

		$("jsn-upgrade-finish-button").addEvent('click', function() {
			if ($(this).get("text") == "Close")
			{
				/* Send AJAX request to clear session */
				var jsonRequest = new Request.JSON({
					url: "<?php echo  $frontIndexPath; ?>" + "?template=" + "<?php echo $this->template; ?>" + "&tmpl=jsn_upgrade&task=ajax_destroy_sesison&template_style_id=" + "<?php echo $templateStyleId; ?>" + "&rand=" + Math.random(),
					onSuccess: function(jsonObj)
					{
						if (jsonObj.sessionclear)
						{
							window.top.SqueezeBox.close();
						}
					}
				}).post();
			}
			else
			{
				var style_id = $("jsn-pro-template-style-id").value;
				if (style_id != "")
				{
					window.top.location = "<?php echo JURI::base() . 'administrator/index.php?option=com_templates&task=style.edit&id=' ?>" + style_id;
				}
				else
				{
					window.top.SqueezeBox.close();
				}
			}
		});
	});
</script>
