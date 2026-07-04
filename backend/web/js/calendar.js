$(document).ready(function() {
    // https://www.jqueryscript.net/time-clock/animated-event-calendar.html
    // https://github.com/brospars/simple-calendar
    $(function() {
        var currentId = null;
        var currentMonth = null;
        var currentYear = null;

        function clearForm() {
            $('#task-video_url').val("");

            $('#task-title').val("");
            $('#task-descr').val("");

            $('#task-meditation_title_1').val("");
            $('#task-meditation_1').val("");
            $('#task-meditation_title_2').val("");
            $('#task-meditation_2').val("");

            $('#task-essay_title').val("");
            $('#task-essay').val("");

            $('#task-prayer').val("");

            $('#save-list').html("");
            $('.item.selected').removeClass("selected");

            $('[name=application_1]').val("");
            $('[name=application_2]').val("");

            $("#main-form select[name=book]").val('').change();
        }

        var container = $("#calendar-container").simpleCalendar({
            months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Снтябррь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            days: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
            // startDate:new Date(new Date().setHours(new Date().getHours() + 24)).toDateString(),

            // event dates
            // events: [
            //     // generate new event after tomorrow for one hour
            //     {
            //         startDate: new Date(new Date().setHours(new Date().getHours() + 24)).toDateString(),
            //         endDate: new Date(new Date().setHours(new Date().getHours() + 25)).toISOString(),
            //         summary: 'Visit of the Eiffel Tower'
            //     },
            //     // generate new event for yesterday at noon
            //     {
            //         startDate: new Date(new Date().setHours(new Date().getHours() - new Date().getHours() - 12, 0)).toISOString(),
            //         endDate: new Date(new Date().setHours(new Date().getHours() - new Date().getHours() - 11)).getTime(),
            //         summary: 'Restaurant'
            //     },
            //     // generate new event for the last two days
            //     {
            //         startDate: new Date(new Date().setHours(new Date().getHours() - 48)).toISOString(),
            //         endDate: new Date(new Date().setHours(new Date().getHours() - 24)).getTime(),
            //         summary: 'Visit of the Louvre'
            //     }
            // ],

            displayYear: true, // Display year in header
            fixedStartDay: true, // Week begin always by monday or by day set by number 0 = sunday, 7 = saturday, false = month always begin by first day of the month
            displayEvent: true, // Display existing event
            disableEventDetails: false, // disable showing event details
            disableEmptyDetails: false, // disable showing empty date details

            onInit: function(calendar) {}, // Callback after first initialization
            onMonthChange: function(month, year) {
                loadDatesForMonth(month + 1, year)
            }, // Callback on month change
            onDateSelect: function(date, events) {
                // console.log(date);

                var d = date.getDate();
                var m = date.getMonth() + 1; //Month from 0 to 11
                var y = date.getFullYear();

                var dateString = (m <= 9 ? '0' + m : m) + '-' + (d <= 9 ? '0' + d : d) + '-' + y;

                $('#task-date').val(dateString);
                clearForm();

                $('#main-form').toggle(true);
                $(".task-form form .btn-danger[type=submit]").toggle(false);

                currentId = null

                $.ajax({
                    type: 'GET',
                    url: '/panel/task/find',
                    data: {
                        date: dateString,
                    },
                    success: function(data) {
                        if (date && data["id"]) {
                            // console.log(data);
                            currentId = data["id"];
                            $(".task-form form .btn-danger[type=submit]").toggle(true);

                            $('#task-date').val(data["date"]);
                            $('#task-video_url').val(data["video_url"]);

                            $('#task-title').val(data["title"]);
                            $('#task-descr').val(data["descr"]);

                            $('#task-meditation_title_1').val(data["meditation_title_1"]);
                            $('#task-meditation_1').val(data["meditation_1"]);
                            $('#task-meditation_title_2').val(data["meditation_title_2"]);
                            $('#task-meditation_2').val(data["meditation_2"]);
                            
                            $('#task-essay_title').val(data["essay_title"]);
                            $('#task-essay').val(data["essay"]);

                            $('#task-prayer').val(data["prayer"]);

                            $('[name=application_1]').val(data["application_1"]);
                            $('[name=application_2]').val(data["application_2"]);

                            for (let index = 0; index < data["verses"].length; index++) {
                                const element = data["verses"][index];

                                $("#main-form #save-list").append($('<div />', {
                                    'class': 'item',
                                    'id': element["bible_id"],
                                    'book_name': element["bible"]["book_name"],
                                    'chapter': element["bible"]["chapter"],
                                }).html(element["bible"]["verse"] + ". " + element["bible"]["text"]));

                            }
                        }
                    },
                    dataType: 'json'
                });


            }, // Callback on date selection
            onEventSelect: function() {}, // Callback on event selection - use $(this).data('event') to access the event
            onEventCreate: function($el) {}, // Callback fired when an HTML event is created - see $(this).data('event')
            onDayCreate: function($el, d, m, y) {} // Callback fired when an HTML day is created
        });

        function loadDatesForMonth(month, year) {
            currentMonth = month;
            currentYear = year;

            let $calendar = container.data('plugin_simpleCalendar')
            $calendar.setEvents([])

            $.ajax({
                type: 'GET',
                url: '/panel/task/items',
                data: {
                    month: month,
                    year: year
                },
                success: function(data) {
                    let $calendar = container.data('plugin_simpleCalendar')

                    for (let index = 0; index < data.length; index++) {
                        const element = data[index];

                        var date = element.split("-")
                        date = new Date(date[2] + "-" + date[0] + "-" + date[1]);
                        // console.log(date);

                        var newEvent = {
                            startDate: date.toISOString(),
                            endDate: new Date(date.setHours(date.getHours() + 1)).getTime(),
                            summary: 'New event'
                        }

                        $calendar.addEvent(newEvent)
                    }

                },
                dataType: 'json'
            });
        }

        loadDatesForMonth((new Date()).getMonth() + 1, (new Date()).getFullYear())

        $("#main-form select[name=book]").change(function() {
            // console.log($(this).val())
            $("#main-form select[name=chapter]").html(
                $("#main-form select[name=chapter] option:nth-child(1)")
            );

            $("#main-form .items").html('');

            $.ajax({
                type: 'GET',
                url: '/panel/task/chapters',
                data: {
                    book_name: $(this).val()
                },
                success: function(data) {
                    for (index in data) {
                        $("#main-form select[name=chapter]").append($('<option />', {
                            'value': index,
                        }).html(data[index]));
                    }
                },
                dataType: 'json'
            });
        });

        $("#main-form select[name=chapter]").change(function() {
            // console.log($(this).val())
            // $("#main-form select[name=chapter]").html(
            //     $("#main-form select[name=chapter] option:nth-child(1)")
            // );

            $("#main-form .items").html('');

            $.ajax({
                type: 'GET',
                url: '/panel/task/verses',
                data: {
                    book_name: $("#main-form select[name=book]").val(),
                    chapter: $(this).val()
                },
                success: function(data) {
                    var items = []

                    $("#main-form #save-list .item").each(function() {
                        items[items.length] = {
                            "id": $(this).attr("id"),
                            "book_name": $(this).attr("book_name"),
                            "chapter": $(this).attr("chapter"),
                        }
                    });

                    // console.log(items)

                    for (index in data) {
                        var isExist = false;

                        for (item in items) {
                            if (items[item]['id'].toString() == data[index]["id"].toString()) {
                                isExist = true;
                                break;
                            }
                        }

                        $("#main-form .items").append($('<div />', {
                            'class': 'item' + (isExist ? " selected" : ""),
                            'id': data[index]["id"],
                            'book_name': data[index]["book_name"],
                            'chapter': data[index]["chapter"],
                        }).html(data[index]["verse"] + ". " + data[index]["text"]));

                    }
                    // console.log(data)
                },
                dataType: 'json'
            });
        });

        $("body").on("click", "#main-form .items .item", function() {
            if (!$(this).hasClass("selected")) {
                $("#main-form #save-list").append($(this).clone())
                $(this).addClass("selected")
            }
        });

        $("body").on("click", "#main-form #save-list .item", function() {
            var id = $(this).attr("id")

            $("#main-form .items .item").each(function() {
                if ($(this).attr("id") == id) {
                    $(this).removeClass("selected");
                }
            });

            $(this).remove();
        });

        $("#save-list").sortable();

        $(".task-form form").submit(function() {
            return false;
        });

        $(".task-form form .btn-success[type=submit]").click(function() {
            var verses = []

            $("#main-form #save-list .item").each(function() {
                verses[verses.length] = $(this).attr("id");
            });

            $.ajax({
                type: 'POST',
                url: '/panel/task/save',
                data: {
                    id: currentId,
                    date: $('#task-date').val(),
                    video_url: $('#task-video_url').val(),

                    title: $('#task-title').val(),
                    descr: $('#task-descr').val(),

                    meditation_title_1: $('#task-meditation_title_1').val(),
                    meditation_1: $('#task-meditation_1').val(),
                    meditation_title_2: $('#task-meditation_title_2').val(),
                    meditation_2: $('#task-meditation_2').val(),

                    essay_title: $('#task-essay_title').val(),
                    essay: $('#task-essay').val(),

                    prayer: $('#task-prayer').val(),
                    verses: verses,
                    application_1: $('[name=application_1]').val(),
                    application_2: $('[name=application_2]').val(),

                },
                success: function(data) {
                    if (data && data["id"]) {
                        currentId = data["id"]

                        let $calendar = container.data('plugin_simpleCalendar')
                        var date = data["date"].split("-")
                        date = new Date(date[2] + "-" + date[0] + "-" + date[1]);

                        var newEvent = {
                            startDate: date.toISOString(),
                            endDate: new Date(date.setHours(date.getHours() + 1)).getTime(),
                            summary: 'New event'
                        }

                        loadDatesForMonth(currentMonth, currentYear)
                        $(".task-form form .btn-danger[type=submit]").toggle(true);
                    }
                },
                dataType: 'json'
            });

            return false;

        });

        $(".task-form form .btn-danger[type=submit]").click(function() {
            var verses = []

            $("#main-form #save-list .item").each(function() {
                verses[verses.length] = $(this).attr("id");
            });

            $.ajax({
                type: 'POST',
                url: '/panel/task/delete?id=' + currentId,
                // data: {
                //     id: currentId,
                // },
                success: function(data) {
                    if (data && data["id"]) {
                        clearForm();
                        currentId = null;
                        $(".task-form form .btn-danger[type=submit]").toggle();

                        loadDatesForMonth(currentMonth, currentYear)
                    }
                },
                dataType: 'json'
            });

            return false;

        });
    });

});