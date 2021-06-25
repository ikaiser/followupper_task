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

@if ($message = Session::get('error'))
    <div class="card-alert card red lighten-5">
        <div class="card-content red-text">
            <p>{{ $message }}</p>
        </div>
        <button type="button" class="close green-text" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
@endif
