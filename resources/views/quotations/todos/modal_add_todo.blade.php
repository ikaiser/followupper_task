<div id="todo_add" class="modal">

  <form id="todo_add_form" action="{{route('todos.create')}}" method="post">

    <div class="modal-content">
        <h5 class="modal-title"> @lang('Add todo')</h5>
        <blockquote class="modal-subtitle">
          @lang('add new Todo for user')
          <span class="user-name black-text"></span>
          @lang('and quotation')
          <span class="quotation-name black-text"></span>
        </blockquote>

        @csrf

        <input type="hidden" name="end_date"       id="end_date">
        <input type="hidden" name="todo_user"      id="todo_user">
        <input type="hidden" name="todo_quotation" id="todo_quotation">

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
              <select name="todo_activity" id="todo_activity">
                  <option value="" hidden selected> @lang('Select an activity') </option>
                  @foreach($activities as $activity)
                      <option value="{{$activity->id}}">{{$activity->name}}</option>
                  @endforeach
              </select>
              <label for="activity"> @lang('Activity') </label>
            </div>
          </div>
          <!-- <div class="col l12 s12">
            <div class="input-field my-3">
                <label for="title"> @lang('TODO title') </label>
                <input type="text" id="todo_title" name="todo_title">
            </div>
          </div> -->
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
