<?php
get_header(); ?>

<div class="error-404 not-found">
    <h1 class="page-title"><?php esc_html_e( 'Oops! That page can’t be found.', 'my-wordpress-theme' ); ?></h1>
    <div class="page-content">
        <p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'my-wordpress-theme' ); ?></p>
        <?php get_search_form(); ?>
    </div>
</div>

<?php
get_footer();