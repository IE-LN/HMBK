<?php
global $ifunc, $pfunc, $ice_img;
?>
<div class="related-galleries">
    <div class="dot-header">
        <div class="dot-header-inner">
            <h3 class="sub-section-title">Related&nbsp;Photo&nbsp;Galleries</h3>
            <div class="dotted-line"></div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="related-gallery-list-wrapper">
        <ul class="related-gallery-list menu blackmaroon">
            <?php foreach ($related as $gal): ?>
            <?php $gal_title = apply_filters('the_title', $gal->post_title, $gal->ID); ?>
            <li class="related-gallery">
                <div class="related-gallery-inner">
                    <a class="left related-image-link" href="<?= $pfunc($gal->ID) ?>" title="<?= esc_attr($gal_title) ?>">
                        <?= $ifunc(apply_filters('ice-gallery-first-image', $gal->ID), array(80, 60), $ice_img ? 'related-image' : false) ?>
                    </a>
                    <h2 class="related-title"><a href="<?= $pfunc($gal->ID) ?>" title="<?= esc_attr($gal_title) ?>"><?= $gal_title ?></a></h2>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        <div class="clear"></div>
    </div>
</div>
