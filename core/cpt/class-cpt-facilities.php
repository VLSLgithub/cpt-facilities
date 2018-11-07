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
		'supports' => array( 'title', 'editor', 'author', 'thumbnail' ),
		'has_archive' => true,
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
			'facility-meta',
			sprintf( _x( '%s Meta', 'Metabox Title', 'cpt-facilities' ), $this->label_singular ),
			array( $this, 'metabox_content' ),
			$this->post_type,
			'normal'
		);

	}

	/**
	 * Add Meta Field
	 * 
	 * @since 1.0.0
	 */
	public function metabox_content() {



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