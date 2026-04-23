<?php
/**
 * EFSEO - Easy Frontend SEO for Joomal! 2.5
 * License: GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * Author: Viktor Vogel
 * Projectsite: http://joomla-extensions.kubik-rubik.de/efseo-easy-frontend-seo
 *
 * @license GNU/GPL
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task) {
        if (task == 'cancel' || document.formvalidator.isValid(document.id('easyfrontendseo-form'))) {
            Joomla.submitform(task, document.getElementById('easyfrontendseo-form'));
        } else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_easyfrontendseo'); ?>" method="post" name="adminForm" id="easyfrontendseo-form" class="form-validate form-horizontal">
    <div class="width-55 fltlft">
        <fieldset class="adminform">
            <ul class="adminformlist">
                <li>
                    <label for="url">
                        <strong><?php echo JText::_('COM_EASYFRONTENDSEO_URL'); ?>:</strong>
                    </label>
                    <input class="text_area" type="text" name="url" id="url" size="80" value="<?php echo $this->entry->url; ?>" />
                </li>
                <li>
                    <label for="title">
                        <strong><?php echo JText::_('COM_EASYFRONTENDSEO_TITLE'); ?>:</strong> <span id="counter_title" class="efseo_counter"></span>
                    </label>
                    <input class="text_area" type="text" name="title" id="title" size="80"  maxlength="<?php echo $this->characters_length['title']; ?>" value="<?php echo htmlspecialchars($this->entry->title); ?>" />
                </li>
                <li>
                    <label for="description">
                        <strong><?php echo JText::_('COM_EASYFRONTENDSEO_DESCRIPTION'); ?>:</strong> <span id="counter_description" class="efseo_counter"></span>
                    </label>
                    <textarea class="text_area" rows="4" cols="140"  maxlength="<?php echo $this->characters_length['description']; ?>" id="description" name="description"><?php echo htmlspecialchars($this->entry->description); ?></textarea>
                </li>
                <li>
                    <label for="keywords">
                        <strong><?php echo JText::_('COM_EASYFRONTENDSEO_KEYWORDS'); ?>:</strong> <span id="counter_keywords" class="efseo_counter"></span>
                    </label>
                    <input class="text_area" type="text" name="keywords" id="keywords" size="80" maxlength="255" value="<?php echo htmlspecialchars($this->entry->keywords); ?>" />
                </li>
                <li>
                    <label for="generator">
                        <strong><?php echo JText::_('COM_EASYFRONTENDSEO_GENERATOR'); ?>:</strong> <span id="counter_generator" class="efseo_counter"></span>
                    </label>
                    <input class="text_area" type="text" name="generator" id="generator" size="80" maxlength="255" value="<?php echo htmlspecialchars($this->entry->generator); ?>" />
                </li>
                <li>
                    <label for="robots">
                        <strong><?php echo JText::_('COM_EASYFRONTENDSEO_ROBOTS'); ?>:</strong> <span id="counter_robots" class="efseo_counter"></span>
                    </label>
                    <select class="" id="robots" name="robots">
                        <?php foreach($this->robots_array as $robots_value) : ?>
                            <?php $selected = ($robots_value == $this->entry->robots) ? 'selected="selected"' : ''; ?>
                            <option value="<?php echo $robots_value; ?>" <?php echo $selected; ?>><?php echo $robots_value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </li>
            </ul>
        </fieldset>
    </div>
    <input type="hidden" name="option" value="com_easyfrontendseo" />
    <input type="hidden" name="id" value="<?php echo $this->entry->id; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="entry" />
    <input type="hidden" name="url_current" value="<?php echo JFactory::getURI()->getQuery(); ?>" />
    <?php echo JHTML::_('form.token'); ?>
</form>