<?php if (is_object($post) && isset($post->ID)):

    $id = gallery_metadata_sidebar_widget::post_from_main_embed($post->post_parent);
    if((int) $id > 0) {
        $back_url = get_permalink($id);
    } else {
        //id is empth
        $back_url = site_url('/photos/');
    }
    setup_postdata($post);
    ?>

    <?php $before_widget ?>
	<div class="bmsbw-gallery-meta-widget span-gutter">
		<div class="bmsbw-gutter-curve gutter-curve"><div class="curve-bottom-left curve"></div></div>
		<div class="bmsbw-inside widget-inside">
			<div class="bmsbw-inside-inner widget-inside-inner">
				<article id="post-<?php get_the_ID() ?>" class="bmsbw-inside-inner-inner widget-inside-inner-inner">
					<header>
						<h3 class="bmsbw-title post-title"><?php the_title() ?></h3>
						<nav class="bmsbw-sub-nav sub-nav">
							<a href="<?php $back_url ?>" class="bmsbw-back back-to-post">&laquo; Back to Story</a>
							/
							<a href="#respond" class="bmsbw-leave-comment its-icon its-quote">Leave a Comment<span></span></a>
						</nav>
					</header>
					
					<div class="bmsbw-description entry-content"><?php get_the_content() ?></div>

					<footer class="bmsbw-metadata metadata">
						<div class="bmsbw-related-to post-tags">
							<span class="related-to-label">Related to: </span>
							<span class="entry-tags"><?php 
							global $wpdb;
							//var_dump($post);

							// get tags on this gallery if it is a main embed
							if($post->back_link_id > 0) {
								$the_tags = get_the_tags($post->back_link_id); 
							} else {
								// get tags of parent gallery
								$the_tags = get_the_tags($post->post_parent); 
							}
							//var_dump($the_tags);

							// get tags on this attachment
							$media_tags = get_the_tags($post->ID);
							//var_dump($media_tags);

							// assert that the gallery is more likely to have tags than the attachment
							// if both are arrays, merge, else accept gallery tags over media tags
							if(is_array($media_tags) && is_array($the_tags)) {
								$tags = array_merge($media_tags, $the_tags);
							} else {
								$tags = $the_tags;
							}

							// instantiate empty arr
							$links = array();

							// generate uniuqe array of links
							if (is_array($tags) && count($tags))
								foreach($tags as $tag) {
									$links["$tag->term_id"] = '<a href="'.get_term_link($tag, 'post_tag').'">'.$tag->name.'</a>';
								}

							if(!empty($links)) { 
								$cnt = count($links);
								$out = implode(', ', $links);
								echo $out;
							} else {
								echo '<a href="' . home_url() . '/photos/">Photos</a>';
							}
							
							?></span>
						</div>
						<div class="bmsbw-like-and-share like-and-share">
							<div class="share-buttons"><?php do_action('add-this-buttons', 'single-image', true) ?></div>
							<div class="like-buttons"><?php apply_filters('like-and-share-buttons', '', get_the_ID(), array('fb')) ?></div>
							<div class="clear"></div>
						</div>
						<div class="clear"></div>
					</footer>
				</article>
			</div>
		</div>
		<?php /*
		<div class="bmsbw-gutter-curve gutter-curve"><div class="curve-top-left curve"></div></div>
		*/ ?>
	</div>
	<?php $after_widget ?>

<?php endif;
