'use strict';
/* function for typehead */
(function($) {	
   var substringMatcher = function(strs) {
      return function findMatches(q, cb) {
        var matches, substringRegex;
        // an array that will be populated with substring matches
        matches = [];
        // regex used to determine if a string contains the substring `q`
        var substrRegex = new RegExp(q, 'i');
        // iterate through the pool of strings and for any string that
        // contains the substring `q`, add it to the `matches` array
        $.each(strs, function(i, str) {
          if (substrRegex.test(str)) {
            matches.push(str);
          }
        });
        cb(matches);
      };
    };

    var content = ['Aadi theme', 'Photography theme', 'Pratham theme', 'News theme', 'Aadi'];

    if($('.basic-typeahead .typeahead').length > 0){
		 $('.basic-typeahead .typeahead').typeahead({
			hint: true,
			highlight: true,
			minLength: 1
		 },
		 {
			name: 'basic',
			source: substringMatcher(content)
		 });
	 }

	//Prefetch Auto Complete
	if($('.prefetch-typeahead .typeahead').length > 0){
		var countries = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.whitespace,
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			// url points to a json file that contains an array of country names, see
			// https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
			prefetch: 'assets/plugins/typeahead/typeahead_countries.json'
		});

		 // passing in `null` for the `options` arguments will result in the default
		 // options being used
	 
		 $('.prefetch-typeahead .typeahead').typeahead(null, {
			name: 'countries',
			source: countries
		 });
	 }
	if($('.typeahead-custom-template .typeahead').lenght > 0){
		 //Custom template view
		 var custom = new Bloodhound({
			datumTokenizer: function(d) { return d.tokens; },
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
			  url: 'assets/plugins/typeahead/typeahead_custom.php?query=%QUERY',
			  wildcard: '%QUERY'
			}
		 });
		  
		$('.typeahead-custom-template .typeahead').typeahead(null, {
			name: 'datypeahead_example_modal_3',
			displayKey: 'value',
			hint: true,
			source: custom,
			templates: {
			  suggestion: Handlebars.compile([
				 '<div class="media">',
						 '<div class="pull-left">',
							  '<div class="media-object">',
									'<img src="{{img}}" width="50" height="50"/>',
							  '</div>',
						 '</div>',
						 '<div class="media-body">',
							  '<h4 class="media-heading">{{value}}</h4>',
							  '<p>{{desc}}</p>',
						 '</div>',
				 '</div>',
			  ].join(''))
			}
		});
	}

	if($('.multiple-typeahead .typeahead').length > 0){
		 //Multiple Datasets
		 var nba = new Bloodhound({
			datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.team); },
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			prefetch: 'assets/plugins/typeahead/typeahead_nba.json'
		 });
					 
		 var nhl = new Bloodhound({
			datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.team); },
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			prefetch: 'assets/plugins/typeahead/typeahead_nhl.json'
		 });
             
	
		$('.multiple-typeahead .typeahead').typeahead({
			hint: true,
			highlight: true
		},
		{
			name: 'nba',
			displayKey: 'team',
			source: nba,
			templates: {header: '<h3>NBA Teams</h3>'}
		},
		{
			name: 'nhl',
			displayKey: 'team',
			source: nhl,
			templates: {
					header: '<h3>NHL Teams</h3>'
			}
		});
	}
})(jQuery);

/*----- code for summer note -------*/
(function($){
	if($('.summernote').length > 0){
		$('.summernote').summernote({height:300});
	}
	
})(jQuery);
var edit = function() {
  $('.click2edit').summernote({focus: true});
};

var save = function() {
  var makrup = $('.click2edit').summernote('code');
  $('.click2edit').summernote('destroy');
};


/*----- tags input function -------*/
(function($) {
    if($('.demo-tagsinput').length > 0){
		var eltdemo = $('.demo-tagsinput');
			eltdemo.tagsinput({
			itemValue: 'value',
			itemText: 'text',
		});
	}
	if($('.demo-tagsinput-add').length > 0){
		$('.demo-tagsinput-add').on('click', function(){
			eltdemo.tagsinput('add', { 
				"value": $('.demo-tagsinput-value').val(), 
				"text": $('.demo-tagsinput-city').val(), 
				"continent": $('.demo-tagsinput-continent').val()    
			});
		});
	}

	if($('.state-demo-tagsinput').length){
		eltdemo.tagsinput('add', { "value": 1 , "text": "Amsterdam"   , "continent": "Europe"    });
		eltdemo.tagsinput('add', { "value": 4 , "text": "Washington"  , "continent": "America"   });
		eltdemo.tagsinput('add', { "value": 7 , "text": "Sydney"      , "continent": "Australia" });
		eltdemo.tagsinput('add', { "value": 10, "text": "Beijing"     , "continent": "Asia"      });
		eltdemo.tagsinput('add', { "value": 13, "text": "Cairo"       , "continent": "Africa"    });
		var elt = $('.state-demo-tagsinput');
		elt.tagsinput({
		  tagClass: function(item) {
				switch (item.continent) {
					 case 'Europe':
						  return 'label label-primary';
					 case 'America':
						  return 'label label-danger label-important';
					 case 'Australia':
						  return 'label label-success';
					 case 'Africa':
						  return 'label label-default';
					 case 'Asia':
						  return 'label label-warning';
				}
		  },
		  itemValue: 'value',
		  itemText: 'text'
		});
	}

	if($('.state-tagsinput-add').length > 0){
		$('.state-tagsinput-add').on('click', function(){
			elt.tagsinput('add', { 
				"value": $('.state-tagsinput-value').val(), 
				"text": $('.state-tagsinput-city').val(), 
				"continent": $('.state-tagsinput-continent').val()    
			});
		});
    
		elt.tagsinput('add', {
		  "value": 1,
		  "text": "Amsterdam",
		  "continent": "Europe"
		});
		elt.tagsinput('add', {
		  "value": 4,
		  "text": "Washington",
		  "continent": "America"
		});
		elt.tagsinput('add', {
		  "value": 7,
		  "text": "Sydney",
		  "continent": "Australia"
		});
		elt.tagsinput('add', {
		  "value": 10,
		  "text": "Beijing",
		  "continent": "Asia"
		});
		elt.tagsinput('add', {
		  "value": 13,
		  "text": "Cairo",
		  "continent": "Africa"
		});
	}
	
	/*------ code to apply bootstrap switches -----*/
	if($('.pratham-switch').length > 0){
		$('.pratham-switch').bootstrapSwitch();
	}
})(jQuery);

(function($) {
	if($('.selectize-tagging').length > 0){
		$('.selectize-tagging').selectize({
			 delimiter: ',',
			 persist: false,
			 create: function(input) {
				  return {
						value: input,
						text: input
				  }
			 }
		});
	}

	if($('.selectize-select').length > 0){
		//Email Demo
		var REGEX_EMAIL = '([a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@' + '(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)';
		$('.selectize-select').selectize({
			 persist: false,
			 maxItems: null,
			 valueField: 'email',
			 labelField: 'name',
			 searchField: ['name', 'email'],
			 options: [
				  {email: 'brian@thirdroute.com', name: 'Brian Reavis'},
				  {email: 'nikola@tesla.com', name: 'Nikola Tesla'},
				  {email: 'someone@gmail.com', name: 'Someone'}
			 ],
			 render: {
				  item: function(item, escape) {
						return '<div>' +
							 (item.name ? '<span class="name">' + escape(item.name) + '</span>' : '') +
							 (item.email ? '<span class="email">' + escape(item.email) + '</span>' : '') +
						'</div>';
				  },
				  option: function(item, escape) {
						var label = item.name || item.email;
						var caption = item.name ? item.email : null;
						return '<div>' +
							 '<span class="label">' + escape(label) + '</span>' +
							 (caption ? '<span class="caption">' + escape(caption) + '</span>' : '') +
						'</div>';
				  }
			 },
			 createFilter: function(input) {
				  var match, regex;
	
				  // email@address.com
				  regex = new RegExp('^' + REGEX_EMAIL + '$', 'i');
				  match = input.match(regex);
				  if (match) return !this.options.hasOwnProperty(match[0]);
	
				  // name <email@address.com>
				  regex = new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i');
				  match = input.match(regex);
				  if (match) return !this.options.hasOwnProperty(match[2]);
	
				  return false;
			 },
			 create: function(input) {
				  if ((new RegExp('^' + REGEX_EMAIL + '$', 'i')).test(input)) {
						return {email: input};
				  }
				  var match = input.match(new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i'));
				  if (match) {
						return {
							 email : match[2],
							 name  : $.trim(match[1])
						};
				  }
				  alert('Invalid email address.');
				  return false;
			 }
		});
	}

	if($('.selectize-single-select').length > 0){
		//Single Item Select
		$('.selectize-single-select').selectize({
			 create: true,
			 sortField: 'text'
		});
	}

	if($('.selectize-gear').length > 0){
		//Option Groups 
		$('.selectize-gear').selectize({
			 sortField: 'text'
		});
	}

	if($('.selectize-max-items').length > 0){
		//Max Items
		$('.selectize-max-items').selectize({
			 maxItems: 3
		});
	}
	
	if($('.selectize-country').length > 0){
		//Country Selection
		$('.selectize-country').selectize();
	}
	if($('.selectize-repo').length > 0){
		//Remote Source
		$('.selectize-repo').selectize({
			 valueField: 'url',
			 labelField: 'name',
			 searchField: 'name',
			 create: false,
			 render: {
				  option: function(item, escape) {
						return '<div>' +
							 '<span class="title">' +
								  '<span class="name"><i class="icon ' + (item.fork ? 'fork' : 'source') + '"></i>' + escape(item.name) + '</span>' +
								  '<span class="by">' + escape(item.username) + '</span>' +
							 '</span>' +
							 '<span class="description">' + escape(item.description) + '</span>' +
							 '<ul class="meta">' +
								  (item.language ? '<li class="language">' + escape(item.language) + '</li>' : '') +
								  '<li class="watchers"><span>' + escape(item.watchers) + '</span> watchers</li>' +
								  '<li class="forks"><span>' + escape(item.forks) + '</span> forks</li>' +
							 '</ul>' +
						'</div>';
				  }
			 },
			 score: function(search) {
				  var score = this.getScoreFunction(search);
				  return function(item) {
						return score(item) * (1 + Math.min(item.watchers / 100, 1));
				  };
			 },
			 load: function(query, callback) {
				  if (!query.length) return callback();
				  $.ajax({
						url: 'https://api.github.com/legacy/repos/search/' + encodeURIComponent(query),
						type: 'GET',
						error: function() {
							 callback();
						},
						success: function(res) {
							 callback(res.repositories.slice(0, 10));
						}
				  });
			 }
		});
	}

	if($('.selectize-backspace').length > 0){
		//Restore on Backspace
		$('.selectize-backspace').selectize({
			 plugins: ['restore_on_backspace'],
			 delimiter: ',',
			 persist: false,
			 create: function(input) {
				  return {
						value: input,
						text: input
				  }
			 }
		});
	}
	
	if($('.selectize-remove-btn').length > 0){
		//Remove Button
		$('.selectize-remove-btn').selectize({
			 plugins: ['remove_button'],
			 delimiter: ',',
			 persist: false,
			 create: function(input) {
				  return {
						value: input,
						text: input
				  }
			 }
		});
	}

	if($('.selectize-draggable').length > 0){
		//Drag Drop
		$('.selectize-draggable').selectize({
			 plugins: ['drag_drop'],
			 delimiter: ',',
			 persist: false,
			 create: function(input) {
				  return {
						value: input,
						text: input
				  }
			 }
		});
	}

	if($(".selectize-optgroup").length > 0 ){
		//Optgroup Columns
		$(".selectize-optgroup").selectize({
			 options: [
				  {id: 'avenger', make: 'dodge', model: 'Avenger'},
				  {id: 'caliber', make: 'dodge', model: 'Caliber'},
				  {id: 'caravan-grand-passenger', make: 'dodge', model: 'Caravan Grand Passenger'},
				  {id: 'challenger', make: 'dodge', model: 'Challenger'},
				  {id: 'ram-1500', make: 'dodge', model: 'Ram 1500'},
				  {id: 'viper', make: 'dodge', model: 'Viper'},
				  {id: 'a3', make: 'audi', model: 'A3'},
				  {id: 'a6', make: 'audi', model: 'A6'},
				  {id: 'r8', make: 'audi', model: 'R8'},
				  {id: 'rs-4', make: 'audi', model: 'RS 4'},
				  {id: 's4', make: 'audi', model: 'S4'},
				  {id: 's8', make: 'audi', model: 'S8'},
				  {id: 'tt', make: 'audi', model: 'TT'},
				  {id: 'avalanche', make: 'chevrolet', model: 'Avalanche'},
				  {id: 'aveo', make: 'chevrolet', model: 'Aveo'},
				  {id: 'cobalt', make: 'chevrolet', model: 'Cobalt'},
				  {id: 'silverado', make: 'chevrolet', model: 'Silverado'},
				  {id: 'suburban', make: 'chevrolet', model: 'Suburban'},
				  {id: 'tahoe', make: 'chevrolet', model: 'Tahoe'},
				  {id: 'trail-blazer', make: 'chevrolet', model: 'TrailBlazer'},
			 ],
			 optgroups: [
				  {id: 'dodge', name: 'Dodge'},
				  {id: 'audi', name: 'Audi'},
				  {id: 'chevrolet', name: 'Chevrolet'}
			 ],
			 labelField: 'model',
			 valueField: 'id',
			 optgroupField: 'make',
			 optgroupLabelField: 'name',
			 optgroupValueField: 'id',
			 optgroupOrder: ['chevrolet', 'dodge', 'audi'],
			 searchField: ['model'],
			 plugins: ['optgroup_columns']
		});
	}
})(jQuery);

$(function(){
	if($(".fancytree-default").length > 0 ){
		// Using default options
		$(".fancytree-default").fancytree({source: SOURCE});
	}
	if($(".fancytree-checkbox").length > 0){
		//Using Checkbox
		$(".fancytree-checkbox").fancytree({checkbox:true,selectMode: 3,source:SOURCE});
	}
});

var SOURCE = [
    {title:'Root with some children (expanded on init)',folder: true, key: 'id0', expanded: true,
        children: [
            {title: "Sub-item 0.1",
                children: [
                    {title: "Sub-item 0.1.1", key: "id0.1.1" },
                    {title: "Sub-item 0.1.2", key: "id0.1.2" }
                ]
            },
            {title: "Sub-item 0.2",
                children: [
                    {title: "Sub-item 0.2.1", key: "id0.2.1" },
                    {title: "Sub-item 0.2.2", key: "id0.2.2" }
                ]
            }
        ]
    },
    {title: "item1"},
    {title: "item2"},
    {title: "Folder", folder: true, key: "id3",
        children: [
            {title: "Sub-item 3.1",
                children: [
                    {title: "Sub-item 3.1.1", key: "id3.1.1" },
                    {title: "Sub-item 3.1.2", key: "id3.1.2" }
                ]
            },
            {title: "Sub-item 3.2",
                children: [
                    {title: "Sub-item 3.2.1", key: "id3.2.1" },
                    {title: "Sub-item 3.2.2", key: "id3.2.2" }
                ]
            }
        ]
    },
    {title: "Document with some children (expanded on init)", key: "id4", expanded: true,
        children: [
            {title: "Sub-item 4.1",
                children: [
                    {title: "Sub-item 4.1.1", key: "id4.1.1" },
                    {title: "Sub-item 4.1.2", key: "id4.1.2" }
                ]
            },
            {title: "Sub-item 4.2 (selected on init)", selected: true,
                children: [
                    {title: "Sub-item 4.2.1", key: "id4.2.1" },
                    {title: "Sub-item 4.2.2", key: "id4.2.2" }
                ]
            },
            {title: "Sub-item 4.3", key: "id4.3"},
            {title: "Sub-item 4.4", key: "id4.4"}
        ]
    },
    {title: "Other folder", folder: true, key: "id5",
        children: [
            {title: "Sub-item 5.1",
                children: [
                    {title: "Sub-item 5.1.1", key: "id5.1.1" },
                    {title: "Sub-item 5.1.2", key: "id5.1.2" }
                ]
            }
        ]
    }
];

/*---- code to integrate dargula ------*/
(function($) {
   if($('#dragula-lft-defaults').length > 0){
		// Default Demo
		dragula([document.getElementById('dragula-lft-defaults'), document.getElementById('dragula-rgt-defaults')]);
	}

	if($('#dragula-rgt-event').length > 0){
		//Event Demo
		dragula([document.getElementById('dragula-lft-event'), document.getElementById('dragula-rgt-event')])
			.on('drag', function (el) {
			  el.className = el.className.replace('ex-moved', '');
			}).on('drop', function (el) {
			  el.className += ' ex-moved';
			}).on('over', function (el, container) {
			  container.className += ' ex-over';
			}).on('out', function (el, container) {
			  container.className = container.className.replace('ex-over', '');
			});
	}

	if($('#dragula-lft-remove').length > 0){
		//Remove when out of container
		dragula([document.getElementById('dragula-lft-remove'), document.getElementById('dragula-rgt-remove')], {
			removeOnSpill: true
		});
	}

	if($('#dragula-lft-rollback').length > 0){
		//Rollback
		dragula([document.getElementById('dragula-lft-rollback'), document.getElementById('dragula-rgt-rollback')], {
			revertOnSpill: true
		});
	}

	if($('#dragula-lft-copy-1tomany').length > 0){		
		//Copy 1tomany
		dragula([document.getElementById('dragula-lft-copy-1tomany'), document.getElementById('dragula-rgt-copy-1tomany')], {
			copy: function (el, source) {
			  return source === document.getElementById('dragula-lft-copy-1tomany')
			},
			accepts: function (el, target) {
			  return target !== document.getElementById('dragula-lft-copy-1tomany')
			}
		});
	}

	if($('#dragula-lft-handle').length > 0){
		//Handle Demo
		dragula([document.getElementById('dragula-lft-handle'), document.getElementById('dragula-rgt-handle')], {
			moves: function (el, container, handle) {
			  return handle.className === 'handle';
			}
		});
	}
	if($('#dragula-sortable').length > 0){
		 //Click or Drag
		 dragula([document.getElementById('dragula-sortable')]);
	}
})(jQuery);


/* ------------------------------------------------------------------------------
*
*  # Plupload multiple file uploader
*
* ---------------------------------------------------------------------------- */
(function($) {
	if($(".queue-widget-upload").length > 0){
    	// Setup all runtimes
		$(".queue-widget-upload").pluploadQueue({
		  // General settings
		  runtimes : 'html5,flash,silverlight,html4',
		  url : "/",
		  chunk_size : '1mb',
		  rename : true,
		  dragdrop: true,
		  filters : {
				// Maximum file size
				max_file_size : '10mb',
				// Specify what files to browse for
				mime_types: [
					 {title : "Image files", extensions : "jpg,gif,png"},
					 {title : "Zip files", extensions : "zip"}
				]
		  },
		  // Resize images on clientside if we can
		  resize: {
				width : 200,
				height : 200,
				quality : 90,
				crop: true // crop to exact dimensions
		  },
		  // Flash settings
		  flash_swf_url : 'assets/plugins/plupload/js/Moxie.swf',
		  // Silverlight settings
		  silverlight_xap_url : 'assets/plugins/plupload/js/Moxie.xap'
		});
	}
})(jQuery);

$(function(){
    /**
     * GENERATES THE ICON CODE
     */
	 if($('#glyphs').length > 0){
		 document.getElementById("glyphs").addEventListener("click", function(e) {
			  var target = e.target,
					glyph  = target.getAttribute("data-js-prompt");
			  if (target.tagName === "SPAN") {
					prompt('Please copy the icon code below:', 'data-icon="' +glyph+ '"');
			  }
		 });
	 }
	 
           
	if($('#snazzymap-demo').length > 0){
		// When the window has finished loading create our google map below
		google.maps.event.addDomListener(window, 'load', init);
         var init =	function init() {
			 // Basic options for a simple Google Map
			 var mapOptions = {
				  zoom: 11,
				  center: new google.maps.LatLng(40.6700, -73.9400), // New York
				  // How you would like to style the map. 
				  // This is where you would paste any style found on Snazzy Maps.
				  styles: [{"featureType": "landscape", "stylers": [{"hue": "#FFA800"}, {"saturation": 0}, {"lightness": 0}, {"gamma": 1}]}, {"featureType": "road.highway", "stylers": [{"hue": "#53FF00"}, {"saturation": -73}, {"lightness": 40}, {"gamma": 1}]}, {"featureType": "road.arterial", "stylers": [{"hue": "#FBFF00"}, {"saturation": 0}, {"lightness": 0}, {"gamma": 1}]}, {"featureType": "road.local", "stylers": [{"hue": "#00FFFD"}, {"saturation": 0}, {"lightness": 30}, {"gamma": 1}]}, {"featureType": "water", "stylers": [{"hue": "#00BFFF"}, {"saturation": 6}, {"lightness": 8}, {"gamma": 1}]}, {"featureType": "poi", "stylers": [{"hue": "#679714"}, {"saturation": 33.4}, {"lightness": -25.4}, {"gamma": 1}]}]
			 };
		
			 var mapElement = document.getElementById('snazzymap-demo');
			 // Create the Google Map using our element and options defined above
			 var map = new google.maps.Map(mapElement, mapOptions);
			 // Let's also add a marker while we're at it
			 var marker = new google.maps.Marker({
				  position: new google.maps.LatLng(40.6700, -73.9400),
				  map: map,
				  title: 'Snazzy!'
			 });
		}
	}
});