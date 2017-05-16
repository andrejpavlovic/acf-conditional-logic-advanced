<?php

class AcfConditionalLogicAdvanced {
    private $settings = [];

    function __construct(array $settings) {
        $this->settings = $settings;

        // admin
		if( is_admin() ) {
            require $this->settings['path'] . 'admin/field-group.php';
		}
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
}
