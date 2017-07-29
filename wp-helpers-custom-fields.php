<?php

Class WP_Helper_Custom_Meta_Box_Text {
	protected $id;
	protected $cpt;
	protected $description;
	function __construct( $id, $cpt, $name, $description ) {
		$this->id = $id;
		$this->cpt = $cpt;
		$this->description = $description;
		$this->name = $name;
		$this->init_hooks();
	}

	function render_form() {
		global $post;
		wp_nonce_field( basename( __FILE__ ), $this->id . '_nonce' ); ?>

		<p>
			<label for="<?php echo $this->id ?>"><?php echo $this->description ?></label>
			<br />
			<?php echo $this->get_field( esc_attr( get_post_meta( $post->ID, $this->id, true ) ) ) ?>
		</p>
	<?php }

	function get_field( $previous ) {
		return "<input class='widefat' type='text' name='$this->id' id='$this->id' value='$previous' size='30' />";
	}

	function init_hooks() {
		add_action( 'load-post.php', array( $this, 'box_setup' ) );
		add_action( 'load-post-new.php', array( $this, 'box_setup' ) );
		add_action( 'save_post', array( $this, 'save' ), 10, 2 );
	}

	function box_setup() {
		add_meta_box(
			$this->id,
			$this->name,
			array( $this, 'render_form' ),
			$this->cpt,
			'normal',
			'core'
		);
	}

	function is_nonce_valid() {
		return (
			isset( $_POST[ $this->id . '_nonce'] ) &&
			wp_verify_nonce( $_POST[ $this->id . '_nonce'], basename( __FILE__ ) )
		);
	}

	function sanitize( $val ) {
		return $val;
	}

	function save( $post_id, $post ) {
		/* Verify the nonce before proceeding. */
		if ( ! $this->is_nonce_valid() )
			return $post_id;

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		/* Get the posted data and sanitize it for use as an HTML class. */
		$new_meta_value = ( isset( $_POST[ $this->id ] ) ? $this->sanitize( $_POST[ $this->id ] ) : '' );

		/* Get the meta key. */
		$meta_key = $this->id;

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );

		/* If there is no new meta value but an old value exists, delete it. */
		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );
	}
}

class WP_Helper_Custom_Meta_Box_Textarea extends WP_Helper_Custom_Meta_Box_Text {
	function get_field( $previous ) {
		return "<textarea class='widefat' type='text' name='$this->id' id='$this->id'>$previous</textarea>";
	}
}
