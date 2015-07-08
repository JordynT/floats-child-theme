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
				'first-name'        => $_POST['first_name'],
				'last-name'         => $_POST['last_name'],
				'email'             => $_POST['email'],
				'site-applying-for' => $_POST['site_applying_for'],
				//bday here
				'street-address'    => $_POST['street'],
				'address-2'         => $_POST['address_2'],
				'city'              => $_POST['city'],
				'zip'               => $_POST['zip'],
				'state'             => $_POST['state'],
				'country-of-origin' => $_POST['country_origin'],
				'referral'          => $_POST['referral'],
				'posts-per-month'   => $_POST['posts'],
				'experience'        => $_POST['experience'],
				'website'           => $_POST['site'],
				'twitter'           => $_POST['twitter'],
				'interests'         => $_POST['interest'],
				'why-work-for-fs'   => $_POST['why_work'],
				'qualities'         => $_POST['qualities'],
				'work-sample'       => $_POST['sample'],
			);
			$new_post = array(
				'post_title'  => $title,
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