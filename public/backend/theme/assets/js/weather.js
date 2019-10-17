'use strict';
(function($) {
  var appid="b1b15e88fa797225412429c1c50c122a1";
  var apikey="69b72ed255ce5efad910bd946685883a";
  var city="Mohali";
  $.getJSON("http://api.openweathermap.org/data/2.5/forecast/daily?q="+city+"&cnt=6&units=metric&mode=json&appid="+appid+"&apikey="+apikey,function(result){
       $("#city").append(result.city.name);
       var today = getDateFormat(result.list[0].dt)
       $("#date").append(today);
       $("#current_temp").append(result.list[0].temp.day +' '+result.list[0].weather[0].description );
       $("#curr-temp-only").append(result.list[0].temp.day);
       $('#min-temp').append(result.list[0].temp.min );
       $('#max-temp').append(result.list[0].temp.max);
       $('#morn-temp').append(result.list[0].temp.morn +' '+result.list[0].weather[0].description);
       $('#eve-temp').append(result.list[0].temp.eve +' '+result.list[0].weather[0].description);
       $('#night-temp').append(result.list[0].temp.night +' '+result.list[0].weather[0].description);
       $('#today-temp').append(result.list[0].weather[0].description);
       $('#today-temp-main').append(result.list[0].weather[0].main);

       //week days weather
       $("#second-day-temp").append(result.list[1].temp.day);
       $('#second-day-main').append(result.list[1].weather[0].main);
       var second_day= getDayName(result.list[1].dt);
       $('#second-day-name').append(second_day);

       $("#third-day-temp").append(result.list[2].temp.day);
       $('#third-day-main').append(result.list[2].weather[0].main);
       var third_day= getDayName(result.list[2].dt);
       $('#third-day-name').append(third_day);

       $("#fourth-day-temp").append(result.list[3].temp.day);
       $('#fourth-day-main').append(result.list[3].weather[0].main);
       var fourth_day= getDayName(result.list[3].dt);
       $('#fourth-day-name').append(fourth_day);

       $("#fifth-day-temp").append(result.list[4].temp.day);
       $('#fifth-day-main').append(result.list[4].weather[0].main);
       var fifth_day= getDayName(result.list[4].dt);
       $('#fifth-day-name').append(fifth_day);


       var weather_icon=result.list[0].weather[0].id;
      if((weather_icon >= 200) && (weather_icon <= 232))
       {
          var icon_name='sleet';
       }else if((weather_icon >= 300) && (weather_icon <= 321))
       {
         var icon_name='rain';
       }else if((weather_icon >= 500) && (weather_icon <= 531))
       {
         var icon_name='rain';
       }else if((weather_icon >= 600) && (weather_icon <= 622))
       {
         var icon_name='snow';
       }else if((weather_icon >= 701) && (weather_icon <= 781))
       {
         var icon_name='fog';
       }else if(weather_icon === 800)
       {
         var icon_name='clear-day';
       }else if((weather_icon >= 801) && (weather_icon <= 804))
       {
         var icon_name='cloudy';
       }else if((weather_icon >= 900) && (weather_icon <= 906))
       {
         var icon_name='wind';
       }

       $(".weather-icon").addClass(icon_name);
       var icon_color=$(".weather-icon").data('color');
       if(icon_color){
          icon_color=icon_color;
       }else{
          icon_color='#ffffff';
       }
       prtm_icons.SkyCons(icon_color);
    });
})(jQuery);
function getDateFormat(date) {
  var d = new Date(1000*date.toString()),
      month = '' + (d.getMonth()+1),
      day = '' + d.getDate(),
      year = d.getFullYear();

      switch(month){
        case ('1'):
          month = 'Jan'; break;
        case ('2'):
          month = 'Feb'; break;
        case ('3'):
          month = 'March'; break;
        case ('4'):
          month = 'April'; break;
        case ('5'):
          month = 'May'; break;
        case ('6'):
          month = 'June'; break;
        case ('7'):
          month = 'July'; break;
        case ('8'):
          month = 'Aug'; break;
        case ('9'):
          month = 'Sep'; break;
        case ('10'):
          month = 'Oct'; break;
        case ('11'):
          month = 'Nov'; break;
        default:
          month = 'Dec';
      }
  return [day, month, year].join('-');
}

function getDayName(date) {
  var d = new Date(1000*date.toString()),
  n = d.getDay();
  switch(n){
    case (0):
      var day_name = 'Sun'; break;
    case (1):
      var day_name = 'Mon'; break;
    case (2):
      var day_name = 'Tue'; break;
    case (3):
      var day_name = 'Wed'; break;
    case (4):
      var day_name = 'Thurs'; break;
    case (5):
      var day_name = 'Fri'; break;
    case (6):
      var day_name = 'Sat'; break;
    default:
      var day_name = 'Mon';
  }
  //alert(n);
return (day_name);
}