'use strict';
(function($) {
      /**
        * Function to load area morris chart
      **/
      if ($(".ecomm-product-detail-morris")[0]){
        Morris.Area({
              element: $('.ecomm-product-detail-morris'),
              behaveLikeLine: true,
              lineColors: ["#5e6db3"],
              data: [
                { y: '2009', a: 0,},
                { y: '2010', a: 50,},
                { y: '2011', a: 100,},
                { y: '2012', a: 80,},
                { y: '2013', a: 170,},
                { y: '2014', a: 190,},
                { y: '2015', a: 160,}
              ],
              xkey: 'y',
              ykeys: ['a'],
              labels: ['Product A'],
              resize: true,
            }).on('click', function(i, row){
              console.log(i, row);
            });
      }

      /**
        * Function to load pie highchart
      **/
      if($(".ecomm-order-pie")[0]){
        $('.ecomm-order-pie').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: ''
            },

            tooltip: {
                pointFormat: '{point.percentage:.1f}%'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Brands',
                colorByPoint: true,
                data: [{
                    name: 'Closed',
                    y: 56.33,
                    color: "#5e6db3"
                }, {
                    name: 'On Hold',
                    y: 24.03,
                    color: "#fd7b6c"
                }, {
                    name: 'Cancelled',
                    y: 10.38,
                    color: "#d24636"
                }]
            }]
        });
    }

    /**
      * Function to load  bar morris chart
    **/
    if($(".ecomm-order-bar")[0]){
      Morris.Bar({
        element: $('.ecomm-order-bar'),
        data: [
          {x: '2011 Q1', y: 3, z: 2, a: 3},
          {x: '2011 Q2', y: 2, z: null, a: 1},
          {x: '2011 Q3', y: 0, z: 2, a: 4},
          {x: '2011 Q4', y: 2, z: 4, a: 3}
        ],
        xkey: 'x',
        ykeys: ['y', 'z', 'a'],
        barColors: ['#5e6db3','#fd7b6c','#00ca95'],
        labels: ['Y', 'Z', 'A']
      }).on('click', function(i, row){
        console.log(i, row);
      });
  }

})(jQuery);