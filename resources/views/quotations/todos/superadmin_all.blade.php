@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('fullcalendar/lib/main.css') }}">
@endsection

@section('content')

    @include('quotations/todos/modal_export_csv')
    @include('quotations/todos/modal_search_superadmin')
    @include('quotations/todos/modal_add_todo')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('todos.superadmin-all') }}'" class="pointer">/&nbsp;@lang('Show todos')  - @lang("Filtered day"): <b>{{ date( "d/m/Y", strtotime($search["search_start_date"]) ) }}</b> </a>
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

                    @if ($message = Session::get('success'))
                      <div class="card-alert card green lighten-5">
                          <div class="card-content green-text">
                              <p>{{ $message }}</p>
                          </div>
                          <button type="button" class="close green-text" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">×</span>
                          </button>
                      </div>
                    @endif

                    <div class="row valign-wrapper">
                      <div class="col l8 s12">
                        <blockquote>
                           * @lang("DoubleClick the cell in corrispondece of user and quotation for add a todo in table") <br/>
                           * @lang("Click one time the todo to open the edit window")
                        </blockquote>
                      </div>
                      <div class="col l4 s12 mb-2 right-align">
                        @php
                          $plusOneWeek   = strtotime( '+1 week', strtotime($search["search_start_date"]) );
                          $minusOneWeek  = strtotime( '-1 week', strtotime($search["search_start_date"]) );

                          if( date('D', $plusOneWeek) === 'Mon' && date('D', $minusOneWeek) === 'Mon' ) {
                            $nextMonday    = date( "d-m-Y",$plusOneWeek);
                            $prevMonday    = date( "d-m-Y",$minusOneWeek);
                          }else{
                            $nextMonday    = date( "d-m-Y",strtotime( 'last monday', $plusOneWeek ));
                            $prevMonday    = date( "d-m-Y",strtotime( 'last monday', $minusOneWeek ));
                          }

                          $prevSearchLink = $search;
                          $nextSearchLink = $search;
                          $prevSearchLink["search_start_date"] = $prevMonday;
                          $nextSearchLink["search_start_date"] = $nextMonday;

                        @endphp
                        <a href="{{route("todos.superadmin-all",$prevSearchLink)}}" class="btn btn-floating waves-effect waves-light"><i class="material-icons">arrow_back</i></a>
                        <a href="{{route("todos.superadmin-all",$nextSearchLink)}}" class="btn btn-floating waves-effect waves-light"><i class="material-icons">arrow_forward</i></a>
                      </div>
                    </div>

                    <table class="striped highlight bordered responsive-table scrollable-table" id="todos_table">
                      <thead class="blue white-text">
                        <tr>
                          <th> @lang("Users") &darr; </th>
                          <th> @lang("Quotation") &darr; | @lang("Days") &rarr; </th>

                          <!-- List 7 days from last monday -->
                          @foreach( $daysArray as $key => $day )
                            <th data-date="{{ date( "d-m-Y", strtotime($day["date"]) ) }}"> {{ $day["label"] }} </th>
                          @endforeach
                        </tr>
                      </thead>
                      <tbody>
                          <!-- List all users with todo from now to weeks -->
                          @if($search["order_by"] == "user")
                            @foreach( $usersTodoArray as $key => $userTodoArray )
                              @foreach( $userTodoArray["quotation_todos"] as $pk => $userProjectTodos )

                                <tr>

                                    <!-- Foreach quotation get user name and user todo -->
                                    <td> {{ $userTodoArray["user"]->name }} </td>
                                    <td> {{$userProjectTodos["quotation"]->name}} ( {{get_code($userProjectTodos["quotation"])}} ) </td>

                                    @foreach( $daysArray as $key => $day )
                                      <td class="pointer add-todo-element" data-date="{{ date( "d-m-Y", strtotime($day["date"]) ) }}" data-quotation="{{$userProjectTodos["quotation"]->id}}" data-quotation-name="{{$userProjectTodos["quotation"]->name}}( {{get_code($userProjectTodos["quotation"])}} )" data-user="{{$userTodoArray["user"]->id}}" data-user-name="{{$userTodoArray["user"]->name}}">
                                        <ul>

                                          <!-- Foreach todo in project for this user -->
                                          @foreach( $userProjectTodos["todos"] as $wk => $todoArr )

                                            @foreach( $todoArr as $todoId => $todo )

                                              @if($wk == $day["date"])
                                                <li>

                                                  <div class="tippy-tooltip @if($todo->completed) green-text @else red-text @endif" data-template="user_{{$wk}}_{{$userTodoArray["user"]->id}}_{{$todo->id}}">
                                                    <a href="#!" class="edit-todo-element @if($todo->completed) green-text @else red-text @endif" data-target="user_{{$wk}}_{{$userTodoArray["user"]->id}}_{{$todo->id}}_edit_modal">
                                                      @php
                                                        $activityName = (!is_null($todo->activities->first())) ? $todo->activities->first()->name : __("No activity")
                                                      @endphp
                                                      {{$activityName}}
                                                    </a>
                                                  </div>

                                                  <!-- Tooltip -->
                                                  <div style="display: none;" id="user_{{$wk}}_{{$userTodoArray["user"]->id}}_{{$todo->id}}">
                                                    <div>
                                                      @lang("ATTIVITA'"): {{$activityName}}
                                                    </div>
                                                    <div>
                                                      @lang("Date"): {{ date( "d/m/Y" , strtotime( $todo->start_date ) ) }}
                                                    </div>
                                                    <div>
                                                      @lang("Completed"): @if($todo->completed) @lang("Yes") @else @lang("No") @endif
                                                    </div>
                                                    <div>
                                                      @lang("DESCRIPTION"): {{$todo->description}}
                                                    </div>
                                                  </div>

                                                  <!-- Modal for editing -->
                                                  @include('quotations/todos/modal_edit_todo')
                                                  <!-- Modal for editing -->

                                                </li>
                                              @else
                                                <li>
                                                </li>
                                              @endif

                                            @endforeach

                                          @endforeach

                                        </ul>
                                      </td>
                                    @endforeach

                                </tr>
                              @endforeach
                            @endforeach
                          @else
                            @foreach( $quotationsTodoArray as $qid => $quotationTodoArray )
                              @foreach( $quotationTodoArray["user_todos"] as $uk => $projectUserTodos )

                                <tr>

                                    <!-- Foreach quotation get user name and user todo -->
                                    <td> {{$projectUserTodos["user"]->name}} </td>
                                    <td> {{$quotationTodoArray["quotation"]->name}} ( {{get_code($quotationTodoArray["quotation"])}} ) </td>

                                    @foreach( $daysArray as $key => $day )
                                      <td class="pointer add-todo-element" data-date="{{ date( "d-m-Y", strtotime($day["date"]) ) }}" data-quotation="{{$quotationTodoArray["quotation"]->id}}" data-quotation-name="{{$quotationTodoArray["quotation"]->name}} ( {{get_code($quotationTodoArray["quotation"])}} )" data-user="{{$projectUserTodos["user"]->id}}" data-user-name="{{$projectUserTodos["user"]->name}}">
                                        <ul>

                                          <!-- Foreach todo in project for this user -->
                                          @foreach( $projectUserTodos["todos"] as $wk => $todoArr )

                                            @foreach( $todoArr as $todoId => $todo )

                                              @if($wk == $day["date"])
                                                <li>

                                                  <div class="pointer tippy-tooltip @if($todo->completed) green-text @else red-text @endif" data-template="user_{{$wk}}_{{$projectUserTodos["user"]->id}}_{{$todo->id}}">
                                                    <a href="#" class="modal-trigger @if($todo->completed) green-text @else red-text @endif" data-target="user_{{$wk}}_{{$projectUserTodos["user"]->id}}_{{$todo->id}}_edit_modal">
                                                      @php
                                                        $activityName = (!is_null($todo->activities->first())) ? $todo->activities->first()->name : __("No activity")
                                                      @endphp
                                                      - {{$activityName}}
                                                    </a>
                                                  </div>

                                                  <!-- Tooltip -->
                                                  <div style="display: none;" id="user_{{$wk}}_{{$projectUserTodos["user"]->id}}_{{$todo->id}}">
                                                    <div>
                                                      @lang("ATTIVITA'"): {{$activityName}}
                                                    </div>
                                                    <div>
                                                      @lang("Date"): {{ date( "d/m/Y" , strtotime( $todo->start_date ) ) }}
                                                    </div>
                                                    <div>
                                                      @lang("Completed"): @if($todo->completed) @lang("Yes") @else @lang("No") @endif
                                                    </div>
                                                    <div>
                                                      @lang("DESCRIPTION"): {{$todo->description}}
                                                    </div>
                                                  </div>

                                                  <!-- Modal for editing -->
                                                  @include('quotations/todos/modal_edit_todo')
                                                  <!-- Modal for editing -->

                                                </li>
                                              @else
                                                <li>
                                                </li>
                                              @endif

                                            @endforeach

                                          @endforeach

                                        </ul>
                                      </td>
                                    @endforeach

                                </tr>
                              @endforeach
                            @endforeach
                          @endif
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

    <!-- Custom todo.js -->
    <script src="{{ asset('js/todo.js') }}"></script>
@endsection
