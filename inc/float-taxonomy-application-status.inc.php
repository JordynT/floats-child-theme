<?php
class FLOAT_Application_Taxonomy {
	const 	TAXONOMY_SLUG = 'application_status';

	function __construct() {
		add_action( 'init', array( get_called_class(), 'create_application_status_custom_tax' ) );
		add_action( 'admin_init', array( get_called_class(), 'insert_application_status_terms' ) );
		add_action( self::TAXONOMY_SLUG . '_edit_form_fields', array( get_called_class(), 'all_attached_applications' ) );
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
		wp_insert_term( 'Accepted', self::TAXONOMY_SLUG , array(
			'description' => 'All Accepted Applications',
			'slug' => 'accepted',
		));
		wp_insert_term( 'New Applications', self::TAXONOMY_SLUG , array(
			'description' => 'applications that have not been given a status',
			'slug' => 'not-assigned',
		));
	}

	/**
	 * adds a list of the posts attached to this specific term, and links to each of their edit screen
	 *
	 * @param $term
	 **/
	static function all_attached_applications( $term ) {
		$tax = get_term( $term, self::TAXONOMY_SLUG );
		$slug = $tax->slug;
		$posts = get_posts( array(
			'showposts'   => 500,
			'post_type'   => FLOAT_Application_cpt::CPT_SLUG,
			'orderby'     => 'menu_order',
			'order'       => 'DESC',
			'post_status' => array( 'draft', 'publish', 'pending', 'future', 'private', 'trash' ),
			'tax_query'   => array(
				array(
					'taxonomy' => self::TAXONOMY_SLUG,
					'field'    => 'slug',
					'terms'    => $slug
				)
			) ) );
		?>
		<tr class="section-header">
			<th scope="row">
				<h4><?php echo $term->name; if ( $term->name == 'Accepted') {?> Applications <?php } ?></h4>
			</th>
		</tr>
		<tr>
			<table class="wp-list-table widefat fixed striped posts">
				<tr>
					<th>Application Name</th>
<!--					<th>Position</th>-->
					<th>Status</th>
				</tr>
			<?php

			foreach ( $posts as $post ) if ( ! empty( $post ) ) {
				$url = get_edit_post_link( $post->ID );
				$position = $post->menu_order;
				$post_title = $post->post_title;
				$status = $post->post_status;
				echo '<tr>';
				echo '<td><a href="' . $url . '">';
				if ( $post_title ) {
					echo $post_title;
				} else {
					echo "no title exists";
				}
				echo '</a></td>';
//				echo '<td>' . $position . '</td>';
				echo '<td>' . $status . '</td>';
				echo '</tr>';
			}
		echo '</table>
		</tr>';
	}
}// end class FLOAT_Application_Taxonomy