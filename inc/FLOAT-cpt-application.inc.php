<?php

class FLOAT_Application_cpt {
	public static $cpt_name = 'Application';

	function __construct() {
		add_action( 'init', array( get_called_class(), 'create_custom_post_type' ) );
	}


	static function create_custom_post_type() {
		$labels = array(
			'name'          => __( static::$cpt_name . 's' ),
			'singular_name' => __( static::$cpt_name ),
			'add_new_item'  => __( 'Add New ' . static::$cpt_name )
		);
		$args = array(
			'labels'               => $labels,
			'public'               => true,
			'publicly_queryable'   => true,
			'show_ui'              => true,
			'query_var'            => true,
			'capability_type'      => 'post',
			'hierarchical'         => false,
			'menu_position'        => null,
			'register_meta_box_cb' => array( get_called_class(), 'create_application_mb' ),
			'supports'             => array( 'title' ),
		);
		register_post_type( static::$cpt_name, $args );//'custom_application'

	}// end of cpt function

	/*
	 *creates the custom metabox for cpt application
	 */
	static function create_application_mb() {
		add_meta_box( 'application_info', 'Application Information', array( get_called_class(), 'application_information_mb' ), self::$cpt_name );
	}

	/*
	 * adds application form data into custom mb
	 */
	static function application_information_mb() {
		global $post;
		$app_info = get_post_meta( $post->ID, 'application_info', true );
		if ( empty( $app_info ) ) {
			echo "no application information at the moment";
		} else {
			foreach ( $app_info as $key => $info ) {
				echo '<strong>' . $key . ':</strong> ' . $info . '<br>';
			}
		}
	} // end of application_information_mb function

}//end of class FLOAT_Application_cpt