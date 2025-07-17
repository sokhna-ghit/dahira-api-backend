@component('mail::message')
# Reçu de Paiement

Bonjour Madame, Monsieur,

Votre paiement a bien été enregistré.

**Montant :** {{ number_format($paiement->amount, 0, ',', ' ') }} FCFA  
**Référence :** {{ $paiement->reference }}  
**Statut :** {{ strtoupper($paiement->status) }}  

Le reçu PDF est en pièce jointe.

Merci,<br>
{{ config('app.name') }}
@endcomponent
