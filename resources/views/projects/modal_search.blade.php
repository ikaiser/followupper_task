<div id="search_modal" class="modal main mt-0 t-0 w-100">
    <div class="modal-content" style="padding: 24px 15%">
        <form method="post" action="{{ route('dce.search') }}" enctype="multipart/form-data">
                <div class="row">
                    <div class="col s12 m6 mt-4">
                        <h4 class="modal-title" id="search_filter"> @lang('Search File') </h4>
                    </div>
                    <div class="col s12 m6 mt-4 right-align">
                        <a href="#!" class="modal-close btn waves-effect waves-light"> @lang('Close') </a>
                        <button class="btn btn-sm btn-primary" id="search"> @lang('Search') </button>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col s12 mb-4">
                        @csrf
                        <div class="input-field my-2">
                            <label for="search_text"> @lang('Query') </label>
                            <input type="text" class="form-control input-button" name="search_text[]" title="Text">
                            <button type="button" id="add_query" name="add_query" class="btn btn-floating waves-effect waves-light ml-2"> <i class="material-icons">add</i> </button>
                        </div>
                    </div>
                </div>

            <div class="row">
                <div class="col s12 l3 mb-4">
                    <div class="input-field my-3">
                        @if(isset($project) && is_object($project))
                            <label for="project_list"> @lang('Project') </label>
                            <input type="hidden" id="projects" name="projects" value="{{$project->id}}"/>
                            <input autocomplete="off" type="text" name="project_list" data-id="" value="{{$project->name}}" readonly/>
                        @else
                            <label for="project_list"> @lang('Project') </label>
                            <input type="hidden" id="projects" name="projects"/>
                            <input autocomplete="off" type="text" name="project_list" data-id=""/>
                            <div id="list"></div>
                        @endif
                    </div>
                </div>

                <div class="col s12 l3 mb-4">
                    <div class="input-field my-3">
                        <label for="doc_type"> @lang('File Type') </label>
                        <input type="text" name="doc_type" id="doc_type">
                        <div id="list_doc_type"></div>
                    </div>
                </div>

                <div class="col s12 l3 mb-4">
                    <div class="input-field my-3">
                        <label for="start_date"> @lang('Creation Date From') </label>
                        <input type="text" class="datepicker" name="start_date" id="start_date">
                    </div>
                </div>


                <div class="col s12 l3 mb-4">
                    <div class="input-field my-3">
                        <label for="end_date"> @lang('Creation Date To') </label>
                        <input type="text" class="datepicker" name="end_date" id="end_date">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col s12 l3 mb-4">
                    <div class="input-field my-3">
                        <label for="tags"> @lang('Tags') </label>
                        <input type="text" name="tags[]" value="" autocomplete="off">
                        <div id="list_doc_type"></div>
                    </div>
                </div>

                <div class="col s12 l3 mb-4">
                    @if(isset($data['author']))
                        <p class="text-muted"> @lang('Author') </p>
                        <div style="overflow:auto; height:100px;">
                            @foreach ($data['authors'] as $author)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="authors[]" value="{{$author->id}}" id="au_{{$author->id}}">
                                    <label class="custom-control-label" for="au_{{$author->id}}">{{$author->name}}</label>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="col s12 l3 mb-4">
                    <div class="input-field my-3">
                        <label for="topics"> @lang('Topics') </label>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
