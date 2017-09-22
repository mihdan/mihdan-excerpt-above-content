<?php
/**
 * Plugin Name: Mihdan: Excerpt Above Content
 * Description: Добавить цитату над редактором контента
 * Version: 1.1
 * Author: Mikhail Kobzarev
 * Author URI: https://www.kobzarev.com/
 * GitHub Plugin URI: https://github.com/mihdan/mihdan-excerpt-above-content
 *
 * @package mihdan_excerpt_above_content
 * @version 1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Mihdan_Excerpt_Above_Content' ) ) {

	/**
	 * Class Mihdan_Excerpt_Above_Content
	 */
	final class Mihdan_Excerpt_Above_Content {

		/**
		 * @var $context
		 */
		public $context;

		/**
		 * Constructor method.
		 *
		 * @since  1.1
		 * @access private
		 */
		private function __construct() {}

		/**
		 * Returns the instance.
		 *
		 * @since  1.1
		 * @access public
		 * @return Mihdan_Excerpt_Above_Content|null
		 */
		public static function get_instance() {

			static $instance = null;

			if ( is_null( $instance ) ) {
				$instance = new self;
				$instance->setup();
				$instance->hooks();
			}

			return $instance;
		}

		/**
		 * Настройка перемнных
		 */
		public function setup() {

			/**
			 * Генерим случайный ID для своего метабокса,
			 * чтобы он вставал на свое место всегда,
			 * даже если его переместили руками в другую позицию
			 */
			$this->context = 'after_title_' . time();
		}

		/**
		 * Инициализация хуков
		 */
		public function hooks() {
			add_action( 'edit_form_after_title', array( $this, 'remove_default_excerpt_meta_box' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_custom_excerpt_meta_box' ), 10, 2 );
		}

		/**
		 * Удалим дефолтный метабок для преамбулы.
		 *
		 * @param WP_Post $post объект поста
		 */
		public function remove_default_excerpt_meta_box( WP_Post $post ) {

			if ( post_type_supports( $post->post_type, 'excerpt' ) ) {
				remove_meta_box( 'postexcerpt', $post->post_type, 'normal' );

				do_meta_boxes( get_current_screen(), $this->context, $post );
			}
		}

		/**
		 * Добавим метабокс для цитаты обратно в новую позицию.
		 *
		 * @param  string $post_type тип поста
		 * @param  string $post объект поста
		 */
		public function add_custom_excerpt_meta_box( $post_type, $post ) {

			// Проверим, что имеем дело именно с постом (так как в этот хук попадают
			// комменты и ссылки ) и для него есть поддержка цитаты.
			if ( ( $post instanceof WP_Post ) && post_type_supports( $post->post_type, 'excerpt' ) ) {
				add_meta_box(
					$this->context,
					__( 'Excerpt' ),
					array( $this, 'add_rich_text_to_excerpt' ),
					$post_type,
					$this->context,
					'high'
				);
			}
		}

		/**
		 * Добавить rich editor для цитаты
		 *
		 * @param WP_Post $post
		 */
		public function add_rich_text_to_excerpt( WP_Post $post ) {
			?><label class="screen-reader-text" for="excerpt"><?php _e( 'Excerpt' ) ?></label><?php
			wp_editor( html_entity_decode( $post->post_excerpt ),  'excerpt', array(
				'editor_height' => 200,
				'media_buttons' => false,
				'teeny' => true,
				'quicktags' => true,
				'wpautop' => true,
			) );
		}
	}

	/**
	 * @return Mihdan_Excerpt_Above_Content|null
	 */
	function mihdan_excerpt_above_content() {
		return Mihdan_Excerpt_Above_Content::get_instance();
	}

	mihdan_excerpt_above_content();
}

// eof;
