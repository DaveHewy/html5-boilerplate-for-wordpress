<?php 

/* Template Name: Pg TPL - Contact */

get_header(); ?>

<section>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

	<h1><?=the_title()?></h1>
	<?=the_content()?>

<?php endwhile; endif; ?>		 

</section>


<?php get_footer(); ?>