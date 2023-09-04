/**
 * Ultimate Membership Pro - Profile Form
 */
"use strict";
var IhcRegisterForm = {
    required_fields             : [],
    conditional_logic_fields    : [],
    conditional_text_fields     : [],
    unique_fields               : [],
    must_submit                 : 0,

    init									: function( args ){
        var obj = this;

        // required fields - from global variables to object properties
        if ( typeof window.ihc_register_required_fields !== 'undefined' ){
            obj.required_fields = JSON.parse( window.ihc_register_required_fields );
        }
        // conditional logic - from global variables to object properties
        if ( typeof window.ihc_register_conditional_logic !== 'undefined' ){
            obj.conditional_logic_fields = JSON.parse( window.ihc_register_conditional_logic );
        }
        // conditional text - from global variables to object properties
        if ( typeof window.ihc_register_conditional_text !== 'undefined' ){
            obj.conditional_text_fields = JSON.parse( window.ihc_register_conditional_text );
        }
        // unique field - from global variables to object properties
        if ( typeof window.ihc_register_unique_fields !== 'undefined' ){
            obj.unique_fields = JSON.parse( window.ihc_register_unique_fields );
        }

        // required fields
        if ( obj.required_fields.length > 0 ){

            // loop through all required fields, put the check function on the blur event.
            jQuery( obj.required_fields ).each( function( index, fieldName ){
                var currentFormFieldType = obj.getFieldTypeByName( fieldName );

                if ( obj.inArray( currentFormFieldType, [ 'text', 'textarea', 'number', 'password', 'conditional_text', 'select' ] )  ){
                    jQuery( ".ihc-form-create-edit [name='" + fieldName + "']" ).on( "blur", function(){
                        obj.checkRequiredField( fieldName );
                    });
                } else if ( currentFormFieldType === 'radio' ){
                    jQuery( ".ihc-form-create-edit input[type=radio][name='" + fieldName + "']" ).on( "change", function(){
                        obj.checkRequiredField( fieldName, currentFormFieldType );
                    });
                } else if ( currentFormFieldType === 'checkbox' ){
                    if ( jQuery( ".ihc-form-create-edit [name='" + fieldName + "[]']" ).length > 0 ){
                        jQuery( ".ihc-form-create-edit [name='" + fieldName + "[]']" ).on( "change", function(){
                            obj.checkRequiredField( fieldName, currentFormFieldType );
                        });
                    } else if ( jQuery(".ihc-form-create-edit [name="+fieldName+"]").length > 0 ) {
                        // checkbox - single value
                        jQuery( ".ihc-form-create-edit [name="+fieldName+"]" ).on( "change", function(){
                            obj.checkRequiredField( fieldName, currentFormFieldType );
                        });
                    }
                } else if ( currentFormFieldType === 'multiselect' ){
                    // multiselect
                    jQuery( ".ihc-form-create-edit [name='" + fieldName + "[]']" ).on( "blur", function(){
                        obj.checkRequiredField( fieldName, currentFormFieldType );
                    });
                } else if ( currentFormFieldType === 'date' ){
                    jQuery( ".ihc-form-create-edit [name='" + fieldName + "']" ).on( "change", function(){
                        obj.checkRequiredField( fieldName );
                    });
                }

            });

            // when the form is submited it will check all the required fields agaian via ajax.
            jQuery( '.ihc-form-create-edit' ).on( 'submit', function( evt ){
                if ( obj.must_submit == 1 ){
                    // everything is ok
                    return true;
                } else {
                    // stop the form from submiting
                    if ( indeedDetectBrowser() === "Firefox" ){
                        evt.preventDefault();
                        evt.stopPropagation();
                        evt.stopImmediatePropagation();
                    } else {
                        evt.preventDefault();
                    }
                    obj.checkAllFieldsBeforeSubmit( obj, evt );
                    return false;
                }
            });
        }
        // end of required fields

        // conditional logic
        if ( obj.conditional_logic_fields.length > 0 ){

            // loop through all conditional logic
            jQuery( obj.conditional_logic_fields ).each( function( index ){
                switch ( obj.conditional_logic_fields[index].type ){
                    case 'text':
                    case 'textarea':
                    case 'number':
                    case 'password':
                    case 'date':
                    case 'conditional_text':
                    case 'unique_value_text':
                      // on blur event
                      jQuery(".ihc-form-create-edit [name=" + obj.conditional_logic_fields[index].field_to_check + "]").on("blur", function(){
                          var checkValue = jQuery(".ihc-form-create-edit [name=" + obj.conditional_logic_fields[index].field_to_check + "]").val();
                          obj.ihcAjaxCheckFieldCondition( checkValue, '#' + obj.conditional_logic_fields[index].target_parent_id, obj.conditional_logic_fields[index].target_field, obj.conditional_logic_fields[index].show );
                      });
                      break;
                    case 'select':
                      // on change event
                      jQuery(".ihc-form-create-edit [name=" + obj.conditional_logic_fields[index].field_to_check + "]").on("change", function(){
                          var checkValue = jQuery(".ihc-form-create-edit [name='" + obj.conditional_logic_fields[index].field_to_check + "']").val();
                          obj.ihcAjaxCheckFieldCondition( checkValue, '#' + obj.conditional_logic_fields[index].target_parent_id, obj.conditional_logic_fields[index].target_field, obj.conditional_logic_fields[index].show );
                      });
                      break;
                    case 'multi_select':
                      // on change event
                      jQuery(".ihc-form-create-edit [name='" + obj.conditional_logic_fields[index].field_to_check + "[]']").on("change", function(){
                          var checkValue = jQuery(".ihc-form-create-edit [name='" + obj.conditional_logic_fields[index].field_to_check + "[]']").val();
                          if ( checkValue != null ){
                              var checkValue = checkValue.join(',');
                          }
                          // do something with checkValue
                          obj.ihcAjaxCheckFieldCondition( checkValue, '#' + obj.conditional_logic_fields[index].target_parent_id, obj.conditional_logic_fields[index].target_field, obj.conditional_logic_fields[index].show );
                      });
                      break;
                    case 'checkbox':
                    case 'radio':
                      // on click event
                      jQuery(".ihc-form-create-edit [name=" + obj.conditional_logic_fields[index].field_to_check + "]").on( "click", function(){
                        if ( obj.conditional_logic_fields[index].type == 'checkbox' ){
                            var vals = [];
                            jQuery( ".ihc-form-create-edit [name='"+obj.conditional_logic_fields[index].field_to_check+"[]']:checked" ).each(function() {
                                vals.push( jQuery( this ).val() );
                            });
                            var checkValue = vals.join( ',' );
                        } else {
                            var checkValue = jQuery( ".ihc-form-create-edit [name="+obj.conditional_logic_fields[index].field_to_check+"]:checked" ).val();
                        }
                        // do something with checkValue
                        obj.ihcAjaxCheckFieldCondition( checkValue, '#' + obj.conditional_logic_fields[index].target_parent_id, obj.conditional_logic_fields[index].target_field, obj.conditional_logic_fields[index].show );
                      });
                      break;
                }
            });
        }

        // conditional text ( Verification Code )
        if ( obj.conditional_text_fields.length > 0 ){
            jQuery( obj.conditional_text_fields ).each( function( index ){
              // on blur event
              jQuery(".ihc-form-create-edit [name=" + obj.conditional_text_fields[index] + "]").on("blur", function(){
                  var checkValue = jQuery(".ihc-form-create-edit [name=" + obj.conditional_text_fields[index] + "]").val();
                  obj.ajaxCheckConditionalText( obj.conditional_text_fields[index], obj );
              });
            });
        }

        // unique fields
        if ( obj.unique_fields.length > 0 ){
            jQuery( obj.unique_fields ).each( function( index ){
              // on blur event
              jQuery(".ihc-form-create-edit [name=" + obj.unique_fields[index] + "]").on("blur", function(){
                  var checkValue = jQuery(".ihc-form-create-edit [name=" + obj.unique_fields[index] + "]").val();
                  obj.ajaxCheckUniqueField( obj.unique_fields[index] );
              });
            });
        }

        // country and state
        if ( jQuery('[name=ihc_country]').length > 0 && jQuery('[name=ihc_state]').length > 0 ){
            // on change event
            jQuery('[name=ihc_country]').on( 'change', function(){
                obj.updateStateField();
            } );
        }

    },

    // check one required field. Trigger by blur event.
    checkRequiredField    : function( fieldName, fieldType ){
      	var target_id = '#' + jQuery('.ihc-form-create-edit [name=' + fieldName + ']').parent().attr('id');
        // getting the value from the specified field
        if ( fieldType === 'radio' ){
            // radio - one value
            var target_id = '#' + jQuery('.ihc-form-create-edit [name=' + fieldName + ']').parent().parent().attr('id');
            var val1 = jQuery( ".ihc-form-create-edit [name=" + fieldName + "]:checked" ).val();
        } else if ( fieldType === 'checkbox' ){
            // checkbox
            if ( jQuery(".ihc-form-create-edit [name='"+fieldName+"[]']").length > 0  ){
                // checkbox - multiple values
                var target_id = '#' + jQuery(".ihc-form-create-edit [name='"+fieldName+"[]']").first().parent().parent().attr('id');
                var vals = [];
                jQuery( ".ihc-form-create-edit [name='"+fieldName+"[]']:checked" ).each(function() {
                    vals.push( jQuery( this ).val() );
                });
                var val1 = vals.join( ',' );
            } else if ( jQuery(".ihc-form-create-edit [name="+fieldName+"]").length > 0 ) {
                // checkbox - single value
                var target_id = '#' + jQuery('.ihc-form-create-edit [name=' + fieldName + ']').parent().parent().attr('id');
                var val1 = jQuery( ".ihc-form-create-edit [name=" + fieldName + "]:checked" ).val();
            }
        } else if ( fieldType === 'multiselect' ) {
            var target_id = '#' + jQuery(".ihc-form-create-edit [name='"+fieldName+"[]']").parent().attr('id');
            var val1 = jQuery(".ihc-form-create-edit [name='"+fieldName+"[]']").val();
            var val2 = '';
        } else {
            var val1 = jQuery('.ihc-form-create-edit [name=' + fieldName + ']').val();
            var val2 = '';
        }

        if ( typeof val1 === 'undefined' ){
            val1 = '';
        }

        // special treatment for pass and confirm email
      	if ( fieldName == 'pass2' ){
      		val2 = jQuery('.ihc-form-create-edit [name=pass1]').val();
      	} else if ( fieldName == 'confirm_email' ){
      		val2 = jQuery('.ihc-form-create-edit [name=user_email]').val();
      	}

        jQuery.ajax({
              type : "post",
              url : decodeURI(window.ihc_site_url)+'/wp-admin/admin-ajax.php',
              data : {
                         action         : "ihc_ajax_register_forms_check_one_field",
                         name           : fieldName,
                         value          : val1,
                         second_value   : val2,
                         is_edit        : 0,
              },
              success: function ( response ) {
                  var data = JSON.parse( response );
                  if ( typeof window.indeedRegisterErrors === 'undefined' ){
                      window.indeedRegisterErrors = [];
                  }

                  //remove prev notice, if its case
                	jQuery(target_id + ' .ihc-register-notice').remove();
                	jQuery('.ihc-form-create-edit [name=' + fieldName + ']').removeClass('ihc-input-notice');

                  if ( data.status == 1 ){
                  		// it's all good
                      indeedRemoveElementFromArray( window.indeedRegisterErrors, fieldName );
                	} else {
                      // error
                  		jQuery( target_id ).append('<div class="ihc-register-notice">' + data.message + '</div>');
                  		jQuery('.ihc-form-create-edit [name='+fieldName+']').addClass('ihc-input-notice');
                      indeedAddElementToArray( window.indeedRegisterErrors, fieldName );
                  }
              }
        });
    },

    // this function will loop through all fields before submiting the form
    checkAllFieldsBeforeSubmit          : function( obj, evt ){
        // remove old notices
        jQuery( '.ihc-register-notice' ).remove();

        // creating the array of fields that must be checked
        var fields_to_send = [];
        var exceptions = jQuery("[name=ihc_exceptionsfields]").val();
      	if ( exceptions ){
            // exceptions are the conditional logic fields, that are required in some case.
            var exceptions_arr = exceptions.split(',');
      	}

        for ( var i=0; i<obj.required_fields.length; i++ ){
          if ( exceptions_arr && exceptions_arr.indexOf( obj.required_fields[i] ) > -1 ){
              //CHECK IF FIELD is in exceptions
              continue;
          }

          var is_unique_field = false;

          jQuery('.ihc-form-create-edit [name='+obj.required_fields[i]+']').removeClass('ihc-input-notice');

          var field_type = obj.getFieldTypeByName( obj.required_fields[i] );

          if (field_type=='checkbox' || field_type=='radio'){
            var val1 = ihcGetCheckboxRadioValue(field_type, obj.required_fields[i]);
          } else if ( field_type=='multiselect' ){
            val1 = jQuery('.ihc-form-create-edit [name=\'' + obj.required_fields[i] + '[]\']').val();
            if (typeof val1=='object' && val1!=null){
              val1 = val1.join(',');// array to string conversion
            }
          } else {
            var val1 = jQuery('.ihc-form-create-edit [name='+obj.required_fields[i]+']').val();
            if (jQuery('.ihc-form-create-edit [name='+obj.required_fields[i]+']').attr('data-search-unique')){
              var is_unique_field = true;
            }
          }

          var val2 = '';
          if (obj.required_fields[i]=='pass2'){
            val2 = jQuery('.ihc-form-create-edit [name=pass1]').val();
          } else if (obj.required_fields[i]=='confirm_email'){
            val2 = jQuery('.ihc-form-create-edit [name=user_email]').val();
          } else if (obj.required_fields[i] == 'tos') {
      			if (jQuery('.ihc-form-create-edit [name=tos]').is(':checked')){
      				val1 = 1;
      			} else {
      				val1 = 0;
      			}
      		} else if ( obj.required_fields[i] == 'recaptcha' ){
              val1 = jQuery( '.ihc-form-create-edit [name=g-recaptcha-response]' ).val();
          }

          var params_to_send = {name: obj.required_fields[i], value: val1, second_value: val2};
          if (is_unique_field){
            params_to_send.is_unique_field = true;
          }
          fields_to_send.push(params_to_send);
        }


        jQuery.ajax({
            type : "post",
            url : decodeURI(window.ihc_site_url)+'/wp-admin/admin-ajax.php',
            data : {
                       action             : "ihc_ajax_register_form_check_all_fields",
                       fields_obj         : fields_to_send
            },
            success: function ( response ) {

              var responseObject = JSON.parse( response );
              var must_submit = 1;

            	for ( var j=0; j<responseObject.length; j++ ){
                  var field_type = obj.getFieldTypeByName( responseObject[j].name );

                	if (field_type=='radio'){
                		var target_id = jQuery('.ihc-form-create-edit [name='+responseObject[j].name+']').parent().parent().attr('id');
                	} else if (field_type=='checkbox' && responseObject[j].name!='tos'){
                		var target_id = jQuery('.ihc-form-create-edit [name=\''+responseObject[j].name+'[]\']').parent().parent().attr('id');
                	} else if ( field_type=='multiselect'){
                		var target_id = jQuery('.ihc-form-create-edit [name=\''+responseObject[j].name+'[]\']').parent().attr('id');
                	} else {
                		var target_id = jQuery('.ihc-form-create-edit [name='+responseObject[j].name+']').parent().attr('id');
                	}

                	if (responseObject[j].value==1){
                		// it's all good
                	} else {
                		//errors
                    	if (typeof target_id=='undefined'){
                    		//no target id...insert msg after input
                    		jQuery('.ihc-form-create-edit [name='+responseObject[j].name+']').after('<div class="ihc-register-notice">'+responseObject[j].message+'</div>');
                    		must_submit = 0;
                    	} else {
                    		jQuery('#'+target_id).append('<div class="ihc-register-notice">'+responseObject[j].message+'</div>');
                    		jQuery('.ihc-form-create-edit [name=' + responseObject[j].name + ']').addClass('ihc-input-notice');
                    		must_submit = 0;
                    	}
                	}
            	}

              window.ihcRegisterCheckFieldsAjaxFired = 0;
            	if (must_submit==1){
                 obj.must_submit = 1;
                 jQuery(".ihc-form-create-edit").submit();
            	} else {
                 obj.must_submit = 0;
        			   return false;
            	}
            }
        });
    },

    // check if an array contain element
    inArray           : function( needle, haystack ) {
        for ( var i = 0; i < haystack.length; i++ ) {
            if ( haystack[i] == needle ){
               return true;
            }
        }
        return false;
    },

    // getting the type of field based on name of field.
    getFieldTypeByName        : function( name ){
        var fieldType = jQuery('.ihc-form-create-edit [name=' + name + ']').attr('type');
        if ( fieldType === 'text' && jQuery( '.ihc-form-create-edit [name=' + name + ']' ).hasClass('iump-form-datepicker') ){
            return 'date';
        }
        if ( typeof fieldType === 'undefined' ){
           fieldType = jQuery('.ihc-form-create-edit [name=\'' + name + '[]\']').attr('type');
        }
        if ( typeof fieldType === 'undefined' ){
           fieldType = jQuery('.ihc-form-create-edit [name=\'' + name + '\']').prop('nodeName');
           if ( typeof fieldType !== 'undefined' && fieldType !== '' ){
              fieldType = fieldType.toLowerCase();
           }
        }
        if ( typeof fieldType === 'undefined' ){
            fieldType = jQuery('.ihc-form-create-edit [name=\'' + name + '[]\']').prop('nodeName');
            if ( typeof fieldType !== 'undefined' && fieldType !== '' ){
               fieldType = fieldType.toLowerCase();
            }
            if ( fieldType == 'select' ){
                fieldType = 'multiselect';
            }
        }
        return fieldType;
    },

    // conditional logic
    ihcAjaxCheckFieldCondition          : function(check_value, field_id, field_name, show){
       	jQuery.ajax({
            type : "post",
            url : decodeURI(window.ihc_site_url)+'/wp-admin/admin-ajax.php',
            data : {
                       action     : "ihc_ajax_register_form_check_one_conditional_logic",
                       value      : check_value,
                       field      : field_name
            },
            success: function ( response ){
            	var str = jQuery("[name=ihc_exceptionsfields]").val();
            	if (str){
                	var arr = str.split(',');
                	var index = arr.indexOf(field_name);
            	} else {
            		var arr = [];
            	}

            	if ( response == '1' ){
                    if (show==1){
                    	jQuery(field_id).css( 'display', 'block' );
                    	if (arr.indexOf(field_name)!=-1){
                          arr.splice(index, 1);
                    	}
                    } else {
                      	jQuery(field_id).css( 'display', 'none' );
                      	if (arr.indexOf(field_name)==-1){
                      		  arr.push(field_name);
                      	}
                    }
            	} else {
                  if (show==1){
                      jQuery(field_id).css( 'display', 'none' );
                      if (arr.indexOf(field_name)==-1){
                        	arr.push(field_name);
                      }
                  } else {
                      jQuery(field_id).css( 'display', 'block' );
                      if (arr.indexOf(field_name)!=-1){
                          arr.splice(index, 1);
                      }
                  }
            	}
            	if (arr){
                	var str = arr.join(',');
                	jQuery("[name=ihc_exceptionsfields]").val( str );
            	}
            }
       	});
    },

    // unique value field
    ajaxCheckUniqueField          : function( fieldName ){
        var targetId = '#' + jQuery('.ihc-form-create-edit [name='+fieldName+']').parent().attr('id');
      	var value = jQuery('.ihc-form-create-edit [name='+fieldName+']').val();
      	if ( value == '' ){
            return;
      	}
        jQuery.ajax({
            type : "post",
            url : decodeURI(window.ihc_site_url)+'/wp-admin/admin-ajax.php',
            data : {
                       action        : "ihc_ajax_register_form_check_unique_field",
                       meta_key      : fieldName,
                       meta_value    : value
            },
            success: function ( response ) {
              //remove prev notice, if its case
              var responseObject = JSON.parse( response );

              jQuery(targetId + ' .ihc-register-notice').remove();
              jQuery('.ihc-form-create-edit [name='+fieldName+']').removeClass('ihc-input-notice');
              if ( responseObject.status == 1){
                // it's all good

              } else {
                jQuery(targetId).append('<div class="ihc-register-notice">' + responseObject.message  + '</div>');
                jQuery('.ihc-form-create-edit [name=' + fieldName + ']').addClass('ihc-input-notice');
                obj.must_submit = 0;
              }
            }
        });
    },

    // conditional text
    ajaxCheckConditionalText            : function( fieldName, obj ){
        var targetId = '#' + jQuery('.ihc-form-create-edit [name='+fieldName+']').parent().attr('id');
      	var value = jQuery('.ihc-form-create-edit [name='+fieldName+']').val();
        if ( value == '' ){
            return;
        }
        jQuery.ajax({
            type : "post",
            url : decodeURI(window.ihc_site_url)+'/wp-admin/admin-ajax.php',
            data : {
                       action        : "ihc_ajax_register_form_check_conditional_text_field",
                       meta_key      : fieldName,
                       meta_value    : value
            },
            success: function ( response ) {
              //remove prev notice, if its case
              var responseObject = JSON.parse( response );

              jQuery(targetId + ' .ihc-register-notice').remove();
              jQuery('.ihc-form-create-edit [name='+fieldName+']').removeClass('ihc-input-notice');
              if ( responseObject.status == 1){
                // it's all good

              } else {
                jQuery(targetId).append('<div class="ihc-register-notice">' + responseObject.message  + '</div>');
                jQuery('.ihc-form-create-edit [name=' + fieldName + ']').addClass('ihc-input-notice');
                obj.must_submit = 0;
              }
            }
        });
    },

    // update state field
    updateStateField                : function(){
        var countryField = jQuery('.ihc-form-create-edit [name=ihc_country]');
        jQuery.ajax({
            type : "post",
            url : decodeURI( window.ihc_site_url ) + '/wp-admin/admin-ajax.php',
            data : {
                     action     : "ihc_ajax_get_state_field_as_html",
                     country    : countryField.val(),
                     is_edit    : 1,
            },
            success: function( response ){
                var field = jQuery('.ihc-form-create-edit [name=ihc_state]');
                var parent = field.parent();
                field.remove();
                parent.append( response );
            }
        });
    }

};

jQuery( window ).on( 'load', function(){
		IhcRegisterForm.init();
});
