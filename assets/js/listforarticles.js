/**
 * Auto submit
 */
jQuery(document).delegate('.lfa-list-container input[name=lfa_votes]', 'click', function(e) {
    e.preventDefault();
    jQuery(this).parent().find("button[name=lfa_action]").click();
});
/**
 * Ajaxify forms
 */
jQuery(document).delegate('.lfa-list-container button[name^="lfa_"]', 'click', function(e) {
    e.preventDefault();
    var $this = jQuery(this);
    var $container = jQuery(this).parent().parent();
    
    var fields = $container.find('form').serializeArray();

    fields.push({name: $this.attr('name'), value: $this.val()});
    
    $container.fadeTo('slow', 0.5).css({'pointer-events': 'none', 'min-height' : $container.outerHeight()});
    
    jQuery.ajax({
        url: this.action,
        type: 'POST',
        data: fields,
        success: function(content)
        { 
            $container.find(".readymsg").css("display","block");
            $container.find("form").css("display","none");
        }
    });
});