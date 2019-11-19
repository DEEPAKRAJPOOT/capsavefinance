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
    


    
    
});