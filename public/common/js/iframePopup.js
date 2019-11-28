$(document).ready(function(){
   
    //on frontend
    //open my account on profile tab
     $("#editLead").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#editLead iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        

        
        $("#noteFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#noteFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
 
    $("#pickLead").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#pickLead iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
    

        $("#appStatusFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#appStatusFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
        $("#assignCaseFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#assignCaseFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });        

        $("#addCaseNote").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#addCaseNote iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        }); 
          $("#addAnchorFrm").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#addAnchorFrm iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
          $("#uploadAnchLead").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#uploadAnchLead iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
        $("#editAnchorFrm").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#editAnchorFrm iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
    
     $("#sendNextstage").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#sendNextstage iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        }); 
    
      $("#addRoleFrm").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#addRoleFrm iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
         $("#manageUserRole").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#manageUserRole iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
    $("#addmanageUserRole").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#addmanageUserRole iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
        
    
});