(function($){

    $.fn.sowSetupForm = function() {
        return $(this).each( function(i, el){
            var $el = $(el);

            // Skip this if we've already set up the form
            if( $el.is('.siteorigin-widget-form-main') ) {
                if( $el.data('sow-form-setup') == true ) return true;
                if( $('body').hasClass('widgets-php') && !$el.is(':visible') ) return true;
            }

            var $fields = $el.find('> .siteorigin-widget-field');

            // Store the field names
            $el.find('> .siteorigin-widget-field .siteorigin-widget-input').each(function(i, input){
                if( $(input).data( 'original-name') == null ) {
                    $(input).data( 'original-name', $(input).attr('name') );
                }
            });

            // Setup all the repeaters
            $fields.find('> .siteorigin-widget-field-repeater').sowSetupRepeater();

            // For any repeater items currently in existence
            $el.find('.siteorigin-widget-field-repeater-item').sowSetupRepeaterActions();

            // Set up any color fields
            $fields.find('> .siteorigin-widget-input-color').wpColorPicker()
                .closest('.siteorigin-widget-field').find('a').click(function(){
                    $(this).closest('.panel-dialog').dialog("option", "position", "center");
                });

            // When anything changes, update the preview
            $fields.find('> .siteorigin-widget-input').change(function(){
                var inputs = $(this).closest('.siteorigin-widget-form').find('.siteorigin-widget-input').clone();
                var preview = $(this).closest('.siteorigin-widget-form').find('.siteorigin-widget-preview');

                // If there isn't a widget preview
                if(preview.length == 0) return;

                var form = $('<form />')
                    .attr({
                        'target' : preview.attr('name'),
                        'method' : 'POST',
                        'action' : ajaxurl + "?action=siteorigin_widget_preview&class=" + $(this).closest('.siteorigin-widget-form').data('class')
                    })
                    .append(inputs)
                    .submit();
            });

            // Handle the media uploader
            $fields.find('> .media-field-wrapper a.media-upload-button' ).click(function(event){
                var $$ = $(this);
                var $c = $(this ).closest('.siteorigin-widget-field');
                var frame = $(this ).data('frame');

                // If the media frame already exists, reopen it.
                if ( frame ) {
                    frame.open();
                    return false;
                }

                // Create the media frame.
                frame = wp.media({
                    // Set the title of the modal.
                    title: $$.data('choose'),

                    // Tell the modal to show only images.
                    library: {
                        type: 'image'
                    },

                    // Customize the submit button.
                    button: {
                        // Set the text of the button.
                        text: $$.data('update'),
                        // Tell the button not to close the modal, since we're
                        // going to refresh the page when the image is selected.
                        close: false
                    }
                });

                // Store the frame
                $$.data('frame', frame);

                // When an image is selected, run a callback.
                frame.on( 'select', function() {
                    // Grab the selected attachment.
                    var attachment = frame.state().get('selection').first().attributes;

                    $c.find('.current .title' ).html(attachment.title);
                    $c.find('input[type=hidden]' ).val(attachment.id);

                    if(typeof attachment.sizes != 'undefined'){
                        if(typeof attachment.sizes.thumbnail != 'undefined')
                            $c.find('.current .thumbnail' ).attr('src', attachment.sizes.thumbnail.url).fadeIn();
                        else
                            $c.find('.current .thumbnail' ).attr('src', attachment.sizes.full.url).fadeIn();
                    }
                    else{
                        $c.find('.current .thumbnail' ).attr('src', attachment.icon).fadeIn();
                    }

                    frame.close();
                });

                // Finally, open the modal.
                frame.open();

                return false;
            });

            // Show/hide the remove button when hovering over the media select button.
            $fields.find('> .media-field-wrapper' )
                .mouseenter(function(){
                    if($(this ).closest('.siteorigin-widget-field').find('input[type=hidden]' ).val() != '') $(this ).find('.media-remove-button').fadeIn('fast');
                })
                .mouseleave(function(){
                    $(this ).find('.media-remove-button').fadeOut('fast');
                })

            $fields.find('> .media-field-wrapper .current' )
                .mouseenter(function(){
                    var t = $(this ).find('.title' );
                    if( t.html() != ''){
                        t.fadeIn('fast');
                    }
                })
                .mouseleave(function(){
                    $(this ).find('.title' ).clearQueue().fadeOut('fast');
                })

            $fields.find('> .media-field-wrapper a.media-remove-button' )
                .click(function(){
                    var $$ = $(this ).closest('.siteorigin-widget-field');

                    $$.find('.current .title' ).html('');
                    $$.find('input[type=hidden]' ).val('');
                    $$.find('.current .thumbnail' ).fadeOut('fast');
                    $(this ).fadeOut('fast');
                });

            // Give plugins a chance to influence the form
            $el.trigger('sowsetupform').data('sow-form-setup', true);
            $el.find('.siteorigin-widget-field-repeater-item').trigger('updateFieldPositions');
        } );
    };

    $.fn.sowSetupRepeater = function(){

        return $(this).each( function(i, el){
            var $el = $(el);
            var $items = $el.find('.siteorigin-widget-field-repeater-items');
            var name = $el.data('repeater-name');

            $items.bind('updateFieldPositions', function(){
                var $$ = $(this);

                // Set the position for the repeater items
                $$.find('> .siteorigin-widget-field-repeater-item').each(function(i, el){
                    $(el).find('.siteorigin-widget-input').each(function(j, input){
                        var pos = $(input).data('repeater-positions');
                        if( typeof pos == 'undefined' ) {
                            pos = {};
                        }

                        pos[name] = i;
                        $(input).data('repeater-positions', pos);
                    });
                });

                // Update the field names for all the input items
                $$.find('.siteorigin-widget-input').each(function(i, input){
                    var pos = $(input).data('repeater-positions');
                    var $in = $(input);

                    if(typeof pos != 'undefined') {
                        var newName = $in.data('original-name');

                        if(typeof newName == 'undefined') {
                            $in.data( 'original-name', $in.attr('name') );
                            newName = $in.attr('name');
                        }

                        for(var k in pos) {
                            newName = newName.replace('#' + k + '#', pos[k] );
                        }
                        $(input).attr('name', newName);
                    }
                });

            });

            $items.sortable( {
                handle : '.siteorigin-widget-field-repeater-item-top',
                items : '> .siteorigin-widget-field-repeater-item',
                update: function(){
                    $items.trigger('updateFieldPositions');
                }
            });
            $items.trigger('updateFieldPositions');

            $el.find('> .siteorigin-widget-field-repeater-add').disableSelection().click( function(e){
                e.preventDefault();
                $el.closest('.siteorigin-widget-field-repeater')
                    .sowAddRepeaterItem()
                    .find('> .siteorigin-widget-field-repeater-items').slideDown('fast');

                // Center the PB dialog
                $(this).closest('.panel-dialog').dialog("option", "position", "center");
            } );

            $el.find('> .siteorigin-widget-field-repeater-top > .siteorigin-widget-field-repeater-expend').click( function(e){
                e.preventDefault();
                $el.closest('.siteorigin-widget-field-repeater').find('> .siteorigin-widget-field-repeater-items').slideToggle('fast');
            } );
        } );
    };

    $.fn.sowAddRepeaterItem = function(){
        return $(this).each( function(i, el){

            var $el = $(el);
            var theClass = $el.closest('.siteorigin-widget-form').data('class');

            var formClass = $el.closest('.siteorigin-widget-form').data('class');

            var item = $('<div class="siteorigin-widget-field-repeater-item" />')
                .append(
                    $('<div class="siteorigin-widget-field-repeater-item-top" />')
                        .append(
                            $('<div class="siteorigin-widget-field-repeater-item-expand" />')

                        )
                        .append(
                            $('<div class="siteorigin-widget-field-repeater-item-remove" />')

                        )
                        .append( $('<h4 />').html( $el.data('item-name') ) )
                )
                .append(
                    $('<div class="siteorigin-widget-field-repeater-item-form" />')
                        .html( window.sow_repeater_html[formClass][$el.data('repeater-name')] )
                )
                .sowSetupRepeaterActions();

            // Add the item and refresh
            $el.find('> .siteorigin-widget-field-repeater-items').append(item).sortable( "refresh").trigger('updateFieldPositions');
            item.hide().slideDown('fast');

        } );
    };

    $.fn.sowSetupRepeaterActions = function(){
        return $(this).each( function(i, el){
            var $el = $(el);

            if(typeof $el.data('sowrepeater-actions-setup') == 'undefined') {
                var top = $el.find('> .siteorigin-widget-field-repeater-item-top');

                top.find('.siteorigin-widget-field-repeater-item-expand')
                    .click(function(e){
                        e.preventDefault();
                        $(this).closest('.siteorigin-widget-field-repeater-item').find('.siteorigin-widget-field-repeater-item-form').eq(0).slideToggle('fast', function(){
                            $(this).closest('.panel-dialog').dialog("option", "position", "center");
                        });
                    });

                top.find('.siteorigin-widget-field-repeater-item-remove')
                    .click(function(e){
                        e.preventDefault();
                        if(confirm(soWidgets.sure)) {
                            var $s = $(this).closest('.siteorigin-widget-field-repeater-items');
                            $(this).closest('.siteorigin-widget-field-repeater-item').slideUp('fast', function(){
                                $(this).remove();
                                $s.sortable( "refresh" ).trigger('updateFieldPositions');
                            });
                        }
                    });

                $el.find('> .siteorigin-widget-field-repeater-item-form').sowSetupForm();

                $el.data('sowrepeater-actions-setup', true);
            }
        });
    }

    // When we click on a widget top
    $('.widgets-holder-wrap').on('click', '.widget:has(.siteorigin-widget-form-main) .widget-top', function(){
        var $$ = $(this).closest('.widget').find('.siteorigin-widget-form-main');
        setTimeout( function(){ $$.sowSetupForm(); }, 200);
    });

    // When we open a Page Builder widget dialog
    $(document).on('dialogopen', function(e){
        $(e.target).find('.siteorigin-widget-form-main').sowSetupForm();
    });

})(jQuery);