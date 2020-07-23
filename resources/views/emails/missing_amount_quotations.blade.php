@component('mail::message')

    <h1> Riepilogo Preventivi senza Importo </h1>
    <br/>
    Ciao {{$user->name}},<br/>
    Sembra che tu abbia creato dei preventivi senza inserire un importo <br/>
    Ecco un elenco di questi preventivi.

    <ul>
        @foreach($quotations as $quotation)
            <li>{{$quotation->name}} - Numero: {{$quotation->sequential_number}} - Codice: {{$quotation->code}}</li>
        @endforeach
    </ul>

    Puoi controllare i tuoi preventivi su <a href="http://followupper.alesresearch.com/">Followupper</a> o visitando l'url http://followupper.alesresearch.com
    <br/><br/>
    Buona Giornata.
    <br/>
    <b>Il team di Followupper</b>
