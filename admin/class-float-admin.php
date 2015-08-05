<?php
/**
* Class FLOAT_Admin
*
* All of the admin side features for FLOAT
*/
class FLOAT_Admin {
	function __construct(){
		add_action( 'admin_menu', array( get_called_class() , 'remove_custom_meta_boxes' ) );
	}

	/**
	 * removes WordPress' original custom taxonomy meta box
	 */
	static function remove_custom_meta_boxes() {
		//removes application status
		remove_meta_box( 'tagsdiv-' . FLOAT_Application_Taxonomy::TAXONOMY_SLUG , FLOAT_Application_cpt::CPT_SLUG, 'side' );
		//removes chosen sport
		remove_meta_box( 'tagsdiv-' . FLOAT_Taxonomy_Chosen_Sport::TAXONOMY_SLUG , FLOAT_Application_cpt::CPT_SLUG, 'side' );
	}



}// end class FLOAT_Admin