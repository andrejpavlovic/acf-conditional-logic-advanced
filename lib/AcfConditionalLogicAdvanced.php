<?php

class AcfConditionalLogicAdvanced {
    private $settings = [];

    function __construct(array $settings) {
        $this->settings = $settings;

        // admin
		if( is_admin() ) {
            require $this->settings['path'] . 'admin/field-group.php';
            $this->acfAdminFieldGroupdConditionalLogicAdvanced = new acf_admin_field_group_conditional_logic_advanced();
		}

        add_action('init',	array($this, 'register_assets'), 5);

		add_action('acf/input/admin_enqueue_scripts', [$this, 'input_admin_enqueue_scripts']);
		add_action('acf/field_group/admin_enqueue_scripts', [$this, 'field_group_admin_enqueue_scripts']);

        add_filter('acf/update_field', function($field) {
            // clean up conditional logic keys
            if( !empty($field['conditional_logic_advanced']) ) {

                // extract groups
                $groups = acf_extract_var( $field, 'conditional_logic_advanced' );


                // clean array
                $groups = array_filter($groups);
                $groups = array_values($groups);


                // clean rules
                foreach( array_keys($groups) as $i ) {

                    $groups[ $i ] = array_filter($groups[ $i ]);
                    $groups[ $i ] = array_values($groups[ $i ]);

                }


                // reset conditional logic
                $field['conditional_logic_advanced'] = $groups;

            }

            return $field;
        });

        add_action('acf/render_field', [$this, 'acf_render_field']);
    }

    function initialize() {
        add_action('acf/render_field_settings', [$this, 'render_field_settings']);
    }

    function render_field_settings($field) {
        $args = [
            'field' => $field,
        ];
        require $this->settings['path'] . 'admin/views/field-group-field-conditional-logic-advanced.php';
    }

    function acf_render_field($field) {
        if (empty($field['conditional_logic_advanced'])) return;

        $groups = $field['conditional_logic_advanced'];

        // convert taxonomy term from slug to id
        foreach($groups as $groupId => $group) {
            foreach ($group as $ruleId => $rule) {
                if ($rule['param'] != 'post_category') continue;
                $param = explode(':', $rule['value']);

                $taxonomyTerm = get_term_by('slug', $param[1], $param[0]);

                $groups[$groupId][$ruleId]['value'] = $taxonomyTerm->term_id;
            }
        }
        ?>
            <script type="text/javascript">
                if(typeof acf !== 'undefined'){ acf.conditional_logic_advanced.add( '<?php echo $field['key']; ?>', <?php echo json_encode($groups); ?>); }
            </script>
        <?php
    }

    function register_assets() {
        // scripts
        wp_register_script('acf-input-conditional-logic-advanced', $this->settings['url'] . 'assets/js/acf-input.js', array('acf-input') );
        wp_register_script('acf-field-group-conditional-logic-advanced', $this->settings['url'] . 'assets/js/acf-field-group.js', array('acf-input-conditional-logic-advanced') );

        // styles
        wp_register_style('acf-input-conditional-logic-advanced', $this->settings['url'] . 'assets/css/acf-input.css', array('acf-input') );
    }

    function input_admin_enqueue_scripts() {
        wp_enqueue_script('acf-input-conditional-logic-advanced');
        wp_enqueue_style('acf-input-conditional-logic-advanced');
    }

    function field_group_admin_enqueue_scripts() {
        wp_enqueue_script('acf-field-group-conditional-logic-advanced');
    }
}
