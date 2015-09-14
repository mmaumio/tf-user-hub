<?php
/*
Plugin Name: Themeforest User Hub
Version: 1.0
Plugin URI: http://themepicasso.com/
Description: Display useful information about a user of Themeforest in widget and/or shortcode with an username
Author: Muntasir Mahmud Aumio
Author URI: http://themepicasso.com/
Text Domain: tfuser

Copyright 2012, 2013, 2015 Muntasir Mahmud Aumio (email: secretspeed.aumz@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if( ! defined( 'WPINC' ) ) die;

class ThemeforestUser {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array($this, 'tu_enqueue_style' ) );
		add_action( 'plugins_loaded', array($this, 'tu_plugin_text_domain' ) );
	}

	function tu_enqueue_style() {
		wp_enqueue_style( 'tu-bootstrap', plugins_url( 'css/bootstrap.min.css', __FILE__ ) );
		wp_enqueue_style( 'tu-style', plugins_url( 'css/style.css', __FILE__ ) );
	}

	/* Load text domain */
	function tu_plugin_text_domain() {
	  load_plugin_textdomain( 'tfuser', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}


	public static function tu_pull_data( $username ) {

		// Initialize Curl
		$ch = curl_init();

		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, 'https://api.envato.com/v1/market/user:'.$username.'.json');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ywxOGRYv0nrpF0sXKKUzEG84lCgqJsPL' ) );
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

		// grab URL and pass it to the browser
		$ch_user_data = curl_exec($ch);

		// close cURL resource, and free up system resources
		curl_close($ch);

		if ( is_wp_error( $ch_user_data ) ) {
	    	return false;
		}

		$user_data = json_decode( $ch_user_data, true );

		/* Check for incorrect data */
	    if ( !is_array( $user_data ) ) {
	        return false;
	    }

		return $user_data;

	}

	public function tu_pull_badges( $username ) {

		// Initialize Curl
		$ch = curl_init();

		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, 'https://api.envato.com/v1/market/user-badges:'.$username.'.json');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ywxOGRYv0nrpF0sXKKUzEG84lCgqJsPL' ) );
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

		// grab URL and pass it to the browser
		$ch_user_badge = curl_exec($ch);

		// close cURL resource, and free up system resources
		curl_close($ch);

	    if ( is_wp_error( $ch_user_badge ) ) {
	        return false;
	    }

	    $user_badge = json_decode( $ch_user_badge, true );

	    /* Check for incorrect data */
	    if ( !is_array( $user_badge ) ) {
	        return false;
	    }

	    return $user_badge;

	}

	public function tu_pull_new_items( $username ) {

		// Initialize Curl
		$ch = curl_init();

		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, 'https://api.envato.com/v1/market/new-files-from-user:'.$username.',themeforest.json');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ywxOGRYv0nrpF0sXKKUzEG84lCgqJsPL' ) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// grab URL and pass it to the browser
		$ch_user_items = curl_exec($ch);

		// close cURL resource, and free up system resources
		curl_close($ch);

	    if ( is_wp_error( $ch_user_items ) ) {
	        return false;
	    }

	    $user_items = json_decode( $ch_user_items, true );

	    /* Check for incorrect data */
	    if ( !is_array( $user_items ) ) {
	        return false;
	    }

	    return $user_items;

	}

}

$instu = new ThemeforestUser();

require_once ('inc/tu-shortcodes.php');
require_once ('inc/tu-widgets.php');

