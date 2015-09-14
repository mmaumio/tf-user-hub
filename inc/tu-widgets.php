<?php
/*
 * Themeforest User Widget
 */
class Themeforest_User_Hub_Widget extends WP_Widget {
    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            'tf_user_hub', 
            __('Themeforest User Hub', 'tfuser'), 
            array('description' => __('Display some useful information about a Themeforest User', 'tfuser'),) 
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {

    	global $instu;

        extract($args);

        $title = apply_filters('widget_title', $instance['title']);
        
        
        echo $before_widget;
        if($title) echo $before_title.$title.$after_title;
        
        /* Get data from the API*/
		$user = $instu->tu_pull_data( $instance['username'] );
		$user_badges = $instu->tu_pull_badges( $instance['username'] );
		$user_new_items = $instu->tu_pull_new_items( $instance['username'] );

		/* Format the $item array */
		$user = $user['user'];
		$user_badges = $user_badges['user-badges'];
		$user_items = $user_new_items['new-files-from-user'];

		$badges = '';
	    foreach ($user_badges as $key => $values) {
			$badges .= '<div class="wd-user_badge col-md-3"><img src="'.esc_url($values['image']).'" title="'.esc_attr($values['label']).'" /></div>';
	    }

		$new_items = '';
		$item_counter = 0;
		foreach ($user_items as $key => $values) {
				if($item_counter < $instance['num_items']) {
					$new_items .= '<div class="col-md-3 col-sm-10 single-item"><a href="'.esc_url($values['url']).'"><img class="img-responsive text-center" src="'.esc_url($values['thumbnail']).'" /></a></div>';
					$item_counter++;
			}
		}

		extract($user);

		if( !empty($location) ) { 
			$location_url = '<li>'.__('Website: ', 'tfuser') .$location.'</li>'; 
		} else {
			$location_url = '';
		}

		echo '<div class="row tu_wrapper">
			        <div class="col-md-4">
			            <div class="user_thumb">
			                <img class="img-responsive" src="'.esc_url($image).'" alt="'.$username.'" />
			            </div>
                    </div>
                    <div class="col-md-8">    
			            <ul class="tu_meta">
			            	<li>'.__('Username: ', 'tfuser') . $username.'</li>
			           		<li>'.__('Country: ', 'tfuser') .$country.'</li>
			                <li>'.__('Sales: ', 'tfuser') .$sales.'</li>
			                <li>'.__('Followers: ', 'tfuser') .$followers.'</li>
			                '.$location_url.'
			            </ul>
			        </div>
			        <div class="col-md-12">'.$badges.'</div>
			</div>
			<div class="row">
				<h4>'.__('Latest Items', 'tfuser').'</h4>
					<div class="items">'.$new_items.'</div>
			</div>';

        echo $after_widget;
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['username'] = strip_tags($new_instance['username']);
        $instance['num_items'] = strip_tags($new_instance['num_items']);

        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {
        if ( isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = '';
        }

        if ( isset($instance['username']) ) {
            $username = $instance['username'];
        } else {
        	$username = '';
        }

        if ( !isset($instance['num_items']) || $instance['num_items'] <= 0 ) {
            $instance['num_items'] = 4;
        }

        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'tfuser'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" 
            		type="text" value="<?php echo esc_attr($title); ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Themeforest Username:', 'tfuser'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" 
            		type="text" value="<?php echo esc_attr($username); ?>"/>
        </p>
        <p>
            <label
                for="<?php echo $this->get_field_id('num_items'); ?>"><?php _e('Number of Recent Items:', 'tfuser'); ?></label>
            <br/>

            <input class="widefat" type="text" id="<?php echo $this->get_field_id('num_items'); ?>" name="<?php echo $this->get_field_name('num_items'); ?>"
                   value="<?php echo esc_attr($instance['num_items']); ?>"/>
        </p>
    <?php
    }


} // class Themeforest_User_Widget

function tf_user_hub() {
    register_widget('Themeforest_User_Hub_Widget');
}

add_action('widgets_init', 'tf_user_hub');
