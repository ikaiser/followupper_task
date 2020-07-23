@component('mail::message')

    <h1> Riepilogo Preventivi in Scadenza </h1>
    <br/>
    Ciao {{$user->name}},<br/>
    Ecco un elenco dei preventivi che stanno per scadere

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
