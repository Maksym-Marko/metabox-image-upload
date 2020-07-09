<?php 

require get_template_directory() . '/inc/metabox.php';

require get_template_directory() . '/inc/metabox-image-upload.php';

Mx_Metaboxes_Image_Upload_Class::register_scrips();


// image upload
new Mx_Metaboxes_Class(
	[
		'id'			=> 'featured-image-metabox',
		'post_types' 	=> 'post',
		'name'			=> esc_html( 'Featured image', 'mx-domain' ),
		'metabox_type'	=> 'image'
	]
);

// image upload 2
new Mx_Metaboxes_Class(
	[
		'id'			=> 'featured-image2-metabox',
		'post_types' 	=> 'post',
		'name'			=> esc_html( 'Featured image 2', 'mx-domain' ),
		'metabox_type'	=> 'image'
	]
);

// desc
new Mx_Metaboxes_Class(
	[
		'id'			=> 'desc-metabox',
		'post_types' 	=> 'post',
		'name'			=> esc_html( 'Some Description', 'mx-domain' ),
		'metabox_type'	=> 'textarea'
	]
);

