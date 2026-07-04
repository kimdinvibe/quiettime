/* DATERANGEPICKER */

function init_daterangepicker_get_ranges() {
    return {
        // 'Today': [moment(), moment()],
        // 'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        // 'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        // 'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        // 'This Month': [moment().startOf('month'), moment().endOf('month')],
        // 'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        '今日': [moment(), moment()],
        '昨日': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        '過去7日間': [moment().subtract(6, 'days'), moment()],
        '過去30日間': [moment().subtract(29, 'days'), moment()],
        '今月': [moment().startOf('month'), moment().endOf('month')],
        '先月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    };
}

function init_daterangepicker_get_locale() {
    return {
    //     applyLabel: 'Submit',
    //     cancelLabel: 'Clear',
    //     fromLabel: 'From',
    //     toLabel: 'To',
    //     customRangeLabel: 'Custom',
    //     daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
    //     monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    //     firstDay: 1

        'applyLabel': '提出する',
        'cancelLabel': 'クリア',
        'fromLabel': 'から',
        'toLabel': 'に',
        'customRangeLabel': 'カスタム',
        //daysOfWeek: ['日曜日', '月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日'],
        'daysOfWeek': ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
        'monthNames': ['一月', '二月', '行進', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
        'firstDay': 1
    }
}

function init_daterangepicker(selector, onSelect) {
    if( typeof ($.fn.daterangepicker) === 'undefined'){ return; }
    console.log('init_daterangepicker');

    var objectDate = $(selector);

    var cb = function(start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
        // $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('span', objectDate).html(start.format('YYYY.MM.DD') + ' - ' + end.format('YYYY.MM.DD'));
    };

    var optionSet1 = {
        startDate: moment().startOf('month'),
        endDate: moment().endOf('month'),
        minDate: '2017.12.01',
        maxDate: '2019.12.01',
        dateLimit: {
            days: 90
        },
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: init_daterangepicker_get_ranges(),
        opens: 'right',
        buttonClasses: ['btn btn-default'],
        applyClass: 'btn-small btn-primary',
        cancelClass: 'btn-small',
        format: 'YYYY.MM.DD',
        separator: ' to ',
        language: 'ja',
        locale: init_daterangepicker_get_locale()
    };

    $('span', objectDate).html(moment().startOf('month').format('YYYY.MM.DD') + ' - ' + moment().endOf('month').format('YYYY.MM.DD'));
    objectDate.daterangepicker(optionSet1, cb);
    objectDate.on('show.daterangepicker', function() {
        console.log("show event fired");
    });
    objectDate.on('hide.daterangepicker', function() {
        console.log("hide event fired");
    });
    objectDate.on('apply.daterangepicker', function(ev, picker) {
        console.log("apply event fired, start/end dates are " + picker.startDate.format('YYYY.MM.DD') + " to " + picker.endDate.format('YYYY.MM.DD'));
        onSelect(picker)
    });
    objectDate.on('cancel.daterangepicker', function(ev, picker) {
        console.log("cancel event fired");
    });
    $('#options1').click(function() {
        objectDate.data('daterangepicker').setOptions(optionSet1, cb);
    });
    $('#options2').click(function() {
        objectDate.data('daterangepicker').setOptions(optionSet2, cb);
    });
    $('#destroy').click(function() {
        objectDate.data('daterangepicker').remove();
    });

}

function init_daterangepicker_one_line(object){
    var model = object.attr('model');
    var id = object.attr('obj');
    var itemtitle = object.attr('itemtitle');

    init_daterangepicker(object, function(picker){
        $('.title-date', object.parent().parent().parent()).html(picker.startDate.format('YYYY.MM.DD') + " - " + picker.endDate.format('YYYY.MM.DD'));

        $.ajax({
            type: 'GET',
            url: '/panel/site/refresh-statistics-one-line',
            data: {
                from: picker.startDate.format('YYYY.MM.DD'),
                to: picker.endDate.format('YYYY.MM.DD'),
                model: model,
                itemtitle: itemtitle
            },
            success: function(data)
            {
                if(data && data.statistics)
                {
                    for(index in data.statistics) {
                        if(jQuery.graph && jQuery.graph[id]){
                            jQuery.graph[id].setData([]);
                        } else {
                            jQuery.graph[id] = new Morris.Area({
                                element: 'graph-'+id,
                                resize: true,
                                data: [],
                                xkey: 'y',
                                ykeys: '',
                                labels: '',
                                lineColors: '',
                                hideHover: 'auto'
                            });
                        }

                        if(jQuery.graph[id]) {
                            if(data.statistics.morris && data.statistics.title && data.statistics.color) {
                                jQuery.graph[id].options.ykeys = [data.statistics.title];
                                jQuery.graph[id].options.labels = [data.statistics.title];
                                jQuery.graph[id].options.lineColors = [data.statistics.color];
                                jQuery.graph[id].setData(data.statistics.morris);
                            }
                        }
                    }
                }
            },
            dataType: 'json'
        });
    });

}

$(document).ready(function() {
    moment.locale('ja');

    // main graphic
    init_daterangepicker('#reportrange', function(picker){
        $('.main-graph .title-date').html(picker.startDate.format('YYYY.MM.DD') + " - " + picker.endDate.format('YYYY.MM.DD'));

        $.ajax({
            type: 'GET',
            url: '/panel/site/refresh-statistics',
            data: {
                from: picker.startDate.format('YYYY.MM.DD'),
                to: picker.endDate.format('YYYY.MM.DD')
            },
            success: function(data)
            {
                $('.box-footer-info').css('display', 'none');

                if(data && data.statistics)
                {
                    for(index in data.statistics) {
                        if($('.location-block[obj='+index+']').length) {
                            if(jQuery.chats && jQuery.chats[index]){
                                jQuery.chats[index].setData([]);
                            } else {
                                jQuery.chats[index] = new Morris.Area({
                                    element: 'locchat-'+index,
                                    resize: true,
                                    data: [],
                                    xkey: 'y',
                                    ykeys: '',
                                    labels: '',
                                    lineColors: '',
                                    hideHover: 'auto'
                                });
                            }

                            if(data.statistics[index] && data.statistics[index].graph && jQuery.chats[index]) {
                                if(data.statistics[index].graph.morris && data.statistics[index].graph.title && data.statistics[index].graph.color) {
                                    var titles = [],
                                        colors = [];

                                    for(key in data.statistics[index].graph.title){
                                        titles[titles.length] = data.statistics[index].graph.title[key];
                                    }

                                    for(key in data.statistics[index].graph.color){
                                        colors[colors.length] = data.statistics[index].graph.color[key];
                                    }

                                    jQuery.chats[index].options.ykeys = titles;
                                    jQuery.chats[index].options.labels = titles;
                                    jQuery.chats[index].options.lineColors = colors;
                                    jQuery.chats[index].setData(data.statistics[index].graph.morris);
                                }
                            }

                            $('.location-block[obj='+index+'] .progress-group-body').html('');

                            if(data.statistics[index]['eventView']) {
                                $('.location-block[obj='+index+'] .progress-group-body').html(data.statistics[index]['eventView']);
                            }
                        }
                    }
                }
            },
            dataType: 'json'
        });
    });

    $('.reportrange').each(function(){
        init_daterangepicker_one_line($(this));
    })

});