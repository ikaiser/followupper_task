@extends('layouts.app')

@section('content')

    @include('quotations/modal-remove')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('quotations.index') }}'" class="pointer">&nbsp;/&nbsp;@lang('Quotations')</a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id <= 2)
                <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='/quotations/export?{{http_build_query($_GET)}}'" role="button" title="Download"><i class="material-icons">file_download</i></button>
            @endif
            <button class="btn btn-floating waves-effect waves-light filters-btn" onclick="open_filters()" role="button" title="Filters"><i class="material-icons">filter_list</i></button>
            <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='{{ route('quotations.create') }}'" role="button" title="Aggiungi"><i class="material-icons">add</i></button>
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="card-alert card green lighten-5">
            <div class="card-content green-text">
                <p>{{ session()->get('message') }}</p>
            </div>
            <button type="button" class="close green-text" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="card-alert card red lighten-5 my-4">
            <div class="card-content red-text">
                {{ session()->get('error') }}
            </div>
            <button type="button" class="close red-text" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    <!-- Filters -->
    <div class="row" id="filters" style="display:none">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title"> @lang('Filters') </span>
                    <form class="filters-form" action="" method="get">

                      <input type="hidden" name="filters_active" value="1">

                      <div class="input-field my-3">
                        <select name="methodologies[]" id="methodologies" multiple>
                            <option value="" disabled> @lang('Select a Methodology') </option>
                            @foreach($methodologies as $methodology)
                                <option value="{{$methodology->id}}"
                                  @if( isset( $_GET['methodologies'] ) )
                                    {{ in_array( $methodology->id, $_GET['methodologies'] ) == 1 ? 'selected' : ''}}
                                  @endif>{{$methodology->name}}</option>
                            @endforeach
                        </select>
                      </div>

                      <div class="input-field my-3">
                        <select name="typologies[]" id="typologies" multiple>
                            <option value="" disabled> @lang('Select a Typology') </option>
                            @foreach($typologies as $typology)
                              <option value="{{$typology->id}}"
                                @if( isset( $_GET['typologies'] ) )
                                  {{ in_array( $typology->id, $_GET['typologies'] ) == 1 ? 'selected' : ''}}
                                @endif>{{$typology->name}}</option>
                            @endforeach
                        </select>
                      </div>

                      <?php if ( is_array($researchers) ){ ?>
                        <div class="input-field my-3">
                          <select name="researchers[]" id="researchers" multiple>
                              <option value="" disabled> @lang('Select a Researcher') </option>
                              @foreach($researchers as $researcher)
                                <option value="{{$researcher->id}}"
                                  @if( isset( $_GET['researchers'] ) )
                                    {{ in_array( $researcher->id, $_GET['researchers'] ) == 1 ? 'selected' : ''}}
                                  @endif>{{$researcher->name}}</option>
                              @endforeach
                          </select>
                        </div>
                      <?php } ?>

                      <div class="input-field my-3">
                        <select name="companies[]" id="companies" multiple>
                            <option value="" disabled> @lang('Select a Company') </option>
                            @foreach($companies as $company)
                              <option value="{{$company->id}}"
                                @if( isset( $_GET['companies'] ) )
                                  {{ in_array( $company->id, $_GET['companies'] ) == 1 ? 'selected' : ''}}
                                @endif>{{$company->name}}</option>
                            @endforeach
                        </select>
                      </div>

                      <div class="input-field my-3">
                        <select name="statuses[]" id="statuses" multiple>
                            <option value="" disabled> @lang('Select a Status') </option>
                            @foreach($statuses as $status)
                              <option value="{{$status->id}}"
                                @if( isset( $_GET['statuses'] ) )
                                  {{ in_array( $status->id, $_GET['statuses'] ) == 1 ? 'selected' : ''}}
                                @endif>{{$status->name}}</option>
                            @endforeach
                        </select>
                      </div>

                      <div class="input-field my-3">
                        <input type="text" name="probability" id="probability" placeholder="@lang('Probability')"
                        @if( isset( $_GET['probability'] ) )
                          value="{{$_GET['probability']}}"
                        @endif>
                      </div>

                      <div class="input-field my-3">
                        <span>
                          <label>
                            <input name="open_projects" id="open_projects" type="checkbox"
                            @if( isset( $_GET['open_projects'] ) && $_GET['open_projects'] == 'on' )
                              checked
                            @endif>
                            <span>@lang('Only open projects')</span>
                          </label>
                        </span>
                      </div>

                      <!-- Delivery date -->
                      <div class="row">
                        <div class="col l6">
                          <div class="input-field my-3">
                              <label for="project_delivery_date_from"> @lang('Quotation Delivery Date from') </label>
                              <input type="text" class="datepicker" name="project_delivery_date_from" id="project_delivery_date_from"
                              @if( isset( $_GET['project_delivery_date_from'] ) && $_GET['project_delivery_date_from'] !== '' )
                                value="{{$_GET['project_delivery_date_from']}}"
                              @endif>
                          </div>
                        </div>
                        <div class="col l6">
                          <div class="input-field my-3">
                              <label for="project_delivery_date_to"> @lang('Quotation Delivery Date to') </label>
                              <input type="text" class="datepicker" name="project_delivery_date_to" id="project_delivery_date_to"
                              @if( isset( $_GET['project_delivery_date_to'] ) && $_GET['project_delivery_date_to'] !== '' )
                                value="{{$_GET['project_delivery_date_to']}}"
                              @endif>
                          </div>
                        </div>
                      </div>

                      <!-- Delivery date -->
                      <div class="row">

                        <div class="col l6">
                          <div class="input-field my-3">
                              <label for="insertion_date_from"> @lang('Insertion Date from') </label>
                              <input type="text" class="datepicker" name="insertion_date_from" id="insertion_date_from"
                              @if( isset( $_GET['insertion_date_from'] ) && $_GET['insertion_date_from'] !== '' )
                                value="{{$_GET['insertion_date_from']}}"
                              @endif>
                          </div>
                        </div>

                        <div class="col l6">
                          <div class="input-field my-3">
                              <label for="insertion_date_to"> @lang('Insertion Date to') </label>
                              <input type="text" class="datepicker" name="insertion_date_to" id="insertion_date_to"
                              @if( isset( $_GET['insertion_date_to'] ) && $_GET['insertion_date_to'] !== '' )
                                value="{{$_GET['insertion_date_to']}}"
                              @endif>
                          </div>
                        </div>

                      </div>

                      <div class="row pull-right">
                        <button class="btn btn-primary" type="submit"> @lang('Apply filters')</button>
                        <button class="btn btn-primary" type="button" onclick="window.location.href='/quotations'"> @lang('Reset filters')</button>
                        <button class="btn btn-error" type="button" onclick="close_filters()"> @lang('Close')</button>
                      </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title"> @lang('Quotations') </span>

                    <table class="table-responsive highlight stripe" id="quotations_table">
                        <thead>
                        <tr>
                            <th> Id </th>
                            <th> @lang('Name') </th>
                            <th> @lang('Sequential Number') </th>
                            <th> @lang('User') </th>
                            <th> @lang('Company') </th>
                            <th> @lang('Description') </th>
                            <th> @lang('Actions') </th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($quotations as $quotation)
                            <tr>
                                <td>{{get_code($quotation)}}</td>
                                <td>{{$quotation->name}}</td>
                                <td>{{$quotation->sequential_number}}</td>
                                <td>{{$quotation->user->name}}</td>
                                <td>{{$quotation->company->name}}</td>
                                <td>{{$quotation->description}}</td>
                                <td>
                                    <a class="mx-1" href="{{ route('quotations.edit', $quotation->id) }}"> @lang('Edit') </a>
                                    <a name="element_remove" class="mx-1 modal-trigger" href="#remove_modal" data-id="{{$quotation->id}}" data-type="quotation"> @lang('Delete') </a>
                                </td>
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
    <script src="{{ asset('js/quotations.js') }}"></script>
@endsection
