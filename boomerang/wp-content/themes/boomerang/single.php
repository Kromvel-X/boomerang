<?php
get_header(); ?>

<main id="main" class="site-main" role="main">
    <?php
    while ( have_posts() ) :
        the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('sctn-post'); ?>>
            <div class="container">
                <div class="row">
                    <div class="col">
                        <header class="entry-header">
                            <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                        </header>

                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    <?php
    endwhile; // End of the loop.
    ?>
</main>

<?php
get_footer();