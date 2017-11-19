(function() {
    "use strict";

    var accommodation_type_id_input = $('#accommodation_type_id');
    var from_date_input = $('#from_date');
    var to_date_input = $('#to_date');

    var schedule_modal = $('#schedule-modal');

    var can_create;

    //This is the main class
    function ScheduleView($e) {

        this.$e = $e;

        this.updateScrollBound = this.updateScroll.bind(this);

        this.taskElements = [];

        this.$e.append(this.$container = $('<div>', {
            'css': {
                'display': 'none'
            }
        }));

        this.$e.append(this.$loadingFade = $('<div>', {
            'class': 'schedule-loading-fade'
        }));

        this.$e.append(this.$indicator = createLoadingIndicator());

        this.refreshHandle = null;
        this.refreshInterval = 3600000; // 60000 milliseconds - 1 minute

        this.updateDates();

        this.bindEvents();

        this.autoscrollRef = null;

        if (!this.scrolled && window.location.hash) {
            var hash = window.location.hash.replace(/^#/, '');
            if (hash.indexOf('=') >= 0) {
                // hash is a key-value pair
                var parts = hash.split('&');
                var values = [];
                for (var i = 0; i < parts.length; i++) {
                    var eq = parts[i].indexOf('=');
                    values[parts[i].substr(0, eq)] = parts[i].substr(eq + 1);
                }

                if (values.ref) {
                    this.autoscrollRef = values.ref;
                }
            }
        }

        this.requestData();

        $(".tip-bottom").tooltip({
            placement : 'bottom',
            trigger : 'hover',
            container: 'body'
        });

        this.$e.on('mousewheel DOMMouseScroll',function(e) {

            var e0 = e.originalEvent,
            delta = e0.wheelDelta || -e0.detail;
            var deltaX = e0.wheelDeltaX;

            if(deltaX === undefined || deltaX == 0) {
                //console.log('vert scroll');

                this.scrollTop += ( delta < 0 ? 1 : -1 ) * 20;
                e.preventDefault();

            }else {
                //console.log('horiz scroll');

                this.scrollLeft += ( delta < 0 ? 1 : -1 ) * 20;
                e.preventDefault();
            }

        });

        this.$e.on( 'touchmove', function(e){
            e.preventDefault();
        });


    }

    function createLoadingIndicator() {
        return $('<div>', {
            'class': 'loading-indicator'
        });
    }

    ScheduleView.prototype.updateDates = function() {

        //TO DATE
        to_date_input.datepicker('setStartDate', from_date_input.val());

        from_date_input.datepicker().on('changeDate', function(ev) {
            to_date_input.datepicker('setStartDate', from_date_input.val());
        });
    };

    ScheduleView.prototype.build = function(data) {

        this.$container.empty();

        /*Can create*/
        can_create = data.can_create;

        if (data) {
            this.data = data;
        }
        else {
            data = this.data;
        }

        this.start = moment(data.from_date, 'YYYY-MM-DD');
        this.end = moment(data.to_date, 'YYYY-MM-DD');

        this.gridWidth = data.dates.length;
        this.gridHeight = data.users.length;

        this.$container.append(this.$dayHeaders = this.createDayHeaders(data.days, data.day_width));
        this.$container.append(this.$headers = this.createHeaders(data.dates));
        this.$container.append(this.$user_headers = this.createUserHeaders(data.users));
        this.$container.append(this.$grid = this.createGrid());

        // Fix container widths

        this.cellWidth = this.$headers.find('.schedule-header:eq(0)').outerWidth();
        this.cellHeight = this.$user_headers.find('.schedule-user-header:eq(0)').outerHeight();

        this.$dayHeaders.css({
            'width': this.cellWidth * (this.gridWidth)	// Headers need extra room for top-left empty cell
        });

        this.$headers.css({
            'width': this.cellWidth * (this.gridWidth)	// Headers need extra room for top-left empty cell
        });


        this.$grid.css({
            'width': this.cellWidth * this.gridWidth,
            'height': this.cellHeight * this.gridHeight
        });

        // Create tasks overlay
        this.$container.append(this.$tasks = this.createTasksOverlay(data.tasks, data.dates, data.days, data.day_width));

        this.$tasks.css({
            'width': this.cellWidth * this.gridWidth,
            'height': this.cellHeight * this.gridHeight
        });

        this.$container.append(this.$corner = $('<div>', { 'class': 'schedule-corner' }));

        this.bindOverlayEvents();

        this.$indicator.fadeOut();
        this.$loadingFade.fadeOut();
        this.$container.fadeIn();

        if (this.autoscrollRef && !this.scrolled) {
            var task = this.getTaskByRef(this.autoscrollRef);

            var $litup = $('#task-schedule [data-task-id="'+task.id+'"]');
            if (! $litup.hasClass('schedule-task-justedited')){
                $litup.addClass('schedule-task-justedited');
                $litup.hover(function(){$(this).removeClass('schedule-task-justedited')});
            }

            var start = moment(task.from_date, 'YYYY-MM-DD');
            var end = moment(task.to_date, 'YYYY-MM-DD');

            var start_offset = start.diff(this.start, 'days') + 1;
            var width = end.diff(start, 'days');

            this.$e.scrollTop(Math.max(0, this.getUserIndex(task.user_id) * this.cellHeight - this.$e.height() / 2));
            this.$e.scrollLeft(Math.max(0, (start_offset * this.cellWidth + (width * this.cellWidth) / 2)) - this.$e.width() / 2);

            this.scrolled = true;
        }
    };

    ScheduleView.prototype.getTaskByRef = function(ref) {
        for (var i = 0; i < this.data.tasks.length; i++) {
            if (this.data.tasks[i].id == ref) {
                return this.data.tasks[i];
            }
        }

        return null;
    };

    ScheduleView.prototype.update = function(data) {
        if (!data) {
            data = this.data;
        }

        /*Can create*/
        can_create = data.can_create;

        this.$tasks.remove();
        this.$container.append(this.$tasks = this.createTasksOverlay(data.tasks, data.dates, data.days, data.day_width));

        this.$tasks.css({
            'width': this.cellWidth * this.gridWidth,
            'height': this.cellHeight * this.gridHeight
        });

        this.bindOverlayEvents();
        if (this.scrollpos !== undefined){
            $('#task-schedule').scrollLeft(this.scrollpos[0]);
            $('#task-schedule').scrollTop(this.scrollpos[1]);
        }

        this.$indicator.fadeOut();

        $(".tip-bottom").tooltip({
            placement : 'bottom',
            trigger : 'hover',
            container: 'body'
        });
    };

    ScheduleView.prototype.createDayHeaders = function(days, width) {
        var $headers = $('<div>', {
            'class': 'schedule-headers-days'
        });

        for (var i = 0; i < days.length; i++) {
            var day = moment(days[i], 'YYYY-MM-DD');

            var header_class = ['schedule-header', 'tip-bottom'];

            $headers.append($('<div>', {
                'class': header_class.join(' '),
                'html': '<strong>' + day.format('dddd, MMM DD') + '</strong>',
                'width': width * 50 +'px'
            }));

        }

        return $headers;
    };

    ScheduleView.prototype.createHeaders = function(dates) {
        var $headers = $('<div>', {
            'class': 'schedule-headers'
        });

        for (var i = 0; i < dates.length; i++) {
            var date = moment(dates[i], 'YYYY-MM-DD hh:mm');
            var header_class = ['schedule-header', 'tip-bottom'];
            if (date.format('HH:mm') === this.data.dayStart) {
                header_class.push('schedule-header-daystart');
            }
            $headers.append($('<div>', {
                'class': header_class.join(' '),
                'html': date.format('HH:mm') //or hA to have 7 am / 2 pm
            }));
        }

        return $headers;
    };

    ScheduleView.prototype.createUserHeaders = function(users) {
        var $headers = $('<div>', {
            'class': 'schedule-user-headers'
        });

        this.user_indexes = {};

        var sorted = users.slice(0);

        for (var i = 0; i < sorted.length; i++) {
            var $header = $('<div>', {
                'class': 'tip-bottom schedule-user-header',
                'text': sorted[i].name
            });

            $header.css({
                'background': sorted[i].colour,
                'color': this.colorForBackground(sorted[i].colour)
            });

            var tooltip_data = "<div class='tooltip-data'>";
            tooltip_data+= "<h4>"+ sorted[i].name +"</h4>";
            tooltip_data+= "<p>Group: "+ sorted[i].group +"</p>";
            tooltip_data+= "<p>Active: "+ sorted[i].active +"</p>";
            tooltip_data+= "</div>";

            $header.attr('data-toggle', 'tooltip');
            $header.attr('data-placement', 'bottom');
            $header.attr('data-html', 'true');
            $header.attr('data-original-title', tooltip_data);

            $headers.append($header);

            this.user_indexes[sorted[i].id] = i;

        }

        return $headers;
    };

    ScheduleView.prototype.brightness = function(color) {
        if (typeof color === 'undefined') return 255;
        color = parseInt(color.substr(1), 16);

        var r = (color & 0xff0000) >> 16;
        var g = (color & 0x00ff00) >> 8;
        var b = (color & 0x0000ff);

        return Math.sqrt(
            r * r * 0.299 +
            g * g * 0.587 +
            b * b * 0.114
        );
    };

    ScheduleView.prototype.lighten = function(color) {

        var b = this.brightness(color);
        if (color==undefined) return;
        color = parseInt(color.substr(1), 16);

        var c = {};

        c['r'] = (color & 0xff0000) >> 16;
        c['g'] = (color & 0x00ff00) >> 8;
        c['b'] = (color & 0x0000ff);

        var j = [];
        var sum = 0;
        for(var k in c){
            c[k]+= 255-b;
            if (c[k]>255)c[k]=255;
            sum += c[k];

        }

        var avg = sum/3.0;

        for(var k in c){
            if (c[k]>avg){
                c[k] -= (c[k]-avg)/3;
            } else if (c[k]<avg){
                c[k] -= (c[k]-avg)/1.2;
            }
            j.push(parseInt(Math.round(c[k])));
        }

        return 'rgb('+j.join(',')+')';
    };

    ScheduleView.prototype.colorForBackground = function (color) {
        var dark = '#000000';
        var light = '#ffffff';

        if (!color) {
            return dark;
        }

        return this.brightness(color) < 130 ? light : dark;
    };

    ScheduleView.prototype.createGrid = function() {
        var $grid = $('<div>', {
            'class': 'schedule-grid'
        });

        return $grid;
    };

    ScheduleView.prototype.createTasksOverlay = function(tasks, dates, days, day_width) {

        var self = this;

        var $overlay = $('<div>', {
            'class': 'schedule-tasks-overlay'
        });

        this.taskElements = [];

        for (var i = 0; i < tasks.length; i++) {
            if (!tasks[i].user_id) {
                continue;
            }

            var user_index = this.getUserIndex(tasks[i].user_id);

            if (user_index === null) {
                // Our list of users doesn't include this task's user.
                continue;
            }

            var $task = $('<div>', {
                'class': 'tip-bottom schedule-task '+ tasks[i].status
            });

            this.taskElements[tasks[i].id] = $task;

            $task.data('taskId', tasks[i].id);
            $task.attr('data-task-id', tasks[i].id);

            var start = moment(tasks[i].from_date, 'YYYY-MM-DD hh:mm');
            var end = moment(tasks[i].to_date, 'YYYY-MM-DD hh:mm');

            // +/- 1 is here to give space for before/after psuedo-elements on the start and end cells
            var start_offset = start.diff(dates[Object.keys(dates)[0]], 'hours') + 1;

            if ( start_offset > day_width ){
                var dates_first_element = moment(dates[Object.keys(dates)[0]], 'YYYY-MM-DD hh:mm');
                var st = dates_first_element.diff(this.start, 'hours');
                var real_offset = start_offset + st;
                var day = Math.floor(start_offset / 24) + 1;

                start_offset = real_offset - ( (day-1) * 24 ) - st + (day-1) * day_width;

            }

            // var blah = moment(dates[Object.keys(dates)[Object.keys(dates).length - 1]], 'YYYY-MM-DD hh:mm');

            var task_length = end.diff(start, 'hours') - 1;

            $task.css({
                'left': start_offset * this.cellWidth,
                'top': user_index * this.cellHeight,
                'width': task_length * this.cellWidth
            });

            // console.log(start_offset);
            //
            // if(day_width > Object.keys(dates).length){
            //     //$task.addClass('schedule-task-without-after');
            // }

            var task_text =  tasks[i].task_name;

            $task.append($('<span>', {
                'text': task_text }));

            if ((start_offset * this.cellWidth + 5)<0){
                console.log('***');
                $('span', $task).css({
                    'margin-left':(-(start_offset * this.cellWidth))+5 +'px',
                    'max-width':(task_length * this.cellWidth) + (start_offset * this.cellWidth) - 5 + 'px'
                });

            }

            var tooltip_data = "<div class='tooltip-data'>";
            tooltip_data+= "<h4>User "+ tasks[i].user_name +"</h4>";
            tooltip_data+= "<p>Task name: " + "<span class='text-warning'> " + tasks[i].task_name +"</span></p>";
            tooltip_data+= "<p>Status: "+ tasks[i].tooltip_data.status +"</p>";
            tooltip_data+= "<p>Start: " + tasks[i].tooltip_data.start_date + "<span class='text-info'> " + tasks[i].tooltip_data.start_time +"</span></p>";
            tooltip_data+= "<p>End: "+ tasks[i].tooltip_data.end_date + "<span class='text-info'> "+ tasks[i].tooltip_data.end_time + "</span></p>";
            tooltip_data+= "</div>";

            $task.attr('data-toggle', 'tooltip');
            $task.attr('data-placement', 'bottom');
            $task.attr('data-html', 'true');
            $task.attr('data-original-title', tooltip_data);

            $overlay.append($task);
        }

        /*Non business periods*/
        var non_business_periods = this.data.non_business_periods;

        /*Disabled users*/
        var users = this.data.users.slice(0);

        var user_from_date = from_date_input.val();
        user_from_date = moment(user_from_date, 'DD-MMM-YYYY hh:mm');

        var user_to_date = to_date_input.val();
        user_to_date = moment(user_to_date, 'DD-MMM-YYYY hh:mm').endOf('day'); //endOf day is added to find the hours accurately

        var disabled_start_offset = user_from_date.diff(user_from_date, 'hours');
        var disabled_task_length = user_to_date.diff(user_from_date, 'hours') + 1;

        for (var j = 0; j < users.length; j++) {

            var user = users[j];

            if(user.disabled == 1) {

                var user_class = '';

                var $disabled = $('<div>', {
                    'class': 'schedule-task disabled' + " " + user_class
                });

                $disabled.css({
                    'left': disabled_start_offset * this.cellWidth,
                    'top': this.getUserIndex(user.id) * this.cellHeight,
                    'width': disabled_task_length * this.cellWidth
                });

                if ((disabled_start_offset * this.cellWidth + 5)<0){
                    $('span', $disabled).css({
                        'margin-left':(-(disabled_start_offset * this.cellWidth))+5 +'px',
                        'max-width':(disabled_task_length * this.cellWidth) + (disabled_start_offset * this.cellWidth) - 5 + 'px'
                    });
                }

                $overlay.append($disabled);
            }

            /*Non business periods*/
            if(user.id in non_business_periods) {


                var non_business_periods_array = non_business_periods[user.id];


                for (var c = 0; c < non_business_periods_array.length; c++) {

                    var non_business_period = non_business_periods_array[c];

                    var non_business_period_from_date = moment(non_business_period.from_time, 'YYYY-MM-DD hh:mm'), non_business_period_to_date;

                    non_business_period_to_date = moment(non_business_period.to_time, 'YYYY-MM-DD hh:mm');

                    var non_business_period_start_offset = non_business_period_from_date.diff(this.start, 'hours');
                    var non_business_period_length = non_business_period_to_date.diff(non_business_period_from_date, 'hours') + 1;

                    if(non_business_period_to_date > this.end) {
                        non_business_period_to_date = this.end;
                        non_business_period_length = non_business_period_to_date.diff(non_business_period_from_date, 'hours');
                    }

                    if(non_business_period_from_date < this.start) {
                        non_business_period_from_date = this.start;
                        non_business_period_start_offset = non_business_period_from_date.diff(this.start, 'hours');
                        non_business_period_length = non_business_period_to_date.diff(non_business_period_from_date, 'hours') + 1;
                    }

                    var $non_business_period = $('<div>', {
                        'class': 'schedule-task non-business'
                    });

                    $non_business_period.css({
                        'left': non_business_period_start_offset * this.cellWidth,
                        'top': this.getUserIndex(user.id) * this.cellHeight,
                        'width': non_business_period_length * this.cellWidth
                    });

                    $non_business_period.append($('<span>', {'text': non_business_period.reason }));

                    if ((non_business_period_start_offset * this.cellWidth + 5)<0){
                        $('span', $non_business_period).css({
                            'margin-left':(-(non_business_period_start_offset * this.cellWidth))+5 +'px',
                            'max-width':(non_business_period_length * this.cellWidth) + (non_business_period_start_offset * this.cellWidth) - 5 + 'px'
                        });
                    }

                    $overlay.append($non_business_period);

                }

            }


        }

        return $overlay;
    };

    ScheduleView.prototype.updateScroll = function() {

        if (this.$headers) {
            var y = this.$e.scrollTop();
            var x = this.$e.scrollLeft();

            this.$dayHeaders.css({
                'top': y
            });

            this.$headers.css({
                'top': '31px'
            });

            this.$user_headers.css({
                'left': x
            });

            this.$corner.css({
                'top': y,
                'left': x
            });

            this.$indicator.css({
                'top':y+this.$e.height()/2,
                'left':x+this.$e.width()/2
            });

            this.$loadingFade.css({
                'top':y,
                'left':x
            })
        }

        window.requestAnimationFrame(this.updateScrollBound);
    };

    ScheduleView.prototype.getUserIndex = function(user_id) {
        return this.user_indexes[user_id];
    };

    ScheduleView.prototype.requestData = function() {

        var data_s = [];

        var accommodation_type_id = accommodation_type_id_input.find(":selected").val();
        var from_date = from_date_input.val();
        var to_date = to_date_input.val();

        $.ajax({
            'url':'/tasks/get-schedule-tasks-ajax',
            'method':'GET',
            'data':{accommodation_type_id: accommodation_type_id, from_date: from_date, to_date: to_date},
            'async':false,
            'success':function(data) {
                data_s = data;
            }
        });
        this.build(data_s);
    };

    ScheduleView.prototype.bindOverlayEvents = function() {
        var self = this;
        var $active_task = null;

        this.$tasks.on('mousemove', function(e) {
            if (self.drawing_range) {
                var x = e.clientX - self.$tasks.offset().left;

                var xi = Math.floor(x/self.cellWidth);
                var xdiff = (xi - self.drawing_range_x) + 1;

                $('#rangeview').css({
                    'width': self.cellWidth * xdiff
                });
            }
            else {

                if (!$(e.target).is('.schedule-tasks-overlay')) {
                    var $task = $(e.target).closest('.schedule-task');

                    if ($active_task) {
                        $active_task.removeClass('schedule-task-hover');
                    }

                    $active_task = $task;

                    $active_task.addClass('schedule-task-hover');
                }
                else {
                    if ($active_task) {
                        $active_task.removeClass('schedule-task-hover');
                    }
                    $active_task = null;
                }
            }
        });

        this.$tasks.on('click', function(e) {
            if ($active_task) {

                window.location.href = "/tasks/view?id="+$active_task.data('taskId');
            }

            e.preventDefault();
        });

        this.$tasks.on('mouseup', function(e) {
            if (self.drawing_range && can_create == true) {
                var start_date = self.gridCellToDate(self.drawing_range_x);
                var user = self.gridCellToUser(self.drawing_range_y);

                if(user.disabled == 1) {

                    var msg;

                    if(user.disabled == 1) {
                        msg = 'Not allowed';
                    }

                    showModalMessage('Error', msg);

                    self.drawing_range = false;
                    $('#rangeview').remove();

                    return;
                }

                var x = e.clientX - self.$tasks.offset().left;

                var xi = Math.floor(x/self.cellWidth);

                var end_date = self.gridCellToDate(xi);

                start_date = moment(start_date, 'DD/MM/YYYY H:mm');
                end_date = moment(end_date, 'DD/MM/YYYY H:mm');

                var start_date_time = start_date.format('H:mm');
                var end_date_time = end_date.format('H:mm');

                start_date = start_date.format('YYYY-MM-DD H:mm');
                end_date = end_date.format('YYYY-MM-DD H:mm');

                self.drawing_range = false;
                $('#rangeview').remove();

                setTimeout(function() {

                    $.ajax({
                        'url':'/tasks/schedule-request-ajax',
                        'method':'GET',
                        'data':{user_id: user.id, from_date: start_date, to_date: end_date, from_date_time: start_date_time, to_date_time: end_date_time},
                        'async':false,
                        'success':function(data) {

                            /*Date*/
                            if(data.date_error) {
                                showModalMessage('Error', data.date_message);
                                return false;
                            }

                            /*Time*/
                            if(data.time_error) {
                                showModalMessage('Error', data.time_message);
                                return false;
                            }

                            /*Conflicts*/
                            if(data.has_conflicts == true) {
                                showModalMessage('Error', data.conflict_message);
                                return false;
                            }

                            /*Non-business*/
                            if(data.under_non_business_period == true) {
                                showModalMessage('Error', data.under_non_business_period_message);
                                return false;
                            }

                            /*Modal*/
                            schedule_modal.find('input[name=user_id]').val(data.form.user_id);
                            schedule_modal.find('input[name=from_date]').val(data.form.from_date);
                            schedule_modal.find('input[name=to_date]').val(data.form.to_date);


                            schedule_modal.find('#user_name').val(data.view.user_name);

                            schedule_modal.find('#start_date').val(data.view.start_date);
                            schedule_modal.find('#end_date').val(data.view.end_date);
                            schedule_modal.find('#start_time').val(data.view.start_time);
                            schedule_modal.find('#end_time').val(data.view.end_time);

                            schedule_modal.modal();
                            return true;
                        }
                    });

                }, 1);
            }
        });

        this.$tasks.on('mousedown', function(e) {
            if (e.which != 1) {
                // Only care about left clicking.
                e.preventDefault();
            }
            if ($active_task) {
                e.preventDefault();
            }
            else {
                self.drawing_range = true;

                var x = e.pageX - self.$tasks.offset().left;
                var y = e.pageY - self.$tasks.offset().top;

                var xi = Math.floor(x/self.cellWidth);
                var yi = Math.floor(y/self.cellHeight);

                self.drawing_range_x = xi;
                self.drawing_range_y = yi;

                self.$tasks.append('<div id="rangeview"></div>');

                $('#rangeview').css({
                    'position':'absolute',
                    'left':xi*self.cellWidth +'px',
                    'top':yi*self.cellHeight +'px',
                    'width':self.cellWidth+'px',
                    'height':self.cellHeight+'px',
                    'opacity':0.3
                });
            }
        });
    };

    ScheduleView.prototype.bindEvents = function() {

        this.refreshHandle = setInterval(this.refresh.bind(this), this.refreshInterval);

        window.requestAnimationFrame(this.updateScrollBound);
    };

    ScheduleView.prototype.refresh = function() {
        this.$indicator.fadeIn();
        this.scrollpos = [$('#task-schedule').scrollLeft(),$('#task-schedule').scrollTop()];

        var data_s = [];

        var accommodation_type_id = accommodation_type_id_input.find(":selected").val();
        var from_date = from_date_input.val();
        var to_date = to_date_input.val();

        $.ajax({
            'url':'/tasks/get-schedule-tasks-ajax',
            'method':'GET',
            'data':{accommodation_type_id: accommodation_type_id, from_date: from_date, to_date: to_date},
            'async':false,
            'success':function(data) {
                data_s = data;
            }
        });

        this.update(data_s);
    };

    ScheduleView.prototype.gridCellToUser = function(cellindex) {
        return this.data.users[cellindex];
    };

    ScheduleView.prototype.gridCellToDate = function(cellindex) {

        return moment(this.data.dates[cellindex], 'YYYY-MM-DD hh:mm').format('DD/MM/YYYY H:mm');
    };

    $(function() {
        var $schedule = $('#task-schedule');

        if ($schedule.length === 0) {
            return;
        }

        window.scheduleView = new ScheduleView($schedule);
    });
})();
