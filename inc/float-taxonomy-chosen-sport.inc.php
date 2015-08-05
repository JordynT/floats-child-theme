<?php
class FLOAT_Taxonomy_Chosen_Sport {
	const	TAXONOMY_SLUG = 'chosen_sport';

	function __construct() {
		add_action( 'init', array( get_called_class(), 'create_chosen_sport_custom_tax' ) );
		add_action( 'admin_init', array( get_called_class(), 'insert_chosen_sport_terms' ) );
	}

	static function create_chosen_sport_custom_tax() {
		register_taxonomy(
			self::TAXONOMY_SLUG,
			FLOAT_Application_cpt::CPT_SLUG,
			array(
				'label'        => __( 'Chosen Sport' ),
				'capabilities' => array(
					'assign_terms' => 'delete_users',
					'edit_terms'   => 'delete_users'
				)
			)
		);
	}

	static function insert_chosen_sport_terms() {
		wp_insert_term( 'NFL', self::TAXONOMY_SLUG, array(
			'description' => 'Fansided NFL',
			'slug'        => 'fs-nfl',
		) );
		wp_insert_term( 'NHL', self::TAXONOMY_SLUG, array(
			'description' => 'Fansided NHL',
			'slug'        => 'fs-nhl',
		) );
		wp_insert_term( 'MLB', self::TAXONOMY_SLUG, array(
			'description' => 'Fansided MLB',
			'slug'        => 'fs-mlb',
		) );
		wp_insert_term( 'NBA', self::TAXONOMY_SLUG, array(
			'description' => 'Fansided NBA',
			'slug'        => 'fs-nba',
		) );
		wp_insert_term( 'Soccer', self::TAXONOMY_SLUG, array(
			'description' => 'Fansided Soccer',
			'slug'        => 'fs-soccer',
		) );
		wp_insert_term( 'FS College', self::TAXONOMY_SLUG, array(
			'description' => 'Fansided College',
			'slug'        => 'fs-college',
		) );
		wp_insert_term( 'Extra/Local', self::TAXONOMY_SLUG, array(
			'description' => 'Fansided Extra/Local',
			'slug'        => 'fs-extra-local',
		) );
		wp_insert_term( 'Lifestyle', self::TAXONOMY_SLUG, array(
			'description' => 'Fansided Lifestyle',
			'slug'        => 'fs-lifestyle',
		) );

	}
} // end class FLOAT_Taxonomy_Chosen_Sport