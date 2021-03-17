<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Add_Site_Nav
 * @subpackage Add_Site_Nav/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Add_Site_Nav
 * @subpackage Add_Site_Nav/includes
 * @author     Your Name <email@example.com>
 */
class Add_Site_Nav_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Unregister the post type, so the rules are no longer in memory.
		unregister_post_type( 'test_product' );
		// Clear the permalinks to remove our post type's rules from the database.
		flush_rewrite_rules();
	}

}
