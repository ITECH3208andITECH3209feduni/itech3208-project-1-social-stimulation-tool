<?php
/**
* @Copyright Copyright (C) 2012 Alfred Bösch
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
$jinput = JFactory::getApplication()->input;
$searchFilter = trim($jinput->get('searchFilter', ''));
$categoryFilter = trim($jinput->get('categoryFilter', ''));
$publishedFilter = trim($jinput->get('publishedFilter', ''));
$languageFilter = $jinput->get('languageFilter', '');
$listOrder	= $jinput->get('filter_order', 'a.title');
$listDirn = $jinput->get('filter_order_Dir', 'ASC');
?>
<form action="<?php echo JRoute::_( 'index.php' );?>" method="post" name="adminForm" id="adminForm">
<div style="background-color: #EBF5FA; border-bottom: 1px solid #ccc; padding: 5px 3px 10px 3px;">
	<table>
		<colgroup>
			<col width="20%" />
			<col width="80%" />
		</colgroup>
		<tbody>
			<tr>
				<td>
					<label for="searchFilter" style="width: 100px;"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
				</td>
				<td>
					<input type="text" name="searchFilter" id="searchFilter" value="<?php echo $searchFilter; ?>" />
					<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
					<button type="button" onclick="document.id('searchFilter').value='';this.form.submit();"><?php echo JText::_('JCLEAR'); ?></button>
				</td>
			</tr>
			<tr>
				<td>
					<label for="categoryFilter"><?php echo JText::_('JCATEGORY'); ?>:</label>
				</td>
				<td>
					<select name="categoryFilter" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
						<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', $categoryFilter); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="publishedFilter"><?php echo JText::_('JSTATUS'); ?>:</label>
				</td>
				<td>
					<select name="publishedFilter" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
						<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $publishedFilter, true); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="languageFilter"><?php echo JText::_('JFIELD_LANGUAGE_LABEL'); ?>:</label>
				</td>
				<td>
					<select name="languageFilter" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
						<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $languageFilter);?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<table class="adminlist">
	<thead>
		<tr>
			<th>
				<?php echo JHtml::_('grid.sort', 'MOD_BO_RESETARTICLEHITS_ITEMS', 'a.title', $listDirn, $listOrder); ?>
			</th>
			<th>
				<?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
			</th>
		</tr>
	</thead>
<?php if (count($list)) : ?>
	<tbody>
	<?php foreach ($list as $i=>$item) : ?>
		<tr>
			<th scope="row">
				<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time); ?>
				<?php endif; ?>

				<?php if ($item->link) :?>
					<a href="<?php echo $item->link; ?>">
						<?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8');?></a>
				<?php else :
					echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8');
				endif; ?>
			</th>
			<td class="center">
				<input type="text" name="newArticleHits[<?php echo $item->id; ?>]" size="6" maxlength="6" style="padding-top: 2px;" value="<?php echo $item->hits;?>" />
				<button type="submit" name="articleId" value="<?php echo $item->id; ?>"><?php echo JText::_('MOD_BO_RESETARTICLEHITS_RESET'); ?></button>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
<?php else : ?>
	<tbody>
		<tr>
			<td colspan="2">
				<p class="noresults"><?php echo JText::_('MOD_BO_RESETARTICLEHITS_NO_MATCHING_RESULTS');?></p>
			</td>
		</tr>
	</tbody>
<?php endif; ?>
</table>
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</form>