<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// metabox creating main class
class Mx_Metaboxes_Class
{

	private $args = [];

	private $defauls = [];

	public function __construct( $args )
	{

		$this->defaults = [
			'id'			=> 'mx-extra-metabox-1',
			'post_types' 	=> 'page', // ['page', 'post']
			'name'			=> esc_html( 'Extra metabox 1', 'mx-domain' ),
			'metabox_type'	=> 'input-text'
		];

		$this->args = wp_parse_args( $args, $this->defaults );

		if( is_array( $this->args['post_types'] ) ) :

			$this->args['metabox_id'] = $this->args['id'] . '_' . implode( '_',  $this->args['post_types'] );

		else :

			$this->args['metabox_id'] = $this->args['id'] . '_' . $this->args['post_types'];

		endif;

		$this->args['post_meta_key'] = '_mx_' . $this->args['metabox_id'] . '_id';
		$this->args['nonce_action']  = $this->args['metabox_id'] . '_nonce_action';
		$this->args['nonce_name']    = $this->args['metabox_id'] . '_nonce_name';

		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );

		add_action( 'save_post', [ $this, 'save_meta_box' ] );

	}

	// add post meta
	public function add_meta_box() {
		add_meta_box(
			$this->args['metabox_id'],
			$this->args['name'],
			[ $this, 'meta_box_content' ],
			$this->args['post_types'],
			'normal'
			// 'low'
		);
	}

	// save post meta
	public function save_meta_box( $post_id ) {
		if ( ! isset( $_POST[ $this->args['nonce_name'] ] ) || ! wp_verify_nonce( wp_unslash( $_POST[ $this->args['nonce_name'] ] ), $this->args['nonce_action'] ) ) { // phpcs:ignore WordPress.Security
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$value = '';
		if ( isset( $_POST ) && isset( $_POST[ $this->args['post_meta_key'] ] ) ) :

			if( $this->args['metabox_type'] == 'input-email' ) :

				// email field
				$value = sanitize_email( wp_unslash( $_POST[ $this->args['post_meta_key'] ] ) );

			elseif( $this->args['metabox_type'] == 'input-url' ) :

				// url field
				$value = esc_url_raw( $_POST[ $this->args['post_meta_key'] ] );


			elseif( $this->args['metabox_type'] == 'textarea' ) :

				// textarea field
				$value = sanitize_textarea_field( $_POST[ $this->args['post_meta_key'] ] );

			elseif( $this->args['metabox_type'] == 'image' ) :

				// image id
				$value = sanitize_text_field( $_POST[ $this->args['post_meta_key'] ] );
				
			else :

				// input text
				$value = sanitize_text_field( wp_unslash( $_POST[ $this->args['post_meta_key'] ] ) );

			endif;


		endif;
		update_post_meta( $post_id, $this->args['post_meta_key'], $value );
	}

	// metabox content
	public function meta_box_content( $post, $meta )
	{

		$meta_value = get_post_meta(
			$post->ID,
			$this->args['post_meta_key'],
			true
		); ?>

		<p>
			<label for="<?php echo esc_attr( $this->args['post_meta_key'] ); ?>"></label>

			<?php if( $this->args['metabox_type'] == 'input-email' ) : ?>

				<!-- email field -->
				<input 
					type="email" id="<?php echo esc_attr( $this->args['post_meta_key'] ); ?>"
					name="<?php echo esc_attr( $this->args['post_meta_key'] ); ?>"
					value="<?php echo $meta_value; ?>"
				/>

			<?php elseif( $this->args['metabox_type'] == 'input-url' ) : ?>

				<!-- url field -->
				<input 
					type="url" id="<?php echo esc_attr( $this->args['post_meta_key'] ); ?>"
					name="<?php echo esc_attr( $this->args['post_meta_key'] ); ?>"
					value="<?php echo $meta_value; ?>"
				/>

			<?php elseif( $this->args['metabox_type'] == 'textarea' ) : ?>

				<!-- textarea field -->
				<textarea name="<?php echo esc_attr( $this->args['post_meta_key'] ); ?>" id="<?php echo esc_attr( $this->args['post_meta_key'] ); ?>" cols="30" rows="10"><?php echo $meta_value; ?></textarea>

			<?php elseif( $this->args['metabox_type'] == 'image' ) : ?>

				<?php

					$image_url = '';

					if( $meta_value !== '' ) {

						$image_url = wp_get_attachment_url( $meta_value );

					}

				?>

				<!-- image upload -->
				<div class="mx-image-uploader">

					<button
						class="mx_upload_image"
						<?php echo $image_url !== '' ? 'style="display: none;"' : ''; ?>
					>Choose image</button>				

					<!-- here we will save an id of image -->
					<input
						name="<?php echo esc_attr( $this->args['post_meta_key'] ); ?>"
						id="<?php echo esc_attr( $this->args['post_meta_key'] ); ?>"
						type="hidden"
						class="mx_upload_image_save"
					/>

					<!-- show an image -->
					<img
						src="<?php echo $image_url !== '' ? $image_url : ''; ?>"					
						style="width: 300px;"
						alt=""
						class="mx_upload_image_show"
						<?php echo $image_url == '' ? 'style="display: none;"' : ''; ?>						
					/>

					<!-- remove image -->
					<a
						href="#"
						class="mx_upload_image_remove"
						<?php echo $image_url == '' ? 'style="display: none;"' : ''; ?>
					>Remove Image</a>

				</div>
				
			<?php else : ?>

				<!-- input text -->
				<input 
					type="text" id="<?php echo esc_attr( $this->args['post_meta_key'] ); ?>"
					name="<?php echo esc_attr( $this->args['post_meta_key'] ); ?>"
					value="<?php echo $meta_value; ?>"
				/>

			<?php endif; ?>


		</p>

		<?php wp_nonce_field( $this->args['nonce_action'], $this->args['nonce_name'], true, true );

	}

}