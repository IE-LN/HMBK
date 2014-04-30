<div class="bar-gallery midpost-gallery-embed midpost-gallery-<?php $this->args->embed_id ?><?php $this->args->title ? ' with-title' : '' ?>">
    <?php /* if we are in the admin (preview mode) then make sure that the title does not link off the post edit page */ ?>
    <?php /* if we are in the admin (preview mode) then create an editable field for the title, with some default text that is only a helper */ ?>
    <?php if ($this->args->preview): ?>
    <?php $title = trim(urldecode($this->args->title)); ?>
    <?php $title = empty($title) ? 'Add a title' : $title ?>
    <h3 class="embed-title" title="Add a title"><?php $title ?></h3>
    <div class="cleaner mceNonEditable"></div>
    <div class="orig-settings mceNonEditable" style="width:0px;height:0px;overflow:hidden;">
        <div class="param"><div class="name">embed_id</div><div class="value"><?php (int)$this->args->embed_id ?></div></div>
        <div class="param"><div class="name">type</div><div class="value">gallery</div></div>
        <div class="param"><div class="name">title</div><div class="value"><?php urlencode($this->args->title) ?></div></div>
    </div>
    <?php /* otherwise just draw the title the way it would normally look */ ?>
    <?php elseif ($this->args->title): ?>
    <a class="bar-title" href="<?php $gal_link ?>"><h3 class="embed-title"><?php urldecode($this->args->title) ?></h3></a>
    <?php endif; ?>
    <div class="bar-list midpost-gallery-embed-inner">
        <div class="bar-list-inner midpost-gallery-embed-row<?php $this->args->preview ? ' mceNonEditable' : '' ?>">
            <?php /* iterate over the list of ids for this embed to display */ ?>
            <?php foreach ($ids as $id): ?>
            <?php /* draw the image for this image id, and wrap that image in a link to the attachment page for that image */ ?>
            <?php $link = get_permalink($id); ?>
            <div class="thumb-keyhole gallery-thumbnail-container gallery-thumb-<?php $id ?>-container<?php $this->args->preview ? ' mceNonEditable' : '' ?>">
                <?php $img_classes = 'gallery-thumbnail gallery-thumbnail-'.$id.($this->args->preview ? ' mceNonEditable mceItem' : ''); ?>
                <a href="<?php $link ?>"><?php self::_attachment_image($id, array(94, 94), $img_classes) ?></a>
            </div>
            <?php endforeach; ?>
            <div class="cleaner"></div>
        </div>
        <div class="cleaner"></div>
    </div>
    <?php /* add a 'see more' link that links to the gallery itself, at the bottom of the mid post gallery */ ?>
    <div class="more-wrapper midpost-gallery-embed-more-link<?php $this->args->preview ? ' mceNonEditable' : '' ?>"><?php $more_photos_link ?></a></div>
    <div class="cleaner"></div>
</div>