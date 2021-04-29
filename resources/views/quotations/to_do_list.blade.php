@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('fullcalendar/lib/main.css') }}">
@endsection

@section('content')

      @include('quotations/todos/modal_export_csv')

    <!-- Modal event creation -->
    <div id="event_add" class="modal">
        <div class="modal-content">
            <h5 class="modal-title mb-4"> @lang('Add event')</h5>
            <span> @lang('Insert the modal datas and create your event') </span>

            @csrf

            <div class="row">
              <div class="col l6 s12">
                <label for="start_date" class="dateslabel"> @lang('Start date') </label>
                <!-- <input type="datetime-local" name="start_date" id="start_date"> -->
                <input type="text" name="start_date" id="start_date" class="start_date_datetimepicker">
              </div>
              <div class="col l6 s12">
                <label for="end_date" class="dateslabel"> @lang('End date') </label>
                <!-- <input type="datetime-local" name="end_date" id="end_date"> -->
                <input type="text" name="end_date" id="end_date" class="end_date_datetimepicker">
              </div>
            </div>

            <input type="hidden" name="all_day" id="all_day">
            <div class="input-field my-3">
                <label for="title"> @lang('Event title') </label>
                <input type="text" id="title" name="title">
            </div>
            <div class="select-field my-3">
              <label for="users"> @lang('Assigned users') </label>
              <select id="users" name="users[]" multiple>
              @foreach( $calendar->users as $user )
                @if( $user->id != Auth::user()->id )
                  <option value="{{$user->id}}">{{$user->name}}</option>
                @endif
              @endforeach
              </select>
              ( <small> @lang('This users are assigned to the event, you can only assign the users assigned to the calendar') </small> )
            </div>
            <div class="select-field my-3">
              <label for="users_groups"> @lang('Assigned groups') </label>
              <select id="users_groups" name="users_groups[]" multiple>
              @foreach( $calendar->groups as $group )
                <option value="{{$group->id}}">{{$group->name}}</option>
              @endforeach
              </select>
              ( <small> @lang('This groups are assigned to the event, you can only assign the groups assigned to the calendar') </small> )
            </div>
            <div class="select-field my-3">
              <label for="activity"> @lang('Select main activity') </label>
              <select id="activity" name="activity">
                <option value="" selected>@lang("No activities")</option>
              @foreach( $activities as $activity )
                <option value="{{$activity->id}}">{{$activity->name}}</option>
              @endforeach
              </select>
            </div>
            <div class="select-field my-3" id="subactivities_container"></div>
            <div class="input-field my-3">
                <label for="description"> @lang('Event description') </label>
                <textarea id="description" name="description" class="materialize-textarea"></textarea>
            </div>
        </div>
        <div class="divider"></div>
        <div class="modal-footer">
            <a href="#!" class="modal-close btn waves-effect waves-light red"> @lang('Cancel') </a>
            <button type="button" id="add_event" class="btn waves-effect waves-light blue" data-id="0"> @lang('Add event') </button>
        </div>
    </div>
    <!-- Modal event creation end -->

    <!-- Modal event edit -->
    <div id="event_edit" class="modal">
        <div class="modal-content">
            <h5 class="modal-title mb-4"> @lang('Edit event')</h5>
            <span> @lang('Edit the event datas') </span>

            <div class="row">
              <div class="col l6 s12">
                <label for="start_date" class="dateslabel"> @lang('Start date') </label>
                <!-- <input type="datetime-local" name="start_date_edit" id="start_date_edit"> -->
                <input type="text" name="start_date_edit" id="start_date_edit" class="start_date_datetimepicker_edit">
              </div>
              <div class="col l6 s12">
                <label for="end_date" class="dateslabel"> @lang('End date') </label>
                <!-- <input type="datetime-local" name="end_date_edit" id="end_date_edit"> -->
                <input type="text" name="end_date_edit" id="end_date_edit" class="end_date_datetimepicker_edit">
              </div>
            </div>

            <input type="hidden" name="all_day_edit" id="all_day_edit">
            <div class="input-field my-3">
                <label for="title"> @lang('Event title') </label>
                <input type="text" id="title_edit" name="title_edit">
            </div>
            <div class="select-field my-3">
              <label for="users"> @lang('Assigned users') </label>
              <select id="users_edit" name="users_edit[]" multiple>
              @foreach( $calendar->users as $user )
                @if( $user->id != Auth::user()->id )
                  <option value="{{$user->id}}">{{$user->name}}</option>
                @endif
              @endforeach
              </select>
              ( <small> @lang('This users are assigned to the event, you can only assign the users assigned to the calendar') </small> )
            </div>
            <div class="select-field my-3">
              <label for="users_groups_edit"> @lang('Assigned groups') </label>
              <select id="users_groups_edit" name="users_groups_edit[]" multiple>
              @foreach( $calendar->groups as $group )
                <option value="{{$group->id}}">{{$group->name}}</option>
              @endforeach
              </select>
              ( <small> @lang('This groups are assigned to the event, you can only assign the groups assigned to the calendar') </small> )
            </div>
            <div class="select-field my-3">
              <label for="activity_edit"> @lang('Select main activity') </label>
              <select id="activity_edit" name="activity_edit">
                <option value="" selected>@lang("No activities")</option>
              @foreach( $activities as $activity )
                <option value="{{$activity->id}}">{{$activity->name}}</option>
              @endforeach
              </select>
            </div>
            <div class="select-field my-3" id="subactivities_container_edit"></div>
            <div class="input-field my-3">
                <label for="description"> @lang('Event description') </label>
                <textarea id="description_edit" name="description_edit" class="materialize-textarea"></textarea>
            </div>
        </div>
        <div class="divider"></div>
        <div class="modal-footer">
            <a href="#!" class="modal-close btn waves-effect waves-light red"> @lang('Cancel') </a>
            <button type="button" id="edit_event" class="btn waves-effect waves-light blue" data-id="0"> @lang('Edit event') </button>
        </div>
    </div>
    <!-- Modal event edit end -->

    <!-- Modal event show -->
    <div id="event_show" class="modal">
        <div class="modal-content">
            <h4 class="modal-title mb-4"><span class="event_title"></span></h4>
            <h6> @lang("Event dates") </h6>
            <div class="event_datas">
              <strong> <span class="data_start"></span> <span class="data_end"></span> </strong>
            </div>
            <div class="activity_section">
              <h6> @lang("Main activities") </h6>
                <div class="event_activity"></div>
              <h6> @lang("Subactivities") </h6>
                <div class="event_subactivities"></div>
            </div>
            <h6> @lang("Event description") </h6>
              <div class="event_description"></div>
        </div>
        <div class="divider"></div>
        <div class="modal-footer">
            <a href="#!" class="modal-close btn waves-effect waves-light blue"> @lang('Cancel') </a>
            @if( Auth::user()->roles->first()->id <= 4 )
              <button type="button" id="go_edit_event"   class="btn waves-effect waves-light orange" data-id="0"> @lang('Edit event') </button>
              <button type="button" id="delete_event" class="btn waves-effect waves-light red" data-id="0"> @lang('Delete event') </button>
            @endif
        </div>
    </div>
    <!-- Modal event show end -->

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('calendars.index') }}'" class="pointer">/&nbsp;@lang('Calendars')</a>
                <a href="#" class="pointer"> / @lang('Show Calendar') </a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button type="button" class="btn btn-floating waves-effect waves-light modal-trigger orange" data-target="calendar_export_csv"><i class="material-icons">file_download</i></button>
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    @if ($errors->any())
        <div class="card-alert card red lighten-5 my-4">
            <div class="card-content red-text">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="close red-text" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col s12">
            <div class="card hoverable">
                <div class="card-content">
                    <span class="card-title ml-2"> @lang('Show calendar') </span>

                    <div id='calendar'></div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    @parent
    <script src="{{ asset('fullcalendar/lib/main.js') }}"></script>
    <script src="{{ asset('fullcalendar/lib/locales/it.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script src="{{ asset('templates/js/materializedatetimepicker.js') }}"></script>

    <script>

    function initDateTimePicker(){
      M.AutoInit();
      var DateFieldStart     = MaterialDateTimePicker.create($(".start_date_datetimepicker"));
      var DateFieldEnd       = MaterialDateTimePicker.create($(".end_date_datetimepicker"));
      var DateFieldStartEdit = MaterialDateTimePicker.create($(".start_date_datetimepicker_edit"));
      var DateFieldEndEdit   = MaterialDateTimePicker.create($(".end_date_datetimepicker_edit"));

      /* Export */
      var DateFieldStartExport = MaterialDateTimePicker.create($(".start_export_date_datetimepicker"));
      var DateFieldEndExport   = MaterialDateTimePicker.create($(".end_export_date_datetimepicker"));
    }

    $(document).ready(function () {

      initDateTimePicker();

      var calendarEl = document.getElementById('calendar');

      var events = [];

      @foreach( $calendar->events as $kn => $event)
        @php
          $retUsers = "start";
          foreach( $event->users as $user ){
            if ( $user->id != Auth::user()->id ) {
              if ( $retUsers == "start") {
                $retUsers = $user->id;
              }else {
                $retUsers .= ",".$user->id;
              }
            }
          }
          $retGroups = "start";
          foreach( $event->groups as $group ){
            if ( $retGroups == "start") {
              $retGroups = $group->id;
            }else {
              $retGroups .= ",".$group->id;
            }
          }
        @endphp

        var mainActivity = false;
        @if( !is_null($event->activity) && $event->activity !== "" )
          mainActivity = {
              id:    {{$event->activity->id}},
              name: "{{$event->activity->name}}",
          };
        @endif

        var subactivities = false;
        var subactity     = {};
        @if( !is_null($event->subactivities) && $event->subactivities !== "" && count($event->subactivities) > 0 )
          subactivities = [];
          @foreach( $event->subactivities as $subactivity )
            subactity = {
                id:    {{$subactivity->id}},
                name: "{{$subactivity->name}}",
            };
            subactivities.push(subactity);
          @endforeach
        @endif

        @if ( Auth::user()->roles->first()->id <= 2 )

          events.push({
            id: {{$event->id}},
            title: "{{$event->title}}",
            start: "{{$event->start_date}}",
            end: "{{$event->end_date}}",
            description: "{{$event->note}}",
            start_date: "{{$event->start_date}}",
            end_date: "{{$event->end_date}}",
            users: "{{$retUsers}}",
            groups: "{{$retGroups}}",
            activity: mainActivity,
            subactivities: subactivities
          });

        @else

          /* Need for add the event only one time */
          @php $eventAlreadyAdded = false; @endphp

          @foreach( $event->users as $user )

            var eventSingle = {
              id: {{$event->id}},
              title: "{{$event->title}}",
              start: "{{$event->start_date}}",
              end: "{{$event->end_date}}",
              description: "{{$event->note}}",
              start_date: "{{$event->start_date}}",
              end_date: "{{$event->end_date}}",
              users: "{{$retUsers}}",
              groups: "{{$retGroups}}",
              activity: mainActivity,
              subactivities: subactivities
            };

            @if( $user->id == Auth::user()->id && !$eventAlreadyAdded )
              events.push( eventSingle );
              @php $eventAlreadyAdded = true; @endphp
            @else
              @foreach( $event->groups as $groupEnabled )
                @if( $groupEnabled->users->contains( Auth::user() ) && !$eventAlreadyAdded )
                  events.push( eventSingle );
                  @php $eventAlreadyAdded = true; @endphp
                @endif
              @endforeach
            @endif

          @endforeach
        @endif
      @endforeach

      var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: "it",
        slotDuration: '00:30',

        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },

        initialView: 'timeGridWeek',
        initialDate: '{{date("Y-m-d")}}',
        navLinks: true, // can click day/week names to navigate views
        selectable: true,
        selectMirror: true,
        select: function(arg) {
          @if( Auth::user()->roles->first()->id <= 4 )
            clearModal( "create" );

            // Readd to string :00+01:00
            if (arg.allDay) {
              $("#start_date").attr("type", "hidden");
              $("#end_date").attr("type", "hidden");

              $(".dateslabel").hide();

              var startDateObj = new Date( arg.startStr );
              var endDateObj   = new Date( arg.endStr );

              var startDate = ("0" + startDateObj.getDate() ).slice(-2) + "/" + ("0" + ( startDateObj.getMonth() + 1) ).slice(-2) + "/" + startDateObj.getFullYear() + " " + ("0" + startDateObj.getHours() ).slice(-2) + ":" + ("0" + startDateObj.getMinutes() ).slice(-2);
              var endDate   = ("0" + endDateObj.getDate() ).slice(-2) + "/" + ("0" + ( endDateObj.getMonth() + 1) ).slice(-2) + "/" + endDateObj.getFullYear() + " " + ("0" + endDateObj.getHours() ).slice(-2) + ":" + ("0" + endDateObj.getMinutes() ).slice(-2);

              $("#start_date").val( startDate );
              $("#end_date").val(  endDate );
            }else{
              var start = arg.startStr.substring(0, 16);
              var end   = arg.endStr.substring(0, 16);

              $(".dateslabel").show();

              // $("#start_date").attr("type", "datetime-local");
              // $("#end_date").attr("type", "datetime-local");
              $("#start_date").attr("type", "text");
              $("#end_date").attr("type", "text");

              /* Re-init */
              initDateTimePicker();

              var startDateObj = new Date( start );
              var endDateObj   = new Date( end );

              var startDate = ("0" + startDateObj.getDate() ).slice(-2) + "/" + ("0" + ( startDateObj.getMonth() + 1) ).slice(-2) + "/" + startDateObj.getFullYear() + " " + ("0" + startDateObj.getHours() ).slice(-2) + ":" + ("0" + startDateObj.getMinutes() ).slice(-2);
              var endDate   = ("0" + endDateObj.getDate() ).slice(-2) + "/" + ("0" + ( endDateObj.getMonth() + 1) ).slice(-2) + "/" + endDateObj.getFullYear() + " " + ("0" + endDateObj.getHours() ).slice(-2) + ":" + ("0" + endDateObj.getMinutes() ).slice(-2);

              $("#start_date").val(startDate);
              $("#end_date").val(endDate);
            }

            $("#all_day").val(arg.allDay);
            $('#event_add').modal('open');
          @endif
        },
        eventClick: function(arg) {
          /* For editing and deleting */
          currentGlobalArg = arg;

          $('#event_show').modal('open');

          /* Set the view datas */
          $('.event_title').html(arg.event._def.title);
          $('.event_description').html(arg.event._def.extendedProps.description);

          if ( arg.event._def.extendedProps.activity !== false ) {
            $('.activity_section').show();

            $('.event_activity').html( arg.event._def.extendedProps.activity.name )
            if( arg.event._def.extendedProps.subactivity !== false ) {
              var subactivitiesHtml = `<ul>`;
              arg.event._def.extendedProps.subactivities.forEach(( subactivity, i ) => {
                subactivitiesHtml += `<li> - `+subactivity.name+`</li>`;
              });
              subactivitiesHtml += `</ul>`;
              $('.event_subactivities').html( subactivitiesHtml );
            }
          }else{
            $('.activity_section').hide();
          }

          startDateObj = new Date( arg.event._def.extendedProps.start_date );
          endDateObj   = new Date( arg.event._def.extendedProps.end_date );

          var startDate = ("0" + startDateObj.getDate() ).slice(-2) + "/" + ("0" + ( startDateObj.getMonth() + 1) ).slice(-2) + "/" + startDateObj.getFullYear() + " " + ("0" + startDateObj.getHours() ).slice(-2) + ":" + ("0" + startDateObj.getMinutes() ).slice(-2);
          var endDate   = ("0" + endDateObj.getDate() ).slice(-2) + "/" + ("0" + ( endDateObj.getMonth() + 1) ).slice(-2) + "/" + endDateObj.getFullYear() + " " + ("0" + endDateObj.getHours() ).slice(-2) + ":" + ("0" + endDateObj.getMinutes() ).slice(-2);

          if ( arg.event._def.allDay ) {
            startDate = startDateObj.getDate() + "/" + ( startDateObj.getMonth() + 1) + "/" + startDateObj.getFullYear()
            $('.event_datas').find('.data_start').html(startDate);
            $('.event_datas').find('.data_end').html("");
          }else{
            $('.event_datas').find('.data_start').html("From - "+startDate);
            $('.event_datas').find('.data_end').html(" To - "+endDate);
          }
        },
        editable: false,
        dayMaxEvents: true, // allow "more" link when too many events
        events: events,
        eventContent: function(arg, createElement){

          var content = "<span style='font-size: 18px'>"+arg.event._def.title+"</span><br>";

          var subactivities = arg.event._def.extendedProps.subactivities;
          var activity = arg.event._def.extendedProps.activity;

          if( subactivities ){
            content += "<ul>";
            if( activity ){
              content += "<li style='font-size: 15px'><b>"+activity.name+"</b></li>"
            }
            subactivities.forEach(( subactity, i) => {
              content +=
                `<li> <b> - `
                  +subactity.name+
                `</b> </li>`;
            });
            content += "</ul>";
          }

          return { html: content };
        }
      });

      $(document).on("click", "#delete_event", function () {

        $('#event_show').modal('close');

        Swal.fire({
          title: 'Are you sure you want to delete this event?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            var eventId = currentGlobalArg.event._def.publicId;
            var token   = $("input[name='_token']").val();

            var params = {
              event_id: eventId,
              _token: token
            }

            $.ajax({
              type: "POST",
              data: params,
              url: "/events/remove",
              success: function ( response ) {
                currentGlobalArg.event.remove();
                Swal.fire({
                  icon: 'success',
                  title: 'Your event has been removed',
                  showConfirmButton: false,
                  timer: 3000
                })
              },
              error: function (request, textStatus, errorThrown) {
                $('#event_add').modal('close');
                Swal.fire({
                  icon: 'error',
                  title: 'Your event has not been removed',
                  showConfirmButton: false,
                  timer: 3000
                })
              },
            });
          }
        });

      });

      $(document).on("click", "#go_edit_event", function () {
        $('#event_show').modal('close');
        $('#event_edit').modal('open');

        clearModal( "edit" );

        $("#description_edit").val(currentGlobalArg.event._def.extendedProps.description);
        $("#description_edit").focus();

        $("#all_day_edit").val(currentGlobalArg.event._def.allDay);

        $("#title_edit").val(currentGlobalArg.event._def.title);
        $("#title_edit").focus();

        if ( currentGlobalArg.event._def.extendedProps.activity !== false ) {
            $("#activity_edit").val(currentGlobalArg.event._def.extendedProps.activity.id).trigger("change");
            $("#activity_edit").formSelect();
        }
        if ( currentGlobalArg.event._def.extendedProps.activity !== false ) {
            $("#activity_edit").val(currentGlobalArg.event._def.extendedProps.activity.id).trigger("change");
            $("#activity_edit").formSelect();

            if ( currentGlobalArg.event._def.extendedProps.subactivities !== false ) {
                var valueArray = [];
                currentGlobalArg.event._def.extendedProps.subactivities.forEach((item, i) => {
                    valueArray.push( item.id );
                    $("#subactivities_edit").val(valueArray).trigger("change");
                    $("#subactivities_edit").formSelect();
                });
            }
        }

        var startDateObj = new Date( currentGlobalArg.event._def.extendedProps.start_date );
        var endDateObj   = new Date( currentGlobalArg.event._def.extendedProps.end_date );

        var startDateEdit = ("0" + startDateObj.getDate() ).slice(-2) + "/" + ("0" + ( startDateObj.getMonth() + 1) ).slice(-2) + "/" + startDateObj.getFullYear() + " " + ("0" + startDateObj.getHours() ).slice(-2) + ":" + ("0" + startDateObj.getMinutes() ).slice(-2);
        var endDateEdit   = ("0" + endDateObj.getDate() ).slice(-2) + "/" + ("0" + ( endDateObj.getMonth() + 1) ).slice(-2) + "/" + endDateObj.getFullYear() + " " + ("0" + endDateObj.getHours() ).slice(-2) + ":" + ("0" + endDateObj.getMinutes() ).slice(-2);

        if ( currentGlobalArg.event._def.allDay ){
          $("#start_date_edit").attr("type", "hidden");
          $("#end_date_edit").attr("type", "hidden");

          $(".dateslabel").hide();

          $("#start_date_edit").val(startDateEdit);
          $("#end_date_edit").val(endDateEdit);
        }else{
          $(".dateslabel").show();

          // $("#start_date_edit").attr("type", "datetime-local");
          // $("#end_date_edit").attr("type", "datetime-local");
          $("#start_date_edit").attr("type", "text");
          $("#end_date_edit").attr("type", "text");

          $("#end_date_edit").val(endDateEdit);
          $("#end_date_edit").focus();
          $("#start_date_edit").val(startDateEdit);
          $("#start_date_edit").focus();
        }

        var users = currentGlobalArg.event._def.extendedProps.users.split(",");

        $.each(users, function(i,e){
            $("#users_edit option[value='" + e + "']").prop("selected", true);
            $('#users_edit').formSelect();
        });

        var groups = currentGlobalArg.event._def.extendedProps.groups.split(",");

        $.each(groups, function(i,e){
            $("#users_groups_edit option[value='" + e + "']").prop("selected", true);
            $('#users_groups_edit').formSelect();
        });

      });

      $(document).on("click", "#add_event", function () {
        var start  = $("#start_date").val();
        var end    = $("#end_date").val();
        var allDay = $("#all_day").val();
        var title  = $("#title").val();
        var users  = $("#users").val();
        var groups = $("#users_groups").val();
        var description  = $("#description").val();

        var activity      = $("#activity").val();
        var subactivities = $("#subactivities").val();

        var token  = $("input[name='_token']").val();

        start = manageDate(start);
        end   = manageDate(end);

        if ( title !== "" ){
          var params = {
            calendar_id:{{$calendar->id}},
            title: title,
            users: users,
            groups: groups,
            start: start,
            notes: description,
            end: end,
            all_day: allDay,
            activity: activity,
            subactivities: subactivities,
            _token: token
          }

          $.ajax({
            type: "POST",
            data: params,
            url: "/events/create",
            success: function ( response ) {
              $('#event_add').modal('close');

              var activity = false;
              if ( response.activity !== false ) {
                activity = {
                  id:   response.activity.id,
                  name: response.activity.name
                };
              }

              var subactivities = false;
              if ( response.subactivities !== false ) {
                if ( subactivities === false ) {
                  subactivities = [];
                }
                response.subactivities.forEach((item, i) => {
                  subactivities.push({
                    id: item.id,
                    name: item.name
                  });
                });
              }

              if (response.title){
                calendar.addEvent({
                  id:     response.event,
                  title:  response.title,
                  start:  response.start,
                  end:    response.end,
                  description: response.notes,
                  start_date: response.start_date,
                  end_date: response.end_date,
                  users: response.users,
                  groups: response.groups,
                  activity: response.activity,
                  subactivities: response.subactivities
                })
              }
              Swal.fire({
                icon: 'success',
                title: 'Your event has been saved',
                showConfirmButton: false,
                timer: 3000
              });
              calendar.unselect();
            },
            error: function (request, textStatus, errorThrown) {
              $('#event_add').modal('close');
              Swal.fire({
                icon: 'error',
                title: 'Your event has not been saved',
                showConfirmButton: false,
                timer: 3000
              })
            }
          })

        }else{
          Swal.fire({
            icon: 'error',
            title: 'Enter title',
            showConfirmButton: false,
            timer: 3000
          });
        }

      });

      $(document).on("click", "#edit_event", function () {

        var start  = $("#start_date_edit").val();
        var end    = $("#end_date_edit").val();
        var title  = $("#title_edit").val();
        var users  = $("#users_edit").val();
        var groups = $("#users_groups_edit").val();
        var description  = $("#description_edit").val();
        var token  = $("input[name='_token']").val();

        var activity      = $("#activity_edit").val();
        var subactivities = $("#subactivities_edit").val();

        start = manageDate( start );
        end   = manageDate( end );

        if ( title !== "" ){
          var params = {
            calendar_id: {{$calendar->id}},
            event_id: currentGlobalArg.event._def.publicId,
            title: title,
            users: users,
            groups: groups,
            start: start,
            notes: description,
            end: end,
            activity: activity,
            subactivities: subactivities,
            _token: token
          }

          $.ajax({
            type: "POST",
            data: params,
            url: "/events/edit",
            success: function ( response ) {
              $('#event_edit').modal('close');

              var activity = false;
              if ( response.activity !== false ) {
                activity = {
                  id:   response.activity.id,
                  name: response.activity.name
                };
              }

              var subactivities = false;
              if ( response.subactivities !== false ) {
                if ( subactivities === false ) {
                  subactivities = [];
                }
                response.subactivities.forEach((item, i) => {
                  subactivities.push({
                    id: item.id,
                    name: item.name
                  });
                });
              }

              if (response.title){
                currentGlobalArg.event.remove();
                calendar.addEvent({
                  id:     response.event,
                  title:  response.title,
                  start:  response.start,
                  end:    response.end,
                  description: response.notes,
                  start_date: response.start_date,
                  end_date: response.end_date,
                  users: response.users,
                  groups: response.groups,
                  activity: response.activity,
                  subactivities: response.subactivities
                })
              }
              Swal.fire({
                icon: 'success',
                title: 'Your event has been edited',
                showConfirmButton: false,
                timer: 3000
              });
              calendar.unselect();
            },
            error: function (request, textStatus, errorThrown) {
              $('#event_edit').modal('close');
              Swal.fire({
                icon: 'error',
                title: 'Your event has not been edited',
                showConfirmButton: false,
                timer: 3000
              })
            }
          })

        }else{
          Swal.fire({
            icon: 'error',
            title: 'Enter title',
            showConfirmButton: false,
            timer: 3000
          });
        }

      });

      calendar.render();

    });

    function clearModal( type ){

      if ( type == "create" ) {
          $("#activity").val("").trigger("change");
          $("#title").val("");
          $("#users").val("").trigger("change");
          $("#users_groups").val("").trigger("change");
          $(".select-dropdown").val("");
          $("#description").val("").trigger("change");
          $("#start_date").val("").trigger("change");
          $("#end_date").val("").trigger("change");
          $("#subactivities_container").html("");
      }

      /* Edit modal*/
      if ( type == "edit" ) {
        $("#activity_edit").val("").trigger("change");
        $("#title_edit").val("");
        $("#users_edit").val("").trigger("change");
        $("#users_groups_edit").val("").trigger("change");
        $(".select-dropdown").val("");
        $("#description_edit").val("").trigger("change");
        $("#start_date_edit").val("").trigger("change");
        $("#end_date_edit").val("").trigger("change");
        $("#subactivities_container_edit").html("");
      }

    }

    function manageDate( date ){

      var bigparts   = date.split(" ");
      var smallparts = bigparts[0].split("/");

      var formattedDate = smallparts[2]+"/"+smallparts[1]+"/"+smallparts[0]+" "+bigparts[1];

      var hour = (new Date( Date.parse( formattedDate ) )).getTimezoneOffset() * 60000; /* 1 hour offset */
      formattedDate = new Date( Date.parse( formattedDate ) - hour ).toISOString().substring(0, 16);

      console.log( formattedDate );

      return formattedDate;

    }

    </script>
    <script src="{{ asset('js/calendar.js') }}"></script>
    <script src="{{ asset('js/export-calendars.js') }}"></script>
@endsection
