@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('users.index') }}'" class="pointer">/&nbsp;@lang('Roles')</a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="card hoverable">
                <div class="card-content">
                    <span class="card-title ml-2"> @lang('Roles') </span>
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

                    <table id="roles-listing" class="table-responsive highlight">
                        <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Actions')</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($roles as $role)
                            <tr>
                                <td>{{$role->name}}</td>
                                <td>
                                    <a href="{{ route('roles.edit', $role->id) }}">@lang('Edit')</a>
                                    <!-- <a href="{{ route('roles.destroy', $role->id) }}" onclick="return confirm('Are you Sure ?')">@lang('Delete')</a> -->
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
    <script src="{{ asset('js/roles.js') }}"></script>
@endsection
