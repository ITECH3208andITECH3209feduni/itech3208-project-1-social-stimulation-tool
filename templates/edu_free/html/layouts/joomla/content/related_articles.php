<?php

defined('_JEXEC') or die;

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;

$articles = $displayData['articles'];
$mainItem = $displayData['item'];
$template = Helper::loadTemplateData();
$tmpl_params = $template->params;

?>
<div class="related-article-list-container uk-margin-medium-top">
    <h3 class="uk-h3 uk-heading-bullet"><?php echo $tmpl_params->get('related_article_title'); ?></h3>
    <?php if( $tmpl_params->get('related_article_view_type') === 'thumb' ): ?> 
        <div class="uk-child-width-1-1 uk-grid-medium uk-grid-divider uk-grid-match" uk-grid>
            <?php foreach( $articles as $item ): ?>
					<?php 
						if (strtotime($item->publish_up) > strtotime(Factory::getDate())) {
							continue;
						}
					?>
                <div>
                <?php echo LayoutHelper::render('joomla.content.related_article', $item); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if( $tmpl_params->get('related_article_view_type') === 'list' ): ?> 
        <ul class="uk-list uk-list-divider">
        <?php foreach( $articles as $item ): ?>
            <li>
            <?php echo LayoutHelper::render('joomla.content.blog_style_default_related_item_title', $item); ?>
            <p class="el-meta uk-text-muted uk-margin-small-top">
            <?php echo LayoutHelper::render('joomla.content.info_block.publish_date', array('item' => $item, 'params' => $item->params,'articleView'=>'intro')); ?>
            </p>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

	<?php if( $tmpl_params->get('related_article_view_type') === 'large' ): ?> 
		<div class="article-list related-article-list">
			<?php foreach( $articles as $item ): ?> 
				<div class="row">
					<div class="col-12">
						<?php echo LayoutHelper::render('joomla.content.related_article_large', $item); ?>
					</div>  
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

</div>
 