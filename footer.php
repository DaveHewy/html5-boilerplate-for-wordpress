<?php
/**
 * @package WordPress
 * @subpackage HTML5_Boilerplate
 */
?>
	
	</section><!-- end #content -->

	</div> <!--! end of #wrapper -->

	<footer id="footer">
		
		<div class="footer-content">
		
	  	<div class="container">
	  	
	  		<div class="column seven">
	  			
	  			<h4 class="footerhead">From the blog</h4>
	  			
	  			<article id="latest_posts">
		  			<?php
					$args = array( 'numberposts' => 6);
					$lastposts = get_posts( $args );
					foreach($lastposts as $post) : setup_postdata($post); ?>
						<article class="posts"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></article>
					<?php endforeach; ?> 			
	  			</article>
	
	  		</div>
	  		
		  	<div class="social-media column five push-2">
		  		<article id="twitter">
		  			<strong>Twitter</strong>
					<p>Etiam porta sem malesuada magna mollis euismod.</p>
		  		</article>
		  		<ul>
		  			<li><a href="#" class="latest-tweet"></a></li>
		  			<li><a href="http://twitter.com/DiscoverSolar" title="Follow Discover Solar on Twitter" class="twitter"></a></li>
		  			<li><a href="http://www.facebook.com/DiscoverSolar" title="Like Discover Solar on Facebook" class="facebook"></a></li>
		  			<li><a href="<?php bloginfo('url');?>/feed/" title="Read Discover Solar's Feed" class="rss"></a></li>
		  		</ul>
		  	</div>
	  	
	  	</div>
		
		</div>
		
		<div class="footer-prop">
			<div class="container">
		  		<nav id="footer_nav" class="column twelve text-center">
					<ul>
						<li>&copy; Copyright Green Energy Software Solutions</li>
					</ul>
				</nav>
				<div id="madebybytewire">
					<a href="http://www.bytewire.co.uk/" title="Web Design Essex" target="_blank">Web design essex</a>
				</div>
			</div>
		</div>
	  
	</footer>
  


  <!-- Javascript at the bottom for fast page loading -->

  <!-- Grab Google CDN's jQuery. fall back to local if necessary -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
  <script>!window.jQuery && document.write('<script src="<?php echo $GLOBALS["TEMPLATE_RELATIVE_URL"] ?>html5-boilerplate/js/jquery-1.4.2.min.js"><\/script>')</script>

  <?php versioned_javascript($GLOBALS["TEMPLATE_RELATIVE_URL"]."html5-boilerplate/js/plugins.js") ?>
  <?php versioned_javascript($GLOBALS["TEMPLATE_RELATIVE_URL"]."html5-boilerplate/js/script.js") ?>

  <!--[if lt IE 7 ]>
    <?php versioned_javascript($GLOBALS["TEMPLATE_RELATIVE_URL"]."html5-boilerplate/js/dd_belatedpng.js") ?>
  <![endif]-->


  <!-- yui profiler and profileviewer - remove for production -->
  <!-- <?php versioned_javascript($GLOBALS["TEMPLATE_RELATIVE_URL"]."html5-boilerplate/js/profiling/yahoo-profiling.min.js") ?>
    <?php versioned_javascript($GLOBALS["TEMPLATE_RELATIVE_URL"]."html5-boilerplate/js/profiling/config.js") ?> -->
  <!-- end profiling code -->


  <!-- asynchronous google analytics: mathiasbynens.be/notes/async-analytics-snippet
       change the UA-XXXXX-X to be your site's ID -->
  <!-- WordPress does not allow Google Analytics code to be built into themes they host. 
       Add this section from HTML Boilerplate manually (html5-boilerplate/index.html), or use a Google Analytics WordPress Plugin-->

  <?php wp_footer(); ?>

</body>
</html>
