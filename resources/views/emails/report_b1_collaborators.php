<h1> Riepilogo Settimanale collaboratori status B1.Inviato in attesa di risposta </h1>
<br/>
Ciao, <?php echo $user->name ?>
<h3> Ecco un riepilogo dei preventivi creati sulla piattaforma per i quali sei in attesa di risposta. E’ il caso di inviare un remind? </h3> <br/>

<ul>
  <?php foreach( $quotation_list as $quotation ){ ?>
    <li> ID: <b> <?php echo get_code($quotation) ?> </b> - Consegna entro: <b> <?php echo date("d/m/Y", strtotime($quotation->deadline) ) ?> </b> - Descrizione: <b> <?php echo $quotation->description ?> </b> </li>
  <?php } ?>
</ul>

Puoi controllare i preventivi su <a href="http://preventivi.alesresearch.com/">Preventivi</a> o visitando l'url http://preventivi.alesresearch.com
<br/><br/>
Buona Giornata.
<br/>
<b>Il team di Followupper</b>
