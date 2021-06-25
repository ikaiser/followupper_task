@extends('layouts.app')

@section('content')

    @include('activities/modals/remove')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('activities.index') }}'" class="pointer">/&nbsp;@lang('Activities')</a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='{{ route('activities.create') }}'"><i class="material-icons">add</i></button>
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="card hoverable">
                <div class="card-content">
                    <span class="card-title ml-2"> @lang('Activities') </span>

                    @include("partials/flashdata")

                    <table class="responsive-table highlight display">
                        <thead>
                          <tr>
                              <th> @lang('Id')          </th>
                              <th> @lang('Name')        </th>
                              <th> @lang('Description') </th>
                              <th> @lang('Actions')     </th>
                          </tr>
                        </thead>
                        <tbody>

                        @foreach( $activities as $activity )
                            <tr>
                                <td>{{$activity->id}}</td>
                                <td>{{$activity->name}}</td>
                                <td>{{$activity->description}}</td>
                                <td>
                                  <!-- Dropdown Trigger -->
                                  <a class='dropdown-trigger btn btn-block' href='#' data-target='dropdown-actions-{{$activity->id}}'>@lang("Actions")</a>

                                  <!-- Dropdown Structure -->
                                  <ul id='dropdown-actions-{{$activity->id}}' class='dropdown-content'>
                                    <li>
                                      <a href="{{ route('activities.edit', $activity->id ) }}"> <i class="material-icons">edit</i> @lang('Edit') </a>
                                    </li>
                                    <li>
                                      <a name="element_remove" class="mx-1 modal-trigger" href="#remove_modal" data-id="{{$activity->id}}" data-type="activity"> <i class="material-icons">delete</i> @lang('Delete') </a>
                                    </li>
                                  </ul>
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

@section('css')

@endsection

@section('js')
    @parent
    <script src="{{ asset('js/remove.js') }}"></script>
@endsection
