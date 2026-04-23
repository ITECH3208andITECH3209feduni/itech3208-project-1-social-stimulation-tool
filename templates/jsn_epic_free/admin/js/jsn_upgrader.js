/**
 * This type of declaration will allow calls like
 * JSNTemplateUpgraderUtil.function()
 */
var JSNTemplateUpgraderUtil = {
	init: function() {},

	disableNextButtonOnSubmit: function(button, replaceText) {
		button.disabled = true;
		if (typeof replaceText !== "undefined")
		{
			button.set("html", replaceText);
		}
	},

	setNextButtonState: function(form, button) {
		var task          = form.task.value;
		var buttonDisable = true;

		if (typeof button === "undefined")
		{
			button = form.next_step_button;
		}

		switch (task)
		{
			case "edition_select":
				/* take care of the selection list */
				var edition = form.jsn_upgrade_edition.value;
				if (edition != '')
				{
					buttonDisable = false;
				}
				break;

			case "manual_upgrade":
				/* take care of the file selector */
				if (form.package.value != "")
				{
					buttonDisable = false;
				}
				break;

			default:
				/* take care of username password input boxes */
				var username = form.username.value;
				var password = form.password.value;
				if (username != '' && password != '')
				{
					buttonDisable = false;
				}
				break;
		}

		button.disabled = buttonDisable;
	}
};

var JSNTemplateUpgrader =  new Class({
	template_name: "",
	template_style_id: "",
	url: "",

	initialize: function(template_name, template_style_id, url) {
		this.template_name = template_name;
		this.template_style_id = template_style_id;
		this.url = url;
	},

	getCustomerPackageEditions: function(data, button, oldButtonText) {
		var login_form          = $("frm-login");
		var error_mes_container = $("system-message-container");

		var jsonRequest = new Request.JSON({
    		url: this.url + "?template=" + this.template_name + "&tmpl=jsn_upgrade&template_style_id=" + this.template_style_id + "&rand=" + Math.random(),
    		data: data,
    		onComplete: function(jsonObj)
    		{
    			if (jsonObj.authenticated)
    			{
    				error_mes_container.set("html", "");
        			if (jsonObj.multiple)
        			{
        				/* Disable old login inputs & button */
        				$("username").disabled = true;
        				$("password").disabled = true;
        				$("jsn-upgrade-old-button-wrapper").addClass("jsn-updater-display-none");

        				/*  Change submit task */
        				$("jsn-upgrade-submit-task").value = "edition_select";

        				/* Add template edition to list and show the selection */
        				jsonObj.editions.each(function(item) {
        					var newoption = new Option(item, item.toLowerCase());
        					$("jsn-upgrade-edition-select").add(newoption, null);
        				});
        				$("jsn-upgrade-edition-wrapper").removeClass("jsn-updater-display-none");
        				$("jsn-upgrade-edition-select").focus();
        			}
        			else
        			{
        				/* Reload page to do upgrade process */
        				window.location = login_form.get("action");
        			}
        		}
        		else
        		{
        			/* Add error message to the container */
        			error_mes_container.set("html", "<dl id=\"system-message\"><dt class=\"error\">Error</dt><dd class=\"error message\"><ul><li>" + jsonObj.message + "</li></ul></dd></dl>");
        			button.set("html", oldButtonText);
        			button.disabled = false;
        			$("username").value = "";
    				$("password").value = "";
        			$("username").focus();
        		}
    		}
    	}).post();
	},

	downloadTemplatePackage: function() {
		var icon_element 		= $("jsn-download-package");
		var subtitle_element 	= $("jsn-download-package-subtitle");
		var message_element 	= $("jsn-download-package-message");

		icon_element.addClass("jsn-icon-small-loader");

		var self = this;
		self.toggleCancelButton(false);

		var jsonRequest = new Request.JSON({
			url: this.url + "?template=" + this.template_name + "&tmpl=jsn_upgrade&task=ajax_download_package&template_style_id=" + this.template_style_id + "&rand=" + Math.random(),
			onSuccess: function(jsonObj)
			{
				if (jsonObj.download)
				{
					icon_element.removeClass("jsn-icon-small-loader");
					icon_element.addClass("jsn-icon-small-successful");
					subtitle_element.addClass("jsn-successful-subtitle");

					var auto_upgrade_li_element = $("jsn-upgrade-template-li");
					auto_upgrade_li_element.removeClass("jsn-updater-display-none");

					self.installTemplate();
				}
				else
				{
					icon_element.removeClass("jsn-icon-small-loader");
					icon_element.addClass("jsn-icon-small-error");
					message_element.set("html", jsonObj.message);

					if (jsonObj.manual)
					{
						self.toggleCancelButton(true);

						var manual_upgrade_container = $("jsn-upgrade-failed-container");
						manual_upgrade_container.removeClass("jsn-updater-display-none");

						$("jsn-upgrade-next-step-button").setProperty("disabled", "disabled");
					}
					else
					{
						$("jsn-upgrade-finish-button").set("html", "Close");
						$("jsn-upgrade-finish-button-wrapper").removeClass("jsn-updater-display-none");
					}
				}
			}
		}).post();
	},

	installTemplate: function() {
		var upgrade_template_icon     = $("jsn-upgrade-template");
		var upgrade_template_message  = $("jsn-upgrade-template-message");
		var upgrade_template_subtitle = $("jsn-upgrade-template-subtitle");

		upgrade_template_icon.addClass("jsn-icon-small-loader");

		var self = this;
		var jsonRequest = new Request.JSON({
			url: this.url + "?template=" + this.template_name + "&tmpl=jsn_upgrade&task=ajax_install_pro&template_style_id=" + this.template_style_id + "&rand=" + Math.random(),
			onSuccess: function(jsonObj)
			{
				if (jsonObj.install)
				{
					upgrade_template_icon.removeClass("jsn-icon-small-loader");
					upgrade_template_icon.addClass("jsn-icon-small-successful");
					upgrade_template_subtitle.addClass("jsn-successful-subtitle");

					var migrate_settings_li_element = $("jsn-migrate-settings-li");
					migrate_settings_li_element.removeClass("jsn-updater-display-none");

					self.migrateTemplateSettings();
				}
				else
				{
					upgrade_template_message.set("html", jsonObj.message);
					upgrade_template_icon.removeClass("jsn-icon-small-loader");
					upgrade_template_icon.addClass("jsn-icon-small-error");
					upgrade_template_subtitle.removeClass("jsn-successful-subtitle");

					$("jsn-upgrade-finish-button").set("html", "Close");
					$("jsn-upgrade-finish-button-wrapper").removeClass("jsn-updater-display-none");
				}
			}
		}).post();
	},

	migrateTemplateSettings: function() {
		var migrate_setting_icon       = $("jsn-migrate-settings");
		var migrate_setting_message    = $("jsn-migrate-settings-message");
		var migrate_setting_subtitle   = $("jsn-migrate-settings-subtitle");

		var upgrade_succesfully_container = $("jsn-upgrade-succesfully-container");
		var pro_template_style_id        = $("jsn-pro-template-style-id");

		migrate_setting_icon.addClass("jsn-icon-small-loader");

		var self = this;
		var jsonRequest = new Request.JSON({
			url: this.url + "?template=" + this.template_name + "&tmpl=jsn_upgrade&task=ajax_migrate_settings&template_style_id=" + this.template_style_id + "&rand=" + Math.random(),
			onSuccess: function(jsonObj)
			{
				if (jsonObj.migrate)
				{
					migrate_setting_icon.removeClass("jsn-icon-small-loader");
					migrate_setting_icon.addClass("jsn-icon-small-successful");
					migrate_setting_subtitle.addClass("jsn-successful-subtitle");

					upgrade_succesfully_container.removeClass('jsn-updater-display-none');
					$("jsn-upgrade-finish-button-wrapper").removeClass('jsn-updater-display-none');

					if (jsonObj.new_template_style_id)
					{
						pro_template_style_id.value = jsonObj.new_template_style_id;
					}
				}
				else
				{
					migrate_setting_message.set("html", jsonObj.message);
					migrate_setting_icon.removeClass("jsn-icon-small-loader");
					migrate_setting_icon.addClass("jsn-icon-small-error");
					migrate_setting_subtitle.removeClass("jsn-successful-subtitle");
				}
			}
		}).post();
	},

	toggleCancelButton: function(toggle) {
		var element_cancel = $("jsn-upgrade-cancel");
		if (toggle)
		{
			element_cancel.removeClass("jsn-updater-display-none");
		}
		else
		{
			element_cancel.addClass("jsn-updater-display-none");
		}
	}
});
