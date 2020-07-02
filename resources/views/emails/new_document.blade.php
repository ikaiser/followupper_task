@component('mail::message')

    <div class="m-2">
        <h1> Nuovo Documento Aggiunto </h1>
        <br/>
        Ciao {{$user->name}},<br/>
        E' stato creato un nuovo documento all'interno del progetto <b>{{$project->name}}</b>
        <br/>
        Puoi visualizzarlo cliccando <a href="http://followupper.alesresearch.com/projects/{{$project->id}}/dc/file/{{$dce->id}}">qui</a> oppure visitando il sito di <a href="http://followupper.alesresearch.com">Followupper</a>
        <br/><br/>
        Buona Giornata.
        <br/>
        <b>Il team di Followupper</b>
    </div>

@endcomponent
