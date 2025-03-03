
<div class="sctn search cnt-vis">
    <div class="container pos-r">
        <div class="row">
            <div class="col col__e@s pos-r z1 search__input-box lazy_image_bc">
                <form id="search-form" role="search">
                    <label for="search" class="visually-hidden">Search for content</label>
                    <div class="col col__e@s pos-r z1 search__input-box lazy_image_bc">
                        <input type="search" id="search" name="search" class="input" placeholder="Search"  autocomplete="off" aria-label="Search">
                        <button type="submit" class="search__button">
                            <span class="visually-hidden">Submit search</span>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col col__a@s search-box pos-r z1">
                <button class="button button--dark search-box__button dropdown-btn" data-dropdown="providers">
                    <span class="search-box__icon lazy_image_bc"></span>
                    Провайдеры
                    <span class="arrow_button search-box__arrow-button lazy_image_bc"></span>
                </button>
            </div>
            <div id="providers" class="col dropdown-content provider">
                <?php foreach ($providers as $category) : ?>
                    <span class='lazy_image_bc provider__item provider__icon icon--<?php echo esc_html($category->slug);?>'>
                        <?php echo esc_html($category->name); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <div id="category" class="row slider">
            <?php foreach ($games as $category) : ?>
                <div class="col col__a category__item pos-r z1">
                    <button class="button button--dark category__button icon--<?php echo esc_html($category->slug);?>"><?php echo esc_html($category->name); ?></button>
                </div>
            <?php endforeach;?>
        </div>
    </div>
</div>