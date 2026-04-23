<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.5
 */

defined('_JEXEC') or die;
 ?>
<div class="com-user <?php echo $this->params->get('pageclass_sfx') ?>">
	<div class="default-login">
	<?php
		if ($this->user->get('guest')):
			// The user is not logged in.
			echo $this->loadTemplate('login');
		else:
			// The user is already logged in.
			echo $this->loadTemplate('logout');
		endif;
	?>
	</div>
</div>