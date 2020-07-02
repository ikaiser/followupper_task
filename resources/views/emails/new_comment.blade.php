@component('mail::message')

    <div class="m-2">
        <h1> Nuovo Commento </h1>
        <br/>
        Ciao {{$user->name}},<br/>
        E' stato inserito un nuovo commento al file <b>{{$dce->name}}</b>
        <br/>
        Puoi visualizzarlo cliccando <a href="http://followupper.alesresearch.com/projects/{{$dce->project_id}}/dc/file/{{$dce->id}}">qui</a> oppure visitando il sito di <a href="http://followupper.alesresearch.com">Followupper</a>
        <br/><br/>
        Buona Giornata.
        <br/>
        <b>Il team di Followupper</b>
    </div>

@endcomponent
