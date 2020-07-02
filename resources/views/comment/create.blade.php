@extends('layouts.app')

@section('content')

    <div class="main-panel">
        <div class="content-wrapper">

            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="d-flex justify-content-between flex-wrap">
                        <div class="d-flex align-items-end flex-wrap">
                            <div class="d-flex">
                                <i class="mdi mdi-home text-muted hover-cursor"></i>
                                <p class="text-muted mb-0 hover-cursor" onclick="document.location.href='{{ route('projects.index') }}'">&nbsp;/&nbsp;Progetti</p>
                                <p class="text-muted mb-0 hover-cursor" onclick="document.location.href='{{ route('dc.index', $project) }}'">&nbsp;/&nbsp;DataCuration</p>
                                <p class="text-muted mb-0 hover-cursor" onclick="document.location.href='{{ route('dce.show', [$project, $file]) }}'">&nbsp;/&nbsp;File</p>
                                <p class="text-muted mb-0 hover-cursor">&nbsp;/&nbsp;Commenta</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-12 stretch-card">
                    <div class="card">
                        <div class="card-header">Inserisci Commento</div>

                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row">

                                <div class="col-md-12 grid-margin stretch-card">
                                    <div class="card">
                                        <form method="post" action="{{ route('comment.store', [$project, $file]) }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="card-body">

                                                <div class="form-group">
                                                    <label for="comment">Commento</label>
                                                    <textarea id="comment" name="comment" class="form-control"></textarea>
                                                </div>

                                                <input type="hidden" name="project" value="{{$project}}"/>
                                                <input type="hidden" name="file" value="{{$file}}"/>

                                                <button type="submit" class="btn btn-primary mr-2">Inserisci Commento</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection

@section('js')
    @parent
    <script src="{{ asset('js/dc.js') }}"></script>
@endsection
