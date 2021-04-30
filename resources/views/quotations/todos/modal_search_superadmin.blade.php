<div class="modal modal-search fade" id="search_superadmin_modal" tabindex="-1" role="dialog" aria-labelledby="search_superadmin_title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

          <form class="" action="" method="get">
            <div class="modal-header">
                <h5 class="modal-title" id="search_superadmin_title"> @lang('Search todos') </h5>
            </div>
            <div class="modal-body">

              <div class="row">
                <div class="col l12 s12">
                  <label for="start_date" class="dateslabel"> @lang('Start from') </label>
                  <input type="text" name="search_start_date" id="search_start_date" class="start_date_datetimepicker_search" value="{{$search['search_start_date']}}">
                </div>
              </div>

              <div class="row">
                <div class="col l12 s12">
                  <label for="text" class="dateslabel"> @lang('Title') </label>
                  <input type="text" name="search_title" id="search_title" value="{{$search['search_title']}}">
                </div>
              </div>

              <div class="row">
                <div class="col l12 s12">
                  <label for="text" class="dateslabel"> @lang('Description') </label>
                  <input type="text" name="search_description" id="search_description" value="{{$search['search_description']}}">
                </div>
              </div>

              <div class="row">
                <div class="col l12 s12">
                  <div class="input-field">
                    <select name="user_search[]" id="user_search" multiple>
                      <option value="" disabled>@lang("Filter by user")</option>
                      @foreach( $userAll as $user )
                        <option value="{{$user->id}}" @if( !empty($search['user_search']) && in_array($user->id, $search['user_search'] ) ) selected @endif> {{$user->name}} ( {{$user->email}} ) </option>
                      @endforeach
                    </select>
                    <label> @lang('User') </label>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col l12 s12">
                  <div class="input-field">
                    <select name="quotation_search[]" id="quotation_search" multiple>
                      <option value="" disabled>@lang("Filter by quotation")</option>
                      @foreach( $quotationAll as $quotation )
                        <option value="{{$quotation->id}}" @if( !empty($search['quotation_search']) && in_array($quotation->id, $search['quotation_search'] ) ) selected @endif>{{$quotation->name}}</option>
                      @endforeach
                    </select>
                    <label> @lang('Quotation') </label>
                  </div>
                </div>
              </div>

            </div>
            <div class="modal-footer">
                <a href="#" class="modal-close btn waves-effect waves-light"> @lang('Cancel') </a>
                <button type="submit" id="confirm_search_superadmin" class="btn waves-effect waves-light red"> @lang('Filter') </button>
            </div>
          </form>

        </div>
    </div>
</div>
