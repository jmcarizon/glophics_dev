<?php
/**
 * Twenty Nineteen functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

/**
 * Twenty Nineteen only works in WordPress 4.7 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function twentynineteen_widgets_init() {

	register_sidebar(
		array(
			'name'          => __( 'Footer', 'twentynineteen' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Add widgets here to appear in your footer.', 'twentynineteen' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

}
add_action( 'widgets_init', 'twentynineteen_widgets_init' );

/** Start of WordPress Team Used Functions **/

function get_includes( $name = null ) {
	do_action( 'get_includes', $name );

	$templates = array();
	$name = (string) $name;
	$templates[] = "includes/{$name}.php";

	// Backward compat code will be removed in a future release
	if ('' == locate_template($templates, true))
		load_template( ABSPATH . WPINC . '/theme-compat/sidebar.php');
}

function getcontenturl( $atts ) {
    $return = '';
    switch($atts['type']){
        case 'templateurl':
            $return = get_template_directory_uri();
        break;
        case 'siteurl':
            $return = site_url($atts['page']);
        break;
        default:
            $return = home_url();
        break;
    }
    return $return;
}
add_shortcode( 'contenturl', 'getcontenturl' );

/* Search Result Pagination*/

function kriesi_pagination($pages = '', $range = 2) {
	$showitems = ($range * 2)+1;

	global $paged;
	if(empty($paged)) $paged = 1;

	if($pages == '')
	{
		global $wp_query;
		$pages = $wp_query->max_num_pages;
		if(!$pages)
		{
			$pages = 1;
		}
	}

	if(1 != $pages)
	{
		echo "<div class='pagination'>";
		if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
		if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";

		for ($i=1; $i <= $pages; $i++)
		{
			if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
			{
				echo ($paged == $i)? "<a class='current'>".$i."</a>":"<a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
			}
		}

		if ($paged < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";
		if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
		echo "</div>\n";
	}
}

if ( ! function_exists( 'twentyten_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentyten_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_comment( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    switch ( $comment->comment_type ) :
        case '' :
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <div id="comment-<?php comment_ID(); ?>">
        <div class="comment-author vcard">
            <!-- <//?php echo get_avatar( $comment, 40 ); ?> -->
            <?php printf( __( '%s <span class="says"></span>', 'twentyten' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
        </div><!-- .comment-author .vcard -->
        <?php if ( $comment->comment_approved == '0' ) : ?>
            <em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentyten' ); ?></em>
            <br />
        <?php endif; ?>

        <div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
            <?php
                /* translators: 1: date, 2: time */
                printf( __( '%1$s', 'twentyten' ), get_comment_date()); ?></a><?php edit_comment_link( __( '(Edit)', 'twentyten' ), ' ' );
            ?>
        </div><!-- .comment-meta .commentmetadata -->

        <div class="comment-body"><?php comment_text(); ?></div>

        <div class="reply">
            <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
        </div>
    </div><!-- #comment-##  -->

    <?php
            break;
        case 'pingback'  :
        case 'trackback' :
    ?>
    <li class="post pingback">
        <p><?php _e( 'Pingback:', 'twentyten' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'twentyten' ), ' ' ); ?></p>
    <?php
            break;
    endswitch;
}
endif;
/* End Result Pagination*/

/* Start Featured Image Thumbnail*/

add_filter( 'post_thumbnail_size', function( $size ) {
	if( is_string( $size ) && 'full' === $size )
		add_filter(
			'wp_calculate_image_srcset_meta',
			'__return_null_and_remove_current_filter'
		);
	return $size;
} );

// Would be handy, in this example, to have this as a core function ;-)
function __return_null_and_remove_current_filter ( $var )
{
    remove_filter( current_filter(), __FUNCTION__ );
    return null;
}
/* End Feeatured Image Thumbnail*/

/* Start for Remove Emoji Warning */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
/* End for Remove Emoji Warning */

/* Start for recent_comments Warning */
add_filter( 'show_recent_comments_widget_style', '__return_false', 99 );

//remove wp-embed warning
function my_deregister_scripts(){
	wp_deregister_script( 'wp-embed' );
}
add_action( 'wp_footer', 'my_deregister_scripts' );
/* End for recent_comments Warning */

/* Start of SEO H1 Shortcode */
function get_short_title( $atts ) {
	extract( shortcode_atts( array( 'id' => null, ), $atts ) );
	$pageTitle = new WP_Query( 'page_id='.$id );
	while ( $pageTitle->have_posts() ) {
		$pageTitle->the_post();
		$short_title= get_post_meta( get_the_ID(), 'Short Title',true );

				if ($short_title == '') {
					$block = '';
				echo $block;

				} else {
					$block = '<h1 class=h1_hdng>'.$short_title.'</h1>';
					echo $block;
				}
	}
	wp_reset_postdata();
}
add_shortcode( 'short_title', 'get_short_title' );
/* End of SEO H1 Shortcode */

/* Start of Page Intro Shortcode */
function get_page_intro( $atts ) {
	extract( shortcode_atts( array( 'id' => null, ), $atts ) );
	$pageIntro = new WP_Query( 'page_id='.$id );
	while ( $pageIntro->have_posts() ) {
		$pageIntro->the_post();
		$short_intro= get_post_meta( get_the_ID(), 'Page Intro',true );

				if ($short_intro == '') {
					$block = '';
				echo $block;

				} else {
					$block = '<div class=intro_txt>'.$short_intro.'</div>';
					echo $block;
				}

	}
	wp_reset_postdata();
}
add_shortcode( 'page_intro', 'get_page_intro' );
/* End of Page Intro Shortcode */

/* Start of Blog  Activation Code*/

if ( ! function_exists( 'twentyten_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since Twenty Ten 1.0
 */
function twentyten_posted_in() {
    // Retrieves tag list of current post, separated by commas.
    $tag_list = get_the_tag_list( '', ', ' );
    if ( $tag_list ) {
        $posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
    } elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
        $posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
    } else {
        $posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
    }
    // Prints the string, replacing the placeholders.
    printf(
        $posted_in,
        get_the_category_list( ', ' ),
        $tag_list,
        get_permalink(),
        the_title_attribute( 'echo=0' )
    );
}

if(!is_admin()){
    add_action('init', 'search_query_fix');
    function search_query_fix(){
        if(isset($_GET['s']) && $_GET['s']==''){
            $_GET['s']=' ';
        }
    }
}

endif;
/* End of Blog  Activation Code*/

/** End of WordPress Team Used Functions **/
