@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('quotations.index') }}'" class="pointer">&nbsp;/&nbsp;@lang('Quotations') </a>
                <a href="#" class="pointer">&nbsp;/&nbsp;@lang('New Quotation') </a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    <div class="card hoverable">
        <div class="card-content">
            <span class="card-title"> @lang('Add Quotation') </span>

            @if ($errors->any())
                <div class="card-alert card red lighten-5 my-4">
                    <div class="card-content red-text">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="close red-text" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col s12">
                    <div class="card">
                        <form method="post" action="{{ route('quotations.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-content">
                                <div class="row">
                                    <div class="col l6 m12">
                                        <div class="input-field my-3">
                                            @if(is_string($researcher))
                                                <label for="researcher"> @lang('Researcher') </label>
                                                <input type="text" id="researcher" name="researcher" value="{{$researcher}}" readonly/>
                                            @else
                                                <select name="researcher" id="researcher">
                                                    <option value="" disabled hidden selected> @lang('Select a Researcher') </option>
                                                    @foreach($researcher as $user)
                                                        <option value="{{$user->id}}" {{(old('researcher') == $user->id) ? 'selected' : ''}}>{{$user->name}}</option>
                                                    @endforeach
                                                </select>
                                                <label for="researcher"> @lang('Researcher') </label>
                                            @endif
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="researchers" id="researcher_label"> @lang('Other Researchers') </label>
                                            <input type="text" name="researchers[]" autocomplete="off" class="my-2" style="width: 90%">
                                            <button class="btn btn-small waves-effect waves-light red" type="button" name="deassign_researcher" style="display: none; padding: 0 1rem;"><i class="material-icons">delete</i></button>
                                        </div>
                                        <div></div>
                                    </div>

                                    <div class="col l6 m12">
                                        <div class="input-field my-3">
                                            <label for="company"> @lang('Company') </label>
                                            <input type="text" id="company" name="company" autocomplete="no" value="{{old('company')}}"/>
                                            <div id="list_company" class="mb-2"></div>
                                        </div>

                                        <div class="input-field my-3">
                                            <select name="company_contact" id="company_contact">
                                                <option value="" disabled hidden selected> @lang('Select a Contact') </option>
                                            </select>
                                            <label for="company_contact"> @lang('Company Contact') </label>
                                        </div>
                                    </div>

                                    <div class="col l12">
                                        <div class="input-field my-3">
                                            <label for="name"> @lang('Name') </label>
                                            <input type="text" id="name" name="name" value="{{old('name')}}"/>
                                        </div>

                                        <p class="my-2">
                                            <label>
                                                <input type="checkbox" name="manual_sequential" id="manual_sequential" {{old('manual_sequential') == 'on' ? 'checked' : ''}}/>
                                                <span> @lang('Enter the sequence number manually') </span>
                                            </label>
                                        </p>
                                        <div class="input-field my-3">
                                            <label for="sequential"> @lang('Sequential Number') </label>
                                            <input type="text" id="sequential" name="sequential" {{old('manual_sequential') == 'on' ? '' : 'disabled'}} value="{{old('manual_sequential') == 'on' ? old('sequential') : ''}}" >
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="code"> @lang('Code') </label>
                                            <input type="text" id="code" name="code" value="{{old('code')}}">
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="description"> @lang('Description') </label>
                                            <textarea id="description" name="description" class="materialize-textarea">{{old('description')}}</textarea>
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="insertion_date"> @lang('Insertion Date') </label>
                                            <input type="text" class="datepicker" name="insertion_date" id="insertion_date" value="{{old('insertion_date')}}">
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="end_date"> @lang('End Date') </label>
                                            <input type="text" class="datepicker" name="end_date" id="end_date" value="{{old('end_date')}}">
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="income"> @lang('Amount') </label>
                                            <input type="number" name="amount" id="amount" min="0" value="{{old('amount')}}">
                                        </div>

                                        <div class="input-field my-3">
                                            <select name="status" id="status">
                                                <option value="" disabled hidden selected> @lang('Select a Status') </option>
                                                @foreach($statuses as $status)
                                                    <option value="{{$status->id}}" {{(old('status') == $status->id) ? 'selected' : ''}}>{{$status->name}}</option>
                                                @endforeach
                                            </select>
                                            <label for="status"> @lang('Status') </label>
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="acquired_income"> @lang('Amount Acquired') </label>
                                            <input type="number" name="amount_acquired" id="amount_acquired" min="0" value="{{old('amount_acquired')}}">
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="probability"> @lang('Probability') </label>
                                            <input type="number" name="probability" id="probability" min="0" max="100" value="{{old('probability')}}">
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="feedback"> @lang('Feedback') </label>
                                            <textarea id="feedback" name="feedback" class="materialize-textarea">{{old('feedback')}}</textarea>
                                        </div>

                                        <p class="my-2">
                                            <label>
                                                <input type="checkbox" name="project_closed" id="project_closed" {{old('project_closed') == 'on' ? 'checked' : ''}}/>
                                                <span> @lang('Project Closed') </span>
                                            </label>
                                        </p>
                                        <div class="input-field my-3">
                                            <label for="acquired_income"> @lang('Invoice Amount') </label>
                                            <input type="number" name="invoice_amount" id="invoice_amount" min="0" {{old('project_closed') == 'on' ? '' : 'disabled'}} value="{{old('project_closed') == 'on' ? old('invoice_amount') : ''}}" >
                                        </div>

                                        <div class="input-field my-3">
                                            <select name="test_typology" id="test_typology">
                                                <option value="" disabled hidden selected> @lang('Select a Test Typology') </option>
                                                @foreach($typologies as $typology)
                                                    <option value="{{$typology->id}}" {{(old('test_typology') == $typology->id) ? 'selected' : ''}}>{{$typology->name}}</option>
                                                @endforeach
                                            </select>
                                            <label for="status"> @lang('Test Typology') </label>
                                        </div>

                                    </div>
                                </div>

                                <button type="submit" class="btn waves-effect waves-light mt-4 mr-2"> @lang('Add')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    @parent
    <script src="{{ asset('js/quotations.js') }}"></script>
@endsection
