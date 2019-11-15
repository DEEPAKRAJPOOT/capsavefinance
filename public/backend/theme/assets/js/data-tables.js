'use strict';
(function($) {
	/**
	  * Function written to load data table buttons based on data attributes.
	**/ 
	$('[data-table="table-button"]').each(function(){
		var buttons = $(this).data('buttons');
		$(this).DataTable( {  
			dom: 'Bfrtip',
	        "buttons": buttons,
	    });
	});

	/**
	  * Function written to load data table autofill based on data attributes.
	**/ 
	$('[data-table="table-autofill"]').each(function(){
		var lengthMenu = [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]] ;
		var autofill = ($(this).data('autofill'))?($(this).data('autofill')):'true';
		var ScrollX = $(this).data('scrollX');
		var ScrollY = $(this).data('scrollY');
		var ScrollCollapse = $(this).data('scrollCollapse');
		var paging = ($(this).data('paging'))? ($(this).data('paging')): 'true';
		$(this).DataTable( {
	        "autoFill": autofill,
	        "lengthMenu": lengthMenu,  
	        "scrollY": ScrollY,
	        "scrollX": ScrollX,
	        "scrollCollapse": ScrollCollapse,
	        "paging":paging,
			  responsive: true
	    });
	});

	/**
	  * Function written to load data table fixed column based on data attributes.
	**/ 
	$('[data-table="table-fixed-column"]').DataTable( { 
	        scrollY: 300,
	        scrollX: true,
	        scrollCollapse: true,
	        paging: false,
	        fixedColumns: true,
			  responsive: true
	    });


	/**
	  * Function written to load data table col reorder based on data attributes.
	**/ 
	$('[data-table="table-col-reorder"]').each(function(){
		var lengthMenu = [[5, 15, 20, -1],[5, 15, 20, "All"]] ;
		var colReorder = ($(this).data('colReorder'))?($(this).data('colReorder')):'true';
		var ScrollY = $(this).data('scrollY');
		var paging = ($(this).data('paging'))? ($(this).data('paging')): 'false';
		var temp = $(this);
		var t = $(this).DataTable( {
	        "colReorder": colReorder,
	        "lengthMenu": lengthMenu,  
	        "scrollY": ScrollY,
	        "paging":paging,
			  responsive: true
	    });
	    $('.reset-colreorder').on('click',function (e) {
	        e.preventDefault();    
	        t.colReorder.reset();
	    });
	});

	/**
	  * Function written to load data table row reorder based on data attributes.
	**/
	$('[data-table="table-row-reorder"]').each(function(){
		var lengthMenu = [[5, 15, 20, -1],[5, 15, 20, "All"]] ;
		var rowReorder = ($(this).data('rowReorder'))?($(this).data('rowReorder')):'true';
		var ScrollY = $(this).data('scrollY');
		var paging = ($(this).data('paging'))? ($(this).data('paging')): 'false';
		var temp = $(this);
		var t = $(this).DataTable( {
					  "rowReorder": rowReorder,
					  "lengthMenu": lengthMenu,  
					  "scrollY": ScrollY,
					  "paging":paging,
					  responsive: true
				 });
		$('.reset-rowreorder').on('click',function (e) {
		  e.preventDefault();    
		  t.rowReorder.reset();
		});
	});

	$('[data-filter="true"] tfoot th').each( function () {
		var title = $('[data-filter="true"] thead th').eq( $(this).index() ).text();
		$(this).html( '<input type="text" placeholder="Search '+title+'" />' );
	});

    // Apply the filter
    $("[data-filter='true'] tfoot input").on( 'keyup change', function () {
        $("[data-filter='true']").dataTable().api().column( $(this).parent().index()+':visible' ).search( this.value ).draw();
    });
	$('.dataTables_length select').addClass('selectbox');
	$('.dataTables_filter input').addClass('form-control data-search');
	$('.dataTables_paginate').addClass('paginate-data');
	$('.dataTables_scroll').addClass('mrgn-b-lg');
	$('.dataTables_scrollFootInner input').addClass('form-control');
	 
})(jQuery);