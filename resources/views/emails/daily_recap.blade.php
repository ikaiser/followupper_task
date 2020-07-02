@component('mail::message')

    <h1> Riepilogo della Giornata </h1>
    <br/>
    Ciao {{$user->name}},<br/>
    Ecco un riepilogo di quello che è successo oggi

    @if($rooms->count())
        <h3> Stanze Create </h3>
        <ul>
            @foreach($rooms as $room)
                <li>{{$room}}</li>
            @endforeach
        </ul>
    @endif

    @if($files->count())
        <h3> File Aggiunti </h3>
        <ul>
            @foreach($files as $file)
                <li>{{$file}}</li>
            @endforeach
        </ul>
    @endif

    Puoi controllare le stanze ed i file su <a href="http://data-curation.alesresearch.com/">Data Curation</a> o visitando l'url http://data-curation.alesresearch.com/
    <br/><br/>
    Buona Giornata.
    <br/>
    <b>Il team di Followupper</b>
