<?php $slotTitle = get_post_meta(get_the_ID(), 'slot-slider-title', true);?>
<section class="sctn slots cnt-vis">
    <div class="container">
        <div class="row">
            <div class="col">
                <h2 class="slots__title"><?php echo $slotTitle;?></h2>
            </div>
        </div>
        <div class="row no-wrap slider" id="sliderSlots">
            <?php foreach ($postsData as $post): ?>
                <article class="col slot__col">
                    <div class="slot-card">
                        <?php echo $post['image']; //wp_get_attachment_image($post['image'], 'full', false, ['class' => 'slot-card__image']);?>
                        <a href="<?php echo $post['link']; ?>" class="slot-card__content">
                            <h3 class="slot-card__title"><?php echo $post['title']; ?></h3>
                            <?php if (!empty($post['tag'])) {
                                echo "<span class='badge'>" . $post['tag'] . "</span>";
                            } ?>
                            <span class="slot-card__favourites lazy_image_bc"></span>
                        </a>
                        <a href="<?php echo $post['link']; ?>" class="slot-card__hover lazy_image_bc">
                            <span class="slot-card__icon lazy_image_bc"></span>
                            <span class="slot-card__desc"><?php echo $post['content']; ?></span>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>