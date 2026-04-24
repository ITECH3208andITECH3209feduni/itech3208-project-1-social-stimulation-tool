<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   (C) 2011 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Finder\Site\View\Search\HtmlView $this */
/*
* This segment of code sets up the autocompleter.
*/
if ($this->params->get('show_autosuggest', 1)) {
    $this->getDocument()->getWebAssetManager()->usePreset('awesomplete');
    $this->getDocument()->addScriptOptions('finder-search', ['url' => Route::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component', false)]);

    Text::script('COM_FINDER_SEARCH_FORM_LIST_LABEL');
    Text::script('JLIB_JS_AJAX_ERROR_OTHER');
    Text::script('JLIB_JS_AJAX_ERROR_PARSE');
}

?>

<form action="<?php echo Route::_($this->query->toUri()); ?>" method="get" class="js-finder-searchform">
    <?php echo $this->getFields(); ?>
	<fieldset class="uk-margin">
		<div class="uk-grid-small" uk-grid>
			
            <div class="uk-width-expand@s">

                <div class="uk-search uk-search-default uk-width-1-1">
					<input type="text" name="q" id="q" class="js-finder-search-query uk-search-input" value="<?php echo $this->escape($this->query->input); ?>">
                </div>

            </div>
            <div class="uk-width-auto@s">

                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-auto@s">
                        <button type="submit" class="uk-button uk-button-primary uk-width-1-1">
                            <span class="icon-search icon-white" aria-hidden="true"></span>
                            <?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>
                        </button>                        
                    </div>
                    <?php if ($this->params->get('show_advanced', 1)) : ?>
                    <div class="uk-width-auto@s">
                    <button class="uk-button uk-button-default uk-width-1-1" type="button" data-bs-toggle="collapse" data-bs-target="#advancedSearch" aria-expanded="<?php echo ($this->params->get('expand_advanced', 0) ? 'true' : 'false'); ?>">
                        <span class="icon-search-plus" aria-hidden="true"></span>
                        <?php echo Text::_('COM_FINDER_ADVANCED_SEARCH_TOGGLE'); ?></button>
					</div>
                    <?php endif ?>
                </div>

            </div>
		</div>
	</fieldset>

    <?php if ($this->params->get('show_advanced', 1)) : ?>
        <fieldset id="advancedSearch" class="com-finder__advanced js-finder-advanced collapse<?php if ($this->params->get('expand_advanced', 0)) {
            echo ' hidden';
                                                                                             } ?>">
            <legend class="com-finder__search-advanced visually-hidden">
                <?php echo Text::_('COM_FINDER_SEARCH_ADVANCED_LEGEND'); ?>
            </legend>
            <?php if ($this->params->get('show_advanced_tips', 1)) : ?>
                <div class="com-finder__tips mb-3">
                    <div>
                        <?php echo Text::_('COM_FINDER_ADVANCED_TIPS_INTRO'); ?>
                        <?php echo Text::_('COM_FINDER_ADVANCED_TIPS_AND'); ?>
                        <?php echo Text::_('COM_FINDER_ADVANCED_TIPS_NOT'); ?>
                        <?php echo Text::_('COM_FINDER_ADVANCED_TIPS_OR'); ?>
                        <?php if ($this->params->get('tuplecount', 1) > 1) : ?>
                            <?php echo Text::_('COM_FINDER_ADVANCED_TIPS_PHRASE'); ?>
                        <?php endif; ?>
                        <?php echo Text::_('COM_FINDER_ADVANCED_TIPS_OUTRO'); ?>
                    </div>
                </div>
            <?php endif; ?>
            <div id="finder-filter-window" class="com-finder__filter">
                <?php echo HTMLHelper::_('filter.select', $this->query, $this->params); ?>
            </div>
        </fieldset>
    <?php endif; ?>
</form>
