<?php
class FLOAT_Application_Taxonomy {
	const 	TAXONOMY_SLUG = 'application_status';

	function __construct() {
		add_action( 'init', array( get_called_class(), 'create_application_status_custom_tax' ) );
		add_action('admin_init', array( get_called_class(), 'insert_application_status_terms' ) );


	}
	static function create_application_status_custom_tax() {
		register_taxonomy(
			self::TAXONOMY_SLUG,
			FLOAT_Application_cpt::CPT_SLUG,
			array(
				'label' => __( 'Application Status' ),
//				'rewrite' => array( 'slug' => 'person' ),
				'capabilities' => array(
					'assign_terms' => 'delete_users',
					'edit_terms' => 'delete_users'
				)
			)
		);
	}

	static function insert_application_status_terms(){
		wp_insert_term('Accepted', self::TAXONOMY_SLUG , array(
			'description' => 'All Accepted Applications',
			'slug' => 'accepted',
		));
		wp_insert_term('New Applications', self::TAXONOMY_SLUG , array(
			'description' => 'applications that have not been given a status',
			'slug' => 'not-assigned',
		));

	}
}// end class FLOAT_Application_Taxonomy