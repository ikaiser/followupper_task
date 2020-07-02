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
                                <p class="text-muted mb-0 hover-cursor" onclick="document.location.href='{{ route('dc.index', $id) }}'">&nbsp;/&nbsp;DataCuration</p>
                                <p class="text-muted mb-0 hover-cursor">&nbsp;/&nbsp;Aggiungi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-12 stretch-card">
                    <div class="card">
                        <div class="card-header">Aggiungi Cartella</div>

                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-12 grid-margin stretch-card">
                                    <div class="card">
                                        <form method="post" action="add-folder">
                                            @csrf
                                            <div class="card-body">

                                                <div class="form-group">
                                                    <label> Carica File </label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="file" name="file" aria-describedby="file_upload">
                                                        <label class="custom-file-label" for="file">Scegli un File</label>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="name">Nome</label>
                                                    <input type="text" class="form-control" placeholder="nome file" name="name" />
                                                </div>

                                                <button type="submit" class="btn btn-primary mr-2">Aggiungi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>


                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{ Form::close() }}

        </div>
    </div>
    </div>

@endsection

@section('js')
    @parent
@endsection
