<?php 
defined('_JEXEC') or die;
$lang = JFactory::getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();
?>

<div class="com-search <?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<form id="searchForm" action="<?php echo JRoute::_( 'index.php?option=com_search' );?>" method="post" name="searchForm">
		<div class="contentpaneopen<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
			<p class="search-keyword">
				<label for="search_searchword"><?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?></label>
				<input type="text" name="searchword" id="search_searchword" size="30" maxlength="<?php echo $upper_limit; ?>" value="<?php echo $this->escape($this->searchword); ?>" class="inputbox" />
				<button name="Search" onclick="this.form.submit()" class="button"><?php echo JText::_( 'COM_SEARCH_SEARCH' );?></button>
			</p>
			<p class="searchintro<?php echo $this->params->get('pageclass_sfx'); ?>"></p>
			<p class="search-phrase"><?php echo $this->lists['searchphrase']; ?></p>
			<p class="search-ordering">
				<label for="ordering"><?php echo JText::_( 'COM_SEARCH_ORDERING' );?></label>
				<?php echo $this->lists['ordering'];?> </p>
			<?php if ($this->params->get( 'search_areas', 1 )) : ?>
			<p class="search-range clearafter"> <span class="search-field-title"><?php echo JText::_( 'COM_SEARCH_SEARCH_ONLY' );?></span>
				<?php foreach ($this->searchareas['search'] as $val => $txt) :
						$checked = is_array( $this->searchareas['active'] ) && in_array( $val, $this->searchareas['active'] ) ? 'checked="checked"' : '';
					?>
				<label for="area_<?php echo $val;?>">
					<input type="checkbox" name="areas[]" value="<?php echo $val;?>" id="area_<?php echo $val;?>" <?php echo $checked;?> />
					<?php echo JText::_($txt); ?> </label>
				<?php endforeach; ?>
			</p>
			<?php if($this->total > 0) : ?>
			<div align="center" class="clearafter">
				<?php if (!empty($this->searchword)):?>
				<div style="float: left"><?php echo JText::plural('COM_SEARCH_SEARCH_KEYWORD_N_RESULTS', $this->total);?></div>
				<?php endif;?>
				<div style="float: right;">
					<label for="limit"> <?php echo JText::_( 'JGLOBAL_DISPLAY_NUM' ); ?> </label>
					<?php echo $this->pagination->getLimitBox( ); ?> </div>
				<div> <?php echo $this->pagination->getPagesCounter(); ?> </div>
			</div>
			<?php endif; ?>
			<?php endif; ?>
		</div>
		<input type="hidden" name="task"   value="search" />
	</form>
</div>