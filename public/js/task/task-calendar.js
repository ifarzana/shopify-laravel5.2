$(document).ready(function() {

    var modal = $('#calendar-modal');
    var create_task_modal = $('#create-task-modal');


    var task_title = $('input[name=task-title]');
    var task_resource = $('input[name=task-resource]');


    var bell_icon = modal.find('i#bell-icon');
    var span_title = modal.find('span#title');
    var span_description = modal.find('span#description');
    var span_start_date = modal.find('span#start_date');
    var span_end_date = modal.find('span#end_date');
    var span_client = modal.find('span#client');
    var edit_link_a = modal.find('a#edit-link');
    var delete_link_a = modal.find('a#delete-link');

    $('#calendar').fullCalendar({

        windowResize: function(view) {

            if ($(window).width() < 660){
                $('#calendar').fullCalendar( 'changeView', 'basicWeek' );
            } else {
                $('#calendar').fullCalendar( 'changeView', 'timelineDay' );
            }

        },

        header: {
            left: 'prev,next today', // prevYear,nextYear
            center: 'title',
            right: 'timelineDay,timelineTwoMonths,month,agendaWeek,agendaDay'
        },
        // footer: true,
        // weekNumbers: true,

        // weekends: false,
        businessHours: [ // specify an array
            {
                dow: [ 1, 2, 3, 4 ], // Monday, Tuesday, Wednesday, Thursday
                start: '08:30', // 8:30am
                end: '18:00' // 6pm
            },
            {
                dow: [ 5 ], // Friday
                start: '08:30', // 8:30am
                end: '17:00' // 5pm
            }
        ],
        aspectRatio: 2,
        navLinks: true, // can click day/week names to navigate views
        selectable: true,
        selectHelper: true,
        select: function(start, end, jsEvent, view, resource) {

            var title = prompt('Event Title:');
            var eventData;
            if (title) {
                eventData = {
                    title: title,
                    resourceId: resource ? resource.id : '',
                    start: start,
                    end: end
                };
                $('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
            }
            $('#calendar').fullCalendar('unselect');
        },
        editable: true,

        eventLimit: true,
        defaultView: 'timelineDay',
        views: {
            month: {
                eventLimit: 3, // adjust to 2 only for agendaWeek/agendaDay
                // titleFormat: 'YYYY, MM, DD',
                // dragOpacity: .2,
            },
            timelineTwoMonths: {

                type: 'timeline',
                duration: { months: 2 },
                weekends: false
            }
        },
        resourceLabelText: 'Users',

        //resourceOrder: '-id', //for descending
        resources: [
            { id: '1', title: 'Israt Farzana' },
            { id: '2', title: 'John Doe', eventColor: 'green'},
            { id: '3', title: 'Jane Smith', eventColor: 'orange' },
            { id: '4', title: 'Catalin Barbu', eventColor: 'green' },
            { id: '5', title: 'Harry Styles', eventColor: 'blue' }
        ],

        firstDay : 1,

        events: {
            url: 'tasks/events',
            error: function() {

            }
        },
        loading: function(bool) {
            //$('#loading').toggle(bool);
        },

        eventMouseover: function(event, jsEvent, view) {

            // change the day's background color just for fun
            $(this).css('background-color', '#64A9C6');

        },

        eventMouseout: function(event, jsEvent, view) {

            $(this).css('background-color', '');

        },

        eventRender: function(event, element) {

            var icons = '';

            /*Display in alerts*/
            if(event.display_in_alerts == 1) {
                icons = "<i class='alerts-icon fa fa-bell'></i>&nbsp;"
            }else {
                bell_icon.addClass('hidden');
            }

            /*Client*/
            if(event.client != null) {
               icons+= "<i class='fa fa-user'></i>&nbsp;"
            }

            element.find('.fc-title').html(icons+event.title);
        },
        eventClick:  function(event, jsEvent, view) {

            /*Date*/
            span_title.html(event.title);
            span_description.html(event.description);
            span_start_date.html(event.start_date);
            span_end_date.html(event.end_date);

            /*Client*/
            var client = 'None';

            if(event.client != null) {
                client = event.client;
            }

            span_client.html(client);

            /*Links*/
            $.ajax({
                'url':'/tasks/links',
                'method':'GET',
                'data':{ id: event.id },
                'async':true,
                'success':function(data) {
                    var edit_link = data.edit_link;
                    var delete_link = data.delete_link;

                    edit_link_a.attr("href", edit_link);

                    delete_link_a.attr("href", "javascript:confirmation('"+delete_link+"', 'Delete task ?')");
                }
            });

            /*Display modal*/
            modal.modal();
            
        }

    });

});