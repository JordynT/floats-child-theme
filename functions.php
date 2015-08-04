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
		require_once( 'inc/FLOAT-application-taxonomy.inc.php' );
		if ( class_exists( 'FLOAT_Application_Taxonomy' ) ) {
			new FLOAT_Application_Taxonomy();
		}
		require_once( 'admin/class-float-admin.php' );
		if ( class_exists( 'FLOAT_Admin' ) ) {
			new FLOAT_Admin();
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
				'post_title'	=> sanitize_title( $title ),
				'post_status'	=> 'publish',           // Choose: publish, preview, future, draft, etc.
				'post_type'		=> FLOAT_Application_cpt::$cpt_name,  //'post',page' or use a custom post type if you want to
				'tax_input' 	=> array ( FLOAT_Application_Taxonomy::TAXONOMY_SLUG => 'New Applications' ),//assigns the post to the new application term
			);
			//save the new post
			$pid = wp_insert_post( $new_post );
			update_post_meta( $pid, 'application_info', $application );
			$application_location = get_edit_post_link( $pid );
			$headers = array(
				'Content-Type: text/html; charset=UTF-8',
//				'From: me <jordyn.tacoronte@fansided.com>',
				'Cc: lee lee <jordyntacoronte@gmail.com>',//cc the director
			);
			wp_mail(
				'jordyn.tacoronte@fansided.com',//send to the manager of site specific applications
				'New Application submitted',//the subject
				'<p>There has been a new application submitted for ' . $_POST['site_applying_for'] .
				 ' To review the application click on the link below</p>
 				<a href="' . $application_location . '"> View Application</a>',//the message
				$headers //stipulations of email
			);
			wp_mail(
				sanitize_email( $_POST['email'] ),
				'Thank you for Submitting your Application',
				'We will be actively reviewing your application and will respond to you as soon as we can',
				$headers
				);
			wp_safe_redirect( home_url() . '/' . 'thank-you' ); exit;
		}
	}
} //end of FLOAT_Functions class

new FLOAT_Functions();