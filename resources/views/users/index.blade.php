@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('users.index') }}'" class="pointer">/&nbsp;@lang('Users')</a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="document.location.href='{{ route('users.create') }}'"><i class="material-icons">add</i></button>
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="card hoverable">
                <div class="card-content">
                    <span class="card-title ml-2"> @lang('Users') </span>
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

                    <table class="table-responsive highlight display" id="user_table">
                        <thead>
                        <tr>
                            <th> @lang('Username') </th>
                            <th> @lang('E-Mail') </th>
                            <th> @lang('Company') </th>
                            <th> @lang('Registration Date') </th>
                            <th> @lang('Role') </th>
                            <th> @lang('Last Login') </th>
                            <th> @lang('Actions') </th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($users as $user)
                            <tr>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->company}}</td>
                                <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y')}}</td>
                                <td>{{$user->roles->first()->display_name}}</td>
                                <td>{{(!is_null($user->lastLoginAt()) ? \Carbon\Carbon::parse($user->lastLoginAt())->format('d/m/Y H:i:s') : '')}}</td>
                                <td>
                                    <a href="{{ route('users.log', $user->id) }}" target="_blank"> Log </a>
                                    <a href="{{ route('users.edit', $user->id) }}"> @lang('Edit') </a>
                                    <a href="{{ route('users.destroy', $user->id) }}" onclick="return confirm('Are you Sure ?')"> @lang('Delete') </a>
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
    <script src="{{ asset('js/users.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#user_table').DataTable( {
                "lengthChange": true,
                'responsive'  : true,
                fixedColumns:   {
                    heightMatch: 'none'
                }
            });
        } );

    </script>
@endsection


