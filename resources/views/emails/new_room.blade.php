@component('mail::message')

    <div class="m-2">
        <h1> Nuova Stanza Creata </h1>
        <br/>
        Ciao {{$user->name}},<br/>
        E' stata aggiunta una nuova stanza all'interno del progetto <b>{{$project->name}}</b>
        <br/>
        Puoi visualizzare la stanza cliccando <a href="http://followupper.alesresearch.com/projects/{{$project->id}}/dc/{{$dc->id}}">qui</a> oppure visitando il sito di <a href="http://followupper.alesresearch.com">Followupper</a>
        <br/><br/>
        Buona Giornata.
        <br/>
        <b>Il team di Followupper</b>
    </div>

@endcomponent
