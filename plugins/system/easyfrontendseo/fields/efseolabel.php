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
defined('JPATH_BASE') or die;
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldEfseoLabel extends JFormField
{
    protected $type = 'EfseoLabel';

    protected function getInput()
    {
        return '';
    }

    protected function getLabel()
    {
        $document = JFactory::getDocument();
        $document->addStyleDeclaration('label {width: 20em;}');
    }

}
