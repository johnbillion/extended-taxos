<?php
/*
Plugin Name:  Extended Taxonomies
Description:  Extended custom taxonomies.
Version:      1.2.2
Author:       John Blackbourn
Author URI:   http://johnblackbourn.com

Extended Taxonomies provides better defaults so taxonomies can be created with very little code, complete with custom meta box control.

 * Better defaults for everything:
   - Intelligent defaults for all labels
   - Hierarchical by default
   - Drop with_front from rewrite rules
 * Allow object terms to be exclusive (woo!)
 * Allow or prevent hierarchy within taxonomy (partial)
 * Custom meta box support
   - Built-in meta boxes for radios and checkboxes


*/

class ExtendedTaxonomy {

	private $taxonomy;
	private $object_types;
	private $tax_slug;
	private $tax_singular;
	private $tax_plural;
	private $tax_singular_low;
	private $tax_plural_low;
	private $args;
	private $defaults = array(
		'public'            => true,
		'show_in_nav_menus' => false,
		'show_ui'           => true,
		'hierarchical'      => true,
		'query_var'         => true,
		'exclusive'         => false, # Custom arg
		'meta_box'          => false, # Custom arg
		'allow_hierarchy'   => false  # Custom arg
	);

	function __construct( $taxonomy, $object_types, $args = array(), $plural = null ) {

		$this->taxonomy         = $taxonomy;
		$this->object_types     = (array) $object_types;
		$this->tax_slug         = ( $plural ) ? $plural : $taxonomy . 's';
		$this->tax_singular     = ucwords( str_replace( array( '-', '_' ), ' ', $this->taxonomy ) );
		$this->tax_plural       = ucwords( str_replace( array( '-', '_' ), ' ', $this->tax_slug ) );
		$this->taxonomy         = strtolower( $this->taxonomy );
		$this->tax_slug         = strtolower( $this->tax_slug );
		$this->tax_singular_low = strtolower( $this->tax_singular );
		$this->tax_plural_low   = strtolower( $this->tax_plural );

		$this->defaults['labels'] = array(
			'name'                       => $this->tax_plural,
			'singular_name'              => $this->tax_singular,
			'menu_name'                  => $this->tax_plural,
			'search_items'               => sprintf( __( 'Search %s', 'theme_admin' ), $this->tax_plural ),
			'popular_items'              => sprintf( __( 'Popular %s', 'theme_admin' ), $this->tax_plural ),
			'all_items'                  => sprintf( __( 'All %s', 'theme_admin' ), $this->tax_plural ),
			'parent_item'                => sprintf( __( 'Parent %s', 'theme_admin' ), $this->tax_singular ),
			'parent_item_colon'          => sprintf( __( 'Parent %s:', 'theme_admin' ), $this->tax_singular ),
			'edit_item'                  => sprintf( __( 'Edit %s', 'theme_admin' ), $this->tax_singular ),
			'update_item'                => sprintf( __( 'Update %s', 'theme_admin' ), $this->tax_singular ),
			'add_new_item'               => sprintf( __( 'Add New %s', 'theme_admin' ), $this->tax_singular ),
			'new_item_name'              => sprintf( __( 'New %s Name', 'theme_admin' ), $this->tax_singular ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'theme_admin' ), $this->tax_plural_low ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'theme_admin' ), $this->tax_plural_low ),
			'choose_from_most_used'      => sprintf( __( 'Choose from most used %s', 'theme_admin' ), $this->tax_plural_low ),
			'view_item'                  => sprintf( __( 'View %s', 'theme_admin' ), $this->tax_singular )
		);
		$this->defaults['rewrite'] = array(
			'slug'         => $this->tax_slug,
			'with_front'   => false,
			'hierarchical' => isset( $args['allow_hierarchy'] ) ? $args['allow_hierarchy'] : $this->defaults['allow_hierarchy']
		);

		if ( isset( $args['public'] ) and !$args['public'] ) {
			$this->defaults['show_in_nav_menus'] = false;
			$this->defaults['show_ui']           = false;
		}

		$this->args = wp_parse_args( $args, $this->defaults );

		# This allows the labels arg to contain some or all labels:
		if ( isset( $args['labels'] ) )
			$this->args['labels'] = wp_parse_args( $args['labels'], $this->defaults['labels'] );

		if ( $this->args['exclusive'] or $this->args['meta_box'] )
			add_action( 'add_meta_boxes', array( $this, '_meta_boxes' ), 10, 2 );

		add_action( 'init', array( $this, 'register_taxonomy' ), 9 );

		#add_action( "manage_edit-{$this->taxonomy}_columns",          array( $this, '_cols' ) );
		#add_action( "manage_{$this->taxonomy}_custom_column",         array( $this, '_col' ), 10, 3 );
		#add_filter( "manage_edit-{$this->taxonomy}_sortable_columns", array( $this, '_sortables' ) );

	}

	function _meta_boxes( $post_type, $post ) {

		$taxos = get_post_taxonomies( $post );

		if ( in_array( $this->taxonomy, $taxos ) ) {

			if ( $this->args['hierarchical'] )
				remove_meta_box( "{$this->taxonomy}div", $post_type, 'side' );
			else
				remove_meta_box( "tagsdiv-{$this->taxonomy}", $post_type, 'side' );

			if ( $this->args['meta_box'] ) {

				if ( 'simple' == $this->args['meta_box'] )
					$this->args['meta_box'] = array( $this, '_simple_meta_box' );

				if ( $this->args['exclusive'] )
					add_meta_box( "{$this->taxonomy}div", $this->tax_singular, $this->args['meta_box'], $post_type, 'side' );
				else
					add_meta_box( "{$this->taxonomy}div", $this->tax_plural, $this->args['meta_box'], $post_type, 'side' );

			} else {
				add_meta_box( "{$this->taxonomy}div", $this->tax_singular, array( $this, '_radio_meta_box' ), $post_type, 'side' );
			}

		}

	}

	function _radio_meta_box( $post, $box ) {
		$walker = new Walker_ExtendedTaxonomyRadio;
		$this->_do_meta_box( $post, $walker, true );
	}

	function _simple_meta_box( $post, $box ) {
		$walker = new Walker_ExtendedTaxonomySimple;
		$this->_do_meta_box( $post, $walker );
	}

	function _do_meta_box( $post, $walker, $show_none = false ) {

		$taxonomy = $this->taxonomy;
		$tax      = get_taxonomy( $taxonomy );
		$selected = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );

		?>
		<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">

			<style type="text/css">
				#<?php echo $taxonomy; ?>-0 {
					color: #999;
					border-top: 1px solid #eee;
					margin-top: 5px;
				}
			</style>

			<input type="hidden" name="tax_input[<?php echo $taxonomy; ?>][]" value="0" />

			<ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy; ?> categorychecklist form-no-clear">
				<?php

				wp_terms_checklist( $post->ID, array(
					'taxonomy'      => $taxonomy,
					'walker'        => $walker,
					'selected_cats' => $selected
				) );

				if ( $show_none ) {
					$output = '';
					$o = (object) array(
						'term_id' => 0,
						'name'    => 'Not Specified',
						'slug'    => 'none'
					);
					if ( empty( $selected ) )
						$_selected = array( 0 );
					else
						$_selected = $selected;
					$args = array(
						'taxonomy'      => $taxonomy,
						'popular_cats'  => array(),
						'selected_cats' => $_selected,
						'disabled'      => false
					);
					$walker->start_el( $output, $o, 1, $args );
					$walker->end_el( $output, $o, 1, $args );
					echo $output;
				}

				?>

			</ul>

		</div>
		<?php
	
	}

	function register_taxonomy() {
		return register_taxonomy( $this->taxonomy, $this->object_types, $this->args );
	}


}

class Walker_ExtendedTaxonomyRadio extends Walker {

	var $tree_type = 'category';
	var $db_fields = array(
		'parent' => 'parent',
		'id' => 'term_id'
	);

	function start_lvl( &$output, $depth, $args ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "$indent<ul class='children'>\n";
	}

	function end_lvl( &$output, $depth, $args ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	function start_el( &$output, $category, $depth, $args ) {

		$name = 'tax_input[' . $args['taxonomy'] . ']';
		$class = in_array( $category->term_id, $args['popular_cats'] ) ? ' class="popular-category popular-' . $args['taxonomy'] . '"' : '';
		$output .= "\n<li id='{$args['taxonomy']}-{$category->term_id}'$class>" .
			'<label class="selectit">' .
			'<input value="' . $category->term_id . '" type="radio" name="'.$name.'[]" ' .
				'id="in-'.$args['taxonomy'].'-' . $category->term_id . '"' .
				checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) .
				disabled( empty( $args['disabled'] ), false, false ) .
			' /> ' .
			esc_html( apply_filters('the_category', $category->name )) .
			'</label>';

	}

	function end_el( &$output, $category, $depth, $args ) {
		$output .= "</li>\n";
	}

}

class Walker_ExtendedTaxonomySimple extends Walker {

	var $tree_type = 'category';
	var $db_fields = array(
		'parent' => 'parent',
		'id' => 'term_id'
	);

	function start_lvl( &$output, $depth, $args ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "$indent<ul class='children'>\n";
	}

	function end_lvl( &$output, $depth, $args ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	function start_el( &$output, $category, $depth, $args ) {

		$name = 'tax_input[' . $args['taxonomy'] . ']';
		$class = in_array( $category->term_id, $args['popular_cats'] ) ? ' class="popular-category popular-' . $args['taxonomy'] . '"' : '';
		$output .= "\n<li id='{$args['taxonomy']}-{$category->term_id}'$class>" .
			'<label class="selectit">' .
			'<input value="' . $category->term_id . '" type="checkbox" name="'.$name.'[]" ' .
				'id="in-'.$args['taxonomy'].'-' . $category->term_id . '"' .
				checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) .
				disabled( empty( $args['disabled'] ), false, false ) .
			' /> ' .
			esc_html( apply_filters('the_category', $category->name )) .
			'</label>';

	}

	function end_el( &$output, $category, $depth, $args ) {
		$output .= "</li>\n";
	}

}

function register_extended_taxonomy( $taxonomy, $object_types, $args = array(), $plural = null ) {
	return new ExtendedTaxonomy( $taxonomy, $object_types, $args, $plural );
}

?>