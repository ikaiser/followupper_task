<h1> Riepilogo giornaliero collaboratori preventivi non chiusi </h1>
<br/>
Ciao, <?php echo $user->name ?>
<h3> Ecco un riepilogo dei preventivi non ancora consegnati presenti sulla piattaforma </h3> <br/>

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
