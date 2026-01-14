<?php get_header(); ?>

<main class="container py-5">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <?php get_template_part('template-parts/content'); ?>
  <?php endwhile; else : ?>
    <?php get_template_part('template-parts/content', 'none'); ?>
  <?php endif; ?>
</main>
<?php get_footer(); ?>
