<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Component\Tags\Site\Helper\RouteHelper;
use Joomla\Registry\Registry;

// Get authorized view levels for current user
$authorised = Factory::getUser()->getAuthorisedViewLevels();

// Only proceed if we have tags to display
if (empty($displayData)) {
    return;
}
?>

<?php foreach ($displayData as $i => $tag) : ?>
    <?php if (in_array($tag->access, $authorised)) : ?>
        <?php 
        // Determine separator (comma after all tags except the last one)
        $separator = ($i < count($displayData) - 1) ? ',' : '';
        
        // Parse tag parameters
        $tagParams = new Registry($tag->params);
        
        // Get and clean link class (remove legacy 'label' classes)
        $link_class = trim(str_replace(['label', 'label-info'], '', $tagParams->get('tag_link_class', 'label')));
        
        // Build tag route
        $tagRoute = Route::_(RouteHelper::getTagRoute($tag->tag_id . ':' . $tag->alias));
        ?>
        <a href="<?php echo $tagRoute; ?>" class="<?php echo $link_class; ?>" property="keywords" title="<?php echo $this->escape($tag->title); ?>"><?php echo $this->escape($tag->title); ?></a><?php echo $separator; ?>
    <?php endif; ?>
<?php endforeach; ?>