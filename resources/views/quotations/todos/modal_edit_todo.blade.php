<div id="user_{{$wk}}_{{$todo->user_id}}_{{$todo->id}}_edit_modal" class="modal">

  <form class="todo_edit_form" action="{{route('todos.edit',$todo->id)}}" method="post">

    <div class="modal-content">
        <h5 class="modal-title"> @lang('Edit todo')</h5>

        <blockquote class="modal-subtitle">
          @lang('edit Todo for user')
          <span class="black-text">{{$todo->user->name}}</span>
          @lang('and quotation')
          <span class="black-text">{{$todo->quotation->name}} ( {{$todo->quotation->code}} )</span>
        </blockquote>

        @csrf

        <input type="hidden" name="todo_id"  class="todo_id_edit"   value="{{$todo->id}}">
        <input type="hidden" name="end_date" class="end_date_edit"  value="{{date("d-m-Y",strtotime($todo->end_date))}}">
        <input type="hidden" name="todo_user"class="todo_user_edit" value="{{$todo->user_id}}">
        <input type="hidden" name="todo_quotation" class="todo_quotation_edit" value="{{$todo->quotation_id}}">

        <div class="row">
          <div class="col l12 s12">
            <div class="select-field my-3">
              <label for="start_date" class="dateslabel"> @lang('Date') </label>
              <input type="text" name="start_date" class="start_date_edit" value="{{date("d-m-Y",strtotime($todo->start_date))}}" readonly>
            </div>
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
          <!-- <div class="col l12 s12">
            <div class="input-field my-3">
                <label for="title"> @lang('TODO title') </label>
                <input type="text" class="todo_title_edit" name="todo_title" value="{{$todo->title}}">
            </div>
          </div> -->
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
