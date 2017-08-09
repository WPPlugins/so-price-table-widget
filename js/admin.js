jQuery(function($){

    $(document).on('change', '.siteorigin-widget-form-main-siteorigin-widget-pricetable-widget .siteorigin-widget-field select[name*="icon"]', function(){
        var $field = $(this);
        var $icon = $field.parent().find('.so-pt-svg-icon');

        if(!$icon.length) {
            // Create the icon
            $icon = $('<img class="so-pt-svg-icon" />')
                .css({
                    'width' : '24px',
                    'height' : '24px',
                    'margin-bottom' : '-8px',
                    'margin-left' : '8px'
                })
                .appendTo($field.parent());
        }

        if($field.val() == '') {
            $icon.hide();
        }
        else {
            $icon.attr('src', siteoriginPricetable.svg_url + $field.val() + '.svg').show();
        }
    });
    $('.siteorigin-widget-form-main-siteorigin-widget-pricetable-widget .siteorigin-widget-field select[name*="icon"]').change();

});