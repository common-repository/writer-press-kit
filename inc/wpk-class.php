<?php

/* Prevent direct access outside of WordPress */
function_exists( 'add_action' ) OR exit;



/**
 * Writer Press Kit UI and Settings
 * @since 1.0.0
 */
class WriterPressKit_Admin {
	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	private $key = 'writer-press-kit';
	/**
 	 * Options page metabox id
 	 * @var string
 	 */
	private $metabox_id = 'wpk-metabox';
	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';
	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = 'writer-press-kit';
	/**
	 * Holds an instance of the object
	 **/
	private static $instance = null;
	/**
	 * Constructor
	 * @since 1.0.0
	 */
	private function __construct() {
		// Set our title
		$this->title = __( 'Writer Press Kit', 'writer-press-kit' );
	}
	/**
	 * Returns the running object
	 * @return WriterPressKit_Admin
	 **/
	public static function get_instance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->hooks();
		}
		return self::$instance;
	}
	
	
	
	
	/**
	 * Initiate our hooks
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
		add_action( 'cmb2_admin_init', array( $this, 'wpk_admin_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wpk_frontend_assets' ), 15 );	
		add_shortcode( 'writer_press_kit', array( $this, 'wpk_press_page' ) );
	}
	
	
	/**
	 * Register settings with WP Settings API
	 * @since 1.0.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}
	
	
	/**
	 * Create page and menu entry
	 * @since 1.0.0
	 */
	public function add_options_page() {
		
		$this->submenu_page = add_submenu_page(
																				'tools.php',
																				$this->title, 
																				__( 'Writer Press Kit', 'writer-press-kit' ), 
																				'manage_options', 
																				$this->key,
																				array( $this, 'admin_page_display' )  
																				
		);
		
		
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css'  ) );
		add_action( "admin_print_styles-{$this->submenu_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
		
	}
	
	
	
	/**
	 * Load backend assets
	 * @since 1.0.0
	 */
	public function wpk_admin_assets() {
		
		if ( is_admin() && isset( $_GET['page'] ) &&  'writer-press-kit'==$_GET['page'] ) {
								
			wp_enqueue_style( 'wpk-plugin', WPK_URL_856 . 'css/wpk-admin.css', array( 'cmb2-styles' ), WPK_VERSION_856 );			
			wp_enqueue_script( 'wpk-scripts', WPK_URL_856 . 'js/wpk-admin.js', array( 'jquery' ), null, true ); 				
			
		}
		
	}	
	
	
	/**
	 * Load frontend assets
	 * @since 1.0.0
	 */
	public function wpk_frontend_assets() {
		
		if ( is_single() || is_page() ) {
			
			global $post;
			
			// Only load on posts/pages with the plugin's shortcode			
			if ( has_shortcode( $post->post_content, 'writer_press_kit' ) ) { 

							if ( ! wpk_get_option ( 'wpk_fld_disable_plugin_styles') ) {
								wp_enqueue_style( 'wpk-plugin', WPK_URL_856 . 'css/wpk-frontend.css', null, WPK_VERSION_856 );			
							}
							
							if ( ! wpk_get_option ( 'wpk_fld_disable_readmore') ) {
								wp_enqueue_script( 'wpk-more', WPK_URL_856 . 'js/readmore.js', array( 'jquery' ), null, true ); 		
								wp_enqueue_script( 'wpk-scripts', WPK_URL_856 . 'js/wpk-frontend.js', array( 'jquery' ), null, true ); 							
							}
				
					}
		
			}
		
	}	
	
	
	
	/**
	 * Define Shortcode Output
	 * @since 1.0.0
	 */
	public function wpk_press_page( $atts ) {
		
		if ( !is_admin() ) {
												
				$wpk_data = '';
				
				$wpk_data .= 	'<div id="press-kit-container">';
				/*-------------------------------------------------------------------*/		
				
				if ( wpk_get_option( 'wpk_fld_display_bio' ) ) {
					
						$wpk_data .= '<h2>'.__( 'Author Profile', 'writer-press-kit' ).'</h2>';
										
						if ( wpk_get_option( 'wpk_fld_bio' ) ) {
							
							$wpk_data .= '<div class="wpk-item-wrap wpk-row"><section class="wpk-item-title">'.__( 'Bio', 'writer-press-kit' ).'</section> <section class="wpk-item-content"> '.wpk_get_option( 'wpk_fld_bio' ).'</section></div>'; 
						}	
						
						if ( wpk_get_option( 'wpk_fld_speaker_intro' ) ) {
							
							$wpk_data .= '<div class="wpk-item-wrap wpk-row"><section class="wpk-item-title">'.__( 'Introduction for Speaking Engagements', 'writer-press-kit' ).'</section> <section class="wpk-item-content"> '.wpk_get_option( 'wpk_fld_speaker_intro' ).'</section></div>'; 
						}											
						
				}
				/*-------------------------------------------------------------------*/						
				if ( wpk_get_option( 'wpk_fld_display_photos' ) ) {
					
						$wpk_data .= '<h2>'.__( 'Photos', 'writer-press-kit' ).'</h2>';
						
						if ( wpk_get_option( 'wpk_fld_photo_color_print' ) OR  wpk_get_option( 'wpk_fld_photo_alpha_print' ) ) {						
						$wpk_data .= '<p class="wpk-qual">'.__( 'Print Quality', 'writer-press-kit' ).'</p>';
						}
						
						$wpk_data .= '<div class="wpk-dl wpk-row">';	
						
							if ( wpk_get_option( 'wpk_photo_credit_1' ) ) { $credit_1 = wpk_get_option( 'wpk_photo_credit_1' ); $placeholder_1 = ''; } else { $credit_1 = ''; $placeholder_1 = '<br>'; }		
							if ( wpk_get_option( 'wpk_photo_credit_2' ) ) { $credit_2 = wpk_get_option( 'wpk_photo_credit_2' ); $placeholder_1 = ''; } else { $credit_2 = ''; $placeholder_1 = '<br>'; }	
							
							$view_full_1 = '<a href="'.wpk_get_option( 'wpk_fld_photo_color_print' ).'">'.__( 'View Full Size', 'writer-press-kit' ).'</a>';
							$view_full_2 = '<a href="'.wpk_get_option( 'wpk_fld_photo_alpha_print' ).'">'.__( 'View Full Size', 'writer-press-kit' ).'</a>';
							
							if ( wpk_get_option( 'wpk_fld_photo_color_print' ) ) { $wpk_data .= '<section><img src="'.wpk_get_option( 'wpk_fld_photo_color_print' ).'" /><li class="wpk-photo-credit">'.$credit_1.'</li><li>'.$view_full_1.'</li></section>';   }	else { $wpk_data .= '<section></section>';  }
							if ( wpk_get_option( 'wpk_fld_photo_alpha_print' ) ) { $wpk_data .= '<section><img src="'.wpk_get_option( 'wpk_fld_photo_alpha_print' ).'" /><li class="wpk-photo-credit">'.$credit_2.'</li><p>'.$view_full_2.'</li></section>';   }	else { $wpk_data .= '<section></section>';  }
						
						$wpk_data .= '</div>';	
						
						if ( wpk_get_option( 'wpk_fld_photo_color_web' ) OR wpk_get_option( 'wpk_fld_photo_alpha_web' ) ) {						
						$wpk_data .= '<p class="wpk-qual">'.__( 'Web Quality', 'writer-press-kit' ).'</p>';						
						}
						
						$wpk_data .= '<div class="wpk-dl wpk-row">';	
						
							if ( wpk_get_option( 'wpk_photo_credit_3' ) ) { $credit_3 = wpk_get_option( 'wpk_photo_credit_3' ); $placeholder_2 = '&nbsp;'; } else { $credit_3 = ''; $placeholder_2 = ''; }		
							if ( wpk_get_option( 'wpk_photo_credit_4' ) ) { $credit_4 = wpk_get_option( 'wpk_photo_credit_4' ); $placeholder_2 = '&nbsp;'; } else { $credit_4 = ''; $placeholder_2 = ''; }	
							
							$view_full_3 = '<a href="'.wpk_get_option( 'wpk_fld_photo_color_web'  ).'">'.__( 'View Full Size', 'writer-press-kit' ).'</a>';
							$view_full_4 = '<a href="'.wpk_get_option( 'wpk_fld_photo_alpha_web' ).'">'.__( 'View Full Size', 'writer-press-kit' ).'</a>';
							
							if ( wpk_get_option( 'wpk_fld_photo_color_web' ) ) { $wpk_data .= '<section><img src="'.wpk_get_option( 'wpk_fld_photo_color_web' ).'" /><li class="wpk-photo-credit">'.$credit_3.'</li><li>'.$view_full_3.'</li></section>';   }	else { $wpk_data .= '<section></section>';  }
							if ( wpk_get_option( 'wpk_fld_photo_alpha_web' ) ) { $wpk_data .= '<section><img src="'.wpk_get_option( 'wpk_fld_photo_alpha_web' ).'" /><li class="wpk-photo-credit">'.$credit_4.'</li><p>'.$view_full_4.'</li></section>';   }	else { $wpk_data .= '<section></section>';  }
						
						$wpk_data .= '</div>';						
												
						
						
						if ( wpk_get_option( 'wpk_fld_photo_policy' ) ){
							
								$wpk_data .= '<div id="photo-footer">';					
									if ( wpk_get_option( 'wpk_fld_photo_policy' ) ) {
										$wpk_data .= '<p><strong>'.__( 'Photo Usage Policy', 'writer-press-kit' ).'</strong>: '.wpk_get_option( 'wpk_fld_photo_policy' ).'</p>'; 
									}
								$wpk_data .= '</div">';			
						}						
						
				}				
				
				/*-------------------------------------------------------------------*/					
				if ( wpk_get_option( 'wpk_fld_display_formats' ) ) {
					
						$wpk_data .= '<h2>'.__( 'Media Formats', 'writer-press-kit' ).'</h2>';
						
						if ( wpk_get_option( 'wpk_fld_formats_intro' ) ) {

							$wpk_data .= '<p>'.wpk_get_option( 'wpk_fld_formats_intro' ).'</p>';
						
						}						
						
										
						if ( wpk_get_option( 'wpk_fld_format_print' ) ) {
							
							if ( wpk_get_option( 'wpk_fld_format_exp_print' ) ) { $exp_print = '<section class="wpk-full"> '.wpk_get_option( 'wpk_fld_format_exp_print' ).'</section>'; } else { $exp_print = ''; }
							$wpk_data .= '<div class="wpk-item-wrap wpk-row"><section class="wpk-item-title">'.__( 'Print', 'writer-press-kit' ).'</section>'.$exp_print.'</div>'; 
						}	
						
						if ( wpk_get_option( 'wpk_fld_format_radio' ) ) {
							
							if ( wpk_get_option( 'wpk_fld_format_exp_radio' ) ) { $exp_radio = '<section class="wpk-full"> '.wpk_get_option( 'wpk_fld_format_exp_radio' ).'</section>'; } else { $exp_radio = ''; }
							$wpk_data .= '<div class="wpk-item-wrap wpk-row"><section class="wpk-item-title">'.__( 'Radio', 'writer-press-kit' ).'</section>'.$exp_radio.'</div>'; 
						}							
						
						if ( wpk_get_option( 'wpk_fld_format_tv' ) ) {
							
							if ( wpk_get_option( 'wpk_fld_format_exp_tv' ) ) { $exp_tv = '<section class="wpk-full"> '.wpk_get_option( 'wpk_fld_format_exp_tv' ).'</section>'; } else { $exp_tv = ''; }
							$wpk_data .= '<div class="wpk-item-wrap wpk-row"><section class="wpk-item-title">'.__( 'Television', 'writer-press-kit' ).'</section>'.$exp_tv.'</div>'; 
						}							
						
						if ( wpk_get_option( 'wpk_fld_format_web' ) ) {
							
							if ( wpk_get_option( 'wpk_fld_format_exp_web' ) ) { $exp_web = '<section class="wpk-full"> '.wpk_get_option( 'wpk_fld_format_exp_web' ).'</section>'; } else { $exp_web = ''; }
							$wpk_data .= '<div class="wpk-item-wrap wpk-row"><section class="wpk-item-title">'.__( 'Internet', 'writer-press-kit' ).'</section>'.$exp_web.'</div>'; 
						}											
						
				}	
				/*-------------------------------------------------------------------*/					
					if ( wpk_get_option( 'wpk_fld_display_questions' ) ) {
						
							$wpk_data .= '<h2>'.__( 'Sample Interview Questions', 'writer-press-kit' ).'</h2>';					
						
							if ( wpk_get_option( 'wpk_fld_interview_q_1' ) ) {
								
								if ( wpk_get_option( 'wpk_fld_interview_a_1' ) ) { $answer_1 = '<section class="wpk-full"> '.wpk_get_option( 'wpk_fld_interview_a_1' ).'</section>'; } else { $answer_1 = ''; }
								$wpk_data .= '<div class="wpk-item-wrap wpk-row"><section class="wpk-full"><strong>'.wpk_get_option( 'wpk_fld_interview_q_1' ) .'</strong></section>'.$answer_1.'</div>'; 
							}						

							if ( wpk_get_option( 'wpk_fld_interview_q_2' ) ) {
								
								if ( wpk_get_option( 'wpk_fld_interview_a_2' ) ) { $answer_2 = '<section class="wpk-full"> '.wpk_get_option( 'wpk_fld_interview_a_2' ).'</section>'; } else { $answer_2 = ''; }
								$wpk_data .= '<div class="wpk-item-wrap wpk-row"><section class="wpk-full"><strong>'.wpk_get_option( 'wpk_fld_interview_q_2' ).'</strong></section>'.$answer_2.'</div>'; 
							}								
							
							if ( wpk_get_option( 'wpk_fld_interview_q_3' ) ) {
								
								if ( wpk_get_option( 'wpk_fld_interview_a_3' ) ) { $answer_3 = '<section class="wpk-full"> '.wpk_get_option( 'wpk_fld_interview_a_3' ).'</section>'; } else { $answer_3 = ''; }
								$wpk_data .= '<div class="wpk-item-wrap wpk-row"><section class="wpk-full"><strong>'.wpk_get_option( 'wpk_fld_interview_q_3' ).'</strong></section>'.$answer_3.'</div>'; 
							}	
					}						
				/*-------------------------------------------------------------------*/		
				
					if ( wpk_get_option( 'wpk_fld_display_rep' ) ) {
						
							$wpk_data .= '<h2>'.__( 'Representation', 'writer-press-kit' ).'</h2>';
											
							if ( wpk_get_option( 'wpk_fld_rep_pub' ) ) {
								
								$wpk_data .= '<div class="wpk-item-wrap wpk-row"><section class="wpk-item-title wpk-full">'.__( 'Publicist', 'writer-press-kit' ).'</section> <section class="wpk-full"> '.wpk_get_option( 'wpk_fld_rep_pub' ).'</section></div>'; 
							}	
							
							if ( wpk_get_option( 'wpk_fld_rep_agent' ) ) {
								
								$wpk_data .= '<div class="wpk-item-wrap wpk-row"><section class="wpk-item-title wpk-full">'.__( 'Agent', 'writer-press-kit' ).'</section> <section class="wpk-full"> '.wpk_get_option( 'wpk_fld_rep_agent' ).'</section></div>'; 
							}											
							
					}						
				/*-------------------------------------------------------------------*/						
				if ( wpk_get_option( 'wpk_fld_display_custom' ) ) {
					
						$wpk_data .= '<h2>'.wpk_get_option( 'wpk_fld_custom_title' ).'</h2>';					
					
						if ( wpk_get_option( 'wpk_fld_custom_content' ) ) {
							$wpk_data .= '<div class="wpk-item-wrap wpk-row"><section class="wpk-full">'.wpk_get_option( 'wpk_fld_custom_content' ) .'</section></div>'; 
						}											
						
				}
				/*-------------------------------------------------------------------*/
				$wpk_data .= 	'</div> <!-- /press-kit-container -->';
				
		
				return wp_kses_post( $wpk_data );		
		
	}	
	
	
}	
	
	
	
	
	/**
	 * Display the UI
	 * @since 1.0.0
	 */
	public function admin_page_display() {
		?>
			<div class="wrap wpk-ui cmb2-options-page <?php echo $this->key; ?>">
			<h2></span> <?php echo esc_html( get_admin_page_title() ); ?></h2>
		
		
		<div class="wrap">

						<div id="poststuff">

							<div id="post-body" class="metabox-holder columns-2">

								<!-- main content -->
								<div id="post-body-content">

									<div class="meta-box-sortables ui-sortable">

										<div class="postbox">

											<div class="handlediv" title="Click to toggle"><br></div>
											<!-- Toggle -->

											<h2 class="hndle"><span><?php esc_attr_e( 'Content for Press Kit', 'writer-press-kit' ); ?></span></h2>

											
											<div class="inside">
												<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>				
												<!--</div> <!-- #wpk-settings -->												
											</div>
											
											
										</div>
										<!-- .postbox -->

									</div>
									<!-- .meta-box-sortables .ui-sortable -->

								</div>
								<!-- post-body-content -->

								<!-- sidebar -->
								<div id="postbox-container-1" class="postbox-container">

									<div class="meta-box-sortables">

										<div class="postbox">

											<div class="handlediv" title="Click to toggle"><br></div>
											<!-- Toggle -->

											<h2 class="hndle"><span><?php esc_attr_e(
														'Plugin Info', 'writer-press-kit'
													); ?></span></h2>

											<div class="inside">
													<div id="wpk-plugin-info">														
														<p><span><?php esc_attr_e( 'Version' , 'writer-press-kit' ); ?>: </span> <?php echo WPK_VERSION_856; ?> </p>														
														<p><span><?php esc_attr_e( 'Docs' , 'writer-press-kit' ); ?>: </span> <a href="https://cato.io/writer-press-kit">cato.io/writer-press-kit</a> </p>	
														<p><span><?php esc_attr_e( 'Rate' , 'writer-press-kit' ); ?>: </span> <a href="https://wordpress.org/plugins/writer-press-kit"> <?php esc_attr_e( 'Rate the plugin' , 'writer-press-kit' ); ?></a> </p>																														
												   </div>
											</div>
											<!-- .inside -->

										</div>
										<!-- .postbox -->
									

									</div>
									<!-- .meta-box-sortables -->

								</div>
								<!-- #postbox-container-1 .postbox-container -->

							</div>
							<!-- #post-body .metabox-holder .columns-2 -->

							<br class="clear">
						</div>
						<!-- #poststuff -->

			</div> <!-- .wrap -->
		
		</div> <!-- .wpk-ui -->
		

		<?php
	}
	
	
		

		
		
	
	/**
	 * Add the options metabox to the array of metaboxes
	 * @since 1.0.0
	 */
	public function add_options_page_metabox() {
		

		
		
		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );
		$wpk = new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'hookup'     => true,
			'cmb_styles' => true,

			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		
		/* AUTHOR BIOS
		------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/		
		$wpk->add_field( array(
		'id'   => 'wpk_section_bios',
		'type' => 'title',
		'before_row' =>  '<div id="wpk-settings"><p class="wpk-group-title accordion-toggle">'.__( 'Author Profile', 'writer-press-kit' ).'<span class="dashicons dashicons-arrow-down"></span><span class="dashicons dashicons-arrow-up"></p><div class="accordion-content">' ,		 	
		) );	
		
		$wpk->add_field( array(
		'name' => __( 'Show/Hide', 'writer-press-kit' ),
		'desc' => __( 'Check to include this section. Uncheck to hide.', 'writer-press-kit' ),
		'id'   => 'wpk_fld_display_bio',
		'type' => 'checkbox',	
		) );		
		
		$wpk->add_field( array(
			'id'   => 'wpk_fld_bio',
			'name' => __( 'Bio', 'writer-press-kit' ),
			'type'    => 'wysiwyg',
			'options' => array( 'textarea_rows' => 12, 'media_buttons' => false, 'tinymce' => true),
		) );			
		
		$wpk->add_field( array(
			'id'   => 'wpk_fld_speaker_intro',
			'name' => __( 'Introduction for Speaking Engagements', 'writer-press-kit' ),
			'desc' => __( 'Many publicists recommend furnishing your own introduction to prevent event organizers from preparing one on your behalf.', 'writer-press-kit' ) ,			
			'type'    => 'wysiwyg',
			'options' => array( 'textarea_rows' => 8, 'media_buttons' => false, 'tinymce' => true),
		
		) );			

		$wpk->add_field( array(
		'id'   => 'wpk_close_bios',
		'type' => 'title',
		'after_row' => '</div>',		// Last row, close accordion		
		) );		
		
		
		/* AUTHOR PHOTOS
		------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/		
		$wpk->add_field( array(
		'id'   => 'wpk_section_photos',
		'type' => 'title',
		'before_row' =>  '<p class="wpk-group-title accordion-toggle">'.__( 'Author Photos', 'writer-press-kit' ).'<span class="dashicons dashicons-arrow-down"></span><span class="dashicons dashicons-arrow-up"></p><div class="accordion-content">' ,		 	
		) );	

		$wpk->add_field( array(
		'name' => __( 'Show/Hide', 'writer-press-kit' ),
		'desc' => __( 'Check to include this section. Uncheck to hide.', 'writer-press-kit' ),
		'id'   => 'wpk_fld_display_photos',
		'type' => 'checkbox',				
		) );		
		
		$wpk->add_field( array(
				'id'   => 'wpk_fld_photo_color_print',
				'name' => __( 'Print Quality - Color', 'writer-press-kit' ),
				'desc' => __( 'Upload a high resolution color photo suitable for printing', 'writer-press-kit'),
				'type' => 'file',
				'options' => array(
												'url' => false, 
											),
				'text'    => array(
												'add_upload_file_text' => __( 'Add Photo', 'writer-press-kit' ) 
									),											
		) );
					
		$wpk->add_field( array(
		'id'   => 'wpk_photo_credit_1',
		'name' => __( 'Photographer Credit', 'writer-press-kit' ),
		'type' => 'text',	
		'after_row' => '<hr/>',
		) );	

					
		$wpk->add_field( array(
				'id'   => 'wpk_fld_photo_alpha_print',
				'name' => __( 'Print Quality - Black & White', 'writer-press-kit' ),
				'desc' => __( 'Upload a high resolution grayscale photo suitable for printing', 'writer-press-kit'),
				'type' => 'file',
				'options' => array(
												'url' => false, 
											),
				'text'    => array(
												'add_upload_file_text' => __( 'Add Photo', 'writer-press-kit' ) 
									),											
		) );									

		$wpk->add_field( array(
		'id'   => 'wpk_photo_credit_2',
		'name' => __( 'Photographer Credit', 'writer-press-kit' ),
		'type' => 'text',	
		'after_row' => '<hr/>',
		) );	

		
		$wpk->add_field( array(
				'id'   => 'wpk_fld_photo_color_web',
				'name' => __( 'Web Quality - Color', 'writer-press-kit' ),
				'desc' => __( 'Upload a color image suitable for use on the web and mobile devices', 'writer-press-kit'),
				'type' => 'file',
				'options' => array(
												'url' => false, 
											),
				'text'    => array(
												'add_upload_file_text' => __( 'Add Photo', 'writer-press-kit' ) 
									),											
		) );

		$wpk->add_field( array(
		'id'   => 'wpk_photo_credit_3',
		'name' => __( 'Photographer Credit', 'writer-press-kit' ),
		'type' => 'text',	
		'after_row' => '<hr/>',
		) );	

		
		$wpk->add_field( array(
				'id'   => 'wpk_fld_photo_alpha_web',
				'name' => __( 'Web Quality - Black & White', 'writer-press-kit' ),
				'desc' =>__( 'Upload a grayscale image suitable for use on the web and mobile devices', 'writer-press-kit'),
				'type' => 'file',
				'options' => array(
												'url' => false, 
											),
				'text'    => array(
												'add_upload_file_text' => __( 'Add Photo', 'writer-press-kit' ) 
									),											
		) );
		
		$wpk->add_field( array(
		'id'   => 'wpk_photo_credit_4',
		'name' => __( 'Photographer Credit', 'writer-press-kit' ),
		'type' => 'text',	
		'after_row' => '<hr/>',		
		) );	
		
		
		$wpk->add_field( array(
		'id'   => 'wpk_fld_photo_policy',
		'name' => __( 'Photo Usage Policy', 'writer-press-kit' ),
		'type' => 'textarea',
		'desc' => __( 'Edit the above text or delete it.', 'writer-press-kit' ),
		'default' => __( 'The above photos may be used by the media as part of coverage of me or my books. All other uses are prohibited without my express consent.', 'writer-press-kit'),
		) );			
		
		
		
		$wpk->add_field( array(
		'id'   => 'wpk_close_photos',
		'type' => 'title',
		'after_row' => '</div>',		// Last row, close accordion		
		) );	
		

		
		/* MEDIA FORMATS
		------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/		
		$wpk->add_field( array(
		'id'   => 'wpk_section_formats',
		'type' => 'title',
		'before_row' =>  '<p class="wpk-group-title accordion-toggle">'.__( 'Media Formats', 'writer-press-kit' ).'<span class="dashicons dashicons-arrow-down"></span><span class="dashicons dashicons-arrow-up"></p><div class="accordion-content">' ,		 	
		) );	

		$wpk->add_field( array(
		'name' => __( 'Show/Hide', 'writer-press-kit' ),
		'desc' => __( 'Check to include this section. Uncheck to hide.', 'writer-press-kit' ),
		'id'   => 'wpk_fld_display_formats',
		'type' => 'checkbox',	
		) );	
		

		$wpk->add_field( array(
		'id'   => 'wpk_fld_formats_intro',
		'name' => __( 'Intro Text', 'writer-press-kit' ),
		'type' => 'text',	
		'default' => __( 'I am comfortable with the following formats:', 'writer-press-kit' ),
		'after_row' => '<hr/>',			
		) );			
		/*-----------------*/		
		$wpk->add_field( array(
		'name' => __( 'Print', 'writer-press-kit' ),		
		'desc' => __( 'Check to include Print', 'writer-press-kit' ),
		'id'   => 'wpk_fld_format_print',
		'type' => 'checkbox',	
		) );	
		
		$wpk->add_field( array(
		'id'   => 'wpk_fld_format_exp_print',
		'name' => __( 'Past Coverage (Optional)', 'writer-press-kit' ),
		'desc' => __( 'Highlight previous experiences with print media, such as past interviews.', 'writer-press-kit' ),
		'type'    => 'wysiwyg',
		'options' => array( 'textarea_rows' => 8, 'media_buttons' => false, 'tinymce' => true),	
		'after_row' => '<hr/>',			
		) );			
		/*-----------------*/
		$wpk->add_field( array(
		'name' => __( 'Radio', 'writer-press-kit' ),		
		'desc' => __( 'Check to include Radio', 'writer-press-kit' ),
		'id'   => 'wpk_fld_format_radio',
		'type' => 'checkbox',	
		) );	
		
		$wpk->add_field( array(
		'id'   => 'wpk_fld_format_exp_radio',
		'name' => __( 'Past Appearances (Optional)', 'writer-press-kit' ),
		'desc' => __( 'Highlight some previous experiences with radio, such as past interviews.', 'writer-press-kit' ),
		'type'    => 'wysiwyg',
		'options' => array( 'textarea_rows' => 8, 'media_buttons' => false, 'tinymce' => true),	
		'after_row' => '<hr/>',			
		) );			
		/*-----------------*/		
		$wpk->add_field( array(
		'name' => __( 'Television', 'writer-press-kit' ),		
		'desc' => __( 'Check to include TV', 'writer-press-kit' ),
		'id'   => 'wpk_fld_format_tv',
		'type' => 'checkbox',	
		) );	
		
		$wpk->add_field( array(
		'id'   => 'wpk_fld_format_exp_tv',
		'name' => __( 'Past Appearances (Optional)', 'writer-press-kit' ),
		'desc' => __( 'Highlight previous TV appearances, if any', 'writer-press-kit' ),
		'type'    => 'wysiwyg',
		'options' => array( 'textarea_rows' => 8, 'media_buttons' => false, 'tinymce' => true),	
		'after_row' => '<hr/>',			
		) );			
		/*-----------------*/						
		$wpk->add_field( array(
		'name' => __( 'Internet', 'writer-press-kit' ),		
		'desc' => __( 'Check to include Internet', 'writer-press-kit' ),
		'id'   => 'wpk_fld_format_web',
		'type' => 'checkbox',	
		) );	
		
		$wpk->add_field( array(
		'id'   => 'wpk_fld_format_exp_web',
		'name' => __( 'Past Experience (Optional)', 'writer-press-kit' ),
		'desc' => __( 'Highlight some previous experiences with Internet-based media, such as blogs, webzines and podcasts.', 'writer-press-kit' ),
		'type'    => 'wysiwyg',
		'options' => array( 'textarea_rows' => 8, 'media_buttons' => false, 'tinymce' => true),			
		) );			
		/*-----------------*/			
						
		
		$wpk->add_field( array(
		'id'   => 'wpk_close_formats',
		'type' => 'title',
		'after_row' => '</div>',		// Last row, close accordion		
		) );				
		
		
		/* INTERVIEW QUESTIONS
		------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/		
		$wpk->add_field( array(
		'id'   => 'wpk_section_questions',
		'type' => 'title',
		'before_row' =>  '<p class="wpk-group-title accordion-toggle">'.__( 'Sample Interview Questions', 'writer-press-kit' ).'<span class="dashicons dashicons-arrow-down"></span><span class="dashicons dashicons-arrow-up"></p><div class="accordion-content">' ,		 	
		'after_row' => '<p><strong>'.__( 'Instructions: Enter one or more questions that a typical interviewer would ask about you or your book. If you prefer, you may also provide answers.', 'writer-press-kit' ).'</strong></p>',
		) );	

		$wpk->add_field( array(
		'name' => __( 'Show/Hide', 'writer-press-kit' ),
		'desc' => __( 'Check to include this section. Uncheck to hide.', 'writer-press-kit' ),
		'id'   => 'wpk_fld_display_questions',
		'type' => 'checkbox',	
		) );	
		/*-----------------*/			
		$wpk->add_field( array(
		'name' => __( 'Question', 'writer-press-kit' ),
		'id'   => 'wpk_fld_interview_q_1',
		'type' => 'text',	
		) );	
		
		$wpk->add_field( array(
		'name' => __( 'Answer (Optional)', 'writer-press-kit' ),
		'id'   => 'wpk_fld_interview_a_1',
		'type' => 'textarea',	
		'after_row' => '<hr/>',		
		) );			
		/*-----------------*/		
		$wpk->add_field( array(
		'name' => __( 'Question', 'writer-press-kit' ),
		'id'   => 'wpk_fld_interview_q_2',
		'type' => 'text',	
		) );	
		
		$wpk->add_field( array(
		'name' => __( 'Answer (Optional)', 'writer-press-kit' ),
		'id'   => 'wpk_fld_interview_a_2',
		'type' => 'textarea',	
		'after_row' => '<hr/>',		
		) );			
		/*-----------------*/		
		$wpk->add_field( array(
		'name' => __( 'Question', 'writer-press-kit' ),
		'id'   => 'wpk_fld_interview_q_3',
		'type' => 'text',	
		) );	
		
		$wpk->add_field( array(
		'name' => __( 'Answer (Optional)', 'writer-press-kit' ),
		'id'   => 'wpk_fld_interview_a_3',
		'type' => 'textarea',		
		) );			
		/*-----------------*/					
		$wpk->add_field( array(
		'id'   => 'wpk_close_questions',
		'type' => 'title',
		'after_row' => '</div>',		// Last row, close accordion		
		) );				
		
		
		/* REPRESENTATION
		------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/		
		$wpk->add_field( array(
		'id'   => 'wpk_section_rep',
		'type' => 'title',
		'before_row' =>  '<p class="wpk-group-title accordion-toggle">'.__( 'Author Representation', 'writer-press-kit' ).'<span class="dashicons dashicons-arrow-down"></span><span class="dashicons dashicons-arrow-up"></p><div class="accordion-content">' ,		 	
		) );	

		$wpk->add_field( array(
		'name' => __( 'Show/Hide', 'writer-press-kit' ),
		'desc' => __( 'Check to include this section. Uncheck to hide.', 'writer-press-kit' ),
		'id'   => 'wpk_fld_display_rep',
		'type' => 'checkbox',	
		) );	
		
		/*-----------------*/		
		$wpk->add_field( array(
		'name' => __( 'Publicist', 'writer-press-kit' ),
		'id'   => 'wpk_fld_rep_pub',
		'desc' => __( 'Enter the name and public contact details for your publicist.', 'writer-press-kit' ),		
		'type'    => 'wysiwyg',
		'options' => array( 'textarea_rows' => 8, 'media_buttons' => false, 'tinymce' => true),	
		) );	
		/*-----------------*/			
		$wpk->add_field( array(
		'name' => __( 'Agent', 'writer-press-kit' ),
		'id'   => 'wpk_fld_rep_agent',
		'desc' => __( 'Enter the name and public contact details for your agent.', 'writer-press-kit' ),		
		'type'    => 'wysiwyg',
		'options' => array( 'textarea_rows' => 8, 'media_buttons' => false, 'tinymce' => true),	
		) );			
		/*-----------------*/					
		
		$wpk->add_field( array(
		'id'   => 'wpk_close_rep',
		'type' => 'title',
		'after_row' => '</div>',		// Last row, close accordion		
		) );				
		
		
		
		/* CUSTOM SECTION
		------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/		
		
		$wpk->add_field( array(
		'id'   => 'wpk_section_custom',
		'type' => 'title',
		'before_row' =>  '<p class="wpk-group-title accordion-toggle">'.__( 'Custom Section', 'writer-press-kit' ).'<span class="dashicons dashicons-arrow-down"></span><span class="dashicons dashicons-arrow-up"></p><div class="accordion-content">' ,		 	
		'after_row' => '<p><strong>'.__( 'Instructions: Use this optional section to add any content of your choosing to your press kit.', 'writer-press-kit' ).'</strong></p>',
		) );
		
		$wpk->add_field( array(
		'name' => __( 'Show/Hide', 'writer-press-kit' ),
		'desc' => __( 'Check to include this section. Uncheck to hide.', 'writer-press-kit' ),
		'id'   => 'wpk_fld_display_custom',
		'type' => 'checkbox',	
		) );			
		
		
		$wpk->add_field( array(
		'name' => __( 'Title', 'writer-press-kit' ),
		'desc' => __( 'Enter the title for your custom section.', 'writer-press-kit' ),
		'id'   => 'wpk_fld_custom_title',
		'type' => 'text',	
		) );			
		
		
		$wpk->add_field( array(
		'name' => __( 'Content', 'writer-press-kit' ),
		'id'   => 'wpk_fld_custom_content',
		'desc' => __( 'Enter content for your custom section.', 'writer-press-kit' ),		
		'type'    => 'wysiwyg',
		'options' => array( 'textarea_rows' => 8, 'media_buttons' => false, 'tinymce' => true),	
		) );
		
		
		$wpk->add_field( array(
		'id'   => 'wpk_close_custom',
		'type' => 'title',
		'after_row' => '</div>',		// Last row, close accordion		
		) );				
		
		
		
		/* PLUGIN OPTIONS
		------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/		
		$wpk->add_field( array(
		'desc' => __( 'Disable the plugin\'s CSS and rely on CSS from my theme', 'writer-press-kit' ),
		'id'   => 'wpk_fld_disable_plugin_styles',
		'type' => 'checkbox',	
		'before_row' => '<p>&nbsp;</p>',
		) );
		
		$wpk->add_field( array(
		'desc' => __( 'Disable the Read More functionality and show all content at its full length', 'writer-press-kit' ),
		'id'   => 'wpk_fld_disable_readmore',
		'type' => 'checkbox',	
		'after_row' => '</div>',  // Close entire settings containter
		) );
		
		
	}
	
	
	/**
	 * Register settings notices for display
	 *
	 * @since 1.0.0
	 * @param  int   $object_id Option key
	 * @param  array $updated   Array of updated fields
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}
		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'myprefix' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}
	/**
	 * Public getter method for retrieving protected/private variables
	 * @since 1.0.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}
		throw new Exception( 'Invalid property: ' . $field );
	}
}
/**
 * Helper function to get/return the WriterPressKit_Admin object
 * @since 1.0.0
 * @return Catoprefix_Admin object
 */
function wpk_admin() {
	return WriterPressKit_Admin::get_instance();
}
/**
 * Wrapper function around cmb2_get_option
 * @since 1.0.0
 * @param  string  $key Options array key
 * @return mixed        Option value
 */
function wpk_get_option( $key = '' ) {
	return cmb2_get_option( wpk_admin()->key, $key );
}
// Start this instance
wpk_admin();