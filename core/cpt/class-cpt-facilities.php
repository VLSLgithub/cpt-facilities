<?php
/**
 * Class CPT_Facilities_CPT
 *
 * Creates the post type.
 *
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CPT_Facilities_CPT extends RBM_CPT {

	public $post_type = 'facility';
	public $label_singular = null;
	public $label_plural = null;
	public $labels = array();
	public $icon = 'admin-multisite';
	public $post_args = array(
		'hierarchical' => true,
		'supports' => array( 'title', 'editor', 'author', 'thumbnail' ), // We need to leave in Editor support for some TinyMCE scripts to load, but we will remove the Meta Box/Div
		'has_archive' => false,
		'rewrite' => array(
			'slug' => 'facility',
			'with_front' => false,
			'feeds' => true,
			'pages' => true
		),
		'menu_position' => 11,
		'capability_type' => 'post',
	);

	/**
	 * CPT_Facilities_CPT constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		// This allows us to Localize the Labels
		$this->label_singular = __( 'Facility', 'cpt-facilities' );
		$this->label_plural = __( 'Facilities', 'cpt-facilities' );

		$this->labels = array(
			'menu_name' => __( 'Facilities', 'cpt-facilities' ),
			'all_items' => __( 'All Facilities', 'cpt-facilities' ),
		);

		parent::__construct();

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		
		add_action( 'after_setup_theme', function() {
		
			add_action( 'do_meta_boxes', array( $this, 'remove_meta_boxes' ) );
			
		} );

		//add_filter( 'manage_' . $this->post_type . '_posts_columns', array( $this, 'admin_column_add' ) );

		//add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'admin_column_display' ), 10, 2 );

	}

	/**
	 * Add Meta Box
	 * 
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {

		global $post;
		
		add_meta_box(
			'vibrant-life-facility-meta',
			sprintf( __( '%s Meta', 'cpt-facilities' ), $this->label_singular ),
			array( $this, 'meta_metabox_content' ),
			$this->post_type,
			'side',
			'low'
		);

		add_meta_box(
			'vibrant-life-hero',
			__( 'Hero', 'cpt-facilities' ),
			array( $this, 'hero_metabox_content' ),
			$this->post_type,
			'normal',
			'low'
		);
		
		add_meta_box(
			'vibrant-life-interstitial',
			__( 'Interstitial', 'cpt-facilities' ),
			array( $this, 'interstitial_metabox_content' ),
			$this->post_type,
			'normal',
			'low'
		);
		
		add_meta_box(
			'vibrant-life-after-interstitial',
			__( 'After Interstitial', 'cpt-facilities' ),
			array( $this, 'after_interstitial_metabox_content' ),
			$this->post_type,
			'normal',
			'low'
		);
		
		add_meta_box(
			'vibrant-life-call-to-action',
			__( 'Call to Action Section', 'cpt-facilities' ),
			array( $this, 'call_to_action_metabox_content' ),
			$this->post_type,
			'normal',
			'low'
		);
		
		add_meta_box(
			'vibrant-life-video',
			__( 'Video Section', 'cpt-facilities' ),
			array( $this, 'video_metabox_content' ),
			$this->post_type,
			'normal',
			'low'
		);

	}
	
	public function remove_meta_boxes() {
		
		remove_post_type_support( 'facility', 'editor' );
		
	}
	
	public function meta_metabox_content( $post_id ) {
		
		rbm_cpts_do_field_text( array(
			'label' => '<strong>' . __( 'Phone Number', 'cpt-facilities' ) . '</strong>',
			'name' => 'phone_number',
			'group' => 'facility_meta',
			'input_class' => '',
			'input_atts' => array(
				'placeholder' => get_theme_mod( 'vibrant_life_phone_number', '(734) 913-0000' ),
			),
		) );
		
		rbm_cpts_init_field_group( 'facility_meta' );
		
	}

	public function hero_metabox_content( $post_id ) {
	
		?>

		<p class="description">
			<?php _e( 'The Hero Image is set using the Featured Image to the right', 'cpt-facilities' ); ?>
		</p>

		<?php 
		
		if ( ! did_action( 'before_wp_tiny_mce' ) ) {
			do_action( 'before_wp_tiny_mce' );
		}

		rbm_cpts_do_field_textarea( array(
			'label' => '<strong>' . __( 'Tagline', 'cpt-facilities' ) . '</strong>',
			'name' => 'hero_tagline',
			'wysiwyg' => true,
			'group' => 'facility_hero',
			'wysiwyg_options' => vibrant_life_get_wysiwyg_options(),
			'default' => '<h1><span style="color: #ffffff;">Senior Assisted Living &amp;  Memory Care in Michigan</span></h1>
	<h2 class="p1"><span style="color: #F5A623;">people helping people thrive!</span></h2>',
			'description' => '<p class="description">' . __( 'For this field, you need to set the Text Color yourself', 'cpt-facilities' ) . '</p>',
			'description_tip' => false,
			'description_placement' => 'after_label',
		) );

		rbm_cpts_init_field_group( 'facility_hero' );

	}

	public function interstitial_metabox_content( $post_id ) {

		rbm_cpts_do_field_media( array(
			'label' => '<strong>' . __( 'Main Image', 'cpt-facilities' ) . '</strong>',
			'name' => 'interstitial_image',
			'group' => 'facility_interstitial',
		) );

		rbm_cpts_do_field_textarea( array(
			'label' => '<strong>' . __( 'Main Content', 'cpt-facilities' ) . '</strong>',
			'name' => 'interstitial_content',
			'wysiwyg' => true,
			'group' => 'facility_interstitial',
			'wysiwyg_options' => vibrant_life_get_wysiwyg_options(),
		) );

		rbm_cpts_init_field_group( 'facility_interstitial' );

	}
	
	public function after_interstitial_metabox_content( $post_id ) {
		
		rbm_cpts_do_field_repeater( array(
			'label' => '<strong>' . __( 'Content Blocks', 'cpt-facilities' ) . '</strong>',
			'name' => 'interstitial_repeater',
			'group' => 'facility_after_interstitial',
			'fields' => array(
				'image' => array(
					'type' => 'media',
					'args' => array(
						'label' => __( 'Image', 'cpt-facilities' ),
					),
				),
				'circle_button_text' => array(
					'type' => 'text',
					'args' => array(
						'label' => __( 'Circle Button Text', 'cpt-facilities' ),
						'description' => '<p class="description">' . __( 'This button becomes a regular Button on Mobile.', 'cpt-facilities' ) . '</p>',
						'description_tip' => false,
						'description_placement' => 'after_label',
					),
				),
				'circle_button_url' => array(
					'type' => 'text',
					'args' => array(
						'label' => __( 'Circle Button URL', 'cpt-facilities' ),
						'description' => '<p class="description">' . __( 'This button becomes a regular Button on Mobile.', 'cpt-facilities' ) . '</p>',
						'description_tip' => false,
						'description_placement' => 'after_label',
						'input_atts' => array(
							'placeholder' => get_site_url(),
						),
					),
				),
			),
		) );
		
		rbm_cpts_init_field_group( 'facility_after_interstitial' );
		
	}

	public function call_to_action_metabox_content( $post_id ) {

		rbm_cpts_do_field_media( array(
			'label' => '<strong>' . __( 'Image', 'cpt-facilities' ) . '</strong>',
			'name' => 'call_to_action_image',
			'group' => 'facility_call_to_action',
		) );

		rbm_cpts_do_field_textarea( array(
			'label' => '<strong>' . __( 'Content', 'cpt-facilities' ) . '</strong>',
			'name' => 'call_to_action_content',
			'group' => 'facility_call_to_action',
			'wysiwyg' => true,
			'wysiwyg_options' => vibrant_life_get_wysiwyg_options(),
		) );

		rbm_cpts_init_field_group( 'facility_call_to_action' );

	}

	public function video_metabox_content( $post_id ) {

		rbm_cpts_do_field_text( array(
			'label' => '<strong>' . __( 'Video Section Header', 'cpt-facilities' ),
			'name' => 'video_header_text',
			'group' => 'facility_video',
			'default' => 'What People Say About Vibrant Life',
		) );

		rbm_cpts_do_field_text( array(
			'label' => '<strong>' . __( 'Video URL', 'cpt-facilities' ) . '</strong>',
			'name' => 'video_url',
			'group' => 'facility_video',
			'description' => '<p class="description">' . __( 'Provide the Video URL, not the Embed Code.', 'cpt-facilities' ) . '</p>',
			'description_tip' => false,
			'description_placement' => 'after_label',
		) );

		rbm_cpts_init_field_group( 'facility_video' );

	}

	/**
	 * Adds an Admin Column
	 * @param  array $columns Array of Admin Columns
	 * @return array Modified Admin Column Array
	 */
	public function admin_column_add( $columns ) {

		$columns['facility_url'] = _x( 'Facility URL', 'Facility URL Admin Column Label', 'cpt-facilities' );

		return $columns;

	}

	/**
	 * Displays data within Admin Columns
	 * @param string $column  Admin Column ID
	 * @param integer $post_id Post ID
	 */
	public function admin_column_display( $column, $post_id ) {

		switch ( $column ) {

			case 'facility_url' :
				echo rbm_field( $column, $post_id );
				break;

		}

	}

}

$instance = new CPT_Facilities_CPT();