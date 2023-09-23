<?php get_header();?>
	
	<div id="container"><div id="content" class="clearfix no-wc">
	<div class="tc-full fmid">
		<div id="content-wrap" class="tc-full fmid clearfix">
			<?php
			while ( have_posts() ) : the_post();
				if(!(is_home() || is_front_page())){the_title( '<h1 class="page-title no-wc">', '</h1>' );}
				the_content();
			endwhile;
			?>
		</div>
	</div>
	</div></div> <!-- #container, #content -->

<?php get_footer();?>