@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin_customer_links',['active'=>'userLocation'])


<div class="content-wrapper">
				
				

               
               
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage User InVoice Location</h3>
            <small>Manage User InVoice Location</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage User Invoice</li>
                <li class="active">Manage User InVoice Location</li>
            </ol>
        </div>
    </section>
    <div class="row grid-margin mt-3">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    
                    <div class="row mb-2">
                        
                     <ul class="company-address">
                          <li>

                        <div class="typesort-c">
                            <label class="float-left">Customer Primary Location   </label>
                          <div class="fancy-select"><select id="typesort" class="fancified" style="width: 1px; height: 1px; display: block; position: absolute; top: 0px; left: 0px; opacity: 0;">
                            <option>Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</option>
                            <option>Plot No-51, 411019</option>
                          </select><div class="trigger">Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</div><ul class="options"><li data-value="Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019" class="selected">Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</li><li data-value="Plot No-51, 411019">Plot No-51, 411019</li></ul></div>
                            </div>
                            </li>
                        <li>              
                        <div class="timesort-c">
                            <label class="float-left">Select Capsave Location</label>
                           <div class="fancy-select"><select id="timesort" class="fancified" style="width: 1px; height: 1px; display: block; position: absolute; top: 0px; left: 0px; opacity: 0;">
                             <option>Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</option>
                              <option>Plot No-51, D-2 Block,Ram Nagar Complex 411019</option>
                              <option>Plot No-51, 411019</option>
                          </select><div class="trigger">Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</div><ul class="options"><li data-value="Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019" class="selected">Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</li><li data-value="Plot No-51, D-2 Block,Ram Nagar Complex 411019">Plot No-51, D-2 Block,Ram Nagar Complex 411019</li><li data-value="Plot No-51, 411019">Plot No-51, 411019</li></ul></div>
                          </div>
                        </li>   
                        </ul>

                        
                        
                       
                    </div>
                    
                     <div class="form-group mb-0 mt-1 d-flex justify-content-end">
                        <button class="btn btn-primary" id="Submit">Submit</button>
                    </div>
                    
                    
                    <div class=" form-fields mb-4 mt-4" id="listTable" style="display:none">
                        <div class="pdf-responsive">
                            <table border="0" cellspacing="0" cellpadding="0" class="table table-bordered overview-table">
                            <thead>
                                
                                <tr>
                                
                                <th>Sr No</th>
                                <th width="34%">Customer Address</th>
                                <th width="34%">Company Address</th>
                                <th>Created by</th>
                                <th>Created at</th>
                                <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</td>
                                    <td>Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</td>
                                    <td>Customer</td>
                                    <td>23/04/2020 04:54PM	 </td>
                                    <td>Active</td>
                                    </tr>
                        
                                <tr>
                                    <td>2</td>
                                    <td>Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</td>
                                    <td>Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</td>
                                    <td>Customer</td>
                                    <td>23/04/2020 04:54PM	 </td>
                                    <td>Inactive </td>
                                    </tr>
                        
                                <tr>
                                    <td>3</td>
                                    <td>Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</td>
                                    <td>Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</td>
                                    <td>Customer</td>
                                    <td>23/04/2020 04:54PM	 </td>
                                    <td>Inactive </td>
                                    </tr>
                        
                                <tr>
                                    <td>4</td>
                                    <td>Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</td>
                                    <td>Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</td>
                                    <td>Customer</td>
                                    <td>23/04/2020 04:54PM	 </td>
                                    <td>Inactive </td>
                                    </tr>
                        
                                <tr>
                                    <td>5</td>
                                    <td>Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</td>
                                    <td>Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019</td>
                                    <td>Customer</td>
                                    <td>23/04/2020 04:54PM	 </td>
                                    <td>Inactive </td>
                                    </tr>
                        
                                
                                </tbody>
                            </table>
                        </div>
                 
           
                    </div>
                    
                    
           
                   

                </div>
            </div>
        </div>
    </div>

      </div>


@endsection
@section('jscript')
<script>
<script>
(function() {
var $;
$ = window.jQuery || window.Zepto || window.$;
$.fn.fancySelect = function(opts) {
var isiOS, settings;
settings = $.extend({
forceiOS: false
}, opts);
isiOS = !!navigator.userAgent.match(/iP(hone|od|ad)/i);
return this.each(function() {
var copyOptionsToList, disabled, options, scrollFlag, scrollTimeout, sel, trigger, updateTriggerText, wrapper;
sel = $(this);
if (sel.hasClass('fancified') || sel[0].tagName !== 'SELECT') {
return;
}
sel.addClass('fancified');
sel.css({
width: 1,
height: 1,
display: 'block',
position: 'absolute',
top: 0,
left: 0,
opacity: 0
});
sel.wrap('<div class="fancy-select">');
wrapper = sel.parent();
if (sel.data('class')) {
wrapper.addClass(sel.data('class'));
}
wrapper.append('<div class="trigger">');
if (!(isiOS && !settings.forceiOS)) {
wrapper.append('<ul class="options">');
}
trigger = wrapper.find('.trigger');
options = wrapper.find('.options');
disabled = sel.prop('disabled');
if (disabled) {
wrapper.addClass('disabled');
}
updateTriggerText = function() {
return trigger.text(sel.find(':selected').text());
};
scrollFlag = false;
scrollTimeout = false;
options.on('scroll', function() {
scrollFlag = true;
if (scrollTimeout) {
clearTimeout(scrollTimeout);
}
return scrollTimeout = setTimeout(function() {
scrollFlag = false;
return sel.focus();
}, 120);
});
sel.on('blur', function() {
if (trigger.hasClass('open')) {
return setTimeout(function() {
if (scrollFlag === false) {
return trigger.trigger('close');
}
}, 333);
}
});
trigger.on('close', function() {
trigger.removeClass('open');
return options.removeClass('open');
});
trigger.on('click', function() {
var offParent, parent;
if (!disabled) {
trigger.toggleClass('open');
if (isiOS && !settings.forceiOS) {
if (trigger.hasClass('open')) {
return sel.focus();
}
} else {
if (trigger.hasClass('open')) {
parent = trigger.parent();
offParent = parent.offsetParent();
if ((parent.offset().top + parent.outerHeight() + options.outerHeight() + 20) > $(window).height()) {
options.addClass('overflowing');
} else {
options.removeClass('overflowing');
}
} 
</script>

<script src="{{ asset('backend/js/ajax-js/lms/userInvoice.js') }}"></script>

@endsection
