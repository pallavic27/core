<?php
/**
 * Framework Functions File
 *
 * Please do not edit this file. This file is part of the Cyber Chimps Framework and all modifications
 * should be made in a child theme.
 *
 * @category CyberChimps Framework
 * @package  Framework
 * @since    1.0
 * @author   CyberChimps
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     http://www.cyberchimps.com/
 */

// Set options function
function cyberchimps_option( $name = false, $subname = false ){
	$options = get_option( 'cyberchimps_options' );
	if( $name ) {
		$value = $options[$name];
		return $value;
	}
}

// Enqueue core scripts and core styles
function cyberchimps_core_scripts() {
	global $post;
	
	// Define paths
	$directory_uri = get_template_directory_uri();
	$js_path = $directory_uri . '/cyberchimps/lib/js/';
	$bootstrap_path = $directory_uri . '/cyberchimps/lib/bootstrap/';
	
	// Load JS for slimbox
	wp_enqueue_script( 'slimbox', $js_path . 'jquery.slimbox.js', array( 'jquery' ), true );

	// Load library for jcarousel
	wp_enqueue_script( 'jcarousel', $js_path . 'jquery.jcarousel.min.js', array( 'jquery' ), true );

	// Load Custom JS
	wp_enqueue_script( 'custom', $js_path . 'custom.js', array( 'jquery' ), true );
	
	// Load JS for swipe functionality in slider
	wp_enqueue_script( 'event-swipe-move', $js_path . 'jquery.event.move.js', array('jquery') );
	wp_enqueue_script( 'event-swipe', $js_path . 'jquery.event.swipe.js', array('jquery') );
	wp_enqueue_script( 'swipe', $js_path . 'swipe.js', array('jquery') );
	
	// Load Bootstrap Library Items
	wp_enqueue_style( 'bootstrap-style', $bootstrap_path . 'css/bootstrap.min.css', false, '2.0.4' );
	wp_enqueue_style( 'bootstrap-responsive-style', $bootstrap_path . 'css/bootstrap-responsive.min.css', array('bootstrap-style'), '2.0.4' );
	wp_enqueue_script( 'bootstrap-js', $bootstrap_path . 'js/bootstrap.min.js', array( 'jquery' ), '2.0.4', true );
	
	// Load Core Stylesheet
	wp_enqueue_style( 'core-style', $directory_uri . '/cyberchimps/lib/css/core.css', array('bootstrap-responsive-style', 'bootstrap-style'), '1.0' );
	
	// Load Theme Stylesheet
	wp_enqueue_style( 'style', get_stylesheet_uri(), array('core-style', 'bootstrap-responsive-style', 'bootstrap-style'), '1.0' );
	
	// Add thumbnail size
	if ( function_exists( 'add_image_size' ) ) { 
        add_image_size( 'featured-thumb', 100, 80, true);
        add_image_size( 'headline-thumb', 200, 225, true);
    } 
	
	// add javascript for comments
	if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
	
	if (cyberchimps_get_option( 'responsive_videos' ) == '1' ) {
		wp_register_script( 'video' , $js_path . 'video.js');
		wp_enqueue_script ('video');	
	}
}
add_action( 'wp_enqueue_scripts', 'cyberchimps_core_scripts', 20 );

function cyberchimps_create_layout() {
	global $post;
	
	if ( is_single() ) {
		$layout_type = cyberchimps_get_option( 'single_post_sidebar_options', 'right_sidebar' );
		
	} elseif ( is_home() ) {
		$layout_type = cyberchimps_get_option( 'sidebar_images', 'right_sidebar' );
	
	} elseif ( is_page() ) {
		$page_sidebar = get_post_meta( $post->ID, 'cyberchimps_page_sidebar' );
		$layout_type = ( isset( $page_sidebar[0] ) ) ? $page_sidebar[0] : 'right_sidebar';
				
	} elseif ( is_archive() ) {
		$layout_type = cyberchimps_get_option( 'archive_sidebar_options', 'right_sidebar' );
			
	} elseif ( is_search() ) {
		$layout_type = cyberchimps_get_option( 'search_sidebar_options', 'right_sidebar' );	
	
	} elseif ( is_404() ) {
		$layout_type = cyberchimps_get_option( 'error_sidebar_options', 'right_sidebar' );
	
	} else {
		$layout_type = apply_filters( 'cyberchimps_default_layout', 'right_sidebar' );
	}
	
	cyberchimps_get_layout($layout_type);
}
add_action('wp', 'cyberchimps_create_layout');

function cyberchimps_get_layout( $layout_type ) {
	
	$layout_type = ( $layout_type ) ? $layout_type : 'right_sidebar';
	
		switch($layout_type) {
			case 'full_width' :
				add_filter( 'cyberchimps_content_class', 'cyberchimps_class_span12');
			break;
			case 'right_sidebar' :
				add_action( 'cyberchimps_after_content_container', 'cyberchimps_add_sidebar_right');
				add_filter( 'cyberchimps_content_class', 'cyberchimps_class_span9');
				add_filter( 'cyberchimps_sidebar_right_class', 'cyberchimps_class_span3');
			break;
			case 'left_sidebar' :
				add_action( 'cyberchimps_before_content_container', 'cyberchimps_add_sidebar_left');
				add_filter( 'cyberchimps_content_class', 'cyberchimps_class_span9');
				add_filter( 'cyberchimps_sidebar_left_class', 'cyberchimps_class_span3');
			break;
			case 'content_middle' :
				add_action( 'cyberchimps_before_content_container', 'cyberchimps_add_sidebar_left');
				add_action( 'cyberchimps_after_content_container', 'cyberchimps_add_sidebar_right');
				add_filter( 'cyberchimps_content_class', 'cyberchimps_class_span6');
				add_filter( 'cyberchimps_sidebar_left_class', 'cyberchimps_class_span3');
				add_filter( 'cyberchimps_sidebar_right_class', 'cyberchimps_class_span3');
			break;
			case 'left_right_sidebar' :
				add_action( 'cyberchimps_after_content_container', 'cyberchimps_add_sidebar_left');
				add_action( 'cyberchimps_after_content_container', 'cyberchimps_add_sidebar_right');
				add_filter( 'cyberchimps_content_class', 'cyberchimps_class_span6');
				add_filter( 'cyberchimps_sidebar_left_class', 'cyberchimps_class_span3');
				add_filter( 'cyberchimps_sidebar_right_class', 'cyberchimps_class_span3');
			break;
		}
}

class cyberchimps_Walker extends Walker_Nav_Menu {
	
    function start_lvl( &$output, $depth ) {
		//In a child UL, add the 'dropdown-menu' class
		if( $depth == 0 ) {
			$indent = str_repeat( "\t", $depth );
			$output .= "\n$indent<ul class=\"dropdown-menu\">\n";
		} else {
			$indent = str_repeat( "\t", $depth );
			$output .= "\n$indent<ul>\n";
		}
	}
	
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$li_attributes = '';
		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : ( array ) $item->classes;

		//Add class and attribute to LI element that contains a submenu UL.
		if ( $args->has_children && $depth < 1 ){
			$classes[] 		= 'dropdown';
			$li_attributes .= 'data-dropdown="dropdown"';
		}
		$classes[] = 'menu-item-' . $item->ID;
		//If we are on the current page, add the active class to that menu item.
		$classes[] = ($item->current) ? 'active' : '';

		//Make sure you still add all of the WordPress classes.
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names . $li_attributes . '>';
		//Add attributes to link element.
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url ) ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		$attributes .= ($args->has_children && $depth < 1) ? ' class="dropdown-toggle"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= ($args->has_children && $depth < 1) ? ' <b class="caret"></b> ' : ''; 
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
	
	//Overwrite display_element function to add has_children attribute. Not needed in >= Wordpress 3.4
	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {

		if ( !$element )
			return;

		$id_field = $this->db_fields['id'];
		
		//display this element
		if ( is_array( $args[0] ) ) 
			$args[0]['has_children'] = ! empty( $children_elements[$element->$id_field] );
		else if ( is_object( $args[0] ) ) 
			$args[0]->has_children = ! empty( $children_elements[$element->$id_field] ); 
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array(&$this, 'start_el'), $cb_args);

		$id = $element->$id_field;

		// descend only when the depth is right and there are childrens for this element
		if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {

			foreach( $children_elements[ $id ] as $child ){

				if ( !isset($newlevel) ) {
					$newlevel = true;
					//start the child delimiter
					$cb_args = array_merge( array(&$output, $depth), $args);
					call_user_func_array(array(&$this, 'start_lvl'), $cb_args);
				}
				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
				unset( $children_elements[ $id ] );
		}

		if ( isset($newlevel) && $newlevel ){
			//end the child delimiter
			$cb_args = array_merge( array(&$output, $depth), $args);
			call_user_func_array(array(&$this, 'end_lvl'), $cb_args);
		}

		//end this element
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array(&$this, 'end_el'), $cb_args);
	}
}

// Sets fallback menu for 1 level. Could use preg_split to have children displayed too
function cyberchimps_fallback_menu() {
	$args = array(
		'depth'        => 1,
		'show_date'    => '',
		'date_format'  => '',
		'child_of'     => 0,
		'exclude'      => '',
		'include'      => '',
		'title_li'     => '',
		'echo'         => 0,
		'authors'      => '',
		'sort_column'  => 'menu_order, post_title',
		'link_before'  => '',
		'link_after'   => '',
		'walker'       => '',
		'post_type'    => 'page',
		'post_status'  => 'publish' 
	);
	$pages = wp_list_pages( $args );
	$prepend = '<ul id="menu-menu" class="nav">';
	$append = '</ul>';
	echo $prepend.$pages.$append;
}


if ( ! function_exists( 'cyberchimps_posted_on' ) ) :

//Prints HTML with meta information for the current post-date/time and author.
function cyberchimps_posted_on() {
	
	if( is_single() ) {
		$show_date = ( cyberchimps_option( 'single_post_byline_date' ) ) ? cyberchimps_option( 'single_post_byline_date' ) : false;
		$show_author = ( cyberchimps_option( 'single_post_byline_author' ) ) ? cyberchimps_option( 'single_post_byline_author' ) : false; 
	}
	elseif( is_archive() ) {
		$show_date = ( cyberchimps_option( 'archive_post_byline_date' ) ) ? cyberchimps_option( 'archive_post_byline_date' ) : false;  
		$show_author = ( cyberchimps_option( 'archive_post_byline_author' ) ) ? cyberchimps_option( 'archive_post_byline_author' ) : false;
	}
	else {
		$show_date = ( cyberchimps_option( 'post_byline_date' ) ) ? cyberchimps_option( 'post_byline_date' ) : false; 
		$show_author = ( cyberchimps_option( 'post_byline_author' ) ) ? cyberchimps_option( 'post_byline_author' ) : false; 
	}
	
	$posted_on = sprintf( __( '%8$s<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a><span class="byline">%9$s<span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'cyberchimps' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		( $show_date ) ? esc_html( get_the_date() ) : '',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'cyberchimps' ), get_the_author() ) ),
		( $show_author ) ? esc_html( get_the_author() ) : '',
		( $show_date ) ? 'Posted on ' : '',
		( $show_author ) ? ' by ' : ''
	);
	apply_filters( 'cyberchimps_posted_on', $posted_on );
	echo $posted_on;
}
endif;

//add meta entry category to single post, archive and blog list if set in options
function cyberchimps_posted_in() {
	global $post;

	if( is_single() ) {
		$show = ( cyberchimps_option( 'single_post_byline_categories' ) ) ? cyberchimps_option( 'single_post_byline_categories' ) : false; 
	}
	elseif( is_archive() ) {
		$show = ( cyberchimps_option( 'archive_post_byline_categories' ) ) ? cyberchimps_option( 'archive_post_byline_categories' ) : false;  
	}
	else {
		$show = ( cyberchimps_option( 'post_byline_categories' ) ) ? cyberchimps_option( 'post_byline_categories' ) : false;  
	}
	if( $show ):
				$categories_list = get_the_category_list( __( ', ', 'cyberchimps' ) );
				if ( $categories_list ) :
				$cats = sprintf( __( 'Posted in %1$s', 'cyberchimps' ), $categories_list );
			?>
			<span class="cat-links">
				<?php echo apply_filters( 'cyberchimps_post_categories', $cats ); ?>
			</span>
      <span class="sep"> <?php echo apply_filters( 'cyberchimps_entry_meta_sep', '|' ); ?> </span>
	<?php endif;
	endif;
}

//add meta entry tags to single post, archive and blog list if set in options
function cyberchimps_post_tags() {
	global $post;
	
	if( is_single() ) {
		$show = ( cyberchimps_option( 'single_post_byline_tags' ) ) ? cyberchimps_option( 'single_post_byline_tags' ) : false; 
	}
	elseif( is_archive() ) {
		$show = ( cyberchimps_option( 'archive_post_byline_tags' ) ) ? cyberchimps_option( 'archive_post_byline_tags' ) : false;  
	}
	else {
		$show = ( cyberchimps_option( 'post_byline_tags' ) ) ? cyberchimps_option( 'post_byline_tags' ) : false;  
	}
	if( $show ):
	$tags_list = get_the_tag_list( '', __( ', ', 'cyberchimps' ) );
				if ( $tags_list ) :
				$tags = sprintf( __( 'Tags: %1$s', 'cyberchimps' ), $tags_list );
			?>
			<span class="tag-links">
				<?php echo apply_filters( 'cyberchimps_post_tags', $tags ); ?>
			</span>
      <span class="sep"> <?php echo apply_filters( 'cyberchimps_entry_meta_sep', '|' ); ?> </span>
			<?php endif; // End if $tags_list
	endif;
}

//add meta entry comments to single post, archive and blog list if set in options
function cyberchimps_post_comments() {
	global $post;
	
	if( is_single() ) {
		$show = ( cyberchimps_option( 'single_post_byline_comments' ) ) ? cyberchimps_option( 'single_post_byline_comments' ) : false; 
	}
	elseif( is_archive() ) {
		$show = ( cyberchimps_option( 'archive_post_byline_comments' ) ) ? cyberchimps_option( 'archive_post_byline_comments' ) : false;  
	}
	else {
		$show = ( cyberchimps_option( 'post_byline_comments' ) ) ? cyberchimps_option( 'post_byline_comments' ) : false;  
	}
	$leave_comment = ( is_single() || is_page() ) ? '' : __( 'Leave a comment', 'cyberchimps' );
	if( $show ):
		if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
			<span class="comments-link"><?php comments_popup_link( $leave_comment, __( '1 Comment', 'cyberchimps' ), __( '% Comments', 'cyberchimps' ) ); ?></span>
      <span class="sep"> <?php echo ( $leave_comment != '' ) ? apply_filters( 'cyberchimps_entry_meta_sep', '|' ) : ''; ?> </span>
    <?php endif;
	endif;
}

// change default comments labels and form
add_filter( 'comment_form_defaults', 'cyberchimps_comment_form_filter' );
function cyberchimps_comment_form_filter( $defaults ) {
	$defaults['title_reply'] = __( 'Leave a comment', 'cyberchimps' );
	return $defaults;
}

// add featured image to single post, archive and blog page if set in options
function cyberchimps_featured_image() {
	global $post;
	
	if( is_single() ) {
		$show = ( cyberchimps_option( 'single_post_featured_images' ) ) ? cyberchimps_option( 'single_post_featured_images' ) : false; 
	}
	elseif( is_archive() ) {
		$show = ( cyberchimps_option( 'archive_featured_images' ) ) ? cyberchimps_option( 'archive_featured_images' ) : false;  
	}
	else {
		$show = ( cyberchimps_option( 'post_featured_images' ) ) ? cyberchimps_option( 'post_featured_images' ) : false;  
	}
	if( $show ):
		if( has_post_thumbnail() ): ?>
    <div class="featured-image">
      <?php the_post_thumbnail( apply_filters( 'cyberchimps_post_thumbnail_size', 'thumbnail' ) ); ?>
    </div>
<?php endif;
		endif;
}

// add breadcrumbs to single posts and archive pages if set in options
function cyberchimps_breadcrumbs() {
	global $post;
	
	if( is_single() ) {
		$show = ( cyberchimps_option( 'single_post_breadcrumbs' ) ) ? cyberchimps_option( 'single_post_breadcrumbs' ) : false; 
	}
	elseif( is_archive() ) {
		$show = ( cyberchimps_option( 'archive_breadcrumbs' ) ) ? cyberchimps_option( 'archive_breadcrumbs' ) : false;  
	}
	if( isset( $show ) ):
		do_action( 'breadcrumbs' );
	endif;
}
add_action( 'cyberchimps_before_container', 'cyberchimps_breadcrumbs' );

function cyberchimps_post_format_icon() {
	global $post;
	
	$format = get_post_format( $post->ID );
	if( $format == '' ) {
		$format = 'default'; 
	}
	
	if( is_single() ) {
		$show = ( cyberchimps_option( 'single_post_format_icons' ) ) ? cyberchimps_option( 'single_post_format_icons' ) : false; 
	}
	elseif( is_archive() ) {
		$show = ( cyberchimps_option( 'archive_format_icons' ) ) ? cyberchimps_option( 'archive_format_icons' ) : false;  
	}
	else {
		$show = ( cyberchimps_option( 'post_format_icons' ) ) ? cyberchimps_option( 'post_format_icons' ) : false;  
	}
	if( $show ):
	?>
	
	<div class="postformats"><!--begin format icon-->
		<img src="<?php echo get_template_directory_uri(); ?>/images/formats/<?php echo $format; ?>.png" alt="formats" />
	</div><!--end format-icon-->
<?php	
	endif;
}

// Returns true if a blog has more than 1 category
function cyberchimps_categorized_blog() {
	if ( false === ( $cyberchimps_categorized_transient = get_transient( 'cyberchimps_categorized_transient' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$cyberchimps_categorized_transient = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$cyberchimps_categorized_transient = count( $cyberchimps_categorized_transient );

		set_transient( 'cyberchimps_categorized_transient', $cyberchimps_categorized_transient );
	}

	if ( '1' != $cyberchimps_categorized_transient ) {
		// This blog has more than 1 category so cyberchimps_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so cyberchimps_categorized_blog should return false
		return false;
	}
}

// Flush out the transients used in cyberchimps_categorized_blog
function cyberchimps_category_transient_flusher() {
	// Remove transient
	delete_transient( 'cyberchimps_categorized_transient' );
}
add_action( 'edit_category', 'cyberchimps_category_transient_flusher' );
add_action( 'save_post', 'cyberchimps_category_transient_flusher' );

// Prints out default title of the site.
function cyberchimps_default_site_title() {
	global $page, $paged;

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'cyberchimps' ), max( $paged, $page ) );
}
add_filter('wp_title', 'cyberchimps_default_site_title');


// Remove default site title if seo plugin is active
function cyberchimps_seo_compatibility_check() {
	if ( cyberchimps_detect_seo_plugins() ) {
		remove_filter( 'wp_title', 'cyberchimps_default_site_title', 10, 3 );
	}
}
add_action( 'after_setup_theme', 'cyberchimps_seo_compatibility_check', 5 );

// Detect some SEO Plugin that add constants, classes or functions.
function cyberchimps_detect_seo_plugins() {

	return cyberchimps_detect_plugin(
		// Use this filter to adjust plugin tests.
		apply_filters(
			'cyberchimps_detect_seo_plugins',
			/** Add to this array to add new plugin checks. */
			array(

				// Classes to detect.
				'classes' => array(
					'wpSEO',
					'All_in_One_SEO_Pack',
					'HeadSpace_Plugin',
					'Platinum_SEO_Pack',
				),

				// Functions to detect.
				'functions' => array(),

				// Constants to detect.
				'constants' => array( 'WPSEO_VERSION', ),
			)
		)
	);
}

// Detect event plugins
function cyberchimps_detect_event_plugins() {
	return cyberchimps_detect_plugin(
		// Use this filter to adjust plugin tests.
		apply_filters(
			'cyberchimps_detect_event_plugins',
			/** Add to this array to add new plugin checks. */
			array(

				// Classes to detect.
				'classes' => array( 'TribeEvents' ),

				// Functions to detect.
				'functions' => array(),

				// Constants to detect.
				'constants' => array(),
			)
		)
	);
}

// Detect plugin by constant, class or function existence.
function cyberchimps_detect_plugin( $plugins ) {

	/** Check for classes */
	if ( isset( $plugins['classes'] ) ) {
		foreach ( $plugins['classes'] as $name ) {
			if ( class_exists( $name ) )
				return true;
		}
	}

	/** Check for functions */
	if ( isset( $plugins['functions'] ) ) {
		foreach ( $plugins['functions'] as $name ) {
			if ( function_exists( $name ) )
				return true;
		}
	}

	/** Check for constants */
	if ( isset( $plugins['constants'] ) ) {
		foreach ( $plugins['constants'] as $name ) {
			if ( defined( $name ) )
				return true;
		}
	}

	/** No class, function or constant found to exist */
	return false;
}

// Set read more link for recent post element
function cyberchimps_recent_post_excerpt_more($more) {

	global $custom_excerpt, $post;
    
   		if ($custom_excerpt == 'recent') {
    		$linktext = 'Continue Reading';
    	}
    	
	return '&hellip;
			</p>
			<div class="more-link">
				<span class="continue-arrow"><img src="'. get_template_directory_uri() .'/cyberchimps/lib/images/continue.png"></span><a href="'. get_permalink($post->ID) . '">  '.$linktext.'</a>
			</div>';
}

// Set read more link for featured post element
function cyberchimps_featured_post_excerpt_more($more) {
	global $post;
	return '&hellip;</p></span><a href="'. get_permalink($post->ID) . '">Read More...</a>';
}

// Set length of the excerpt
function cyberchimps_featured_post_length( $length ) {
	return 70;
}

// For magazine wide post
function cyberchimps_magazine_post_wide( $length ) {
	return 130;
}

// more text for search results excerpt
function cyberchimps_search_excerpt_more( $more ){
	global $post;
	if( cyberchimps_option( 'search_post_read_more' ) != '' ){
		$more = '<p><a href="'. get_permalink($post->ID) . '">'.cyberchimps_option( 'search_post_read_more' ).'</a></p>';
		return $more;
	}
	else {
		$more = '<p><a href="'. get_permalink($post->ID) . '">Read More...</a></p>';
		return $more;
	}
}

// excerpt length for search results
function cyberchimps_search_excerpt_length( $length ){
	global $post;
	if( cyberchimps_option( 'search_post_excerpt_length' ) != '' ) {
		$length = cyberchimps_option( 'search_post_excerpt_length' );
		return $length;
	}
	else {
		$length = 55;
		return $length;
	}
}

//For archive posts
function cyberchimps_archive_excerpt_more( $more ){
	global $post;
	if( cyberchimps_option( 'blog_read_more_text' ) != '' ){
		$more = '<p><a href="'. get_permalink($post->ID) . '">'.cyberchimps_option( 'blog_read_more_text' ).'</a></p>';
		return $more;
	}
	else {
		$more = '<p><a href="'. get_permalink($post->ID) . '">Read More...</a></p>';
		return $more;
	}
}
if( cyberchimps_option( 'archive_post_excerpts' ) ){
	add_filter( 'excerpt_more', 'cyberchimps_blog_excerpt_more', 999 );
}

//For blog posts
function cyberchimps_blog_excerpt_more( $more ){
	global $post;
	if( cyberchimps_option( 'blog_read_more_text' ) != '' ){
		$more = '<p><a href="'. get_permalink($post->ID) . '">'.cyberchimps_option( 'blog_read_more_text' ).'</a></p>';
		return $more;
	}
	else {
		$more = '<p><a href="'. get_permalink($post->ID) . '">Read More...</a></p>';
		return $more;
	}
}
if( cyberchimps_option( 'post_excerpts' ) ){
	add_filter( 'excerpt_more', 'cyberchimps_blog_excerpt_more', 999 );
}

function cyberchimps_blog_excerpt_length( $length ) {
	global $post;
	if( cyberchimps_option( 'blog_excerpt_length' ) != '' ) {
		$length = cyberchimps_option( 'blog_excerpt_length' );
		return $length;
	}
	else {
		$length = 55;
		return $length;
	}
}
if( cyberchimps_option( 'post_excerpts' ) ){
	add_filter( 'excerpt_length', 'cyberchimps_blog_excerpt_length', 999 );
}

/*	gets post views */
function cyberchimps_getPostViews($postID){ 
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 View";
    }
    return $count.' Views';
}

/*	Sets post views	*/
function cyberchimps_setPostViews($postID) { 
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

/* To correct issue: adjacent_posts_rel_link_wp_head causes meta to be updated multiple times */
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

// Set up half slide for iFeature pro slider, adds it before post/page content
function cyberchimps_half_slider() {
	global $post;
	if( is_page() ) {
		$page_section_order = get_post_meta($post->ID, 'cyberchimps_page_section_order' , true);
		//if page_section_order is empty sets page as default
		$page_section_order = ( $page_section_order == '' ) ? array( 'page_section' ) : $page_section_order;
		if( in_array( 'page_slider', $page_section_order, true ) ) {
			$slider_size = get_post_meta( $post->ID, 'cyberchimps_slider_size', true );
			if( $slider_size == 'half' ) {
				do_action( 'page_slider' );
			}
		}
	}
	else {
		$blog_section_order = cyberchimps_get_option( 'blog_section_order' );
		//select default in case options are empty
		$blog_section_order = ( $blog_section_order == '' ) ? array( 'blog_post_page' ) : $blog_section_order;
		if( in_array( 'page_slider', $blog_section_order, true ) ) {
			$slider_size = cyberchimps_get_option( 'blog_slider_size' );
			if( $slider_size == 'half' ) {
				do_action( 'page_slider' );
			}
		}
	}
}
add_action( 'cyberchimps_before_content', 'cyberchimps_half_slider' );

// Modal welcome note
function cyberchimps_modal_welcome_note() { 
	if( cyberchimps_option( 'modal_welcome_note_display' ) == 1 ): ?>
  <div class="modal" id="welcomeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <h3 id="myModalLabel">Welcome</h3>
    </div>
    <div class="modal-body">
      	<?php printf( __( '
					<p>Congratulations you have successfully installed %1$s!</p>
										
					<p>Your website is important to us, so please read the <a href="%3$s" target="_blank">instructions</a> to learn how to use %1$s.</p>
					
					<p>If you have any questions please post in our <a href="%4$s" target="_blank">support forum</a>, and we will get back to you as soon as we can.</p>
										
					<p>Thank you for choosing CyberChimps Professional WordPress Themes!</p>', 'cyberchimps' ),
					apply_filters( 'cyberchimps_current_theme_name', 'CyberChimps' ),
					apply_filters( 'cyberchimps_upgrade_link', 'http://cyberchimps.com/store/' ),
					apply_filters( 'cyberchimps_upgrade_pro_title', __( 'Pro', 'cyberchimps' ) ),
					apply_filters( 'cyberchimps_documentation', 'http://cyberchimps.com/help/' ),
					apply_filters( 'cyberchimps_support_forum', 'http://cyberchimps.com/forum/pro/' )
					);					
		?>
    </div>
    <div class="modal-footer">
      <input type="submit" id="welcomeModalSave" class="btn btn-primary" name="update" value="<?php esc_attr_e( 'Complete Installation', 'cyberchimps' ); ?>" />
    </div>
  </div>
<?php
	endif;
}
add_action( 'cyberchimps_options_form_start', 'cyberchimps_modal_welcome_note' );

// Help text
function cyberchimps_options_help_text() {
	$text = '';
	$instruction_img = get_template_directory_uri().'/cyberchimps/options/lib/images/document.png';
	$support_img = get_template_directory_uri().'/cyberchimps/options/lib/images/questionsupport.png';
	$text .= '<div class="cc_help_section">
						<div class="row-fluid"><div class="span3">
							<a href="'.apply_filters( 'cyberchimps_documentation', 'http://cyberchimps.com' ).'" title="CyberChimps Instructions">
								<img src="'.$instruction_img.'" alt="CyberChimps Instructions" />
								<div class="cc_help_caption"><p>'.__( 'Instructions', 'cyberchimps' ).'</p></div>
							</a>
						</div>
						<div class="span3">
							<a href="'.apply_filters( 'cyberchimps_support_forum', 'http://cyberchimps.com' ).'" title="CyberChimps Support">
								<img src="'.$support_img.'" alt="CyberChimps Help" />
								<div class="cc_help_caption"><p>'.__( 'Support', 'cyberchimps' ).'</p></div>
							</a>
						</div>
						</div>';
	// Upgrade Button and text for free themes
	if( cyberchimps_theme_check() == 'free' ) {
	$text .= 	'<div class="row-fluid">
						<div class="span6">
						<div class="cc_help_upgrade_bar">'. sprintf( __( 'Upgrade to %1$s', 'cyberchimps' ), apply_filters( 'cyberchimps_upgrade_pro_title', 'CyberChimps Pro' ) ) .'</div>
						</div>
						</div>
						</div>
						<div class="clear"></div>';
		$text .= sprintf( __( '<p>If you want even more amazing new features <a href="%1$s" title="%2$s">%2$s</a> which includes a Custom Features Slider, Product Element, Image Carousel, Widgetized Boxes, Callout Section, expanded typography and many more powerful new features. Please visit <a href="cyberchimps.com" title="CyberChimps">CyberChimps.com</a> to learn more!</p>', 'cyberchimps' ),
		apply_filters( 'cyberchimps_upgrade_link', 'http://cyberchimps.com' ),
		apply_filters( 'cyberchimps_upgrade_pro_title', 'CyberChimps Pro' )
		);
	}
	//text for pro themes
	else {
		$text .= '</div><div class="clear"></div>';
	}
	return $text;
}
add_filter( 'cyberchimps_help_description', 'cyberchimps_options_help_text' );

// upgrade bar for free themes
function cyberchimps_upgrade_bar() { ?>
	<div class="upgrade-callout">
		<p><img src="<?php echo get_template_directory_uri() ;?>/cyberchimps/options/lib/images/chimp.png" alt="CyberChimps" />
    <?php printf( __( 'Welcome to %1$s! Learn more now about upgrading to <a href="%2$s" target="_blank" title="%3$s">%3$s</a> today.', 'cyberchimps' ),
		apply_filters( 'cyberchimps_current_theme_name', 'CyberChimps' ),
		apply_filters( 'cyberchimps_upgrade_link', 'http://cyberchimps.com' ),
		apply_filters( 'cyberchimps_upgrade_pro_title', 'Pro' )
		 ); ?>	
		</p>
		<div class="social-container">
		<div class="social">
			<a href="https://twitter.com/cyberchimps" class="twitter-follow-button" data-show-count="false" data-size="small">Follow @cyberchimps</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</div>
		<div class="social">
			<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fcyberchimps.com%2F&amp;send=false&amp;layout=button_count&amp;width=200&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:21px;" allowTransparency="true"></iframe>
		</div>
		</div>
	</div>
<?php
}
if( cyberchimps_theme_check() == 'free' ) {
	add_action( 'cyberchimps_options_before_container', 'cyberchimps_upgrade_bar' );
}

// Hide preview and view on custom post types
function cyberchimps_posttype_admin_css() {
    global $post_type;
    if($post_type == 'custom_slides' || $post_type == 'boxes' || $post_type == 'featured_posts' || $post_type == 'portfolio_images') {
    echo '<style type="text/css">#view-post-btn,#post-preview{display: none;}</style>';
    }
}
add_action('admin_head', 'cyberchimps_posttype_admin_css');

// funationality for responsive toggle
function cyberchimps_responsive_stylesheet() {
	if( cyberchimps_get_option( 'responsive_design' ) ){
		wp_enqueue_style( 'cyberchimps_responsive', get_template_directory_uri() . '/cyberchimps/lib/bootstrap/css/cyberchimps-responsive.min.css', array('bootstrap-responsive-style', 'bootstrap-style'), '1.0' );
	}
	else {
		wp_dequeue_style( 'cyberchimps_responsive' );
	}
}
add_action( 'wp_enqueue_scripts', 'cyberchimps_responsive_stylesheet', 25 );

/**
* Add link to theme options in Admin bar.
*/ 
function cyberchimps_admin_link() {
	global $wp_admin_bar;

	$wp_admin_bar->add_menu( array( 
								'id'	 => 'cyberchimps',
								'title'	 => apply_filters( 'cyberchimps_current_theme_name', 'CyberChimps '. __( 'Options', 'cyberchimps' ) ) . __( ' Options', 'cyberchimps' ),
								'href'	 => admin_url('themes.php?page=cyberchimps-theme-options')  
								  ) ); 
}
add_action( 'admin_bar_menu', 'cyberchimps_admin_link', 113 );

function cyberchimps_google_analytics() {
	$code = cyberchimps_option( 'google_analytics' );
	if( $code != '' ) {
		echo '<script type="text/javascript">'.$code.'</script>';
	}
}
add_action( 'cyberchimps_after_wrapper', 'cyberchimps_google_analytics' );
?>