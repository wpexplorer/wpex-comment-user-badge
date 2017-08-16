<?php
/**
 * Plugin Name: Comment User Badge
 * Plugin URI: http://wpexplorer.com/comment-user-badge-plugin
 * Description: Displays a user badge below comments depending on the commentor's role.
 * Author: AJ Clarke
 * Author URI: http://www.wpexplorer.com
 * Version: 1.0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Only needed on the front-end
if ( is_admin() ) return;

if ( ! class_exists( 'WPEX_Comment_User_Badge' ) ) :

	class WPEX_Comment_User_Badge {


		/**
		 * Start things up
		 */
		public function __construct() {
			require_once( plugin_dir_path( __FILE__ ) .'inc/updates.php' );
			add_action( 'plugins_loaded', array( $this, 'locale' ) );
			add_filter( 'wp_enqueue_scripts', array( $this, 'add_css' ) );
			add_filter( 'get_comment_text', array( $this, 'alter_comment_text' ), 10, 3 );

		}

		/**
		 * Add locale for translations
		 *
		 * @access public
		 * @since  1.0.0
		 *
		 * @link   https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
		 */
		public function locale() {
			load_plugin_textdomain( 'wpex-comment-user-badge', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
		}

		/**
		 * Alter the comment text
		 *
		 * @access public
		 * @since  1.0.0
		 *
		 * @link   https://codex.wordpress.org/Function_Reference/comment_text
		 */
		public function alter_comment_text( $content, $comment, $args ) {
			global $wp_roles;
			if ( $wp_roles ) {
				$user_id = $comment->user_id;
				if ( $user_id && $user = new WP_User( $user_id ) ) {
					if ( isset( $user->roles[0] ) ) {
						$content .= '<div class="wpex-comment-user-badge '. $user->roles[0] .'">'. translate_user_role( $wp_roles->roles[ $user->roles[0] ]['name'] ) .'</div>';
					}
				} else {
					$content .= '<div class="wpex-comment-user-badge guest">'. __( 'Guest', 'wpex' ) .'</div>';
				}
			}
			return $content;
		}

		/**
		 * Add roles CSS to footer as needed
		 *
		 * @access public
		 * @since  1.0.0
		 *
		 * @link   https://codex.wordpress.org/Function_Reference/wp_enqueue_style
		 *
		 */
		public function add_css() {
			if ( is_singular() && comments_open() ) {
				wp_enqueue_style( 'wpex-comment-user-badge', plugins_url( 'css/comment-user-badge.css', __FILE__ ) );
			}
		}


	}

	new WPEX_Comment_User_Badge;

endif;