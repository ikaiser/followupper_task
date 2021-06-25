@component('mail::message')

Ciao {{$user->name}}, <br />

Di seguito puoi visualizzare la lista dei todo divisi per progetto e non ancora completati

@foreach( $todos as $todoArr )
  <h4> Preventivo: {{$todoArr["quotation"]->name}} </h4>
  @foreach( $todoArr["todos"] as $todo )
    @php $activityName = (!is_null($todo->activities->first())) ? "( ".$todo->activities->first()->name ." )" : "" @endphp
    <div>
      - Scadenza: {{ date("d/m/Y",strtotime($todo->start_date)) }} - {{ $todo->title }} {{ $activityName }}
    </div>
  @endforeach
@endforeach
<br/><br/>
Puoi controllare i tuoi TODO su <a href="https://preventivi.alesresearch.com/">Preventivi</a> o visitando l'url https://preventivi.alesresearch.com/ nella sezione TODO
<br/><br/>
Buona Giornata.
<br/>
<b>Il team di Preventivi</b>
