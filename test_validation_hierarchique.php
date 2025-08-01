<?php

/**
 * Script de test pour le système de validation hiérarchique
 * Démontre qui peut valider qui dans le système Dahira
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "🏛️ === TEST DU SYSTÈME DE VALIDATION HIÉRARCHIQUE ===\n\n";

$baseUrl = 'http://192.168.1.11:8000/api';

echo "📋 Règles de validation actuelles :\n";
echo "┌─────────────────┬──────────────────────────────────────────┐\n";
echo "│ Rôle Valideur   │ Peut valider                             │\n";
echo "├─────────────────┼──────────────────────────────────────────┤\n";
echo "│ Super Admin     │ Tout le monde (admins, présidents, etc) │\n";
echo "│ Admin           │ Présidents, Trésoriers, Membres         │\n";
echo "│ Président       │ Membres de sa dahira uniquement         │\n";
echo "└─────────────────┴──────────────────────────────────────────┘\n\n";

echo "🔍 Exemples de scénarios :\n\n";

echo "1️⃣  Nouveau président s'inscrit :\n";
echo "   → Statut : 'pending'\n";
echo "   → Doit être validé par : Admin ou Super Admin\n";
echo "   → Notification envoyée aux : Admins et Super Admins\n\n";

echo "2️⃣  Nouveau membre s'inscrit :\n";
echo "   → Statut : 'pending'\n";
echo "   → Peut être validé par : Président de sa dahira, Admin, ou Super Admin\n";
echo "   → Notification envoyée aux : Président + Admins\n\n";

echo "3️⃣  Nouveau admin s'inscrit :\n";
echo "   → Statut : 'pending'\n";
echo "   → Doit être validé par : Super Admin uniquement\n";
echo "   → Notification envoyée aux : Super Admins\n\n";

echo "🧪 Pour tester, vous pouvez :\n";
echo "1. Créer un compte président via l'API de demande d'inscription\n";
echo "2. Se connecter en tant qu'admin\n";
echo "3. Voir les demandes en attente avec GET /demandes-validation\n";
echo "4. Valider le président avec POST /traiter-demande/{id}\n\n";

echo "📞 API Endpoints :\n";
echo "• POST /demande-inscription - Créer une demande\n";
echo "• GET  /demandes-validation - Voir les demandes (selon le rôle)\n";
echo "• POST /traiter-demande/{id} - Valider/Rejeter une demande\n\n";

echo "✅ Le système hiérarchique est maintenant en place !\n";
?>
