<?php
/**
* Class FLOAT_Admin
*
* All of the admin side features for FLOAT
*/
class FLOAT_Admin {
	function __construct(){
		add_action( 'admin_menu', array( get_called_class() , 'remove_custom_tax_meta_box' ) );

	}

	/**
	 * removes WordPress' original custom taxonomy meta box
	 */
	static function remove_custom_tax_meta_box() {
		remove_meta_box( 'tagsdiv-' . FLOAT_Application_Taxonomy::TAXONOMY_SLUG , FLOAT_Application_cpt::CPT_SLUG, 'side' );
	}


}// end class FLOAT_Admin