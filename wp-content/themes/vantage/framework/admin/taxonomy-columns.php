<?php

add_action( 'registered_taxonomy', array( 'APP_Tax_Admin_Column', 'register_column' ), 10, 3 );


/**
 * Generates a column with the associated terms,
 * for any taxonomy with 'show_admin_column' => true
 */
class APP_Tax_Admin_Column {

	static function register_column( $taxonomy, $object_type, $args ) {
		if ( !isset( $args['show_admin_column'] ) )
			return;

		$instance = new APP_Tax_Admin_Column;
		$instance->taxonomy = get_taxonomy( $taxonomy );

		add_filter( 'manage_' . $object_type . '_posts_columns', array( $instance, 'column_headers' ) );
		add_action( 'manage_' . $object_type . '_posts_custom_column', array( $instance, 'column_content' ), 10, 2 );
	}

	function column_headers( $columns ) {
		$this->column_name = 'tax_' . $this->taxonomy->name;

		$columns[ $this->column_name ] = $this->taxonomy->labels->singular_name;

		return $columns;
	}

	function column_content( $column, $post_id ) {
		if ( $column != $this->column_name )
			return;

		$terms = get_the_terms( $post_id, $this->taxonomy->name );

		if ( !empty( $terms ) ) {
			$out = array();
			foreach ( $terms as $c ) {
				$url = add_query_arg( array(
					'post_type' => get_current_screen()->post_type,
					$this->taxonomy->query_var => $c->slug
				), 'edit.php' );

				$out[] = html( 'a', array( 'href' => $url ),
					esc_html( sanitize_term_field( 'name', $c->name, $c->term_id, 'tag', 'display' ) )
				);
			}
			echo implode( ', ', $out );
		}
	}
}

