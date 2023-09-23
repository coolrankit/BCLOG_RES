<?php get_header();?>
		
	<div id="container"><div id="content" class="clearfix">
	<div class="tc-full fmid">
		<?php
		while ( have_posts() ) : the_post();
			the_content();
		endwhile;
		?>
	</div>
	</div></div> <!-- #container, #content -->

<?php get_footer();?>