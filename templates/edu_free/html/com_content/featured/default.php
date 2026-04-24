<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Language\Text;

$template = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
$tmpl_params = $template->params;

$blog_divider = $tmpl_params->get('blog_divider') && $tmpl_params->get('blog_list_grid_column_gap') != 'collapse' && $tmpl_params->get('blog_list_grid_row_gap') != 'collapse' ? ' uk-grid-divider' : '';
$center_columns = $tmpl_params->get('center_columns') ? ' uk-flex-center' : '';
$center_rows = $tmpl_params->get('center_rows') ? ' uk-flex-middle' : '';

$grid = '';
$grid .= $center_columns . $center_rows . $blog_divider;

$grid_column_gap = $tmpl_params->get('blog_list_grid_column_gap', '');
$grid_row_gap    = $tmpl_params->get('blog_list_grid_row_gap', '');

if ( $grid_column_gap == $grid_row_gap ) {
	$grid .= ( ! empty( $grid_column_gap ) && ! empty( $grid_row_gap ) ) ? ' uk-grid-' . $grid_column_gap : '';
} else {
	$grid .= ! empty( $grid_column_gap ) ? ' uk-grid-column-' . $grid_column_gap : '';
	$grid .= ! empty( $grid_row_gap ) ? ' uk-grid-row-' . $grid_row_gap : '';
}

$blog_list_breakpoint = $tmpl_params->get('blog_list_breakpoint', 'm');

$blog_masonry = $tmpl_params->get('blog_masonry') ? 'masonry: true;' : '';
$blog_parallax = $tmpl_params->get('blog_parallax', '');
$blog_parallax_cls = $blog_parallax ? ' parallax: '.$blog_parallax : '';

$init_style = '';
if($blog_masonry && $blog_parallax) {
	$init_style .= ' style="box-sizing:initial;"';
}

$grid_row = $tmpl_params->get('blog_list_grid_row_gap') ? ' uk-grid-'.$tmpl_params->get('blog_list_grid_row_gap') : '';

?>
<div class="blog-featured<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Blog" uk-height-viewport="expand: true">
<?php if ($this->params->get('show_page_heading') != 0) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
<?php endif; ?>

<?php $leadingcount = 0; ?>
<?php if (!empty($this->lead_items)) : ?>
<div class="uk-child-width-1-1<?php echo $grid_row; ?>" uk-grid>
	<?php foreach ($this->lead_items as &$item) : ?>
	<div>
		<div id="leading-<?php echo $leadingcount; ?>" class="uk-article<?php echo $item->state == 0 ? ' system-unpublished' : null; ?> clearfix"
			itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
			<?php
				$this->item = &$item;
				$this->item->leading = true;
				echo $this->loadTemplate('item');
			?>
		</div>
	</div>
		<?php
			$leadingcount++;
		?>
	<?php endforeach; ?>
</div>
<?php endif; ?>
<?php
	$introcount = count($this->intro_items);
	$counter = 0;
	$this->columns = $this->params->get('num_columns');
?>
<?php if (!empty($this->intro_items)) : ?>

	<?php if($this->columns > 1) : ?>
		<div class="uk-child-width-1-<?php echo (int) $this->columns; ?>@<?php echo $blog_list_breakpoint; echo $grid; ?>" uk-grid="<?php echo $blog_masonry . $blog_parallax_cls; ?>"<?php echo $init_style; ?>>
	<?php else: ?>
		<div class="uk-child-width-1-1<?php echo $grid_row; ?>" uk-grid>
	<?php endif; ?>	

	<?php foreach ($this->intro_items as $key => &$item) : ?>

		<?php
		$key = ($key - $leadingcount) + 1;
		$rowcount = (((int) $key - 1) % (int) $this->columns) + 1;
		$row = $counter / $this->columns;

		if ($rowcount === 1) : ?>

		<?php endif; ?>
			<div>
			<div id="article-<?php echo $item->id; ?>" class="uk-article<?php echo $item->state == 0 ? ' system-unpublished' : null; ?>"
				itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
			<?php
					$this->item = &$item;
					echo $this->loadTemplate('item');
			?>
			</div>
			</div>
			<?php $counter++; ?>

			<?php if (($rowcount == $this->columns) or ($counter == $introcount)) : ?>

		<?php endif; ?>

	<?php endforeach; ?>
	</div>
<?php endif; ?>

<?php if (!empty($this->link_items)) : ?>
	<div class="uk-margin-large<?= $tmpl_params->get('detail_center_content') ? ' uk-text-center' : '' ?>">
		<h3><?= Text::_('COM_CONTENT_MORE_ARTICLES') ?></h3>
		<?php echo $this->loadTemplate('links'); ?>
	</div>
<?php endif; ?>

<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->pagesTotal > 1)) : ?>
	<?php echo $this->pagination->getPagesLinks(); ?>
	<?php if ($this->params->def('show_pagination_results', 1)) : ?>
		<div class="uk-text-center">
			<?php echo $this->pagination->getPagesCounter(); ?>
		</div>
	<?php endif; ?>
<?php endif; ?>

</div>