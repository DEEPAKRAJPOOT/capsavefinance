$(document).ready(function(){
   
    //on frontend
    //open my account on profile tab
     $("#viewMyAccoutPopup").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#viewMyAccoutPopup iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
         //on frontend
    //open my account on profile tab
     $("#putStakePopup").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#putStakePopup iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
        
        //open valid and claim on right Detai blade
     $("#validClaimPopup").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#validClaimPopup iframe").attr(
                        {
                           
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
        
     //open valid and claim on right Detai blade
     $("#rightpurchase").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#rightpurchase iframe").attr(
                        {
                           
                            'src': url,
                            'height': height,
                            'width': width,
                        }
                );
        });
        
    $("#feedback").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#feedback iframe").attr(
                        {
                           
                            'src': url,
                            'height': height,
                            'width': width,
                        }
                );
        });
        
        
     //open report it on right Detai blade
     $("#ReportPopup").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#ReportPopup iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width,
                        }
                );
        });    
    
    
});