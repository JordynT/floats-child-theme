<?php

class FLOAT_Application_cpt {
	public static $cpt_name = 'Application';

	function __construct() {
		add_action( 'init', array( get_called_class(), 'create_custom_post_type' ) );
		add_action('save_post', array( get_called_class(), 'save_application_mb_status' ) );
	}


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
		register_post_type( static::$cpt_name, $args );//'custom_application'

	}// end of cpt function

	/*
	 *creates the custom metabox for cpt application
	 */
	static function create_application_mb() {
		add_meta_box( 'application_info', 'Application Information', array( get_called_class(), 'application_information_mb' ), self::$cpt_name );
		add_meta_box('application_status', 'Application Status', array( get_called_class(), 'application_status_mb' ), self::$cpt_name, 'side', 'default' );
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
	static function application_status_mb(){
		wp_nonce_field('float_app_status', 'nonce_app_status');
		$html = '<div><input type="checkbox" name="accepted" value="1" '. (!empty($is_accepted) ? ' checked="checked" ' : null) .' /><label><strong> Accept Application</strong></label></div>';
		$html .= '<div><input type="checkbox" name="rejected" value="1" '. (!empty($is_rejected) ? ' checked="checked" ' : null) .' /><label><strong> Reject Application</strong></label></div>';
		echo $html;

	}

	/*
	 *@TODO work on saving checkbox field, or making checkbox a dropdown/radio button to only allow one to be clicked
	 */
	static function save_application_mb_status( $post_id ) {
		if ( self::user_can_save_campaign( $post_id, 'nonce_app_status' ) ) {
			//Save Data
//			$my_campaign_options = [
//				'start-date'      => esc_attr( $_POST['annual-campaign-start-date'] ),
//				'end-date'        => esc_attr( $_POST['annual-campaign-end-date'] ),
//				'goal'            => esc_attr( $_POST['annual-campaign-goal'] ),
//				'is_fully_booked' => esc_attr( $_POST['is_fully_booked'] )
//			];
//			update_post_meta( $post_id, 'campaign_options', $my_campaign_options );
//
//
//			update_post_meta( $post_id, 'is_active', esc_attr( $_POST['is_active'] ) );
//
//			update_post_meta( $post_id, 'is_pledge_option_active', esc_attr( $_POST['is_pledge_option_active'] ) );
		}
	}

	static function user_can_save_campaign($post_id, $nonce){
		//is an autosave?
		$is_autosave = wp_is_post_autosave($post_id);
		//is revision?
		$is_revision = wp_is_post_revision($post_id);
		//is valid nonce?
		$is_valid_nonce = (isset($_POST[$nonce]) && wp_verify_nonce($_POST[ $nonce ], 'float_app_status'));
		//return info
		return ! ($is_autosave || $is_revision) && $is_valid_nonce;
	}


}//end of class FLOAT_Application_cpt