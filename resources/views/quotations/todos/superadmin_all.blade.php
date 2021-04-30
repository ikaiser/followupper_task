@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('fullcalendar/lib/main.css') }}">
@endsection

@section('content')

    @include('quotations/todos/modal_export_csv')
    @include('quotations/todos/modal_search_superadmin')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('todos.superadmin-all') }}'" class="pointer">/&nbsp;@lang('Show todos')  - @lang("Starting year"): <b>{{ $search["search_start_year"] }}</b> - @lang("Starting week"): <b>{{ $search["search_start_week"] }}</b>  </a>
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
                    <table class="striped highlight">
                      <thead class="grey white-text">
                        <tr>
                          <th> &darr; @lang("Users") | @lang("Weeks") &rarr; </th>
                          <!-- List 4 week from now -->
                          @foreach( $weeksArray as $key => $week )
                            <th> {{ $week["label"] }}</th>
                          @endforeach
                        </tr>
                      </thead>
                      <tbody>
                          <!-- List all users with todo from now to weeks -->
                          @foreach( $usersTodoArray as $key => $userTodoArray )
                            <tr>
                              <td> {{ $userTodoArray["user"]->name }} </td>

                              @foreach( $userTodoArray["todos"] as $wk => $userTodoInWeek )
                                <td>
                                  <ul>
                                    @foreach( $userTodoInWeek["todos"] as $todoId => $userTodo )
                                      @if($userTodo !== "")
                                        <li>

                                          <div class="pointer tippy-tooltip" data-template="user_{{$wk}}_{{$userTodoArray['user']->id}}_{{$userTodo->id}}">
                                            - {{$userTodo->title}}
                                          </div>

                                          <!-- Tooltip -->
                                          <div style="display: none;" id="user_{{$wk}}_{{$userTodoArray['user']->id}}_{{$userTodo->id}}">
                                            <div>
                                              @lang("TITLE"): {{$userTodo->title}}
                                            </div>
                                            <div>
                                              @lang("DESCRIPTION"): {{$userTodo->description}}
                                            </div>
                                            <div>
                                              @lang("Start"): {{ date( "d/m/Y H:i" , strtotime( $userTodo->start_date ) ) }} @lang("End"): {{ date( "d/m/Y H:i" ,strtotime( $userTodo->end_date ) ) }}
                                            </div>
                                          </div>

                                        </li>
                                      @endif
                                    @endforeach
                                  </ul>
                                </td>
                              @endforeach
                            </tr>
                          @endforeach
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="{{ asset('templates/js/materializedatetimepicker.js') }}"></script>

    <!-- Tippy -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>

    <script>

    function initDateTimePicker(){
      M.AutoInit();
      /* Search */
      if ( $(".start_date_datetimepicker_search").length > 0 ){
        // var DateFieldStartExport = MaterialDateTimePicker.create($(".start_date_datetimepicker_search"));
        $('.start_date_datetimepicker_search').datepicker({
          format: 'dd-mm-yyyy',
          defaultDate: new Date('{{ date( "Y-m-d" ,strtotime($search["search_start_date"])) }}'),
          setDefaultDate: true
        });
      }
    }

    $(document).ready(function () {

      tippy('.tippy-tooltip', {
        content(reference){
          var id       = reference.getAttribute('data-template');
          var template = document.getElementById(id);
          return template.innerHTML;
        },
        allowHTML: true,
      });

      initDateTimePicker();

    });

    </script>
@endsection
