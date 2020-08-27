@component('mail::message')

    <h1> Riepilogo Settimanale Preventivi Admin </h1>
    <br/>
    Ciao,
    Ecco un riepilogo dei preventivi creati sulla piattaforma <br/>

    <ul>
        <li> Totale Preventivi: {{$quotation_stats[0]}}  </li>
        <li> Preventivi Attualmente Aperti: {{$quotation_stats[1]}}  </li>
        <li> Preventivi Aperti non Fatturati: {{$quotation_stats[2]}}  </li>
        <li> Preventivi Chiusi non Fatturati: {{$quotation_stats[3]}}  </li>
    </ul>

    Puoi controllare i preventivi su <a href="http://preventivi.alesresearch.com/">Preventivi</a> o visitando l'url http://preventivi.alesresearch.com
    <br/><br/>
    Buona Giornata.
    <br/>
    <b>Il team di Followupper</b>
