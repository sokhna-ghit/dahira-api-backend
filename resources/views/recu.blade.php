<!DOCTYPE html>
<html>
<head>
    <title>Reçu de paiement</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <h1>Reçu de paiement - {{ $dahira }}</h1>
    <p>Référence: {{ $paiement->reference }}</p>
    <p>Montant: {{ $paiement->amount }} FCFA</p>
    <p>Téléphone: {{ $paiement->phone }}</p>
    <p>Statut: {{ $paiement->status }}</p>
</body>
</html>
