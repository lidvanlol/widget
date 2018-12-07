<?php

class Recent_Posts extends WP_Widget {

	
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_recent_entries',
			'description' => __( 'Your sites most recent Posts.' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'recent-posts', __( 'Recent Posts' ), $widget_ops );
		$this->alt_option_name = 'widget_recent_entries';
	}


	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts' );

		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}
		$show_featured = isset( $instance['show_featured'] ) ? $instance['show_featured'] : false;
        
		
		$r = new WP_Query( apply_filters( 'widget_posts_args', array(
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
		), $instance ) );

		if ( ! $r->have_posts() ) {
			return;
		}
		?>
		<?php echo $args['before_widget']; ?>
		<?php
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		?>
		<ul>
			<?php foreach ( $r->posts as $recent_post ) : ?>
				<?php
				$post_title = get_the_title( $recent_post->ID );
				$title      = ( ! empty( $post_title ) ) ? $post_title : __( '(no title)' );
				?>
                <li>
                <?php if ( $show_excerpt ) : ?>
						<span class="post_excerpt"> <?php the_excerpt() ?></span>
					<?php endif; ?>

                </li>
				<li>
					<a href="<?php the_permalink( $recent_post->ID ); ?>"><?php echo $title ; ?></a>
					<?php if ( $show_featured ) : ?>
						<span class="post-thumbnail"> <?php has_post_thumbnail() ? the_post_thumbnail() : ''?></span>
					<?php endif; ?>


				</li>
                <li>
                </li>

			<?php endforeach; ?>
		</ul>
		<?php
		echo $args['after_widget'];
	}


	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['number'] = (int) $new_instance['number'];
        $instance['show_excerpt'] = isset( $new_instance['show_excerpt'] ) ? (bool) $new_instance['show_excerpt'] : false;
		$instance['show_featured'] = isset( $new_instance['show_featured'] ) ? (bool) $new_instance['show_featured'] : false;
        $instance['exc_num'] = (int) $new_instance['exc_num'];

		return $instance;
	}


	public function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
		<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>

        <p>
<label for="<?php echo $this->get_field_id( 'blog-title' ); ?>"><?php  _e( 'Select Title:' ); ?></label> 
<select class="widefat" id="<?php echo $this->get_field_id( 'blog-title' ); ?>" name="<?php echo $this->get_field_name( 'blog-title' ); ?>">

<?php  

                $fullpost = new WP_Query(array(
                    'post_type' => 'post',                      
                ));

if($fullpost->have_posts()): while($fullpost->have_posts()): $fullpost->the_post(); ?>

    <option value="<?php the_title();?>"><?php the_title();?></option>

<?php endwhile; endif;?>

</select>
</p>

	
        <p>
        <input class="checkbox" type="checkbox"<?php checked( $show_featured); ?> id="<?php echo $this->get_field_id( 'show_featured' ); ?>" name="<?php echo $this->get_field_name( 'show_featured' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_featured' ); ?>"><?php _e( 'Display Featured Image?' ); ?></label></p>
        </p>

         <p>
        <input class="checkbox" type="checkbox"<?php checked( $show_excerpt); ?> id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>"><?php _e( 'Display Excerpt' ); ?></label></p>
        </p>

<p><label for="<?php echo $this->get_field_id( 'exc_num' ); ?>"><?php _e( 'Limit the characters excerpt' ); ?></label>
		<input class="tiny-text" id="<?php echo $this->get_field_id( 'exc_num' ); ?>" name="<?php echo $this->get_field_name( 'exc_num' ); ?>" type="exc_num" step="50" min="150" value="<?php echo $number; ?>" size="3" /></p>

        
<?php
	}
}


function Recent_Posts() {
	register_widget( 'Recent_Posts' );
}

add_action( 'widgets_init', 'Recent_Posts' );