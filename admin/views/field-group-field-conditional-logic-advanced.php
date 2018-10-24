<?php

// vars
$field = acf_extract_var( $args, 'field');
$groups = acf_extract_var( $field, 'conditional_logic_advanced');
$disabled = empty($groups) ? 1 : 0;

// UI needs at least 1 conditional logic rule
if( empty($groups) ) {

	$groups = array(

		// group 0
		array(

			// rule 0
			array(
                'param'		=>	'post_template',
                'operator'	=>	'==',
                'value'		=>	'default',
            )

		)

	);

}

// vars
$rule_types = apply_filters('acf/conditional_logic_advanced/rule_types', array(
	__("Post",'acf') => array(
		'post_template'	=>	__("Post Template",'acf'),
		'post_format'	=>	__("Post Format",'acf'),
        'post_category'	=>	__("Post Taxonomy",'acf'),
	),
));


// WP < 4.7
if( acf_version_compare('wp', '<', '4.7') ) {

	unset( $rule_types[ __("Post",'acf') ]['post_template'] );

}

$rule_operators = apply_filters( 'acf/conditional_logic_advanced/rule_operators', array(
	'=='	=>	__("is equal to",'acf'),
	'!='	=>	__("is not equal to",'acf'),
));

?>
<tr class="acf-field acf-field-true-false acf-field-setting-conditional_logic_advanced" data-type="true_false" data-name="conditional_logic_advanced">
	<td class="acf-label">
		<label><?php _e("Conditional Logic Advanced",'acf'); ?></label>
	</td>
	<td class="acf-input">
		<?php

		acf_render_field(array(
			'type'			=> 'true_false',
			'name'			=> 'conditional_logic_advanced',
			'prefix'		=> $field['prefix'],
			'value'			=> $disabled ? 0 : 1,
			'ui'			=> 1,
			'class'			=> 'conditional-logic-advanced-toggle',
		));

		?>
		<div class="rule-groups" <?php if($disabled): ?>style="display:none;"<?php endif; ?>>

			<?php foreach( $groups as $group_id => $group ):

				// validate
				if( empty($group) ) {

					continue;

				}


				// $group_id must be completely different to $rule_id to avoid JS issues
				$group_id = "group_{$group_id}";
				$h4 = ($group_id == "group_0") ? __("Show this field group if",'acf') : __("or",'acf');

				?>

				<div class="rule-group" data-id="<?php echo $group_id; ?>">

					<h4><?php echo $h4; ?></h4>

					<table class="acf-table -clear">
						<tbody>
							<?php foreach( $group as $rule_id => $rule ):

								// valid rule
								$rule = wp_parse_args( $rule, array(
									'field'		=>	'',
									'operator'	=>	'==',
									'value'		=>	'',
								));


								// $group_id must be completely different to $rule_id to avoid JS issues
								$rule_id = "rule_{$rule_id}";
								$prefix = "{$field['prefix']}[conditional_logic_advanced][{$group_id}][{$rule_id}]";

								?>
								<tr data-id="<?php echo $rule_id; ?>">
								<td class="param"><?php

									// create field
									acf_render_field(array(
										'type'		=> 'select',
										'prefix'	=> $prefix,
										'name'		=> 'param',
										'value'		=> $rule['param'],
										'choices'	=> $rule_types,
										'class'		=> 'conditional-logic-advanced-rule-param',
										'disabled'	=> $disabled,
									));

								?></td>
								<td class="operator"><?php

									// create field
									acf_render_field(array(
										'type'		=> 'select',
										'prefix'	=> $prefix,
										'name'		=> 'operator',
										'value'		=> $rule['operator'],
										'choices' 	=> $rule_operators,
										'class'		=> 'conditional-logic-advanced-rule-operator',
										'disabled'	=> $disabled,
									));

								?></td>
								<td class="value"><?php

                                $this->acfAdminFieldGroupdConditionalLogicAdvanced->render_conditional_logic_advanced_value(array(
									'field_id'  => $field['ID'],
									'group_id'	=> $group_id,
									'rule_id'	=> $rule_id,
									'value'		=> $rule['value'],
									'param'		=> $rule['param'],
									'class'		=> 'conditional-logic-advanced-rule-value',
                                    'disabled'	=> $disabled,
								));
								?></td>
								<td class="add">
									<a href="#" class="button add-conditional-logic-advanced-rule"><?php _e("and",'acf'); ?></a>
								</td>
								<td class="remove">
									<a href="#" class="acf-icon -minus remove-conditional-logic-advanced-rule"></a>
								</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

				</div>
			<?php endforeach; ?>

			<h4><?php _e("or",'acf'); ?></h4>

			<a href="#" class="button add-conditional-logic-advanced-group"><?php _e("Add rule group",'acf'); ?></a>

		</div>
	</td>
</tr>
