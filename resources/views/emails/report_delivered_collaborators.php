Ciao, <?php echo $user->name ?>
<h3> Ecco un riepilogo dei progetti entrati e non ancora chiusi presenti sulla piattaforma </h3> <br/>

<ul>
  <?php foreach( $quotation_list as $quotation ){ ?>
    <li> ID: <b> <?php echo get_code($quotation) ?> </b> - Descrizione: <b> <?php echo $quotation->description ?> </b> </li>
  <?php } ?>
</ul>

Puoi controllare i tuoi preventivi su <a href="https://preventivi.alesresearch.com/">Preventivi</a> o visitando l'url https://preventivi.alesresearch.com/
<br/><br/>
Buona Giornata.
<br/>
<b>Il team di Preventivi</b>
