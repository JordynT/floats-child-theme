<?php

class FLOAT_Application_cpt {
	public static $cpt_name = 'Application';
//	public static $cpt = 'application';
	const CPT_SLUG = 'application';

	function __construct() {
		add_action( 'init', array( get_called_class(), 'create_custom_post_type' ) );
		add_action( 'save_post', array( get_called_class(), 'save_application_mb_status' ) );
		add_filter( 'redirect_post_location', array( get_called_class() , 'my_redirect_after_save' ) );
		add_action( 'admin_menu', array( get_called_class(), 'modify_cpt_menu' ) );
		add_action( 'current_screen', array( get_called_class(), 'redirect_on_add_new_application' ) );
	}

	/*
	 * creates application cpt
	 */
	static function create_custom_post_type() {
		$labels = array(
			'name'          => __( static::$cpt_name . 's' ),
			'singular_name' => __( static::$cpt_name ),
			'add_new_item'  => __( 'Add New ' . static::$cpt_name ),
			'edit_item'		=> __( 'Edit ' . static::$cpt_name )
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
		register_post_type( self::CPT_SLUG, $args );//'custom_application'

	}// end of cpt function

	/*
	 *creates the custom metabox for cpt application
	 */
	static function create_application_mb() {
		//TODO fix these boxes to have application info in content section and appl status on side
		add_meta_box( 'application_info', 'Application Information', array( get_called_class(), 'application_information_mb' ), self::$cpt_name );
		add_meta_box('application_status', 'Application Status', array( get_called_class(), 'application_status_mb' ), self::$cpt_name );
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
			?>
<!--			<div>-->
<!--				<a href="http://local.wordpress.dev/wp-admin/admin.php?page=sensei_learners&course_id=9&view=learners" class="button">accept</a>-->
<!--				<a href="--><?php //wp_mail('jordyn.tacoronte@fansided.com', 'The subject', 'sorry, your application is declined'); ?><!--" class="button">decline</a>-->
<!--			</div>-->
		<?php
		}
	} // end of application_information_mb function

	/*
	 *
	 */
	static function application_status_mb( $post ) {
		wp_nonce_field( 'float_app_status', 'nonce_app_status' );
		$is_accepted = get_post_meta($post->ID, 'application_status', true);

		//if application is rejected, a button is added to send applicant a rejection email
		if( !empty( $is_accepted  ) && $is_accepted == "reject" ) {

			$html  = '<div class="application-status">';
			$html .= '<input type="radio" name="application_status" value="accept"><label><strong> Accept</strong></label>';
			$html .= '</div>';
			$html .= '<div class="application-status">';
			$html .= '<input type="radio" name="application_status" value="reject" checked="checked"><label><strong> Reject</strong></label>';
			$html .= '<a href="' . wp_mail( 'jordyn.tacoronte@fansided.com', 'The subject', 'sorry, your application is declined' ) . '" class="button reject-button" style="margin-left:10px;" >Send Rejection E-mail</a>';
			$html .= '</div>';

		} elseif ( !empty( $is_accepted  ) && $is_accepted == "accept" ) {

			$html = '<div><input type="radio" name="application_status" value="accept" checked="checked"><label><strong> Accept</strong></label></div>';
			$html .= '<div><input type="radio" name="application_status" value="reject" ><label><strong> Reject</strong></label><div>';

		} elseif ( empty( $is_accepted ) ){

			$html = '<div><input type="radio" name="application_status" value="accept"><label><strong> Accept</strong></label></div>';
			$html .= '<div><input type="radio" name="application_status" value="reject"><label><strong> Reject</strong></label><div>';
		}

		echo $html;

	}

	/*
	 *@TODO work on saving checkbox field, or making checkbox a dropdown/radio button to only allow one to be clicked
	 */
	static function save_application_mb_status( $post_id ) {
		if ( self::user_can_save_application( $post_id, 'nonce_app_status' ) ) {
			update_post_meta( $post_id, 'application_status', esc_attr( $_POST['application_status'] ) );
		}
	}

	static function user_can_save_application( $post_id, $nonce ){
		//is an autosave?
		$is_autosave = wp_is_post_autosave( $post_id );
		//is revision?
		$is_revision = wp_is_post_revision( $post_id );
		//is valid nonce?
		$is_valid_nonce = ( isset( $_POST[$nonce] ) && wp_verify_nonce( $_POST[ $nonce ], 'float_app_status' ) );
		//return info
		return ! ( $is_autosave || $is_revision ) && $is_valid_nonce;
	}

	static function my_redirect_after_save( $location ) {
		global $post;
		if (self::CPT_SLUG == get_post_type( $post->ID ) ) {

			$is_accepted = get_post_meta( $post->ID, 'application_status', true );
			//if applicant is accepted, takes application reviewer to sensei learner management to add user
			//TODO will need to also create user in this function
			if ( ! empty( $is_accepted ) && $is_accepted == 'accept' ) {
				//get application information to create new user
				$app_info = get_post_meta( $post->ID, 'application_info', true );
				$user_email = $app_info['email'];
				$first_name = $app_info['first-name'];
				$last_name = $app_info['last-name'];
				//creates a user name
				$user_name = substr($first_name, 0, 1) . $last_name;
				//checks if user name exists
				$user = username_exists( $user_name );

				//user creation logic
				if ( $user && email_exists( $user_email ) == false ) {
					$user_name .= '1';
					$random_password = 'password';
					wp_create_user( $user_name, $random_password, $user_email );
				}elseif ( !$user && email_exists( $user_email ) == false ){
					$random_password = 'password';
					wp_create_user( $user_name, $random_password, $user_email );
				}
				//redirects applicant's approver to learner management to add new user to chosen course
				$location = admin_url( 'admin.php?page=sensei_learners' );
			}

			return $location;
		}
	}

	static function modify_cpt_menu() {
		global $submenu;
		//removes the 'add new' submenu tab
		unset( $submenu[ 'edit.php?post_type=' . self::CPT_SLUG ][10] );
	}

	static function redirect_on_add_new_application(){
//		if ( is_multisite() && ! current_user_can( 'manage_sites' ) || ! is_multisite() && ! current_user_can( 'delete_users' ) ) {
			$current_screen = get_current_screen();
			if ( $current_screen->id === 'post-new.php?post_type=' . self::CPT_SLUG  ) {
//				wp_safe_redirect( home_url() . '/wp-admin/' );
				die('what whaaaat!');
			}
		}
//	}

//	}


}//end of class FLOAT_Application_cpt