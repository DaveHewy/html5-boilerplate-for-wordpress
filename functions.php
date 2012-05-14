<?php
/**
 * @package WordPress
 * @subpackage HTML5_Boilerplate
 */


// Custom HTML5 Comment Markup
function mytheme_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li>
     <article <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
       <header class="comment-author vcard">
          <?php echo get_avatar($comment,$size='48',$default='<path_to_url>' ); ?>
          <?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
          <time><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a></time>
          <?php edit_comment_link(__('(Edit)'),'  ','') ?>
       </header>
       <?php if ($comment->comment_approved == '0') : ?>
          <em><?php _e('Your comment is awaiting moderation.') ?></em>
          <br />
       <?php endif; ?>

       <?php comment_text() ?>

       <nav>
         <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
       </nav>
     </article>
    <!-- </li> is added by wordpress automatically -->
<?php
}

automatic_feed_links();

// Widgetized Sidebar HTML5 Markup
if ( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'before_widget' => '<section>',
		'after_widget' => '</section>',
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => '</h2>',
	));
}

// Custom Functions for CSS/Javascript Versioning
$GLOBALS["TEMPLATE_URL"] = get_bloginfo('template_url')."/";
$GLOBALS["TEMPLATE_RELATIVE_URL"] = wp_make_link_relative($GLOBALS["TEMPLATE_URL"]);

// Add ?v=[last modified time] to style sheets
function versioned_stylesheet($relative_url, $add_attributes=""){
  echo '<link rel="stylesheet" href="'.versioned_resource($relative_url).'" '.$add_attributes.'>'."\n";
}

// Add ?v=[last modified time] to javascripts
function versioned_javascript($relative_url, $add_attributes=""){
  echo '<script src="'.versioned_resource($relative_url).'" '.$add_attributes.'></script>'."\n";
}

// Add ?v=[last modified time] to a file url
function versioned_resource($relative_url){
  $file = $_SERVER["DOCUMENT_ROOT"].$relative_url;
  $file_version = "";

  if(file_exists($file)) {
    $file_version = "?v=".filemtime($file);
  }

  return $relative_url.$file_version;
}


add_shortcode('wp_caption', 'fixed_img_caption_shortcode');
add_shortcode('caption', 'fixed_img_caption_shortcode');
function fixed_img_caption_shortcode($attr, $content = null) {
	// Allow plugins/themes to override the default caption template.
	$output = apply_filters('img_caption_shortcode', '', $attr, $content);
	if ( $output != '' ) return $output;
	extract(shortcode_atts(array(
		'id'=> '',
		'align'	=> 'alignnone',
		'width'	=> '',
		'caption' => ''), $attr));
	if ( 1 > (int) $width || empty($caption) )
	return $content;
	if ( $id ) $id = 'id="' . esc_attr($id) . '" ';
	return '<div ' . $id . 'class="wp-caption ' . esc_attr($align)
	. '">'
	. do_shortcode( $content ) . '<p class="wp-caption-text">'
	. $caption . '</p></div>';
}

if(!function_exists('ds_output_stepper')):
	function ds_output_stepper($current,$postcode=false){
	
		$html = '<ul class="income-stepper">';
		$html.= '<li class="first'; if($current==1): $html.=' current'; endif; if($current>1): $html.=' done'; endif; $html.= '"><span class="num">1.</span><a href="'.get_bloginfo('url').'/roof-calculator/">'.__("Find your property").'</a></li>';
		$html.= '<li'; if($current==2): $html.=' class="current"'; endif; if($current>2 && $current!=2): $html.=' class="done"'; endif; $html.= '><span class="num">2.</span><a href="'.get_bloginfo('url').'/roof-calculator/'; if($postcode): $html.= '?postcode='.urlencode($postcode); endif; $html.= '">'.__("Draw your roof").'</a></li>';
		$html.= '<li'; if($current==3): $html.=' class="current"'; endif; if($current>3 && $current!=3): $html.=' class="done"'; endif; $html.= '><span class="num">3.</span><a href="'.get_bloginfo('url').'/results/">'.__("Your estimation").'</a></li>';
		$html.= '<li class="last'; if($current==4): $html.=' current'; endif; $html.= '"><span class="num">4.</span><a href="#">'.__("Confirmation").'</a></li>';
		$html.= '</ul>';
		echo $html;

	}
endif;