
/* global messages, message */

try {
    jQuery(document).ready(function ($) {
        /*
        $("input[type='checkbox']").change(function () {
            $(this).siblings('ul')
                    .find("input[type='checkbox']")
                    .prop('checked', this.checked);
            $('#myerr').css('display','none');
        });
        */
       
        $('#checkAll').click(function () {
            $("input[type='checkbox']")
                    .prop('checked', true);
            $('#myerr').css('display','none');
        })
        $('#uncheckAll').click(function () {
            $("input[type='checkbox']")
                    .prop('checked', false);
        })                                        

        $("form").submit(function () {
            if ($('input:checkbox').filter(':checked').length < 1) {
                $('#myerr').css('display','block');
                return false;
            }
        });          
            
        /*  
        $('.nested input[type=checkbox]').click(function () {
            $(this).parent().find('li input[type=checkbox]').prop('checked', $(this).is(':checked'));
            var sibs = false;
            $(this).closest('ul').children('li').each(function () {
               if($('input[type=checkbox]', this).is(':checked')) sibs=true;
            })
            $(this).parents('ul').prev().prop('checked', sibs);
        });
        */
       
        /*
        $.fn.linkNestedCheckboxes = function () {
            var childCheckboxes = $(this).find('input[type=checkbox] ~ ul li input[type=checkbox]');

            childCheckboxes.change(function(){
                var parent = $(this).closest("ul").prevAll("input[type=checkbox]").first();
                var anyChildrenChecked = $(this).closest("ul").find("li input[type=checkbox]").is(":checked");
                $(parent).prop("checked", anyChildrenChecked);
            });

            // Parents
            childCheckboxes.closest("ul").prevAll("input[type=checkbox]").first().change(function(){
               $(this).nextAll("ul").first().find("li input[type=checkbox]")
                        .prop("checked", $(this).prop("checked"));        
            });

            return $(this);
        };
        
        $("#tree").linkNestedCheckboxes();
        */
        /*
        $('input').on('change', function() {
            var $this = $(this);
            var checkboxes = $this.parents().children('input');
            if($this.is(":checked")) {
                return checkboxes.prop("checked", true);
            }
            checkboxes.prop("checked", false);
        });
        */

        $('.treeList :checkbox').change(function (){
            $(this).siblings('ul').find(':checkbox').prop('checked', this.checked);
            if (this.checked) {
                $(this).parentsUntil('.treeList', 'ul').siblings(':checkbox').prop('checked', true);
            } else {
                $(this).parentsUntil('.treeList', 'ul').each(function(){
                    var $this = $(this);
                    var childSelected = $this.find(':checkbox:checked').length;
                    if (!childSelected) {
                        $this.prev(':checkbox').prop('checked', false);
                    }
                });
            }
        });       
    });






} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
