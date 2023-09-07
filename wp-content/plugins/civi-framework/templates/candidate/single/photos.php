<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_style('lightgallery');
wp_enqueue_script('lightgallery');

wp_enqueue_style('newgallery');
wp_enqueue_script('newgallery');

$id = get_the_ID();
$candidate_galleries     = get_post_meta(get_the_ID(), CIVI_METABOX_PREFIX . 'candidate_galleries', true);
$attach_id         = get_post_thumbnail_id();
$show = 3;
$count = 0;
?>
<?php if (!empty($candidate_galleries)) : ?>
<div class="block-archive-inner candidate-gallery-details">
	<h4 class="title-candidate"><?php esc_html_e('Photos', 'civi-framework') ?></h4>
	<div class="interior container clearfix">
		<div class="row"> 
			<?php 
				$civi_candidate_galleries = explode('|', $candidate_galleries);
					$count = count($civi_candidate_galleries);
					foreach ($civi_candidate_galleries as $key => $image) :
						if ($image) {
							$image_full_src = wp_get_attachment_image_src($image, 'full');
							if (isset($image_full_src[0])) {
								$thumb_src      = $image_full_src[0];
							}
						}
					if (!empty($thumb_src)) { 
						$count++;
						if($count > 3) { $cStyle = 'style="display:none;"'; }
			?>
			<div class="col-xs-12 col-sm-6 col-md-4 blogBox moreBox" <?php echo $cStyle; ?> >
				<div class="item">

					<img src="<?php echo esc_url($thumb_src); ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>">
				</div>
			</div>
			<?php } ?>
			<?php endforeach; ?>
			<div id="loadMore" style="">
				<a href="#">Load More</a>
			</div>
		</div>
	</div>
	</div>



<?php endif; ?>
