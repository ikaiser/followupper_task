<h1> Riepilogo Giornaliero collaboratori status A1.Da inviare </h1>
<br/>
Ciao, <?php echo $user->name ?>
<h3> Ecco un riepilogo dei preventivi creati sulla piattaforma per i quali devi inviare la proposta progettuale </h3> <br/>

<ul>
  <?php foreach( $quotation_list as $quotation ){ ?>
    <li> ** STATUS = <?php echo $quotation->status->name ?> ** ID: <b> <?php echo get_code($quotation) ?> </b> - Consegna entro: <b> <?php echo date("d/m/Y", strtotime($quotation->deadline) ) ?> </b> - Descrizione: <b> <?php echo $quotation->description ?> </b> </li>
  <?php } ?>
</ul>

Puoi controllare i tuoi preventivi su <a href="https://preventivi.alesresearch.com/">Preventivi</a> o visitando l'url https://preventivi.alesresearch.com/
<br/><br/>
Buona Giornata.
<br/>
<b>Il team di Preventivi</b>
