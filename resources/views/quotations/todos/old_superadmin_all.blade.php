@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('fullcalendar/lib/main.css') }}">
@endsection

@section('content')

    @include('quotations/todos/modal_export_csv')
    @include('quotations/todos/modal_search_superadmin')

    <!-- Modal event creation -->
    <div id="event_add" class="modal">
        <div class="modal-content">
            <h5 class="modal-title mb-4"> @lang('Add event')</h5>
            <span> @lang('Create your TODO') </span>

            @csrf

            <div class="row">
              <div class="col l6 s12">
                <label for="start_date" class="dateslabel"> @lang('Start date') </label>
                <input type="text" name="start_date" id="start_date" class="start_date_datetimepicker">
              </div>
              <div class="col l6 s12">
                <label for="end_date" class="dateslabel"> @lang('End date') </label>
                <input type="text" name="end_date" id="end_date" class="end_date_datetimepicker">
              </div>
            </div>

            <input type="hidden" name="all_day" id="all_day">

            <div class="input-field my-3">
                <label for="title"> @lang('TODO title') </label>
                <input type="text" id="todo_title" name="todo_title">
            </div>

            <div class="input-field my-3">
                <label for="description"> @lang('TODO description') </label>
                <textarea id="todo_description" name="todo_description" class="materialize-textarea"></textarea>
            </div>

        </div>
        <div class="divider"></div>
        <div class="modal-footer">
            <a href="#" class="modal-close btn waves-effect waves-light red"> @lang('Cancel') </a>
            <button type="button" id="add_event" class="btn waves-effect waves-light blue" data-id="0"> @lang('Add TODO') </button>
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
                <input type="text" name="start_date_edit" id="start_date_edit" class="start_date_datetimepicker_edit">
              </div>
              <div class="col l6 s12">
                <label for="end_date" class="dateslabel"> @lang('End date') </label>
                <input type="text" name="end_date_edit" id="end_date_edit" class="end_date_datetimepicker_edit">
              </div>
            </div>

            <input type="hidden" name="all_day_edit" id="all_day_edit">

            <div class="input-field my-3">
                <label for="title"> @lang('TODO title') </label>
                <input type="text" id="todo_title_edit" name="todo_title_edit">
            </div>
            <div class="input-field my-3">
                <label for="description"> @lang('TODO description') </label>
                <textarea id="todo_description_edit" name="todo_description_edit" class="materialize-textarea"></textarea>
            </div>
        </div>
        <div class="divider"></div>
        <div class="modal-footer">
            <a href="#" class="modal-close btn waves-effect waves-light red"> @lang('Cancel') </a>
            <button type="button" id="edit_event" class="btn waves-effect waves-light blue" data-id="0"> @lang('Edit event') </button>
        </div>
    </div>
    <!-- Modal event edit end -->

    <!-- Modal event show -->
    <div id="event_show" class="modal">
        <div class="modal-content">
            <h4 class="modal-title mb-4"><span class="todo_title"></span></h4>
            <h6> @lang("Event dates") </h6>
            <div class="event_datas">
              <strong> <span class="data_start"></span> <span class="data_end"></span> </strong>
            </div>
            <h6> @lang("Event description") </h6>
            <div class="todo_description"></div>
            <h6> @lang("Event quotation") </h6>
            <div class="todo_quotation"></div>
            <h6> @lang("Event user") </h6>
            <div class="todo_user"></div>
        </div>
        <div class="divider"></div>
        <div class="modal-footer">
            <a href="#" class="modal-close btn waves-effect waves-light blue"> @lang('Cancel') </a>
            @if( Auth::user()->roles->first()->id <= 4 )
              <!-- <button type="button" id="go_edit_event"   class="btn waves-effect waves-light orange" data-id="0"> @lang('Edit event') </button>
              <button type="button" id="delete_event" class="btn waves-effect waves-light red" data-id="0"> @lang('Delete event') </button> -->
            @endif
        </div>
    </div>
    <!-- Modal event show end -->

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('todos.superadmin-all') }}'" class="pointer">/&nbsp;@lang('Show todos')</a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <!-- <button type="button" class="btn btn-floating waves-effect waves-light modal-trigger orange" data-target="todo_export_csv"><i class="material-icons">file_download</i></button> -->
            <button class="btn btn-floating waves-effect waves-light modal-trigger" data-target="search_superadmin_modal"><i class="material-icons">search</i></button>
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="card hoverable">
                <div class="card-content">
                    <span class="card-title ml-2"> @lang('Show todo') </span>
                    <div id='todo'></div>
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
      // var DateFieldStartExport = MaterialDateTimePicker.create($(".start_export_date_datetimepicker"));
      // var DateFieldEndExport   = MaterialDateTimePicker.create($(".end_export_date_datetimepicker"));

      /* Search */
      if ( $(".start_date_datetimepicker_search").length > 0 && $(".end_date_datetimepicker_search").length > 0 ){
        var DateFieldStartExport = MaterialDateTimePicker.create($(".start_date_datetimepicker_search"));
        var DateFieldEndExport   = MaterialDateTimePicker.create($(".end_date_datetimepicker_search"));
      }
    }

    $(document).ready(function () {

      initDateTimePicker();

      var todoEl = document.getElementById('todo');

      var events = [];

      @foreach( $todos as $ke => $event )

          @php $eventAlreadyAdded = false; @endphp

          var eventSingle = {
            id: {{$event->id}},
            title: "{{$event->title}}",
            start: "{{$event->start_date}}",
            end: "{{$event->end_date}}",
            start_date: "{{$event->start_date}}",
            end_date: "{{$event->end_date}}",
            description: "{{$event->description}}",
            quotation_name: "{{$event->quotation->name}}",
            user_name: "{{$event->user->name}} ( {{$event->user->email}} )",
          };

          @if( $user->id == Auth::user()->id && !$eventAlreadyAdded )
            events.push( eventSingle );
            @php $eventAlreadyAdded = true; @endphp
          @endif

      @endforeach

      var calendar = new FullCalendar.Calendar(todoEl, {
        locale: "it",
        slotDuration: '00:30',
        slotLabelFormat: [
        {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }],
        slotLabelInterval: 30,
        headerToolbar:{
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        initialView: 'timeGridWeek',
        initialDate: '{{ date("Y-m-d") }}',
        navLinks: true, // can click day/week names to navigate views
        selectable: true,
        selectMirror: true,
        // select: function(arg) {
        //     clearModal( "create" );
        //
        //     // Readd to string :00+01:00
        //     if (arg.allDay) {
        //       $("#start_date").attr("type", "hidden");
        //       $("#end_date").attr("type", "hidden");
        //
        //       $(".dateslabel").hide();
        //
        //       var startDateObj = new Date( arg.startStr );
        //       var endDateObj   = new Date( arg.endStr );
        //
        //       var startDate = ("0" + startDateObj.getDate() ).slice(-2) + "/" + ("0" + ( startDateObj.getMonth() + 1) ).slice(-2) + "/" + startDateObj.getFullYear() + " " + ("0" + ( endDateObj.getHours() - 2 ) ).slice(-2) + ":" + ("0" + startDateObj.getMinutes() ).slice(-2);
        //       var endDate   = ("0" + endDateObj.getDate() ).slice(-2) + "/" + ("0" + ( endDateObj.getMonth() + 1) ).slice(-2) + "/" + endDateObj.getFullYear() + " " + ("0" + ( endDateObj.getHours() - 2 ) ).slice(-2) + ":" + ("0" + endDateObj.getMinutes() ).slice(-2);
        //
        //       $("#start_date").val( startDate );
        //       $("#end_date").val(  endDate );
        //
        //     }else{
        //       var start = arg.startStr.substring(0, 16);
        //       var end   = arg.endStr.substring(0, 16);
        //
        //       $(".dateslabel").show();
        //
        //       $("#start_date").attr("type", "text");
        //       $("#end_date").attr("type", "text");
        //
        //       /* Re-init */
        //       initDateTimePicker();
        //
        //       var startDateObj = new Date( start );
        //       var endDateObj   = new Date( end );
        //
        //       var startDate = ("0" + startDateObj.getDate() ).slice(-2) + "/" + ("0" + ( startDateObj.getMonth() + 1) ).slice(-2) + "/" + startDateObj.getFullYear() + " " + ("0" + startDateObj.getHours() ).slice(-2) + ":" + ("0" + startDateObj.getMinutes() ).slice(-2);
        //       var endDate   = ("0" + endDateObj.getDate() ).slice(-2) + "/" + ("0" + ( endDateObj.getMonth() + 1) ).slice(-2) + "/" + endDateObj.getFullYear() + " " + ("0" + endDateObj.getHours() ).slice(-2) + ":" + ("0" + endDateObj.getMinutes() ).slice(-2);
        //
        //       $("#start_date").val(startDate);
        //       $("#end_date").val(endDate);
        //     }
        //
        //     $("#all_day").val(arg.allDay);
        //     $('#event_add').modal('open');
        // },
        eventClick: function(arg) {
          /* For editing and deleting */
          currentGlobalArg = arg;

          $('#event_show').modal('open');

          /* Set the view datas */
          $('.todo_title').html(arg.event._def.title);
          $('.todo_description').html(arg.event._def.extendedProps.description);
          $('.todo_quotation').html(arg.event._def.extendedProps.quotation_name);
          $('.todo_user').html(arg.event._def.extendedProps.user_name);

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
          return { html: content };
        }
      });

      // $(document).on("click", "#delete_event", function () {
      //
      //   $('#event_show').modal('close');
      //
      //   Swal.fire({
      //     title: 'Are you sure you want to delete this event?',
      //     text: "You won't be able to revert this!",
      //     icon: 'warning',
      //     showCancelButton: true,
      //     confirmButtonColor: '#3085d6',
      //     cancelButtonColor: '#d33',
      //     confirmButtonText: 'Yes, delete it!'
      //   }).then((result) => {
      //     if (result.isConfirmed) {
      //       var eventId = currentGlobalArg.event._def.publicId;
      //       var token   = $("input[name='_token']").val();
      //
      //       var params = {
      //         _token: token
      //       }
      //
      //       $.ajax({
      //         type: "POST",
      //         data: params,
      //         url: "/todos/delete/"+eventId,
      //         success: function ( response ) {
      //           if ( response.status == "success" ) {
      //             currentGlobalArg.event.remove();
      //             Swal.fire({
      //               icon: 'success',
      //               title: 'Your event has been removed',
      //               showConfirmButton: false,
      //               timer: 3000
      //             })
      //           }else{
      //             $('#event_add').modal('close');
      //             Swal.fire({
      //               icon: 'error',
      //               title: 'Your event has not been removed',
      //               showConfirmButton: false,
      //               timer: 3000
      //             })
      //           }
      //         },
      //         error: function (request, textStatus, errorThrown) {
      //           $('#event_add').modal('close');
      //           Swal.fire({
      //             icon: 'error',
      //             title: 'Your event has not been removed',
      //             showConfirmButton: false,
      //             timer: 3000
      //           })
      //         },
      //       });
      //     }
      //   });
      //
      // });

      // $(document).on("click", "#go_edit_event", function () {
      //   $('#event_show').modal('close');
      //   $('#event_edit').modal('open');
      //
      //   clearModal( "edit" );
      //
      //   $("#todo_description_edit").val(currentGlobalArg.event._def.extendedProps.description);
      //   $("#todo_description_edit").focus();
      //
      //   $("#all_day_edit").val(currentGlobalArg.event._def.allDay);
      //
      //   $("#todo_title_edit").val(currentGlobalArg.event._def.title);
      //   $("#todo_title_edit").focus();
      //
      //   var startDateObj = new Date( currentGlobalArg.event._def.extendedProps.start_date );
      //   var endDateObj   = new Date( currentGlobalArg.event._def.extendedProps.end_date );
      //
      //   var startDateEdit = ("0" + startDateObj.getDate() ).slice(-2) + "/" + ("0" + ( startDateObj.getMonth() + 1) ).slice(-2) + "/" + startDateObj.getFullYear() + " " + ("0" + startDateObj.getHours() ).slice(-2) + ":" + ("0" + startDateObj.getMinutes() ).slice(-2);
      //   var endDateEdit   = ("0" + endDateObj.getDate() ).slice(-2) + "/" + ("0" + ( endDateObj.getMonth() + 1) ).slice(-2) + "/" + endDateObj.getFullYear() + " " + ("0" + endDateObj.getHours() ).slice(-2) + ":" + ("0" + endDateObj.getMinutes() ).slice(-2);
      //
      //   if ( currentGlobalArg.event._def.allDay ){
      //     $("#start_date_edit").attr("type", "hidden");
      //     $("#end_date_edit").attr("type", "hidden");
      //
      //     $(".dateslabel").hide();
      //
      //     $("#start_date_edit").val(startDateEdit);
      //     $("#end_date_edit").val(endDateEdit);
      //   }else{
      //     $(".dateslabel").show();
      //
      //     $("#start_date_edit").attr("type", "text");
      //     $("#end_date_edit").attr("type", "text");
      //
      //     $("#end_date_edit").val(endDateEdit);
      //     $("#end_date_edit").focus();
      //     $("#start_date_edit").val(startDateEdit);
      //     $("#start_date_edit").focus();
      //   }
      // });

      $(document).on("click", "#add_event", function () {
        var start  = $("#start_date").val();
        var end    = $("#end_date").val();
        var title  = $("#todo_title").val();
        var description  = $("#todo_description").val();

        var allDay = $("#all_day").val();

        var token  = $("input[name='_token']").val();

        start = manageDate(start);
        end   = manageDate(end);

        if ( title !== "" ){
          // quotation: $quotation->id,
          var params = {
            title: title,
            description: description,
            start: start,
            end: end,
            all_day: allDay,
            _token: token
          }

          $.ajax({
            type: "POST",
            data: params,
            url: "/todos/create",
            success: function ( response ) {
              $('#event_add').modal('close');

              if (response.title){
                calendar.addEvent({
                  id:     response.event,
                  title:  response.title,
                  start:  response.start,
                  end:    response.end,
                  start_date:  response.start,
                  end_date:    response.end,
                  description: response.description,
                })
              }
              Swal.fire({
                icon: 'success',
                title: 'Your todo has been saved',
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
        var title  = $("#todo_title_edit").val();
        var description = $("#todo_description_edit").val();
        var token  = $("input[name='_token']").val();
        var allDay = $("#all_day_edit").val();

        start = manageDate( start );
        end   = manageDate( end );

        if ( title !== "" ){
          // quotation: $quotation->id,
          var params = {
            title: title,
            description: description,
            start: start,
            end: end,
            all_day: allDay,
            _token: token
          }

          $.ajax({
            type: "POST",
            data: params,
            url: "/todos/edit/"+currentGlobalArg.event._def.publicId,
            success: function ( response ){
              $('#event_edit').modal('close');

              if (response.title){
                currentGlobalArg.event.remove();
                calendar.addEvent({
                  id:     response.event,
                  title:  response.title,
                  start:  response.start,
                  end:    response.end,
                  description: response.description,
                })
              }
              Swal.fire({
                icon: 'success',
                title: 'Your todo has been edited',
                showConfirmButton: false,
                timer: 3000
              });
              calendar.unselect();
            },
            error: function (request, textStatus, errorThrown){
              $('#event_edit').modal('close');
              Swal.fire({
                icon: 'error',
                title: 'Your todo has not been edited',
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
          $("#todo_title").val("");
          $("#todo_description").val("").trigger("change");
          $("#start_date").val("").trigger("change");
          $("#end_date").val("").trigger("change");
      }

      /* Edit modal*/
      if ( type == "edit" ) {
        $("#todo_title_edit").val("");
        $("#todo_description_edit").val("").trigger("change");
        $("#start_date_edit").val("").trigger("change");
        $("#end_date_edit").val("").trigger("change");
      }

    }

    function manageDate( date ){

      var bigparts   = date.split(" ");
      var smallparts = bigparts[0].split("/");

      var formattedDate = smallparts[2]+"/"+smallparts[1]+"/"+smallparts[0]+" "+bigparts[1];

      var hour = (new Date( Date.parse( formattedDate ) )).getTimezoneOffset() * 60000; /* 2 hour offset ( if needed ) */
      formattedDate = new Date( Date.parse( formattedDate ) - hour ).toISOString().substring(0, 16);

      return formattedDate;
    }

    </script>
    <!-- <script src="{{ asset('js/export-calendars.js') }}"></script> -->
@endsection
