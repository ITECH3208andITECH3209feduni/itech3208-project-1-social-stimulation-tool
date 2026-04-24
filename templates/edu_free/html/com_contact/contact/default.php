<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Contact\Site\Helper\RouteHelper;

/** @var \Joomla\Component\Contact\Site\View\Contact\HtmlView $this */

// Initialize variables
$tparams = $this->item->params;
$canDo   = ContentHelper::getActions('com_contact', 'category', $this->item->catid);
$canEdit = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by === $this->getCurrentUser()->id);

// Determine heading tags based on page heading visibility
$htag  = $tparams->get('show_page_heading') ? 'h2' : 'h1';
$htag2 = ($tparams->get('show_page_heading') && $tparams->get('show_name')) ? 'h3' : 'h2';

// Check if address information exists
$hasAddress = $this->item->address || $this->item->suburb || $this->item->state || $this->item->country || $this->item->postcode;

// Determine if contact info section should be displayed
$showContactInfo = $this->params->get('show_info', 1) && (
    ($this->item->image && $tparams->get('show_image')) ||
    ($this->item->con_position && $tparams->get('show_position')) ||
    (($this->params->get('address_check') > 0) && $hasAddress) ||
    $tparams->get('allow_vcard') ||
    ($this->item->misc && $tparams->get('show_misc'))
);

$showContactCategory = $tparams->get('show_contact_category');
?>

<div class="com-contact contact el-contact-form">
    <?php if ($tparams->get('show_page_heading')) : ?>
        <h1>
            <?php echo $this->escape($tparams->get('page_heading')); ?>
        </h1>
    <?php endif; ?>

    <?php if ($this->item->name && $tparams->get('show_name')) : ?>
        <div class="page-header">
            <<?php echo $htag; ?>>
                <?php if ($this->item->published == 0) : ?>
                    <span class="badge bg-warning text-light"><?php echo Text::_('JUNPUBLISHED'); ?></span>
                <?php endif; ?>
                <span class="contact-name"><?php echo $this->item->name; ?></span>
            </<?php echo $htag; ?>>
        </div>
    <?php endif; ?>

    <?php if ($showContactCategory === 'show_no_link') : ?>
        <<?php echo $htag2; ?>>
            <span class="contact-category"><?php echo $this->item->category_title; ?></span>
        </<?php echo $htag2; ?>>
    <?php elseif ($showContactCategory === 'show_with_link') : ?>
        <?php $contactLink = RouteHelper::getCategoryRoute($this->item->catid, $this->item->language); ?>
        <<?php echo $htag2; ?>>
            <span class="contact-category">
                <a href="<?php echo $contactLink; ?>">
                    <?php echo $this->escape($this->item->category_title); ?>
                </a>
            </span>
        </<?php echo $htag2; ?>>
    <?php endif; ?>

    <?php echo $this->item->event->afterDisplayTitle; ?>

    <?php if ($tparams->get('show_contact_list') && count($this->contacts) > 1) : ?>
        <form action="#" method="get" name="selectForm" id="selectForm">
            <label for="select_contact"><?php echo Text::_('COM_CONTACT_SELECT_CONTACT'); ?></label>
            <?php echo HTMLHelper::_(
                'select.genericlist',
                $this->contacts,
                'select_contact',
                'class="form-select" onchange="document.location.href = this.value"',
                'link',
                'name',
                $this->item->link
            ); ?>
        </form>
    <?php endif; ?>

    <?php if ($tparams->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
        <div class="com-contact__tags">
            <?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
            <?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
        </div>
    <?php endif; ?>

    <?php echo $this->item->event->beforeDisplayContent; ?>

    <?php if ($showContactInfo) : ?>
        <div class="uk-grid uk-child-width-expand" uk-grid>
            <div class="uk-width-1-3@m">
                <div class="uk-tile-muted">
                    <?php if ($this->item->image && $tparams->get('show_image')) : ?>
                        <div class="uk-card-media-top com-contact__thumbnail thumbnail">
                            <?php echo LayoutHelper::render(
                                'joomla.html.image',
                                [
                                    'src' => $this->item->image,
                                    'alt' => $this->item->name,
                                    'class' => 'uk-width-1-1',
                                ]
                            ); ?>
                        </div>
                    <?php endif; ?>

                    <div class="com-contact__container uk-padding uk-margin-remove-first-child">
                        <?php echo '<' . $htag2 . ' class="uk-h4 uk-heading-bullet uk-margin-remove-top">' . Text::_('COM_CONTACT_DETAILS') . '</' . $htag2 . '>'; ?>

                        <?php if ($this->item->con_position && $tparams->get('show_position')) : ?>
                            <dl class="com-contact__position contact-position dl-horizontal">
                                <dt><?php echo Text::_('COM_CONTACT_POSITION'); ?>:</dt>
                                <dd><?php echo $this->item->con_position; ?></dd>
                            </dl>
                        <?php endif; ?>

                        <?php if (($this->params->get('address_check') > 0) && ($hasAddress || $tparams->get('allow_vcard'))) : ?>
                            <div class="com-contact__info uk-margin">
                                <?php if ($hasAddress) : ?>
                                    <?php echo $this->loadTemplate('address'); ?>
                                <?php endif; ?>
                                
                                <?php if ($tparams->get('allow_vcard')) : ?>
                                    <p>
                                        <?php echo Text::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS'); ?>
                                        <a href="<?php echo Route::_('index.php?option=com_contact&view=contact&catid=' . $this->item->catslug . '&id=' . $this->item->slug . '&format=vcf'); ?>">
                                            <?php echo Text::_('COM_CONTACT_VCARD'); ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($this->item->misc && $tparams->get('show_misc')) : ?>
                            <?php echo '<' . $htag2 . ' class="uk-h4 uk-heading-bullet uk-margin-remove-top">' . Text::_('COM_CONTACT_OTHER_INFORMATION') . '</' . $htag2 . '>'; ?>

                            <div class="com-contact__miscinfo contact-miscinfo">
                                <dl class="uk-description-list">
                                    <dt>
                                        <span class="<?php echo $this->params->get('marker_class'); ?>">
                                            <?php echo Text::_('COM_CONTACT_OTHER_INFORMATION'); ?>
                                        </span>
                                    </dt>
                                    <dd>
                                        <span class="contact-misc">
                                            <?php echo $this->item->misc; ?>
                                        </span>
                                    </dd>
                                </dl>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="uk-margin-remove-first-child">
    <?php endif; ?>

                <?php if ($tparams->get('show_email_form') && ($this->item->email_to || $this->item->user_id)) : ?>
                    <?php echo '<' . $htag2 . ' class="el-form-heading uk-margin-remove-top uk-margin-small-bottom">' . Text::_('COM_CONTACT_EMAIL_FORM') . '</' . $htag2 . '>'; ?>
                    <?php echo $this->loadTemplate('form'); ?>
                <?php endif; ?>

                <?php if ($tparams->get('show_links')) : ?>
                    <?php echo '<' . $htag2 . '>' . Text::_('COM_CONTACT_LINKS') . '</' . $htag2 . '>'; ?>
                    <?php echo $this->loadTemplate('links'); ?>
                <?php endif; ?>

                <?php if ($tparams->get('show_articles') && $this->item->user_id && $this->item->articles) : ?>
                    <?php echo '<' . $htag2 . '>' . Text::_('JGLOBAL_ARTICLES') . '</' . $htag2 . '>'; ?>
                    <?php echo $this->loadTemplate('articles'); ?>
                <?php endif; ?>

                <?php if ($tparams->get('show_profile') && $this->item->user_id && PluginHelper::isEnabled('user', 'profile')) : ?>
                    <?php echo '<' . $htag2 . '>' . Text::_('COM_CONTACT_PROFILE') . '</' . $htag2 . '>'; ?>
                    <?php echo $this->loadTemplate('profile'); ?>
                <?php endif; ?>

                <?php if ($tparams->get('show_user_custom_fields') && $this->contactUser) : ?>
                    <?php echo $this->loadTemplate('user_custom_fields'); ?>
                <?php endif; ?>

                <?php if ($canEdit) : ?>
                    <div class="btn-canEdit">
                        <?php echo HTMLHelper::_('contacticon.edit', $this->item, $tparams); ?>
                    </div>
                <?php endif; ?>

    <?php if ($showContactInfo) : ?>
            </div>
        </div>
    <?php endif; ?>

    <?php echo $this->item->event->afterDisplayContent; ?>
</div>