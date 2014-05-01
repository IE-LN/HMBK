<?php
global $total_found_posts, $search_term, $tag_term, $post, $wp_query;
$raw_results = search_posts_with_tag($search_term, 0, 8, array('celebrity', 'noncelebs', 'minisite', 'campaign'),  $set_the_loop = false, $enable_cache = true);

$found_posts = $raw_results['found_posts'];
$results = $raw_results['results'];

$per_row = 6;
$total_found_posts += $found_posts;
?>
<?php if ($found_posts > 0): ?>
	<div class="search-results-section results-section topics-results-section">
		<h3 class="search-results-section-heading results-section-heading topics-results-section-heading">
			<?= sprintf('%s (%d)', __('Celebs & Topics'), $found_posts) ?>
		</h3>
		<div class="search-results-section-list-wrap results-section-list-wrap topics-results-section-list-wrap">
			<div class="search-results-section-list results-section-list topics-results-section-list half-wrap">
				<?php $_post = $post; ?>
				<?php foreach ($results as $result): ?>
					<?php $post = $result;?>
					<?php setup_postdata($post); ?>
					<?php 
					//var_dump($post);
					if('celebrity' == $post->post_type) {
						$celeb_img = get_post_custom_values('_celebrities_profile_pic', $post->ID);
						//echo "<pre>\n"; print_r($celeb_img); echo "</pre>\n"; 
					}
					?>
					<?php if ($_pos === 0): ?>
			</div>
			<div class="search-results-section-list results-section-list topics-results-section-list half-wrap">
					<?php endif; ?>
					<?php $_pos = !isset($_pos) ? 0 : $_pos; ?>
					<div class="search-results-section-list-item results-section-list-item topics-results-section-list-item half">
						<div class="result-item-wrap topics-result-item-wrap half-inner">
							<div class="t50x50 image-wrap image-wrap-news floatL fake-pic">
								<a href="<?= get_permalink($post->ID) ?>">
								<?= wp_get_attachment_image($celeb_img[0], array(50, 50), 'i50x50 topic-result') ?></a>
							</div>
							<div class="post-content-wrap">
								<div class="post-title-wrap">
									<h2 class="posttitle"><a href="<?= get_permalink($post->ID) ?>"><?= get_the_title($post->ID) ?></a></h2>
								</div>
								<div class="post-meta">
									<div class="post-drill-down-links">
										<span class="icon-arrow"></span>
										<a href="<?= get_permalink($post->ID) ?>photos">Photos</a> /
										<a href="<?= get_permalink($post->ID) ?>news">News</a><?php if (in_array(get_post_type($post->ID), array('celebrity', 'noncelebs'))):?> /
										<a href="<?= get_permalink($post->ID) ?>bio">Bio</a>
										<?php endif; ?>
									</div>
									<div class="post-type"><?php
										$ptype = 'topic';
										switch (strtolower(get_post_type((int)$post->ID))) {
											case 'celebrity': $ptype = 'celebrity'; break;
											case 'celebrities': $ptype = 'celebrity'; break;
											case 'noncelebs': $ptype = 'non-celebrity'; break;
											case 'minisite': $ptype = 'topic'; break;
											case 'campaign': $ptype = 'ccampaign'; break;
										}
										echo $ptype;
									?></div>
								</div>
							</div>
						</div>
					</div>
					<?php $_pos = ($_pos + 1) % 2; ?>
				<?php endforeach; ?>
				<?php $post = $_post; ?>
				<?php setup_postdata($post); ?>
			</div>
		</div>
		<div class="cleaner"></div>
	</div>
<?php endif; ?>
