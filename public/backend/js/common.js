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
 //   });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
    