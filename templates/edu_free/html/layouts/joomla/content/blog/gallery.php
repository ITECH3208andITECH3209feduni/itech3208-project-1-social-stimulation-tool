<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

extract($displayData);
?>

<?php if(isset($attribs->helix_ultimate_gallery) && $attribs->helix_ultimate_gallery) : ?>
	<?php $gallery = json_decode($attribs->helix_ultimate_gallery ?? ""); ?>
	<?php $images = (isset($gallery->helix_ultimate_gallery_images) && $gallery->helix_ultimate_gallery_images) ? $gallery->helix_ultimate_gallery_images : array(); ?>

	<?php if(count((array)$images)) : ?>

		<div class="uk-position-relative uk-visible-toggle uk-light" uk-slider="center: true">
		    <ul class="uk-slider-items uk-grid">
					<?php foreach ( $images as $key => $image ) : ?>
					<li class="ui-item uk-width-1-1 uk-width-1-1@s uk-width-1-1@m" tabindex="-1">
							<div class="uk-panel">
								<img src="<?php echo $image; ?>" <?php echo !empty($attribs->helix_ultimate_image_alt_txt) ? "alt='" . $attribs->helix_ultimate_image_alt_txt . "'" : '' ?>>
							</div>
						</li>
					<?php endforeach; ?>
		    </ul>

		    <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
		    <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next"></a>

		</div>

	<?php endif; ?>
<?php endif; ?>
