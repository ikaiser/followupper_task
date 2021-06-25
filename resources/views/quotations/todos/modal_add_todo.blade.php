<div id="todo_add" class="modal">

  <form id="todo_add_form" action="{{route('todos.create')}}" method="post">

    <div class="modal-content">
        <h5 class="modal-title"> @lang('Add todo')</h5>

        @csrf

        <input type="hidden" name="end_date" id="end_date">

        <div class="row">
          <div class="col l12 s12">
            <div class="select-field my-3">
              <label for="start_date" class="dateslabel"> @lang('Date') </label>
              <!-- todo_start_date_datetimepicker -->
              <input type="text" name="start_date" id="start_date" readonly>
            </div>
          </div>

          <div class="col l12 s12">
            <div class="input-field my-3">
              <select name="todo_quotation" id="todo_quotation">
                  <option value="" hidden selected> @lang('Select a quotation') </option>
                  @foreach($quotationAll as $quotationSingle)
                      <option class="select2" value="{{$quotationSingle->id}}">{{$quotationSingle->name}}</option>
                  @endforeach
              </select>
              <label for="quotations"> @lang('Quotations') </label>
            </div>
          </div>
          <div class="col l12 s12">
            @if( $user->hasRole("SuperAdmin") )
              <div class="input-field my-3">
                <select name="todo_user" id="todo_user">
                    <option value="" hidden selected> @lang('Select a user') </option>
                    @foreach($userAll as $userSingle)
                        @if( $userSingle->hasRole("SuperAdmin") ) @continue @endif
                        <option value="{{$userSingle->id}}">{{$userSingle->name}} ( {{$userSingle->roles->first()->name}} )</option>
                    @endforeach
                </select>
                <label for="researcher"> @lang('Researcher') </label>
              </div>
            @else
              <input type="hidden" name="todo_user" id="todo_user" value="{{$user->id}}">
            @endif
          </div>
          <div class="col l12 s12">
            <div class="input-field my-3">
              <select name="todo_activity" id="todo_activity">
                  <option value="" disabled hidden selected> @lang('Select an activity') </option>
                  @foreach($activities as $activity)
                      <option value="{{$activity->id}}">{{$activity->name}}</option>
                  @endforeach
              </select>
              <label for="activity"> @lang('Activity') </label>
            </div>
          </div>
          <div class="col l12 s12">
            <div class="input-field my-3">
                <label for="title"> @lang('TODO title') </label>
                <input type="text" id="todo_title" name="todo_title">
            </div>
          </div>
          <div class="col l12 s12">
            <div class="input-field my-3">
                <label for="description"> @lang('TODO description') </label>
                <textarea id="todo_description" name="todo_description" class="materialize-textarea"></textarea>
            </div>
          </div>
        </div>

    </div>
    <div class="divider"></div>
    <div class="modal-footer">
        <a href="#" class="modal-close btn waves-effect waves-light red"> @lang('Cancel') </a>
        <button type="button" id="todo_add_btn" class="btn waves-effect waves-light blue" data-id="0"> @lang('Add TODO') </button>
    </div>

  </form>

</div>
