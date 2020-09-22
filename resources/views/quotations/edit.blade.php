@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col s12 m6 mt-4">
            <h6>
                <a href="#" class="pointer">Home</a>
                <a onclick="document.location.href='{{ route('quotations.index') }}'" class="pointer">&nbsp;/&nbsp;@lang('Quotations') </a>
                <a href="#" class="pointer">&nbsp;/&nbsp;{{$quotation->name}}</a>
                <a href="#" class="pointer">&nbsp;/&nbsp;@lang('Edit')</a>
            </h6>
        </div>
        <div class="col s12 m6 mt-4 right-align">
            <button class="btn btn-floating waves-effect waves-light" onclick="window.history.back()"><i class="material-icons">arrow_back</i></button>
        </div>
    </div>

    <div class="card hoverable">
        <div class="card-content">
            <span class="card-title"> @lang('Edit Quotation') </span>

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
                        <form method="post" action="{{ route('quotations.update', [$quotation->id]) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-content">
                                <div class="row">
                                    <div class="col l6 m12">
                                        <div class="input-field my-3">
                                            @if(is_string($researcher))
                                                <label for="researcher"> @lang('Researcher') </label>
                                                <input type="text" id="researcher" name="researcher" value="{{$quotation->user->name}}" readonly/>
                                            @else
                                                <select name="researcher" id="researcher">
                                                    <option value="" disabled hidden selected> @lang('Select a Researcher') </option>
                                                    @foreach($researcher as $user)
                                                        <option value="{{$user->id}}" {{($quotation->user_id == $user->id) ? 'selected' : ''}}>{{$user->name}}</option>
                                                    @endforeach
                                                </select>
                                                <label for="researcher"> @lang('Researcher') </label>
                                            @endif
                                        </div>

                                        @foreach($quotation->collaborators as $collaborator)
                                            <div class="input-field my-3">
                                                <label for="researchers" id="researcher_label"> @lang('Other Researchers') </label>
                                                <input type="text" name="researchers[]" autocomplete="off" class="my-2" style="width: 90%" value="{{$collaborator->name}}">
                                                <button class="btn btn-small waves-effect waves-light red" type="button" name="deassign_researcher" style="padding: 0 1rem;"><i class="material-icons">delete</i></button>
                                            </div>
                                            <div></div>
                                        @endforeach
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
                                            <input type="text" id="company" name="company" autocomplete="no" value="{{$quotation->company->name}}"/>
                                            <div id="list_company" class="mb-2"></div>
                                        </div>

                                        <div class="input-field my-3">
                                            <select name="company_contact[]" id="company_contact" multiple>
                                                <option value="" disabled> @lang('Select a Contact') </option>
                                                @foreach($quotation->company->contacts as $contact)
                                                    <option value="{{$contact->id}}"
                                                      @if( !is_null( $quotation->company_contact_id ) )
                                                        {{($quotation->company_contact_id == $contact->id) ? 'selected' : ''}}
                                                      @else
                                                        {{$quotation->company_contacts->where('id', $contact->id)->count() == 1 ? 'selected' : ''}}
                                                      @endif
                                                    >{{$contact->name}} </option>
                                                @endforeach
                                            </select>
                                            <label for="company_contact"> @lang('Company Contact') </label>
                                        </div>
                                    </div>

                                    <div class="col l12">
                                        <div class="input-field my-3">
                                            <label for="name"> @lang('Project Fantasy Name') </label>
                                            <input type="text" id="name" name="name" value="{{$quotation->name}}"/>
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="sequential"> @lang('Sequential Number') </label>
                                            <input type="text" id="sequential" name="sequential" {{\Illuminate\Support\Facades\Auth::user()->roles->first()->id > 2 ? 'readonly' : ''}} value="{{$quotation->sequential_number}}" >
                                        </div>

                                        <!-- <div class="input-field my-3">
                                            <label for="code"> @lang('Code') </label> -->
                                            <input type="hidden" id="code" name="code" value="{{$quotation->code}}">
                                        <!-- </div> -->

                                        <div class="input-field my-3">
                                            <label for="description"> @lang('Description') </label>
                                            <textarea id="description" name="description" class="materialize-textarea">{{$quotation->description}}</textarea>
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="insertion_date"> @lang('Insertion Date') </label>
                                            <input type="text" class="datepicker" name="insertion_date" id="insertion_date" value="{{$quotation->insertion_date}}">
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="end_date"> @lang('Quotation Delivery Date') </label>
                                            <input type="text" class="datepicker" name="project_delivery_date" id="project_delivery_date" value="{{$quotation->deadline}}">
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="income"> @lang('Amount') </label>
                                            <input type="number" name="amount" id="amount" min="0" value="{{$quotation->amount}}">
                                        </div>

                                        <div class="input-field my-3">
                                            <select name="status" id="status">
                                                <option value="" disabled hidden> @lang('Select a Status') </option>
                                                @foreach($statuses as $status)
                                                    <option value="{{$status->id}}" {{($quotation->status_id == $status->id) ? 'selected' : ''}}>{{$status->name}}</option>
                                                @endforeach
                                            </select>
                                            <label for="status"> @lang('Status') </label>
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="acquired_income"> @lang('Amount Acquired') </label>
                                            <input type="number" name="amount_acquired" id="amount_acquired" min="0" value="{{$quotation->amount_acquired}}">
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="probability"> @lang('Probability') </label>
                                            <input type="number" name="probability" id="probability" min="0" max="100" value="{{$quotation->chance}}">
                                        </div>

                                        <div class="input-field my-3">
                                            <label for="feedback"> @lang('Feedback') </label>
                                            <textarea id="feedback" name="feedback" class="materialize-textarea">{{$quotation->feedback}}</textarea>
                                        </div>

                                        <p class="my-2">
                                            <label>
                                                <input type="checkbox" name="project_closed" id="project_closed" {{$quotation->closed == 1 ? 'checked' : ''}}/>
                                                <span> @lang('Project closed') </span>
                                            </label>
                                        </p>
                                        <div class="input-field my-3">
                                            <label for="invoice_amount"> @lang('Invoice Amount') </label>
                                            <input type="number" name="invoice_amount" id="invoice_amount" min="0" {{$quotation->closed == 1 ? '' : 'disabled'}} value="{{$quotation->closed == 1 ? $quotation->invoice_amount : ''}}" >
                                        </div>

                                        <div class="input-field my-3">
                                            <select name="test_typology[]" id="test_typology" multiple>
                                                <option value="" disabled> @lang('Select a Test Typology') </option>
                                                @foreach($typologies as $typology)
                                                    <option value="{{$typology->id}}" {{$quotation->typologies->where('id', $typology->id)->count() == 1 ? 'selected' : ''}}>{{$typology->name}}</option>
                                                @endforeach
                                            </select>
                                            <label for="status"> @lang('Test Typology') </label>
                                        </div>

                                        <div class="input-field my-3">
                                            <select name="methodology[]" id="methodology" multiple>
                                                <option value="" disabled> @lang('Select a Methodology') </option>
                                                @foreach($methodologies as $methodology)
                                                    <option value="{{$methodology->id}}"
                                                      @if( !is_null($quotation->methodology_id) )
                                                        {{($quotation->methodology_id == $methodology->id) ? 'selected' : ''}}
                                                      @else
                                                        {{$quotation->methodologies->where('id', $methodology->id)->count() == 1 ? 'selected' : ''}}
                                                      @endif
                                                    >{{$methodology->name}}</option>
                                                @endforeach
                                            </select>
                                            <label for="status"> @lang('Methodology') </label>
                                        </div>

                                    </div>
                                </div>

                                <button type="submit" class="btn waves-effect waves-light mt-4 mr-2"> @lang('Update')</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-action mt-4">
                        <span class="card-title"> @lang('Changes History') </span>

                        <div class="row">
                            @foreach($history as $change)
                                <div class="col s12 my-3">
                                    @php
                                        $user = \App\User::find($change->user_id);
                                    @endphp

                                        <p> <b class="black-text">{{$user->name}}</b> @lang('changed the field') "@lang(ucwords($change->field))" - <b class="black-text">{{date('d-m-Y', strtotime($change->created_at))}}</b> </p>
                                        {{quotation_change($change)}}
                                </div>
                            @endforeach
                        </div>
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
