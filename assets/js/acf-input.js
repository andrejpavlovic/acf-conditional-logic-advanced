
(function($){

    acf.conditional_logic_advanced = acf.model.extend({

        actions: {
            'prepare 20': 	'render',
            'append 20': 	'render'
        },

        events: {
            'change #page_template':								'_change_template',
            'change #post-formats-select input':					'_change_format',
            'change .categorychecklist input':						'_change_term',
            'change .categorychecklist select':						'_change_term',
			'change .acf-field input': 	                            'change',
			'change .acf-field textarea': 	                        'change',
			'change .acf-field select':                          	'change'
        },
        o: {
            //'page_template':	0,
            //'post_format':	0,
            //'post_taxonomy':	0
        },

        items: {},
        triggers: {},


        /*
         *  add
         *
         *  This function will add a set of conditional logic rules
         *
         *  @type	function
         *  @date	22/05/2015
         *  @since	5.2.3
         *
         *  @param	target (string) target field key
         *  @param	groups (array) rule groups
         *  @return	$post_id (int)
         */

        add: function( target, groups ){

            // debug
            //console.log( 'conditional_logic.add(%o, %o)', target, groups );


            // populate triggers
            for( var i in groups ) {

                // vars
                var group = groups[i];

                for( var k in group ) {

                    // vars
                    var rule = group[k],
                        trigger = (rule.param) ? rule.param : rule.field,
                        triggers = this.triggers[ trigger ] || {};


                    // append trigger (sub field will simply override)
                    triggers[ target ] = target;


                    // update
                    this.triggers[ trigger ] = triggers;

                }

            }


            // append items
            this.items[ target ] = groups;
        },

        update: function( k, v ){

            this.o[ k ] = v;

            //console.log('update', k, v);

            return this;

        },


        _update_template: function(){

            // vars
            var page_template = $('#page_template').val();


            // update & fetch
            return this.update('page_template', page_template);

        },

        _change_template: function() {
            this._update_template().refresh();
        },

        _update_format: function(){

            // vars
            var post_format = $('#post-formats-select input:checked').val();


            // default
            if( post_format == '0' ) {

                post_format = 'standard';

            }


            // update & fetch
            return this.update('post_format', post_format);

        },

        _change_format: function() {
            this._update_format().refresh();
        },

        _update_term: function(){

            // reference
            var self = this;


            // bail early if within media popup
            if( $('.categorychecklist input, .categorychecklist select').closest('.media-frame').exists() ) {

                return;

            }

            // vars
            var values = [];


            // loop over term lists
            $('.categorychecklist').each(function(){

                // vars
                var $el = $(this),
                    $checkbox = $el.find('input[type="checkbox"]').not(':disabled'),
                    $radio = $el.find('input[type="radio"]').not(':disabled'),
                    $select = $el.find('select').not(':disabled'),
                    $hidden = $el.find('input[type="hidden"]').not(':disabled');


                // bail early if in attachment
                if( $el.closest('.media-frame').exists() ) {

                    return;

                }


                // checkbox
                if( $checkbox.exists() ) {

                    $checkbox.filter(':checked').each(function(){

                        values.push( parseInt($(this).val()) );

                    });

                } else if( $radio.exists() ) {

                    $radio.filter(':checked').each(function(){

                        values.push( parseInt($(this).val()) );

                    });

                } else if( $select.exists() ) {

                    $select.find('option:selected').each(function(){

                        values.push( parseInt($(this).val()) );

                    });

                } else if( $hidden.exists() ) {

                    $hidden.each(function(){

                        // ignor blank values
                        if( ! $(this).val() ) {

                            return;

                        }

                        values.push( parseInt($(this).val()) );

                    });

                }

            });


            // filter duplicates
            values = values.filter (function (v, i, a) { return a.indexOf (v) == i });


            // update screen
            return this.update( 'post_taxonomy', values );

        },

        _change_term: function() {
            this._update_term().refresh();
        },


        /*
         *  render
         *
         *  This function will render all fields
         *
         *  @type	function
         *  @date	22/05/2015
         *  @since	5.2.3
         *
         *  @param	$post_id (int)
         *  @return	$post_id (int)
         */

        render: function( $el ) {
            // debug
            //console.log('conditional_logic.render(%o)', $el);

            this._update_format();
            this._update_template();
            this._update_term();

            this.refresh();
        },

		/*
		*  change
		*
		*  This function is called when an input is changed and will render any fields which are considered targets of this trigger
		*
		*  @type	function
		*  @date	22/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/

		change: function( e ){

			// debug
			//console.log( 'conditional_logic.change(%o)', $input );


			// vars
			var $input = e.$el,
				$field = acf.get_field_wrap( $input ),
				key = $field.data('key');


			// bail early if this field does not trigger any actions
			if( typeof this.triggers[key] === 'undefined' ) {

				return false;

			}


			// vars
			$parent = $field.parent();


			// update visibility
			for( var i in this.triggers[ key ] ) {

				// get the target key
				var target_key = this.triggers[ key ][ i ];


				// get targets
				var $targets = acf.get_fields(target_key, $parent, true);


				// render
				this.render_fields( $targets );

			}


			// action for 3rd party customization
			//acf.do_action('refresh', $parent);

		},

        refresh: function() {
            // get targets
            var $targets = acf.get_fields( '', '', true );


            // render fields
            this.render_fields( $targets );


            // action for 3rd party customization
            //acf.do_action('refresh', $el);

        },


        /*
         *  render_fields
         *
         *  This function will render a selection of fields
         *
         *  @type	function
         *  @date	22/05/2015
         *  @since	5.2.3
         *
         *  @param	$post_id (int)
         *  @return	$post_id (int)
         */

        render_fields: function( $targets ) {

            // reference
            var self = this;


            // loop over targets and render them
            $targets.each(function(){

                self.render_field( $(this) );

            });

        },


        /*
         *  render_field
         *
         *  This function will render a field
         *
         *  @type	function
         *  @date	22/05/2015
         *  @since	5.2.3
         *
         *  @param	$post_id (int)
         *  @return	$post_id (int)
         */

        render_field : function( $target ){

            // vars
            var key = $target.data('key');


            // bail early if this field does not contain any conditional logic
            if( typeof this.items[ key ] === 'undefined' ) {

                return false;

            }


            // vars
            var visibility = false;


            // debug
            //console.log( 'conditional_logic.render_field(%o)', $field );


            // get conditional logic
            var groups = this.items[ key ];


            // calculate visibility
            for( var i = 0; i < groups.length; i++ ) {

                // vars
                var group = groups[i],
                    match_group	= true;

                for( var k = 0; k < group.length; k++ ) {

                    // vars
                    var rule = group[k];

                    // break if rule did not validate
                    if( !this.calculate(rule, $target) ) {

                        match_group = false;
                        break;

                    }

                }


                // set visibility if rule group did validate
                if( match_group ) {

                    visibility = true;
                    break;

                }

            }


            // hide / show field
            if( visibility ) {

                this.show_field( $target );

            } else {

                this.hide_field( $target );

            }

        },


        /*
         *  show_field
         *
         *  This function will show a field
         *
         *  @type	function
         *  @date	22/05/2015
         *  @since	5.2.3
         *
         *  @param	$post_id (int)
         *  @return	$post_id (int)
         */

        show_field: function( $field ){

            // debug
            //console.log('show_field(%o)', $field);


            // vars
            var key = $field.data('key');


            // remove class
            $field.removeClass( 'hidden-by-conditional-logic-advanced' );


            // enable
            acf.enable_form( $field, 'condition_'+key );


            // action for 3rd party customization
            acf.do_action('show_field', $field, 'conditional_logic_advanced' );

        },


        /*
         *  hide_field
         *
         *  This function will hide a field
         *
         *  @type	function
         *  @date	22/05/2015
         *  @since	5.2.3
         *
         *  @param	$post_id (int)
         *  @return	$post_id (int)
         */

        hide_field : function( $field ){

            // debug
            //console.log('hide_field(%o)', $field);


            // vars
            var key = $field.data('key');


            // add class
            $field.addClass( 'hidden-by-conditional-logic-advanced' );


            // disable
            acf.disable_form( $field, 'condition_'+key );


            // action for 3rd party customization
            acf.do_action('hide_field', $field, 'conditional_logic_advanced' );

        },


		/*
		*  get_trigger
		*
		*  This function will return the relevant $trigger for a $target
		*
		*  @type	function
		*  @date	22/05/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/

		get_trigger: function( $target, key ){

			// vars
			var selector = acf.get_selector( key );


			// find sibling $trigger
			var $trigger = $target.siblings( selector );


			// parent trigger
			if( !$trigger.exists() ) {

				// vars
				var parent = acf.get_selector();


				// loop through parent fields and review their siblings too
				$target.parents( parent ).each(function(){

					// find sibling $trigger
					$trigger = $(this).siblings( selector );


					// bail early if $trigger is found
					if( $trigger.exists() ) {

						return false;

					}

				});

			}


			// bail early if no $trigger is found
			if( !$trigger.exists() ) {

				return false;

			}


			// return
			return $trigger;

		},


        /*
         *  calculate
         *
         *  This function will calculate if a rule matches based on the $trigger
         *
         *  @type	function
         *  @date	22/05/2015
         *  @since	5.2.3
         *
         *  @param	$post_id (int)
         *  @return	$post_id (int)
         */

        calculate : function( rule, $target ){

            // debug
            //console.log( 'calculate(%o, %o, %o)', rule, $trigger, $target);

            // vars
            var match = false;

            switch(rule['param']) {
                case 'post_template':
                    match = this.o['page_template'] == rule['value'];
                    break;

                case 'post_category':
                    match = this.o['post_taxonomy'].indexOf(rule['value']) > -1;
                    break;

                case 'post_format':
                    match = this.o['post_format'] == rule['value'];
                    break;

                default:
                    if (rule['field']) {
                        var $trigger = this.get_trigger( $target, rule.field ),
                            type = $trigger.data('type');

                        // input with :checked
                        if( type == 'true_false' || type == 'checkbox' || type == 'radio' ) {

                            match = this.calculate_checkbox( rule, $trigger );


                        } else if( type == 'select' ) {

                            match = this.calculate_select( rule, $trigger );

                        }
                    } else {
                        console.error('Unknown rule param: ' + rule['param']);
                    }
                    break;
            }

            // reverse if 'not equal to'
            if( rule.operator === "!=" ) {

                match = !match;

            }


            // return
            return match;

        },

		calculate_checkbox: function( rule, $trigger ){

			// look for selected input
			var match = $trigger.find('input[value="' + rule.value + '"]:checked').exists();


			// override for "allow null"
			if( rule.value === '' && !$trigger.find('input:checked').exists() ) {

				match = true;

			}


			// return
			return match;

		},


		calculate_select: function( rule, $trigger ){

			// vars
			var $select = $trigger.find('select'),
				val = $select.val();


			// check for no value
			if( !val && !$.isNumeric(val) ) {

				val = '';

			}


			// convert to array
			if( !$.isArray(val) ) {

				val = [ val ];

			}


			// calc
			match = ($.inArray(rule.value, val) > -1);


			// return
			return match;

		}
    });

})(jQuery);
