<?php
/**
 * @Copyright
 * @package     Field - Kubik-Rubik Versioncheck
 * @author      Viktor Vogel {@link http://www.kubik-rubik.de}
 * @version     Joomla! 2.5 - 1.2
 * @date        Created on 22-Feb-2013
 * @link        Project Site {@link http://joomla-extensions.kubik-rubik.de}
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
defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for Kubik-Rubik Joomla! Extensions.
 * Provides a version check.
 */
class JFormFieldKRVersionCheck extends JFormField
{
    protected $type = 'krversioncheck';

    protected function getInput()
    {
        $field_set = $this->form->getFieldset();

        $version_check_enabled = $field_set['jform_versioncheck_enable']->value;

        if(!empty($version_check_enabled))
        {
            $info = explode('|', $field_set['jform_krversioncheck']->value);
            $extension = $info[0];
            $version_installed = $info[1];

            if($version_check_enabled == 1)
            {
                $session = JFactory::getSession();
                $field_value_session = $session->get('field_value', null, 'krversioncheck');
                $extension_session = $session->get('extension', null, 'krversioncheck');
                $version_installed_session = $session->get('version_installed', null, 'krversioncheck');

                if(!empty($field_value_session) AND ($version_installed == $version_installed_session) AND ($extension == $extension_session))
                {
                    return $field_value_session;
                }
            }

            $version_check = $this->getVersionStatus($info);

            $field_value = '';

            if($version_check['status'] == 1)
            {
                $field_value = '<div style="border: 1px solid #DD87A2; border-radius: 2px; padding: 5px; background-color: #F9CAD9; font-size: 120%; margin: 10px 0;">'.JTEXT::sprintf('KR_VERSION_CHECK_NEWUPDATEAVAILABLE', $version_check['version_latest'], $version_check['url'], $version_check['name']).'</div>';
            }
            elseif($version_check['status'] == 0)
            {
                $field_value = '<div style="border: 1px solid #87DB93; border-radius: 2px; padding: 5px; background-color: #CBF7CA; font-size: 120%; margin: 10px 0;">'.JTEXT::_('KR_VERSION_CHECK_UPTODATE').'</div>';
            }
            elseif($version_check['status'] == -1)
            {
                $field_value .= '<div style="border: 1px solid #F2DB82; border-radius: 2px; padding: 5px; background-color: #F7EECA; font-size: 120%; margin: 10px 0;">'.JTEXT::_('KR_VERSION_CHECK_CHECK_ERROR_SERVER').'</div>';
            }
            elseif($version_check['status'] == -2)
            {
                $field_value .= '<div style="border: 1px solid #F2DB82; border-radius: 2px; padding: 5px; background-color: #F7EECA; font-size: 120%; margin: 10px 0;">'.JTEXT::_('KR_VERSION_CHECK_CHECK_ERROR').'</div>';
            }
            elseif($version_check['status'] == -3)
            {
                $field_value .= '<div style="border: 1px solid #F2DB82; border-radius: 2px; padding: 5px; background-color: #F7EECA; font-size: 120%; margin: 10px 0;">'.JTEXT::sprintf('KR_VERSION_CHECK_CHECK_JOOMLA_VERSION', $version_check['joomla_version']).'</div>';
            }

            if($version_check_enabled == 1)
            {
                $session->set('field_value', $field_value, 'krversioncheck');
                $session->set('version_installed', $version_installed, 'krversioncheck');
                $session->set('extension', $extension, 'krversioncheck');
            }

            return $field_value;
        }
    }

    protected function getLabel()
    {
        return;
    }

    private function getVersionStatus($info)
    {
        $version_check = array('status' => 0, 'name' => '', 'url' => '', 'version_latest' => '', 'joomla_version' => '');

        $url_fopen = ini_get('allow_url_fopen');

        if(function_exists('curl_init') OR !empty($url_fopen))
        {
            $url_check = 'http://joomla-extensions.kubik-rubik.de/scripts/kr-joomla-extensions.xml';

            if(function_exists('curl_init'))
            {
                $ch = curl_init($url_check);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                $version_check_xml = curl_exec($ch);
                $curl_errno = curl_errno($ch);
                curl_close($ch);

                if($curl_errno != 0)
                {
                    $version_check['status'] = -1;
                }
            }
            else
            {
                $version_check_xml = @file_get_contents($url_check, 'r');

                if(empty($version_check_xml))
                {
                    $version_check['status'] = -1;
                }
            }
        }
        else
        {
            $version_check['status'] = -2;
        }

        if(!empty($version_check_xml))
        {
            if(!preg_match('@(error|access denied)@i', $version_check_xml))
            {
                $jversion = new JVersion;
                $version_check['joomla_version'] = $jversion->RELEASE;
                $joomla_version = 'joomlaversion'.str_replace('.', '', $version_check['joomla_version']);
                $type = $info[0];

                if(class_exists('SimpleXMLElement'))
                {
                    $version_check_xml_simple = new SimpleXMLElement($version_check_xml);

                    if(!empty($version_check_xml_simple->$type->$joomla_version))
                    {
                        $version_latest = (string)$version_check_xml_simple->$type->$joomla_version->extensionversion;
                        $version_installed = $info[1];

                        if(version_compare($version_latest, $version_installed) == 1)
                        {
                            $version_check['name'] = (string)$version_check_xml_simple->$type->name;
                            $version_check['url'] = (string)$version_check_xml_simple->$type->$joomla_version->downloadurl;
                            $version_check['version_latest'] = $version_latest;
                            $version_check['status'] = 1;
                        }
                    }
                    else
                    {
                        $version_check['status'] = -3;
                    }
                }
                else
                {
                    $version_check_regex_extension = $this->regEx($type, $version_check_xml);

                    if(!empty($version_check_regex_extension))
                    {
                        $version_check_regex_joomlaversion = $this->regEx($joomla_version, $version_check_regex_extension);

                        if(!empty($version_check_regex_joomlaversion))
                        {
                            $version_latest = $this->regEx('extensionversion', $version_check_regex_extension);
                            $version_installed = $info[1];

                            if(version_compare($version_latest, $version_installed) == 1)
                            {
                                $version_check['name'] = $this->regEx('name', $version_check_regex_extension);
                                $version_check['url'] = $this->regEx('downloadurl', $version_check_regex_extension);
                                $version_check['version_latest'] = $version_latest;
                                $version_check['status'] = 1;
                            }
                        }
                        else
                        {
                            $version_check['status'] = -3;
                        }
                    }
                }
            }
            else
            {
                $version_check['status'] = -1;
            }
        }

        return $version_check;
    }

    private function regEx($pattern, $subject)
    {
        preg_match('@<'.$pattern.'>(.*)</'.$pattern.'>@isU', $subject, $matches);

        if(!empty($matches[1]))
        {
            $match = $matches[1];
        }
        else
        {
            $match = false;
        }

        return $match;
    }

}
