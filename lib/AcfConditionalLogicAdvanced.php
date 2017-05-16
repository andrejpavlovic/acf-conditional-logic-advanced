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
		add_action('acf/input/admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
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

    function register_assets() {
        // scripts
        wp_register_script('acf-input-conditional-logic-advanced', $this->settings['url'] . 'assets/js/acf-input.js', array('acf-input') );
        wp_register_script('acf-field-group-conditional-logic-advanced', $this->settings['url'] . 'assets/js/acf-field-group.js', array('acf-field-group', 'acf-input-conditional-logic-advanced') );
    }

    function admin_enqueue_scripts() {
        wp_enqueue_script('acf-input-conditional-logic-advanced');
        wp_enqueue_script('acf-field-group-conditional-logic-advanced');
    }
}
