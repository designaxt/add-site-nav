<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Add_Site_Nav
 * @subpackage Add_Site_Nav/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Add_Site_Nav
 * @subpackage Add_Site_Nav/admin
 * @author     Your Name <email@example.com>
 */
class Add_Site_Nav_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $add_site_nav    The ID of this plugin.
	 */
	private $add_site_nav;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $add_site_nav       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $add_site_nav, $version ) {

		$this->add_site_nav = $add_site_nav;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Add_Site_Nav_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Add_Site_Nav_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->add_site_nav, plugin_dir_url( __FILE__ ) . 'css/add-site-nav-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Add_Site_Nav_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Add_Site_Nav_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->add_site_nav, plugin_dir_url( __FILE__ ) . 'js/add-site-nav-admin.js', array( 'jquery' ), $this->version, false );

	}

}
