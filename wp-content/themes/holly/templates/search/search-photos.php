<?php global $ice_img, $ifunc, $size, $post, $wp_query; ?>
<?php if (have_posts()): ?>
<?php $size = array(94, 94); ?>
<?php $generic_heading = __('Photos'); ?>
<div class="search-results-section results-section photo-results-section">
    <h3 class="search-results-section-heading results-section-heading photo-results-section-heading">
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

    <div class="results-section-list-wrap search-results-section-list-wrap photo-results-section-list-wrap">
        <div class="six-column-580 grid results-section-listsearch-results-section-list photo-results-section-list">
            <?php while (have_posts()): the_post(); ?>
            <div class="grid-item column photo-item results-list-item search-results-section-list-item results-section-list-item photo-results-section-list-item">
                <div class="grid-item-inner">
                    <article id="post-<?= $post->ID ?>" <?php post_class(); ?>>
                        <header>
                            <?php $add_url = '?page='.($ind+1).'&t='.$post->term_match; ?>
                            <a href="<?= get_permalink($post->ID) ?><?= $add_url ?>"  title="View <?= esc_attr(get_the_title()) ?>"><span class="key-hole kht94x94 t94x94"><?=
                                $ifunc(apply_filters('ice-get-thumbnail-id', 0, $post->ID), $size, $ice_img ? 'i94x94 photo-result' : false)
                                ?></span></a>
                        </header>
                    </article><!-- end post-<?= $post->ID ?> -->
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php if($block['is_main']): ?>
    <div class="search-results-section-bottom">
        <?php
        $args = $block['request'];
        $args['search_type'] = $block['type'];
        ?>
        <div class="results-more more"><a href="<?=apply_filters('ice-search-link', '', $args);?>">see all photo &raquo;</a></div>
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
