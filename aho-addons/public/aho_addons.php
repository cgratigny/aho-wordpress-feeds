<?php
/**
 * Plugin Name.
 *
 * @package   Aho_Addons
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright Abundant Harvest Organics
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-plugin-name-admin.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 * @package Aho_Addons
 * @author  Your Name <email@example.com>
 */
class Aho_Addons {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * @TODO - Rename "plugin-name" to the name your your plugin
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'aho-addons';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	private $base_uri = "https://my.abundantharvestorganics.com/api/";
	// private $base_uri = "http://localhost:5000/api/";

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_shortcode("addons", array($this, "render_addons"));

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate()
	{
		
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * NOTE:  invoke via [addons] shortcode
	 *
	 * @since    1.0.0
	 */
	public function render_addons($atts)
	{
		$week = $week_obj = $this->get_week();

		extract( shortcode_atts( array(
			'week' => esc_attr( $week->id ),
			'charge_time_id' => esc_attr( $_GET["charge_time_id"] ),
		), $atts ) );

		$add_ons = $this->get_addons($week);
		$previous_week = $this->previous_week($week);
		$next_week = $this->next_week($week);

		require_once( plugin_dir_path( __FILE__ ) . 'views/addons.php' );
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function get_addons($week)
	{
		$option_name = "{$this->base_uri}add_ons.json?week={$week}";
		if(isset($_GET["reset_addon_cache"]))
		{
			delete_option(sanitize_title($option_name));
		}
		
		$add_ons = get_option(sanitize_title($option_name));

		if(empty($add_ons))
		{
			$add_ons = file_get_contents($option_name);
			$add_ons = json_decode($add_ons);
			update_option(sanitize_title($option_name), $add_ons);
		}

		return $add_ons;
	}


	/**
	 * Grab the weeks from the api or db
	 *
	 * @return array
	 * @author Brandon Hansen
	 **/
	public function load_weeks()
	{
		$weeks = get_option("aho_weeks");

		if(!$weeks)
		{
			$weeks = file_get_contents("{$this->base_uri}weeks.json");
			$weeks = json_decode($weeks);
			update_option("aho_weeks", $weeks);
		}

		return $weeks;
	}


	/**
	 * Get the week that we are trying to get the boxes for
	 *
	 * @return int
	 * @author Brandon Hansen
	 **/
	private function get_week()
	{
		$weeks = $this->load_weeks();
		$week = array_filter($weeks, array($this, "filter_week"));

		if(!is_array($week))
		{
			throw new Exception("No week available");
		}

		return array_pop($week);
	}


	/**
	 * Check to see if the week that the user is requesting is the current week object
	 *
	 * @return bool
	 * @author Brandon Hansen
	 **/
	public function filter_week($week)
	{
		if(isset($_GET["week"]))
		{
			return $_GET["week"] == $week->id;
		}
		else
		{
			return date("Y-m-d") >= $week->start_date && date("Y-m-d") <= $week->end_date;
		}
	}


	/**
	 * Check the records to find the previous week
	 *
	 * @return int
	 * @author Brandon Hansen
	 **/
	private function previous_week($week)
	{
		return $this->get_week()->id - 1; // So lame, but it works for now since ids are in order :)
	}

	/**
	 * Check the records to find the next week
	 *
	 * @return int
	 * @author Brandon Hansen
	 **/
	private function next_week($week)
	{
		return $this->get_week()->id + 1; // So lame, but it works for now :)
	}

}
