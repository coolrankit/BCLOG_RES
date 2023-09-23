<?php get_header();?>
	
	<div id="container">
	<div class="tc-full fmid">
		<div id="content" class="padded clearfix">
			<?php
			while ( have_posts() ) : the_post();
				//if(!is_front_page()){the_title( '<h1 class="page-title no-wc">', '</h1>' );}
				the_title( '<h1 class="page-title no-wc">', '</h1>' );
				the_content();
			endwhile;
			?>
		</div>
	</div>
	</div></div> <!-- #container, #content -->

<?php get_footer();?>