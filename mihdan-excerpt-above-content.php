<?php
/**
 * Plugin Name: Mihdan: Excerpt Above Content
 * Description: Добавить цитату над редактором контента
 *
 * GitHub Plugin URI: https://github.com/mihdan/mihdan-excerpt-above-content
 */

/**
 * Генерим случайный ID для своего метабокса,
 * чтобы он вставал на свое место всегда,
 * даже если его переместили руками в другую позицию
 */
$context = 'after_title_' . time();

/**
 * Удалим дефолтный метабок для преамбулы.
 *
 * @param WP_Post $post объект поста
 */
function mihdan_eac_remove_default_excerpt_meta_box( WP_Post $post ) {
	global $context;

	if ( post_type_supports( $post->post_type, 'excerpt' ) ) {
		remove_meta_box( 'postexcerpt', $post->post_type, 'normal' );

		do_meta_boxes( get_current_screen(), $context, $post );
	}
}
add_action( 'edit_form_after_title', 'mihdan_eac_remove_default_excerpt_meta_box' );

/**
 * Добавим метабокс для цитаты обратно в новую позицию.
 *
 * @param  string $post_type тип поста
 * @param  string $post объект поста
 * @return null
 */
function mihdan_eac_add_custom_excerpt_meta_box( $post_type, $post ) {
	global $context;

	// Проверим, что имеем дело именно с постом (так как в этот хук попадают
	// комменты и ссылки ) и для него есть поддержка цитаты.
	if ( ( $post instanceof WP_Post ) && post_type_supports( $post->post_type, 'excerpt' ) ) {
		add_meta_box(
			$context,
			__( 'Excerpt' ),
			'mihdan_eac_add_rich_text_to_excerpt',
			$post_type,
			$context,
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'mihdan_eac_add_custom_excerpt_meta_box', 10, 2 );

/**
 * Добавить rich editor для цитаты
 *
 * @param WP_Post $post
 */
function mihdan_eac_add_rich_text_to_excerpt( WP_Post $post ) {
	?><label class="screen-reader-text" for="excerpt"><?php _e( 'Excerpt' ) ?></label><?php
	wp_editor(
		html_entity_decode( $post->post_excerpt ),
		'excerpt', [
			//'textarea_rows' => 100,
			'editor_height' => 200,
			'media_buttons' => false,
			'teeny' => true,
			'quicktags' => true,
			'wpautop' => true,
			//'textarea_name' => 'excerpt'
		]
	);
}