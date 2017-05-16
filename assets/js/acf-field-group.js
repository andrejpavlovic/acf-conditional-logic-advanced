(function($){

    /*
     *  locations
     *
     *  This model will handle location rule events
     *
     *  @type	function
     *  @date	19/08/2015
     *  @since	5.2.3
     *
     *  @param	$post_id (int)
     *  @return	$post_id (int)
     */

    acf.field_group.conditional_logic_advanced = acf.model.extend({

        _checked: false,

        events: {
            'change .conditional-logic-advanced-toggle':		'change_toggle',
            'click .add-conditional-logic-advanced-rule':		'add_rule',
            'click .add-conditional-logic-advanced-group':	    'add_group',
            'click .remove-conditional-logic-advanced-rule':	'remove_rule',
            'change .conditional-logic-advanced-rule-param':	'change_rule'
        },

        /*
         *  change_toggle
         *
         *  This function is triggered by changing the 'Conditional Logic' radio button
         *
         *  @type	function
         *  @date	8/04/2014
         *  @since	5.0.0
         *
         *  @param	$input
         *  @return	n/a
         */

        change_toggle: function( e ){

            // vars
            var $input = e.$el,
                checked = e.$el.prop('checked'),
                $td = $input.closest('.acf-input');

            if( checked ) {

                $td.find('.rule-groups').show();
                $td.find('.rule-groups').find('[name]').prop('disabled', false);

            } else {

                $td.find('.rule-groups').hide();
                $td.find('.rule-groups').find('[name]').prop('disabled', true);

            }

            this._checked = checked;
        },

        /*
         *  add_rule
         *
         *  This function will add a new rule below the specified $tr
         *
         *  @type	function
         *  @date	8/04/2014
         *  @since	5.0.0
         *
         *  @param	$tr
         *  @return	n/a
         */

        add_rule: function( e ){

            // vars
            var $tr = e.$el.closest('tr');


            // duplicate
            $tr2 = acf.duplicate( $tr );

        },


        /*
         *  remove_rule
         *
         *  This function will remove the $tr and potentially the group
         *
         *  @type	function
         *  @date	8/04/2014
         *  @since	5.0.0
         *
         *  @param	$tr
         *  @return	n/a
         */

        remove_rule: function( e ){

            // vars
            var $tr = e.$el.closest('tr');


            // save field
            $tr.find('select:first').trigger('change');


            if( $tr.siblings('tr').length == 0 ) {

                // remove group
                $tr.closest('.rule-group').remove();

            }


            // remove tr
            $tr.remove();


        },


        /*
         *  add_group
         *
         *  This function will add a new rule group to the given $groups container
         *
         *  @type	function
         *  @date	8/04/2014
         *  @since	5.0.0
         *
         *  @param	$tr
         *  @return	n/a
         */

        add_group: function( e ){

            // vars
            var $groups = e.$el.closest('.rule-groups'),
                $group = $groups.find('.rule-group:last');


            // duplicate
            $group2 = acf.duplicate( $group );


            // update h4
            $group2.find('h4').text( acf._e('or') );


            // remove all tr's except the first one
            $group2.find('tr:not(:first)').remove();

        },


        /*
         *  change_rule
         *
         *  This function is triggered when changing a location rule trigger
         *
         *  @type	function
         *  @date	8/04/2014
         *  @since	5.0.0
         *
         *  @param	$select
         *  @return	n/a
         */

        change_rule: function( e ){

            // vars
            var $select = e.$el,
                $tr = $select.closest('tr'),
                rule_id = $tr.attr('data-id'),
                $group = $tr.closest('.rule-group'),
                group_id = $group.attr('data-id');


            // add loading gif
            var $div = $('<div class="acf-loading"></div>');

            $tr.find('td.value').html( $div );


            // load location html
            $.ajax({
                url: acf.get('ajaxurl'),
                data: acf.prepare_for_ajax({
                    'field_id': $select.parents('.acf-field-object').eq(0).data('id'),
                    'action':	'acf/field_group/render_conditional_logic_advanced_value',
                    'rule_id':	rule_id,
                    'group_id':	group_id,
                    'param':	$select.val(),
                    'value':	''
                }),
                type: 'post',
                dataType: 'html',
                success: function(html){
                    $select = $(html).prop('enabled', this._checked);
                    $div.replaceWith($select);
                }.bind(this)
            });

        }
    });

})(jQuery);