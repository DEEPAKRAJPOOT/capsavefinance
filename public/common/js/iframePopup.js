$(document).ready(function(){
   
    //on frontend
    //open my account on profile tab
     $("#editLead").on('show.bs.modal', function (e) {alert("asd");
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
        
    
    
});