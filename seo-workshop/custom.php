<?php
function wpb_imagelink_setup() {
	$image_set = get_option( 'image_default_link_type' );
	
	if ($image_set !== 'none') {
		update_option('image_default_link_type', 'none');
	}
}
add_action('admin_init', 'wpb_imagelink_setup', 10);

/* Enqueue the scripts 

function jq_script_method() {
    wp_enqueue_script( 'jquery' );
}

add_action( 'wp_enqueue_scripts', 'jq_script_method' ); // wp_enqueue_scripts action hook to link only on the front-end

function masonry_script_method() {
    wp_enqueue_script( 'jquery-masonry', 1 );
}

add_action( 'wp_enqueue_scripts', 'masonry_script_method' ); // wp_enqueue_scripts action hook to add masonry in footer

*/

/*====================================================================*/
/* jQuery Masonry
 * 
 * ===================================================================*/

/* Add Masonry and link to masonry control script file into footer */

function cw_enqueue_masonry() {
	
	// Run on the front page (static page) only
	if (is_front_page() || is_archive()) {
    
    // Register the control script - in a folder called js in the active Thesis skin
    wp_register_script( 'masonry_control', THESIS_USER_SKIN_URL . '/js/masonry_control.js', array( 'jquery' ), null, true);
	
	// Don't need to register masonry as it already has a WP handle - just enqueue it, and WP builds it into footer
	wp_enqueue_script('jquery-masonry', '', array('jquery'), '', true);
	
	// Enqueue the masonry controls - they will be built into the footer
    wp_enqueue_script('masonry_control', '', array('jquery'), '', true);
    
    }
}

// Bring link to masonry and control script through on front end
add_action('wp_enqueue_scripts', 'cw_enqueue_masonry', 10, 0);

/* Add CSS for masonry into head- this MUST be in head rather than in stylesheet
 * 
 * Define the masonry container as #linky. Masonry units as .boxy, .boxy2
 * 
 * Important to define widths
 * 
 */


function cw_masonry_css() {
	
	// Run on the front page (static page) only
	if (is_front_page()) {
		
		echo '
		<style type="text/css">
			#linky { width:100%; }
			#linky .boxy { width: 300px; }
			#linky .boxy2 { width:620px; }
			@media only screen and (max-width:660px),
				screen and (max-device-width:660px) {
				#linky { width: 350px; }
				.boxy2 { max-width: 100%; width:auto; height: auto; }
				.boxy { max-width: 100%; width:auto; height: auto; }
				}
			@media only screen and (max-width:340px),
				screen and (max-device-width:340px) {
				#linky { width:auto; }
				}
				
			@media (min-width: 660px){
				#linky { width:100%; }
				#linky .boxy { width: 300px; }
				#linky .boxy2 { width:620px; }
				}
		</style>
		';
	}
}

// Add hook for front-end
add_action('wp_head', 'cw_masonry_css');

/*======================================================================
 * End masonry 
 * ===================================================================*/
 
add_action( 'wp_enqueue_scripts', 'carawebs_load_common_javascript_files' );

function carawebs_load_common_javascript_files() {
	
	wp_register_script( 'global_scripts', THESIS_USER_SKIN_URL . '/js/common_scripts.js', array( 'jquery' ), '1.0', true);
	
	wp_enqueue_script('global_scripts');
	
}

/*====================================================================*/

/**MAIN QUERY: CUSTOM POST TYPES**/

// Show posts of 'post', 'page' and 'project' post types in the main query

add_action( 'pre_get_posts', 'add_my_post_types_to_query' );

function add_my_post_types_to_query( $query ) {
	if ( ! is_admin() && ! is_home() && $query->is_main_query() )
		$query->set( 'post_type', array( 'post', 'page', 'project', 'people' ) );
	return $query;
}

/*====================================================================*/

/***Allow vCards***/

add_filter('upload_mimes', 'custom_upload_mimes');
function custom_upload_mimes ( $existing_mimes=array() ) {
	// add your extension to the array
	$existing_mimes['vcf'] = 'text/x-vcard';
	return $existing_mimes;
}

/*====================================================================*/

/**Next & previous project in category link**/

function carawebs_add_next_project() {
	
	$categories = get_the_category();
	$category_id = $categories[0]->cat_ID; // The category ID for the current post
	
		// Previous Posts
		if( get_adjacent_post(true, '', true) ) { // Is there an adjacent post in the same category, previous = true
			
		?><div class ="half"><?php echo previous_post_link('%link', '&laquo View Previous Project', TRUE);?></div><?php
		
		}
		
		// Get the last post to make an infinite loop
		else { 
						
			$first = new WP_Query(array(
				'post_type'=> 'project',
				'cat' => $category_id,
				'order' => 'DESC')
			);
						
			$first->the_post(); // last post in the loop which hasn't looped
			
			echo '<a href="' . get_permalink() . '">&laquo; View Previous Project</a>';
			
			wp_reset_postdata();
			
		}; 
			
		// Next Posts	
		if( get_adjacent_post(true, '', false) ) { // Adjacent post, same cat, previous = false
			
			?><div class="half_right"><?php echo next_post_link('%link', 'View Next Project &raquo', TRUE);?></div><?php
		}
		
		// Get the first post in this category
		else { 
			
			$last = new WP_Query(array(
				'post_type'=> 'project',
				'cat' => $category_id,
				'order' => 'ASC')
			);
			
			$last->the_post();
			
			?><div class="half_right"><?php	echo '<a href="' . get_permalink() . '">View Next Project &raquo;</a>';?></div><?php
						
			wp_reset_postdata();
		}; 
	
}

add_action('hook_bottom_next_in_cat', 'carawebs_add_next_project');

/*====================================================================*/

/**Back to Blog page **/

function add_back_to_blog() {
	
	//get the page id for the page used for blog posts
	$index_id = ( int ) get_option( 'page_for_posts', 0 );
	//set a constant with this page's permalink
	$blogpage = get_permalink( $index_id );
	//set a constant with this page's title
	$blogtitle = get_the_title( $index_id);
	
	// Link to the Blog archive page
	?><a class="small_font" href="<?php echo $blogpage; ?>">&laquo Back to all news</a><?php
	
}

add_action('hook_bottom_next_post', 'add_back_to_blog');

/*====================================================================*/

/**Back to Team **/

function add_back_to_about() {
	
	$url = site_url('/about/#people')
	
	?><a class="small_font" href="<?php echo $url; ?>">&laquo Back to Team</a><?php
	
}

add_action('hook_bottom_back_to_about', 'add_back_to_about');

/*====================================================================*/

/***ADD INTRO CUSTOM FIELD TO FRONT PAGE***/

function add_home_intro_text() {
	
	?><div class ="home_intro_text"><?php the_field('home_page_intro'); ?></div>
    
   <?php
    
}
add_action('hook_bottom_home_intro', 'add_home_intro_text');

/*====================================================================*/

/* Search Title */

function add_search_title() {
	
	?><h2 class="headline no_top_padding">Search Results for the Term: <?php echo the_search_query();?></h2>
	
	<?php
	
}

add_action('hook_top_search_title', 'add_search_title');


/*====================================================================*/

/* Enable Excerpts on Pages*/

add_action('init', 'page_excerpts');

	function page_excerpts() {
		
		add_post_type_support( 'page', 'excerpt' );
}

/*====================================================================*/

/*CHANGE WORDPRESS EXCERPT LENGTH*/


function new_excerpt_length($length) {
	return 29;
}
add_filter('excerpt_length', 'new_excerpt_length');

/*====================================================================*/

/* Insert Read more link on Thesis hook */

function add_read_more() {
	
		?><a href="<?php echo get_permalink();?>">Read More...</a><?php

	}

add_action ('hook_bottom_read_more', 'add_read_more');

/*====================================================================*/

/***Read more same line**/

function add_read_more_sameline() {
	
	echo '<a href="' . get_permalink($post->ID) . '">' . '<span class="readmore">Read more &raquo</span></a>';
		

	}

add_action ('hook_bottom_read_more_sameline', 'add_read_more_sameline');

/*====================================================================*/

/**FORCE MEDIUM IMAGE CROP**/

function add_force_crop() {


		if(false === get_option("medium_crop")) {
			add_option("medium_crop", "1");
		} else {
			update_option("medium_crop", "1");
		}
	}
	
add_action ('add_attachment','add_force_crop');

/**FORCE LARGE IMAGE CROP**/
/*add_action( 'init', create_function( '', 'add_image_size( "cropped_large", 940, 540, true );' ) );*/
/*
function add_force_crop_large() {


		if(false === get_option("large_crop")) {
			add_option("large_crop", "1");
		} else {
			update_option("large_crop", "1");
		}
	}
	
add_action ('add_attachment','add_force_crop_large');

*/

/************************************************************************
 * 					ABOUT US PAGE FUNCTIONS
 * 
 ***********************************************************************/

/***ADD INTRO CUSTOM FIELD TO ABOUT PAGE***/

function carawebs_about_intro_text() {
	
	the_field('about_intro_text'); 
	
}
add_action('hook_bottom_about_intro_text', 'carawebs_about_intro_text');

/*====================================================================*/

/***ADD TEAM IMAGE TO ABOUT PAGE***/

function carawebs_about_us_picture() {
	
	?>
		<img src ="<?php echo get_field('team_picture'); ?>"title="The Thomas.Matthews team">
	<?php
		
}
add_action('hook_bottom_about_us_picture', 'carawebs_about_us_picture');

/*====================================================================*/

/***ADD ABOUT US MAIN TEXT****/

function carawebs_about_us_text() {
	
	the_field('about_us'); 
	
}
add_action('hook_bottom_about_us', 'carawebs_about_us_text');

/*====================================================================*/

/***ADD ABOUT US QUOTE****/

function add_about_us_quote() {
	
	?><h3 class="bottom_padding"><?php the_field('quote_title');?></h3>
	<?php the_field('about_quote'); ?>
	<p class="no_bottom_margin main_colour"><?php
	the_field('person_quoted'); ?></p>
	<p><?php
	the_field('job_title'); ?></p>
		
	<?php
	
}
add_action('hook_bottom_about_quote', 'add_about_us_quote');

/*====================================================================*/

/***Add Job Title***/
function add_job_title() {
	
	//Next line: don't run function if field is empty
	if (get_field('job_title')!= ""){
	
	?><div class="bottom_padding"><?php the_field('job_title'); ?></div><?php
	}
}
add_action('hook_after_job_title','add_job_title');

/***Add Job Title Small***/
function carawebs_job_title_small() {
	
	//Next line: don't run function if field is empty
	if (get_field('job_title')!= ""){
	
	
	the_field('job_title'); 
	
	}
}
add_action('hook_top_job_title_small','carawebs_job_title_small');

/*====================================================================*/

/***Add Social Links****/

function add_social_links() {
	
	?><ul class="no_bullet small_font"><?php
		
	//Next line: don't run function if field is empty
	if (get_field('blog_url')!= ""){
	
	?><li><a href="<?php the_field('blog_url'); ?>">Blog</a></li><?php
	}
		
	//Next line: don't run function if field is empty
	if (get_field('linked_in')!= ""){
	
	?><li><a href="<?php the_field('linked_in'); ?>">Linked In</a></li><?php
	}
	
	
	//Next line: don't run function if field is empty
	if (get_field('vcard')!= ""){
	
	?><li><a href="<?php the_field('vcard'); ?>">V Card</a></li><?php
	}
	
	?></ul><?php
}
add_action('hook_after_social_links','add_social_links');


/*====================================================================*/

function add_first_testimonial() {

$rows = get_field('testimonials' ); // get all the rows
$first_row = $rows[0]; // get the first row
$first_row_text = $first_row['testimonial_text' ]; // get the sub field value
$first_row_person = $first_row['testimonial_person' ]; // get the sub field value
$first_row_company = $first_row['company' ]; // get the sub field value

	?>
	
		<div class="testimonial_text">
		<p class="bold_font"><?php
		echo $first_row_text;
		?></p>
		<p class="bold_font no_bottom_margin"><?php
		echo $first_row_person;
		?></p>
		<p><?php
		echo $first_row_company;
		?></p>
		</div>
	
	<?php
}
add_action('hook_top_first_testimonial', 'add_first_testimonial');

/***ADD SECOND TESTIMONIAL***/
function add_second_testimonial() {

$rows = get_field('testimonials' ); // get all the rows
$second_row = $rows[1]; // get the first row
$second_row_text = $second_row['testimonial_text' ]; // get the sub field value
$second_row_person = $second_row['testimonial_person' ]; // get the sub field value
$second_row_company = $second_row['company' ]; // get the sub field value

	?>
		
		<div class="testimonial_text">
		<p class="bold_font"><?php
		echo $second_row_text;
		?></p>
		<p class="bold_font no_bottom_margin"><?php
		echo $second_row_person;
		?></p>
		<p><?php
		echo $second_row_company;
		?></p>
		</div>
	
	<?php
}
add_action('hook_top_second_testimonial', 'add_second_testimonial');

/***ADD THIRD TESTIMONIAL***/
function add_third_testimonial() {

$rows = get_field('testimonials' ); // get all the rows
$third_row = $rows[2]; // get the first row
$third_row_text = $third_row['testimonial_text' ]; // get the sub field value
$third_row_person = $third_row['testimonial_person' ]; // get the sub field value
$third_row_company = $third_row['company' ]; // get the sub field value 

	?>
		<div class="testimonial_text">
		<p class="bold_font"><?php
		echo $third_row_text;
		?></p>
		<p class="bold_font no_bottom_margin"><?php
		echo $third_row_person;
		?></p>
		<p><?php
		echo $third_row_company;
		?></p>
		</div>
	
	<?php
}
add_action('hook_top_third_testimonial', 'add_third_testimonial');

/*====================================================================*/

/***ADD TEAM INFO****/

function carawebs_team_heading(){
	
	$team_heading = get_field('team_heading');
	
	if (!empty($team_heading)) {
	
	?>
	<h2><a name="team"><?php echo $team_heading;?></a></h2>
	<?php
	
	}
	
}

add_action('hook_after_team_heading', 'carawebs_team_heading'); 


function add_team_fields() {
	
	if(get_field('team')): 
	
			while(has_sub_field('team')): ?>
					
					<div class="project_archive_teaser boxy">
						
						<div class="post_thumb">
							
							<img src ="<?php echo get_sub_field('person_image'); ?>"height="200" width="300">
												
								<div class="overlay_title_view">
						
										<div class="name"><h3 class="headline overlay_title"><?php echo get_sub_field('name'); ?></h3></div>
										<div class="post_content post_excerpt overlay_excerpt"><?php echo get_sub_field('biog'); ?></div><!--the biog field-->
																
								</div><!--overlay_title_view-->
								
						</div><!--post_thumb-->
					
					</div>
						
												
            <?php endwhile;        
			

	endif;
}
        
add_action('hook_top_team_fields', 'add_team_fields'); 

/*====================================================================*/

function add_team() {
	
	$posts = get_field('the_team');

		if( $posts ): //only displays if field has a value
			
				foreach( $posts as $post_object): ?>
					
					<div class="team_member">
						
						<div class="post_thumb">
						
						<a href="<?php echo get_permalink($post_object->ID); ?>"title="View this project" class="img_hover"><?php echo get_the_post_thumbnail($post_object->ID, 'thumbnail'); ?></a>
							
							<div class="overlay_title_view">
								
								<div class="image_title">
								<h3 class="headline overlay_title">
									<a href="<?php echo get_permalink($post_object->ID); ?>" title="View this Team Member's Page" ><?php echo get_the_title($post_object->ID); ?></a>
								</h3>
								</div>
								
								<div class="post_content post_excerpt overlay_excerpt_small">
									<?php echo the_excerpt(); ?>
								</div>
								
									
								
							</div>
						</div>
					</div>
					
				<?php endforeach;
				
			
		endif;
		
		?><div class="gridbreak"></div>
		<div class="gridbreak"></div><?php
}
        
add_action('hook_top_team', 'add_team'); 

/************************************************************************
 * 					PERSON PAGE FUNCTIONS
 * 
 * *********************************************************************/
 
/***RELATED PROJECTS****/

function add_related_content() {

$posts = get_field('related_projects');
 
	if( $posts ): //only displays if field has a value
        
        ?>
        		<h3><?php the_field('first_name'); ?>'s Projects</h3>
			    <p>NOTE: THIS CONTENT IS NOT YET STYLED</p>   
				<?php foreach( $posts as $post_object): ?>
				
						<a href="<?php echo get_permalink($post_object->ID); ?>"title="View this project" class="img_hover"><?php echo get_the_post_thumbnail($post_object->ID, 'thumbnail'); ?></a>
						
						<a href="<?php echo get_permalink($post_object->ID); ?>" title="View the project" ><?php echo get_the_title($post_object->ID); ?></a>
            	
            	<?php endforeach; ?>
		
		<?php
				
	endif;

}
add_action('hook_top_related_projects', 'add_related_content');

/***********************************************************************
 * 					CLIENT PAGE FUNCTIONS
 * 
 * *********************************************************************/
 /***ADD CLIENT INTRO****/

function add_clients_intro() {
	
	the_field('clients_intro'); 
	
		
}
add_action('hook_bottom_clients_intro', 'add_clients_intro');

/***CLIENT LIST****/

function add_client_list() {

	if(get_field('clients')): 
		
				?>
					<ul id ="clients">
									
						<?php while(has_sub_field('clients')): 
						
							if (get_sub_field('client_link')!= "")  {
									                
								?><li><a href ="<?php echo get_sub_field('client_link'); ?>"><?php echo get_sub_field('client_name'); ?></a></li><?php 
								}
							
							else {
								
								?><li><?php echo get_sub_field('client_name'); ?></li><?php
								}
									
						endwhile; ?>	
																														
					</ul>
				
				<?php
				
	endif;

}
add_action('hook_top_client_list', 'add_client_list');
 
/************************************************************************
 * 					PROJECT PAGE FUNCTIONS
 * 
 ***********************************************************************/

/**DISPLAY PROJECT INFO**/

function add_project_fields() {

    ?>
    
		<p><span style="text-decoration:underline">Client</span><br>
		<?php the_field('client'); ?></p>
		<p><span style="text-decoration:underline">Status</span><br>
		<?php the_field('completed'); ?></p>
		<p><span style="text-decoration:underline">Scope</span><br>
		<?php the_field('scope'); ?></p>
		
	<?php 
    
}
add_action('hook_top_project_intro', 'add_project_fields');

/*====================================================================*/
/***ADD PROJECT IMAGES****/
function add_project_images() {

if(get_field('project_images')):


	while(has_sub_field('project_images')): ?>
                
              <img class="project_images" src ="<?php echo get_sub_field('p_images'); ?>"title=""><?php
		
	endwhile;
 
	 
	endif; 
}


add_action('hook_top_project_images', 'add_project_images');

/*====================================================================*/

/***ADD PROJECT VIDEOS****/
function carawebs_add_video() {

if(get_field('video')):


	while(has_sub_field('video')):
                
            $video_url = get_sub_field( 'video_url' );
            $video_embed = get_sub_field( 'video_embed' );
            
            if( $video_embed ) {
            
            echo '<div class="video_wrapper">';
			echo $video_embed;
			echo '</div>'; 
			
            }
			
			elseif ( $video_url) {
				
			echo '<div class="video_wrapper">';
				$embed_code = wp_oembed_get( $video_url, array('width'=>940));
			echo $embed_code;
			echo '</div>';
			
			}
              
              
	endwhile;
 
	 
	endif; 
}


add_action('hook_after_add_video', 'carawebs_add_video');

/*====================================================================*/

/***ADD BLOG POST VIDEOS****/
function carawebs_add_news_video() {

if(get_field('news_video')):


	while(has_sub_field('news_video')):
                
            $news_video_url = get_sub_field( 'news_video_url' );
            $news_video_embed = get_sub_field( 'news_video_embed' );
            
            if( $news_video_embed ) {
            
            echo '<div class="video_wrapper">';
			echo $news_video_embed;
			echo '</div>'; 
			
            }
			
			elseif ( $news_video_url) {
				
			echo '<div class="video_wrapper">';
				$embed_code = wp_oembed_get( $news_video_url, array('width'=>940));
			echo $embed_code;
			echo '</div>';
			
			}
              
              
	endwhile;
 
	 
	endif; 
}


add_action('hook_after_add_news_video', 'carawebs_add_news_video');

/*====================================================================*/

/***Add News Intro***/
function add_news_introduction() {
	
	//Next line: don't run function if field is empty
	if (get_field('blog_intro')!= ""){
	
	the_field('blog_intro'); 
	}
}
add_action('hook_bottom_news_introduction','add_news_introduction');

/*====================================================================*/

/************************************************************************
 * 					ETHOS PAGE FUNCTIONS
 * 
 ***********************************************************************/
function carawebs_ethos_one() {

	// Variables
	$intro_title = get_field('intro_title');
	$intro_text = get_field('intro_text');
	$first_title = get_field('first_section_title');
	$first_text = get_field('first_text_section');
	
	// If there is content in the intro_text field
	if (!empty($intro_text)) {
	
			// If there's an intro title, build it and the intro text, followed by first title with normal padding
			if (!empty($intro_title)) {
				?>
				<h2 class="no_top_padding"><?php echo $intro_title; ?></h2>
				<div class="post_content"><?php echo $intro_text; ?></div>
				<?php
			}
			// If not just build the intro text
			
			else {
				?>
				<div class="post_content no_top_margin"><?php echo $intro_text; ?></div>
				<?php
			}
		
			// If there is a first section title, display with proper padding
			
			// If there is a first section title, display it
			if (!empty($first_title)) {
	
				?>
		
				<h2><?php echo $first_title; ?></h2>
			
				<?php
	
			}
	}
			
	else {
			
	// If there is no content in $intro_txt
	// Display $first_title with no top padding
			
			// If there is a first section title, display it
			if (!empty($first_title)) {
	
				?>
		
				<h2 class="no_top_padding"><?php echo $first_title; ?></h2>
			
				<?php
	
			}
			
	} // End Else
	
	
	// If there is a text section, display it
	if (!empty($first_text)) {
			
		?>
			<div class="post_content"><?php echo $first_text; ?></div>
						
		<?php
			
	}
		

	
}
	
add_action('hook_after_ethos_one', 'carawebs_ethos_one');

 
/* Display Ethos section Two */

function carawebs_ethos_two() {
	
	// Variables
	$second_title = get_field('second_section_title');
	$second_text = get_field('second_text_section');
	
	// If there is a first section title, display it
	if (!empty($second_title)) {
	
	?>
    
		<h2><?php echo $second_title; ?></h2>
		
	<?php
	
	}
	
	// If there is a text section, display it
	if (!empty($second_text)) {
    
    ?>
		<div class="post_content"><?php echo $second_text; ?></div>
				
	<?php
	
	}

}

add_action('hook_after_ethos_two', 'carawebs_ethos_two');

/* Display Ethos section Three */

function carawebs_ethos_three() {
	
	// Variables
	$third_title = get_field('third_section_title');
	$third_text = get_field('third_text_section');
	
	// If there is a first section title, display it
	if (!empty($third_title)) {
	
	?>
    
		<h2><?php echo $third_title; ?></h2>
		
	<?php
	
	}
	
	// If there is a text section, display it
	if (!empty($third_text)) {
    
    ?>
		<div class="post_content"><?php echo $third_text; ?></div>
				
	<?php
	
	}

}

add_action('hook_after_ethos_three', 'carawebs_ethos_three');
/*====================================================================*/

/* Add Ethos section One images */

function carawebs_ethos_images() {

if(get_field('ethos1_images')):


	while(has_sub_field('ethos1_images')): 
	
		$attachment_id = get_sub_field('ethos1_image');
		$size = "full"; // (thumbnail, medium, large, full or custom size)
		$image = wp_get_attachment_image_src( $attachment_id, $size );
		// url = $image[0];
		// width = $image[1];
		// height = $image[2];
               
        ?><img class="project_images" src ="<?php echo $image[0]; ?>"title=""><?php
		
	endwhile;
 
	 
	endif; 
}


add_action('hook_after_ethos1_images', 'carawebs_ethos_images');

/* Downloadable PDFs on Ethos page */


/****ADDS REPEATER FIELD FOR UPLOADED FILES******************************/

function carawebs_download_1() {

if(get_field('pdf_download')): ?>
 
        
        <?php while(has_sub_field('pdf_download')): ?>
                <p>
                <a href="<?php the_sub_field('download_file'); ?>"><?php the_sub_field('link_text'); ?></a> (PDF, <?php the_sub_field('file_size'); ?>)
				</p>
		<?php endwhile; ?>
 	
 
<?php endif; 
}
function carawebs_download_2() {

if(get_field('section_2_pdf_download')): ?>
 
        
        <?php while(has_sub_field('section_2_pdf_download')): ?>
                <p>
                <a href="<?php the_sub_field('download_file_2'); ?>"><?php the_sub_field('link_text_2'); ?></a> (PDF, <?php the_sub_field('file_size_2'); ?>)
				</p>
		<?php endwhile; ?>
 	
 
<?php endif; 
}
function carawebs_download_3() {

if(get_field('section_3_pdf_download')): ?>
 
        
        <?php while(has_sub_field('section_3_pdf_download')): ?>
                <p>
                <a href="<?php the_sub_field('download_file_3'); ?>"><?php the_sub_field('link_text_3'); ?></a> (PDF, <?php the_sub_field('file_size_3'); ?>)
				</p>
		<?php endwhile; ?>
 	
 
<?php endif; 
}

add_action('hook_after_pdf_download_1', 'carawebs_download_1');
add_action('hook_after_pdf_download_2', 'carawebs_download_2');
add_action('hook_after_pdf_download_3', 'carawebs_download_3');$str = get_the_excerpt();
	
/*====================================================================*/

/* Custom Excerpt */
 
// Remove default hellip 
function carawebs_remove_hellip( $more ) {
	    return '';
	}
	
add_filter('excerpt_more', 'carawebs_remove_hellip');
 
 
// Build a new excerpt with a nice Read More link

function carawebs_custom_excerpt() {
	
	$str = get_the_excerpt();
	
 	$trimmed = rtrim ( $str, ".,:;!?" );
 	
	
	echo $trimmed; ?>&hellip;<br><a href="<?php echo get_permalink();?>">Read More&hellip;</a><?php
 
}
 
// Add to a Thesis 2.x hook
add_action ('hook_after_custom_excerpt', 'carawebs_custom_excerpt');

/*====================================================================*/

/* No search results found */

function carawebs_no_results() {
	
	if (!have_posts()): ?>
			
		<p>Sorry, we found no content that matches your search term. Please try searching again, or visit our <a href ="<?php echo home_url(); ?>">home page</a>.</p>

	<?php endif;
}
add_action('hook_after_no_results', 'carawebs_no_results');

/*====================================================================*/

/* Empty Search Redirect */
 
function carawebs_search_redirect( $vars ) {
 
	if( isset( $_GET['s'] ) && empty( $_GET['s'] ) )
 
    // Adds the term Empty Search in place of an empty entry
		$vars['s'] = "'Empty Search'";
	 return $vars;
}
add_filter( 'request', 'carawebs_search_redirect' );

/*====================================================================*/

/*Social Sharing*/

function carawebs_simple_social(){
	
	$the_url = urlencode(get_permalink());
	$the_title = get_the_title();
	$the_image_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
	$desc = get_the_excerpt();
	
?>

	<br><p><span style="text-decoration: underline">Share</span><br>
		<span style="color: #8B8988">
			<a target="_blank" href="http://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink());?>">Facebook</a>&nbsp;&#124;
			<a target="_blank" href="https://plus.google.com/share?url={<?php echo urlencode(get_permalink());?>}" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">Google+</a>&nbsp;&#124;
			<a target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink());?>">LinkedIn</a>&nbsp;&#124;
			<a target="_blank" href="http://pinterest.com/pin/create/button/?media=<?php echo $the_image_url; ?>&url=<?php echo $the_url; ?>&is_video=false&description=<?php echo get_the_title(); ?>">Pin It</a>&nbsp;&#124;
			<a target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink());?>&amp;text=<?php echo get_the_title(); ?>">Twitter</a>&nbsp;&#124;
			<a target="_blank" href="http://www.tumblr.com/share?v=3&u=<?php echo urlencode(get_permalink());?>&t=<?php echo get_the_title(); ?>">Tumblr</a>
		</span>
	</p>
	
	<?php
}

add_action('hook_after_simple_social','carawebs_simple_social');

/*====================================================================*/

/* Post Title for Frontpage */

function carawebs_frontpage_newstitle() {
	
	$the_url = urlencode(get_permalink());
	$the_title = get_the_title();
	$the_date = get_the_date();
	
	?><h3 class="headline overlay_title">
		
		<a href="<?php echo $the_url; ?>">News: <?php echo $the_date . " - " . $the_title; ?></a>
		
		 Febuary 27, 2014 –
		
	</h3><?php
	
}

add_action('hook_after_frontpage_newstitle','carawebs_frontpage_newstitle');

/*====================================================================*/

/* Team Image Contact Us Page */

function carawebs_team_image(){
	
	$img_url = get_field('team_image');
	
	?>
	<img src ="<?php echo $img_url; ?>" class = "margin_bottom" title="The Thomas.Matthews team">
	<?php
	
}

add_action('hook_after_team_image','carawebs_team_image');

/*====================================================================*/

/* Dashboard Mods */

function remove_footer_admin () {
    echo "<h3>This <a href='http://wordpress.org'>WordPress</a> website was built by <a href = 'http://carawebs.com'>carawebs</a>.</h3>";
} 
 
add_filter('admin_footer_text', 'remove_footer_admin');

/* login page logo */

function carawebs_login_logo() {
	
	
	$logo_img = THESIS_USER_SKIN_URL . '/images/TM_logo.png';
	
    ?><style type="text/css">h1 a {
		background: url('<?php echo $logo_img; ?>') 100% 100% no-repeat !important; 
		background-size: 310px 82px !important;
		width: 310px !important;
		}
		</style><?php
    
}
add_action('login_head', 'carawebs_login_logo');

/*====================================================================*/

/* Remove default CSS reset */

function no_css_reset() {
    return '';
}
 
add_filter('thesis_css_reset','no_css_reset');
