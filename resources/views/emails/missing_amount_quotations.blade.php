@component('mail::message')

    <h1> Riepilogo Preventivi senza Importo </h1>
    <br/>
    Ciao {{$user->name}},<br/>
    Sembra che tu abbia creato dei preventivi senza inserire un importo <br/>
    Ecco un elenco di questi preventivi, ricordati di completarli il prima possibile.<br/>

    <ul>
        @foreach($quotations as $quotation)
            @php
              $quotation = \App\Quotation::find($quotation);
            @endphp
            <li>{{$quotation->name}} - Numero: {{$quotation->sequential_number}} - Codice: {{get_code($quotation)}}</li>
        @endforeach
    </ul>

    Puoi controllare i tuoi preventivi su <a href="http://preventivi.alesresearch.com/">Preventivi</a> o visitando l'url http://preventivi.alesresearch.com
    <br/><br/>
    Buona Giornata.
    <br/>
    <b>Il team di Followupper</b>
