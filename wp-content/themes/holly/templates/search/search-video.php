<?php global $ice_img, $ifunc, $size, $post, $wp_query; ?>
<?php if (have_posts()): ?>
<?php $size = array(180, 135); ?>
<?php $generic_heading = __('Videos'); ?>
<div class="search-results-section results-section video-results-section">
    <h3 class="search-results-section-heading results-section-heading video-results-section-heading">
        <?php if (!$block['is_main']): ?>
        <?php
        $args = $block['request'];
        unset($args['search_type']);
        ?>
        <a class="search-results-back-to-all" href="<?=apply_filters('ice-search-link', '', $args);?>">Back to All Results &raquo;</a>
        <?= sprintf('%s (%d)', $generic_heading, $wp_query->found_posts) ?>
        <?php else: ?>
        <?php
        $args = $block['request'];
        $args['search_type'] = $block['type'];
        ?>
        <?= sprintf('<a class="search-results-section-heading-link" href="%s">%s (%d)</a>', apply_filters('ice-search-link', '', $args), $generic_heading, $wp_query->found_posts) ?>
        <?php endif; ?>
    </h3>

<div class="results-section-list-wrap grid three-column">
    <?php $ind = 0; ?>
    <?php $per_row = 3; ?>
    <?php $position = 1; ?>
    <?php while (have_posts()): the_post(); ?>
    <?php if ($ind % $per_row == 0): ?><div class="grid-row"><?php endif; ?>
    <div class="grid-item column video-item third photos photos-<?= get_the_ID() ?>">
        <div class="grid-item-inner third-inner<?= $position % $per_row == 0 ? ' third-no-right-border' : '' ?>">
            <article id="post-<?= $post->ID ?>" <?php post_class(); ?>>
                <header>
                    <a href="<?= get_permalink($post->ID) ?>"  title="View <?= esc_attr(get_the_title()) ?>"><span class="key-hole kht180x135"><?=
                        $ifunc(apply_filters('ice-get-thumbnail-id', 0, $post->ID), $size, $ice_img ? 'video-image' : false)
                        ?></span></a>
                    <div class="video-categories entry-categories grey"><?= the_category(', ') ?></div>
                    <h2 class="video-title blackmaroon"><a href="<?= get_permalink() ?>" title="View <?= esc_attr(get_the_title()) ?>"><? the_title() ?></a></h2>
                    <div class="video-photos-count bold">
                        <a href="<?= get_permalink($post->ID) ?>" title="View <?= esc_attr(get_the_title()) ?>"><?=
                            sprintf('Watch Now &raquo;')
                            ?></a>
                    </div>
                </header>
            </article><!-- end post-<?= $post->ID ?> -->
        </div>
    </div>
    <?php $ind++ ?>
    <?php if ($ind % $per_row == 0): ?></div><?php endif; ?>
    <?php endwhile; ?>
    <?php if ($ind % $per_row != 0): ?>
    <?php while ($ind % $per_row != 0): ?>
        <div class="grid-item column video-item third photos photos-0">
            <div class="grid-item-inner third-inner">
                <article id="post-0-<?= $ind ?>" <?php post_class(); ?>>
                    <header>
                        <span class="key-hole kht180x135 fake-pic"></span>
                        <div class="thirdDesc center">
                            <div class="video-categories entry-categories cattitle grey">&nbsp;</div>
                            <h2 class="video-title posttitle blackmaroon">&nbsp;</h2>
                            <div class="video-photos-count more bold">&nbsp;</div>
                        </div>
                    </header>
                </article><!-- end post-0-<?= $ind ?> -->
            </div>
        </div>
        <?php $ind++; ?>
        <?php endwhile; ?>
				</div>
			<?php endif; ?>
    <div class="clear"></div>
</div>

<?php if($block['is_main']): ?>
    <div class="search-results-section-bottom">
        <?php
        $args = $block['request'];
        $args['search_type'] = $block['type'];
        ?>
        <div class="results-more more"><a href="<?=apply_filters('ice-search-link', '', $args);?>">see all videos &raquo;</a></div>
    </div>
    <div class="cleaner"></div>
    <?php else: ?>
    <?php
        $args = $block['request'];
        $args['search_type'] = $block['type'];
        $pagination_out = its_pagination(array('base' => trailingslashit(apply_filters('ice-search-link', '', $args)).'%_%', 'greyed' => true, 'type' => 'list', 'per_page' => $block['count_type']));
        ?>
    <div class="pagination-bottom"><?= $pagination_out ?></div>
    <?php endif; ?>
</div>
<?php endif; ?>
