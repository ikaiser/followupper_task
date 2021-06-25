<div id="user_{{$wk}}_{{$todo->user_id}}_{{$todo->id}}_edit_modal" class="modal">

  <form class="todo_edit_form" action="{{route('todos.edit',$todo->id)}}" method="post">

    <div class="modal-content">
        <h5 class="modal-title"> @lang('Edit todo')</h5>

        @csrf

        <input type="hidden" name="todo_id"  class="todo_id_edit" value="{{$todo->id}}">
        <input type="hidden" name="end_date" class="end_date_edit" value="{{date("d-m-Y",strtotime($todo->end_date))}}">

        <div class="row">
          <div class="col l12 s12">
            <div class="select-field my-3">
              <label for="start_date" class="dateslabel"> @lang('Date') </label>
              <input type="text" name="start_date" class="start_date_edit" value="{{date("d-m-Y",strtotime($todo->start_date))}}" readonly>
            </div>
          </div>

          <div class="col l12 s12">
            <div class="input-field my-3">
              <select name="todo_quotation" class="todo_quotation_edit">
                  <option value="" hidden selected> @lang('Select a quotation') </option>
                  @foreach($quotationAll as $quotationSingle)
                      <option class="select2" value="{{$quotationSingle->id}}" @if($todo->quotation_id == $quotationSingle->id) selected @endif>{{$quotationSingle->name}}</option>
                  @endforeach
              </select>
              <label for="quotations"> @lang('Quotations') </label>
            </div>
          </div>
          <div class="col l12 s12">
            @if( $user->hasRole("SuperAdmin") )
              <div class="input-field my-3">
                <select name="todo_user" class="todo_user_edit">
                    <option value="" hidden selected> @lang('Select a user') </option>
                    @foreach($userAll as $userSingle)
                        @if( $userSingle->hasRole("SuperAdmin") ) @continue @endif
                        <option value="{{$userSingle->id}}" @if($todo->user_id == $userSingle->id) selected @endif>{{$userSingle->name}} ( {{$userSingle->roles->first()->name}} )</option>
                    @endforeach
                </select>
                <label for="researcher"> @lang('Researcher') </label>
              </div>
            @else
              <input type="hidden" name="todo_user" class="todo_user_edit" value="{{$todo->user_id}}">
            @endif
          </div>
          <div class="col l12 s12">
            <div class="input-field my-3">
              @php
                $selActivity = (!is_null($todo->activities->first())) ? $todo->activities->first() : ""
              @endphp
              <select name="todo_activity" class="todo_activity_edit">
                  <option value="" disabled hidden selected> @lang('Select an activity') </option>
                  @foreach($activities as $activity)
                      <option value="{{$activity->id}}" @if( $selActivity->id == $activity->id) selected @endif>{{$activity->name}}</option>
                  @endforeach
              </select>
              <label for="activity"> @lang('Activity') </label>
            </div>
          </div>
          <div class="col l12 s12">
            <div class="input-field my-3">
                <label for="title"> @lang('TODO title') </label>
                <input type="text" class="todo_title_edit" name="todo_title" value="{{$todo->title}}">
            </div>
          </div>
          <div class="col l12 s12">
            <div class="input-field my-3">
                <label for="description"> @lang('TODO description') </label>
                <textarea class="materialize-textarea todo_description_edit" name="todo_description">{{$todo->description}}</textarea>
            </div>
          </div>
          <div class="col l12 s12">
            <p class="my-1">
                <label>
                    <input type="checkbox" name="todo_completed" class="todo_completed" value="1" @if($todo->completed) checked @endif />
                    <span> @lang('Sign as completed') </span>
                </label>
            </p>
          </div>
        </div>

    </div>
    <div class="divider"></div>
    <div class="modal-footer">
        <a href="#" class="modal-close btn waves-effect waves-light orange"> @lang('Cancel') </a>
        <a href="{{route('todos.delete', $todo->id)}}" class="modal-close btn waves-effect waves-light red"> @lang('Delete TODO') </a>
        <button type="button" class="btn waves-effect waves-light blue todo_edit_form_submit" data-id="0"> @lang('edit TODO') </button>
    </div>

  </form>

</div>
