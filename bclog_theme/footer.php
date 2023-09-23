	<div id="footer">
		<div id="footer-widgets">
			<?php if(is_active_sidebar('footer-1')) :?>
			<div id="footer-1" class="widget-area tc-full fmid clearfix">
			<?php dynamic_sidebar('footer-1');?>
			</div><!-- #footer-1.widget-area -->
			<?php endif;?>
			<?php if(is_active_sidebar('footer-2')) :?>
			<div id="footer-2" class="widget-area tc-full fmid clearfix">
			<?php dynamic_sidebar('footer-2');?>
			</div><!-- $footer-2.widget-area -->
			<?php endif;?>
		</div><!-- #footer-widgets -->
		
		<div id="site-footer">
			<div id="colophon" class="tc-full fmid clearfix">
				<!-- <div><p align="center">&copy; <?php echo date("Y");?> abcd.com. All Rights Reserved.<br>Designed by: RegulusReign Technologies Pvt. Ltd.</p></div> -->
			</div> <!-- #colophon -->
		</div><!-- #site-footer -->
	</div> <!-- #footer -->
	
</div></div> <!-- #wrapper, #wrap -->

<?php wp_footer(); ?>
<table class="alert-box-wrap" style="display:none;"><tr><td><div class="alert-box"><span class="iicon genericon-close alert-box-close"></span><div class="alert-box-msg"></div></div></td></tr></table>
</body>

</html>