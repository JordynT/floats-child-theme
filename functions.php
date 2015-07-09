<?php

class FLOAT_Functions {
	function __construct(){
		/*
		* Build and initialize the cpt
		*/
		require_once ( 'inc/FLOAT-cpt-application.inc.php' );
		if ( class_exists( 'FLOAT_Application_cpt' ) ) {
			new FLOAT_Application_cpt();
		}
		require_once( 'inc/FLOAT-shortcodes.inc.php' );
		if ( class_exists( 'FLOAT_Shortcodes' ) ) {
			new FLOAT_Shortcodes();
		}

		add_action( 'wp_enqueue_scripts', array( get_called_class() ,'theme_enqueue_styles') );
		add_action( 'template_redirect', array( get_called_class(), 'form_to_post' ) );
	}

	/*
	 * enqueue parent scripts
	 */
	static function theme_enqueue_styles() {

		$parent_style = 'parent-style';

		wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
		wp_enqueue_style( 'child-style',
			get_stylesheet_directory_uri() . '/style.css',
			array( $parent_style )
		);
	}

	/*
	 * saves the form data in a serialized array
	 */
	static function form_to_post() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['action'] ) && $_POST['action'] == "new_post" ) {
			if ( isset ( $_POST['email'] ) ) {
				$title = $_POST['email'] . ': ';
			}
			if ( isset ( $_POST['last_name'] ) ) {
				$title .= $_POST['last_name'] . ', ';
			}
			if ( isset ( $_POST['first_name'] ) ) {
				$title .= $_POST['first_name'];
			}
			$application = array(
				'first-name'        => sanitize_text_field( $_POST['first_name'] ),
				'last-name'         => sanitize_text_field( $_POST['last_name'] ),
				'email'             => sanitize_email( $_POST['email'] ),
				'site-applying-for' => $_POST['site_applying_for'],
				//bday here
				'street-address'    => sanitize_text_field( $_POST['street'] ),
				'address-2'         => sanitize_text_field( $_POST['address_2'] ),
				'city'              => sanitize_text_field( $_POST['city'] ),
				'zip'               => sanitize_text_field( $_POST['zip'] ),
				'state'             => sanitize_text_field( $_POST['state'] ),
				'country-of-origin' => $_POST['country_origin'],
				'referral'          => sanitize_text_field( $_POST['referral'] ),
				'posts-per-month'   => sanitize_text_field( $_POST['posts'] ),
				'experience'        => sanitize_text_field( $_POST['experience'] ),
				'website'           => esc_url_raw( $_POST['site'] ),
				'twitter'           => sanitize_text_field( $_POST['twitter'] ),
				'interests'         => sanitize_text_field( $_POST['interest'] ),
				'why-work-for-fs'   => sanitize_text_field( $_POST['why_work'] ),
				'qualities'         => sanitize_text_field( $_POST['qualities'] ),
				'work-sample'       => sanitize_text_field( $_POST['sample'] ),
			);
			$new_post = array(
				'post_title'  => sanitize_title( $title ),
				'post_status' => 'publish',           // Choose: publish, preview, future, draft, etc.
				'post_type'   => FLOAT_Application_cpt::$cpt_name  //'post',page' or use a custom post type if you want to
			);
			//save the new post
			$pid = wp_insert_post( $new_post );
			update_post_meta( $pid, 'application_info', $application );
		}
	}
} //end of FLOAT_Functions class

new FLOAT_Functions();