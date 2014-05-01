<?php global $ice_img, $ifunc, $size, $post, $wp_query; ?>
<?php if (have_posts()): ?>
<?php $size = array(80, 60); ?>
<?php $generic_heading = __('News'); ?>
<div class="search-results-section results-section news-results-section">
    <h3 class="search-results-section-heading results-section-heading news-results-section-heading">
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

    <div class="results-section-list-wrap search-results-section-list-wrap news-results-section-list-wrap">
        <ul class="results-section-listsearch-results-section-list news-results-section-list">
            <?php while (have_posts()): the_post(); ?>
            <li class="results-list-item search-results-section-list-item results-section-list-item news-results-section-list-item">
                <article id="post-<?= $post->ID ?>" <?php post_class('news-item'); ?>>
                    <header>
                        <a class="news-image-link" href="<?= get_permalink($post->ID) ?>"  title="View <?= esc_attr(get_the_title()) ?>"><span class="key-hole kht80x60 t80x60 img-wrap image-wrap-news floatL"><?=
                            $ifunc(apply_filters('ice-get-thumbnail-id', 0, $post->ID), $size, $ice_img ? 'gallery-image' : false)
                            ?></span></a>
                        <div class="news-result-info">
                            <div class="news-categories entry-categories grey"><?= the_category(', ') ?></div>
                            <h2 class="search-entry-title posttitle blackmaroon"><a href="<?= get_permalink() ?>" title="View <?= esc_attr(get_the_title()) ?>"><? the_title() ?></a></h2>
                            <div class="post-meta">
										<span class="blue bold comment-count"><?php
                                            comments_popup_link(' Leave a Comment &raquo;', '1 Comment &raquo;', '% Comments &raquo;');
                                            ?></span>
                                <span class="post-date"><?= get_the_date() ?></span>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </header>
                </article><!-- end post-<?= $post->ID ?> -->
            </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <?php if($block['is_main']): ?>
    <div class="search-results-section-bottom">
        <?php
        $args = $block['request'];
        $args['search_type'] = $block['type'];
        ?>
        <div class="results-more more"><a href="<?=apply_filters('ice-search-link', '', $args);?>">see all news &raquo;</a></div>
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
