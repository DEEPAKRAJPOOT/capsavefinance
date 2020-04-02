/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
        config.extraPlugins = 'uploadimage';
};

CKEDITOR.on('instanceReady', function(ev) {

    //resp. images for bootstrap 3
    ev.editor.dataProcessor.htmlFilter.addRules(
            {
                elements:
                        {
                            $: function(element) {
                                // Output dimensions of images as width and height
                                if (element.name == 'img') {
                                    var style = element.attributes.style;
                                    //responzive images

                                    //declare vars
                                    var tclass = "";
                                    var add_class = false;

                                    tclass = element.attributes.class;

                                    //console.log(tclass);
                                    //console.log(typeof (tclass));

                                    if (tclass === "undefined" || typeof (tclass) === "undefined") {
                                        add_class = true;
                                    } else {
                                        //console.log("I am not undefined");
                                        if (tclass.indexOf("img-responsive") == -1) {
                                            add_class = true;
                                        }
                                    }

                                    if (add_class) {
                                        var rclass = (tclass === undefined || typeof (tclass) === "undefined" ? "img-responsive" : tclass + " " + "img-responsive");
                                        element.attributes.class = rclass;
                                    }

                                    if (style) {
                                        // Get the width from the style.
                                        var match = /(?:^|\s)width\s*:\s*(\d+)px/i.exec(style),
                                                width = match && match[1];

                                        // Get the height from the style.
                                        match = /(?:^|\s)height\s*:\s*(\d+)px/i.exec(style);
                                        var height = match && match[1];

                                        if (width) {
                                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)width\s*:\s*(\d+)px;?/i, '');
                                            element.attributes.width = width;
                                        }

                                        if (height) {
                                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)height\s*:\s*(\d+)px;?/i, '');
                                            element.attributes.height = height;
                                        }
                                    }
                                }



                                if (!element.attributes.style)
                                    delete element.attributes.style;

                                return element;
                            }
                        }
            });
});