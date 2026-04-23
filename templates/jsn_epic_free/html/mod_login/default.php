<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive');
?>
<?php if ($type == 'logout') : ?>

<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="form-login">
	<?php if ($params->get('greeting')) : ?>
	<div class="login-greeting">
		<?php if($params->get('name') == 0) : {
		echo JText::sprintf('MOD_LOGIN_HINAME', $user->get('name'));
	} else : {
		echo JText::sprintf('MOD_LOGIN_HINAME', $user->get('username'));
	} endif; ?>
	</div>
	<?php endif; ?>
	<div class="logout-button button-wrapper">
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?> </div>
</form>
<?php else : ?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="form-login">
	<?php if ($params->get('pretext')): ?>
	<div class="pretext">
		<p><?php echo $params->get('pretext'); ?></p>
	</div>
	<?php endif; ?>
	<fieldset class="input userdata">
		<p id="form-login-username">
			<label for="modlgn_username"><?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?></label>
			<br />
			<input id="modlgn-username" type="text" name="username" class="inputbox"  size="18" />
		</p>
		<p id="form-login-password">
			<label for="modlgn_passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
			<br />
			<input id="modlgn-passwd" type="password" name="password" class="inputbox" size="18"  />
		</p>
		<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
		<p id="form-login-remember">
			<input id="modlgn_remember" type="checkbox" name="remember" value="yes" alt="Remember Me" />
			<label for="modlgn_remember"><?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?></label>
		</p>
		<?php endif; ?>
		<div class="button-wrapper">
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGIN') ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
		</div>
	</fieldset>
	<ul>
		<li> <a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"> <?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a> </li>
		<li> <a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"> <?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a> </li>
		<?php
		$usersConfig = JComponentHelper::getParams('com_users');
		if ($usersConfig->get('allowUserRegistration')) : ?>
		<li> <a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>"> <?php echo JText::_('MOD_LOGIN_REGISTER'); ?></a> </li>
		<?php endif; ?>
	</ul>
	<?php if ($params->get('posttext')): ?>
	<div class="posttext">
		<p><?php echo $params->get('posttext'); ?></p>
	</div>
	<?php endif; ?>
</form>
<?php endif; ?>