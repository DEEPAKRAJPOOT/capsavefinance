try {
    // jQuery(document).ready(function ($) {
    /**
     * Create custom alert box
     * 
     * @param {string} title
     * @param {string} content
     * @returns {dialoge}
     */
    function customAlert(title, content)
    {
        jQuery.alert({
            title: title,
            content: content,
            icon: 'fa fa-warning',
            animation: 'scale',
            closeAnimation: 'scale',
            opacity: 0.5
        });
    }

    jQuery.validator.addMethod("lettersonly", function (value, element) {
        return this.optional(element) || /^[a-zA-Z\s]+$/i.test(value);
    }, "Please enter alphabetical characters.");


    jQuery.validator.addMethod("numberOnly", function (value, element) {
        var re = new RegExp('^\\d+$');
        return this.optional(element) || re.test(value);
    }, "Please enter valid number."
            );
    //   });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
    