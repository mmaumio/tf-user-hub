<?php

/**
 * Add the shortcode using the WordPress function add_shortcode()
 * 
 */
add_shortcode( 'tf_user', 'tu_add_shortcode' );

/**
 * Hook to run when the shortcode is found
 * @param Array $atts - shortcode attributes
 * @param String $content - shortcode content (not necessary for our plugin)
 * @return String - plugin HTML code
 */
function tu_add_shortcode( $atts, $content = null ) {

	global $instu;

    /* Default shortcode attributes  */
    $atts = shortcode_atts( array(
        'username' => '',
		'item_count' => '',
    ), $atts );

    extract( $atts );

    /* Validation */
    if ( empty( $username ) ) {
        return '<p>'.__('Please insert a Themeforest User ID.', 'tfuser').'</p>';
    }

    /* Get data from the API*/
    $user = $instu->tu_pull_data( $username );
    $user_badges = $instu->tu_pull_badges( $username );
	$user_new_items = $instu->tu_pull_new_items( $username );

    /* Validation - Check if something went wrong */
    if ( $user === false || $user_badges === false ) {
        return '<p>'.__('Oops… Something went wrong. Please check out the User ID and try again.', 'tfuser').'</p>';
    }

    /* Format the $item array */
    $user = $user['user'];
    $user_badges = $user_badges['user-badges'];
	$user_items = $user_new_items['new-files-from-user'];

    $badges = '';
    foreach ($user_badges as $key => $values) {
			$badges .= '<div class="user_badge col-md-2"><img src="'.esc_url($values['image']).'" title="'.esc_attr($values['label']).'" /></div>';
    }

	$new_items = '';
	$item_counter = 0;
	foreach ($user_items as $key => $values) {
			if($item_counter < $item_count) {
				$new_items .= '<div class="col-md-2 single-item"><a href="'.esc_url($values['url']).'"><img class="img-responsive text-center" src="'.esc_url($values['thumbnail']).'" /></a></div>';
				$item_counter++;
		}
	}

    extract( $user );

    if( !empty($location) ) { 
		$location_url = '<li>'.__('Website: ', 'tfuser') .$location.'</li>';
	} else {
		$location_url = '';
	}

    /* Prepare the Plugin HTML */
    $html = '';

    $html .= '<div class="row tu_wrapper">
		        <div class="col-md-3 col-sm-8">
		            <div class="user_thumb">
		                <img class="img-responsive" src="'.esc_url($image).'" alt="'.$username.'" />
		            </div>
		            <ul class="tu_meta">
		            	<li>'.__('Username: ', 'tfuser') . $username.'</li>
		           		<li>'.__('Country: ', 'tfuser') .$country.'</li>
		                <li>'.__('Sales: ', 'tfuser') .$sales.'</li>
		                <li>'.__('Followers: ', 'tfuser') .$followers.'</li>
		                '.$location_url.'
		            </ul>
			    </div>    
		        <div class="col-md-9 col-sm-10">
		            <div class="row">'.$badges.'</div>
		        </div>
			</div>
			<div class="row">
				<h4>'.__('Latest Items', 'tfuser').'</h4>
				<div class="items">'.$new_items.'</div>
			</div>';

    return $html;
}
