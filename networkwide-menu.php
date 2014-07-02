<?php
/**
 * Plugin Name: Multisite Menu Widget
 * Plugin URI: http://stboston.com
 * Description: Multisite Menu Widget
 * Version: 0.1
 * Author: Brian Hanna
 * Author URI: http://stbsoton.com
 * License: GPL2
 */

class Networkwide_Widget extends WP_Widget {

	/*  Register widget with WordPress */
	function __construct() {
		parent::__construct(
			'networkwide_menu_widget', // Base ID
			__('Networkwide Menu', 'networkwide_menu'), // Name
			array( 'description' => __( 'Select a menu being used on primary site/blog for use on other network sites as a widget.', 'networkwide_menu' ), ) // Args
		);
	}

	/* Front-end */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$current_menu = $instance['menu'];
		if (!(function_exists('grab_menu'))){
			function grab_menu($current_menu){
				switch_to_blog( 1 );
			    	echo wp_nav_menu( array( 'menu' => $current_menu ) );   
			    restore_current_blog();
		    }
	    }

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		echo grab_menu($current_menu);
		echo $args['after_widget'];
	}

	/* Back-end */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		$current_menu = $instance['menu'];
		$current_menu_id = get_term_by('slug', $current_menu, 'nav_menu');
		?>
		<p><?php if(!is_super_admin()) { echo 'Please contact a network administrator to edit this menu.';} else { echo '<a href="'.network_home_url().'wp-admin/nav-menus.php" target="_blank">Click here to edit the menu on the primary network site.</a>'; }?></p>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'menu' ); ?>"><?php _e( 'Select Menu:' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'menu' ); ?>" name="<?php echo $this->get_field_name( 'menu' ); ?>">
		</p>
		<?php 
		
		switch_to_blog( 1 );
	    $menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );; 
	    restore_current_blog();
	    foreach( $menus as $menu ){?>
	    	<option value="<?php echo $menu->slug; ?>" <?php echo ( $current_menu === $menu->slug ? 'selected' : '' ); ?>><?php echo $menu->name; ?></option>
		<?php }
		?>
		</select>
		<?php
	}

	/* Update Values */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['menu'] = ( ! empty( $new_instance['menu'] ) ) ? strip_tags( $new_instance['menu'] ) : '';

		return $instance;
	}

}

// Register Widget On Init
function register_nwm_widget() {
	global $blog_id;
	if ($blog_id != 1){
    	register_widget( 'Networkwide_Widget' );
    }
}
add_action( 'widgets_init', 'register_nwm_widget' );
?>