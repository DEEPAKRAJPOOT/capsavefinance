/* global messages */

$(document).ready(function () {

    $(".recommended").mouseover(function () {

        $(".recommended").addClass("other-box");
        $(this).addClass("current");
    });
    $(".recommended").mouseout(function () {

        $(".recommended").removeClass("other-box");
        $(this).removeClass("current");
    });


});
/**
 * 
 * @param {integer} right_id
 * @param {integer} right_user_id
 * @returns {response}
 */
function doLike(right_id, right_user_id, that = null) {

    $.ajax({
        url: save_right_likes,
        method: "post",
        data: {_token: token, right_id: right_id, right_user_id: right_user_id},
        dataType: "JSON",
        success: function (data) {
            if (data > 0) {
                if (that != null) {
                    $('#' + that.children().eq(1).attr('id')).html(data);
                    $('#' + that.children().eq(1).attr('id')).prev().addClass('active');
                } else {
                    $('#' + right_id).html(data);
                    $('#' + right_id).prev().addClass('active');
                }

            } else {

                if (that != null) {
                    $('#' + that.children().eq(1).attr('id')).html(data);
                    $('#' + that.children().eq(1).attr('id')).prev().removeClass('active');
                } else {
                    $('#' + right_id).html(data);
                    $('#' + right_id).prev().removeClass('active');
                }
            }
        }
    });
}

(function (i, s, o, g, r, a, m) {
    i['GoogleAnalyticsObject'] = r;
    i[r] = i[r] || function () {
        (i[r].q = i[r].q || []).push(arguments)
    }, i[r].l = 1 * new Date();
    a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
    a.async = 1;
    a.src = g;
    m.parentNode.insertBefore(a, m)
})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
ga('create', 'UA-37452180-6', 'github.io');
ga('send', 'pageview');


(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id))
        return;
    js = d.createElement(s);
    js.id = id;
//    js.src = "http://connect.facebook.net/en_GB/all.js#xfbml=1";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


!function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (!d.getElementById(id)) {
        js = d.createElement(s);
        js.id = id;
//        js.src = "http://platform.twitter.com/widgets.js";
        fjs.parentNode.insertBefore(js, fjs);
    }
}(document, "script", "twitter-wjs");

/**
 * 
 * Datepicker
 */

$(function () {
    $(".datepicker").datepicker();
    $("#format").on("change", function () {
        $(".datepicker").datepicker("option", "dateFormat", $(this).val());
    });
});


$(document).ready(function() {
	var s = $(".search-top");
	var pos = s.position();					   
	$(window).scroll(function() {
		var windowpos = $(window).scrollTop();
		if (windowpos >= pos.top) {
			s.addClass("stick");
		} else {
			s.removeClass("stick");	
		}
	});
});