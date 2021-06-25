@component('mail::message')

Ciao {{$user->name}}, <br />
Ti informiamo che è stato creato un nuovo TODO da parte dell'utente {{$todo->user->name}} per il progetto {{$todo->quotation->name}}
<br/><br/>
Buona Giornata.
<br/>
<b>Il team di Preventivi</b>
