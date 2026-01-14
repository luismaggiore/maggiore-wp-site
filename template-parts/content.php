<article id="post-<?php the_ID(); ?>" <?php post_class('mb-4'); ?>>
  <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
  <div> 
    <?php the_excerpt(); ?>
  </div>
</article>
