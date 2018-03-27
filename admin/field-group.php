<?php

/*
*  ACF Admin Field Group Class
*
*  All the logic for editing a field group
*
*  @class 		acf_admin_field_group
*  @package		ACF
*  @subpackage	Admin
*/

if( ! class_exists('acf_admin_field_group_conditional_logic_advanced') ) :

class acf_admin_field_group_conditional_logic_advanced {
	
	
	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		// ajax
		add_action('wp_ajax_acf/field_group/render_conditional_logic_advanced_value',		array($this, 'ajax_render_conditional_logic_advanced_value'));
	}

	/*
	*  render_location_value
	*
	*  This function will render out an input containing location rule values for the given args
	*
	*  @type	function
	*  @date	30/09/13
	*  @since	5.0.0
	*
	*  @param	$options (array)
	*  @return	N/A
	*/
	
	function render_conditional_logic_advanced_value( $options ) {

		// vars
		$options = wp_parse_args( $options, array(
		    'field_id'  => 0,
			'group_id'	=> 0,
			'rule_id'	=> 0,
			'value'		=> null,
			'param'		=> null,
            'disabled'  => false
		));
		
		
		// vars
		$choices = array();

		
		switch( $options['param'] ) {
			
			case "post_template" :

				// vars
				$choices = array(
					'default' => apply_filters( 'default_page_template_title',  __('Default Template', 'acf') )
				);

				// get templates (WP 4.7)
				if( acf_version_compare('wp', '>=', '4.7') ) {
					$templates = acf_get_post_templates();
					$choices = array_merge($choices, $templates);
				}
				
				// break
				break;
			
			
			case "post_format" :
				
				$choices = get_post_format_strings();
								
				break;
			
			
			case "post_category" :
				
				$taxonomies = array_values(array_filter(get_object_taxonomies($post_type, 'objects'), function($taxonomy) {
					if ($taxonomy->name == 'post_format') return;
					if (!$taxonomy->hierarchical) return;
					return true;
				}));

				$choices = acf_get_taxonomy_terms(array_map(function($taxonomy) {
					return $taxonomy->name;
				}, $taxonomies));
							
				break;
		}
		
		
		// allow custom location rules
		$choices = apply_filters( 'acf/conditional_logic_advanced/rule_values/' . $options['param'], $choices );
							
		
		// create field
		acf_render_field(array(
			'type'		=> 'select',
			'prefix'	=> "acf_fields[{$options['field_id']}][conditional_logic_advanced][{$options['group_id']}][{$options['rule_id']}]",
			'name'		=> 'value',
			'value'		=> $options['value'],
			'choices'	=> $choices,
            'disabled'  => $options['disabled'],
		));
		
	}
	
	
	/*
	*  ajax_render_location_value
	*
	*  This function can be accessed via an AJAX action and will return the result from the render_location_value function
	*
	*  @type	function (ajax)
	*  @date	30/09/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function ajax_render_conditional_logic_advanced_value() {

		// validate
		if( !acf_verify_ajax() ) {

			die();

		}


		// call function
		$this->render_conditional_logic_advanced_value( $_POST );


		// die
		die();

	}
}

endif;