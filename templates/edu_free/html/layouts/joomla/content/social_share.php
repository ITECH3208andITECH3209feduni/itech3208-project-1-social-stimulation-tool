<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;

// Build article URL
$url = Route::_(RouteHelper::getArticleRoute($displayData->id . ':' . $displayData->alias, $displayData->catid, $displayData->language));

// Convert to absolute URL
$root = Uri::base();
$rootUri = new Uri($root);
$url = $rootUri->getScheme() . '://' . $rootUri->getHost() . $url;

// Load template data
$template = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
$tmpl_params = $template->params;

// Get social share settings
$socialShares = $tmpl_params->get('social_share_lists');

// Only display if social sharing is enabled and platforms are configured
if (is_array($socialShares) && $tmpl_params->get('social_share')) :
    // Prepare encoded title for sharing
    $encodedTitle = rawurlencode($displayData->title);
?>
<div class="article-social-share">
    <ul class="uk-grid-small uk-flex-inline uk-flex-middle uk-flex-nowrap" uk-grid>
        <?php foreach ($socialShares as $socialSite) : ?>
            <?php if ($socialSite === 'facebook') : ?>
                <li>
                    <a class="uk-icon-button facebook" 
                       href="https://www.facebook.com/sharer.php?u=<?php echo $url; ?>" 
                       title="<?php echo Text::_('HELIX_ULTIMATE_SHARE_FACEBOOK'); ?>"
                       onclick="window.open(this.href,'Facebook','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;"
                       rel="noopener noreferrer"
                       target="_blank"
                       aria-label="<?php echo Text::_('HELIX_ULTIMATE_SHARE_FACEBOOK'); ?>">
                        <span class="fab fa-facebook" aria-hidden="true"></span>
                    </a>
                </li>
            <?php endif; ?>
            
            <?php if ($socialSite === 'twitter') : ?>
                <li>
                    <a class="uk-icon-button twitter" 
                       href="https://twitter.com/share?url=<?php echo $url; ?>&amp;text=<?php echo $encodedTitle; ?>" 
                       title="<?php echo Text::_('HELIX_ULTIMATE_SHARE_TWITTER'); ?>"
                       onclick="window.open(this.href,'Twitter share','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;"
                       rel="noopener noreferrer"
                       target="_blank"
                       aria-label="<?php echo Text::_('HELIX_ULTIMATE_SHARE_TWITTER'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" style="width: 13.56px; position: relative; top: -1.5px;">
                            <path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/>
                        </svg>
                    </a>
                </li>
            <?php endif; ?>
            
            <?php if ($socialSite === 'linkedin') : ?>
                <li>
                    <a class="uk-icon-button linkedin" 
                       href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>" 
                       title="<?php echo Text::_('HELIX_ULTIMATE_SHARE_LINKEDIN'); ?>"
                       onclick="window.open(this.href,'Linkedin','width=585,height=666,left='+(screen.availWidth/2-292)+',top='+(screen.availHeight/2-333)+''); return false;"
                       rel="noopener noreferrer"
                       target="_blank"
                       aria-label="<?php echo Text::_('HELIX_ULTIMATE_SHARE_LINKEDIN'); ?>">
                        <span class="fab fa-linkedin" aria-hidden="true"></span>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>