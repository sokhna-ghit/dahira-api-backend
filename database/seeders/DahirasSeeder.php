<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dahira;

class DahirasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver les contraintes de clé étrangère temporairement
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Vider d'abord la table
        Dahira::truncate();
        
        // Réactiver les contraintes de clé étrangère
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $dahiras = [
            // Région de Dakar - Mourides
            [
                'nom' => 'Dahira Touba Dakar',
                'ville' => 'Dakar',
                'region' => 'Dakar',
                'adresse' => 'Plateau, Dakar',
                'confrerie' => 'Mouride',
                'description' => 'Principal dahira mouride de la capitale',
                'imageUrl' => '',
                'nombreMembres' => 8000,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Serigne Touba Plateau',
                'ville' => 'Dakar',
                'region' => 'Dakar',
                'adresse' => 'Avenue Léopold Sédar Senghor',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride du centre-ville',
                'imageUrl' => '',
                'nombreMembres' => 3500,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Khadimou Rassoul Pikine',
                'ville' => 'Pikine',
                'region' => 'Dakar',
                'adresse' => 'Pikine Nord',
                'confrerie' => 'Mouride',
                'description' => 'Grand dahira mouride de Pikine',
                'imageUrl' => '',
                'nombreMembres' => 6000,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Serigne Fallou Guédiawaye',
                'ville' => 'Guédiawaye',
                'region' => 'Dakar',
                'adresse' => 'Golf Sud, Guédiawaye',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride de Guédiawaye',
                'imageUrl' => '',
                'nombreMembres' => 4200,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Lamp Fall Parcelles',
                'ville' => 'Parcelles Assainies',
                'region' => 'Dakar',
                'adresse' => 'Unité 25, Parcelles',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride des Parcelles Assainies',
                'imageUrl' => '',
                'nombreMembres' => 5100,
                'statut' => 'actif'
            ],
            
            // Région de Dakar - Tijanes
            [
                'nom' => 'Dahira Seydi El Hadji Malick',
                'ville' => 'Dakar',
                'region' => 'Dakar',
                'adresse' => 'Médina, Dakar',
                'confrerie' => 'Tijane',
                'description' => 'Principal dahira tijane de Dakar',
                'imageUrl' => '',
                'nombreMembres' => 7200,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Fass Tijani',
                'ville' => 'Fass',
                'region' => 'Dakar',
                'adresse' => 'Fass Delorme',
                'confrerie' => 'Tijane',
                'description' => 'Ancien dahira tijane historique',
                'imageUrl' => '',
                'nombreMembres' => 4800,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Al Mustaqim Liberté',
                'ville' => 'Liberté',
                'region' => 'Dakar',
                'adresse' => 'Liberté 6',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane moderne',
                'imageUrl' => '',
                'nombreMembres' => 3200,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Ansarou Dine Grand Yoff',
                'ville' => 'Grand Yoff',
                'region' => 'Dakar',
                'adresse' => 'Cité Millionnaire',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Grand Yoff',
                'imageUrl' => '',
                'nombreMembres' => 3600,
                'statut' => 'actif'
            ],
            
            // Région de Dakar - Layennes
            [
                'nom' => 'Dahira Layenne Yoff',
                'ville' => 'Yoff',
                'region' => 'Dakar',
                'adresse' => 'Village traditionnel de Yoff',
                'confrerie' => 'Layenne',
                'description' => 'Dahira principal layenne',
                'imageUrl' => '',
                'nombreMembres' => 15000,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Seydina Limamou Cambérène',
                'ville' => 'Cambérène',
                'region' => 'Dakar',
                'adresse' => 'Cambérène Centre',
                'confrerie' => 'Layenne',
                'description' => 'Grand dahira layenne de Cambérène',
                'imageUrl' => '',
                'nombreMembres' => 8500,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Mahdi Ouakam',
                'ville' => 'Ouakam',
                'region' => 'Dakar',
                'adresse' => 'Cité Ouakam',
                'confrerie' => 'Layenne',
                'description' => 'Dahira layenne d\'Ouakam',
                'imageUrl' => '',
                'nombreMembres' => 4200,
                'statut' => 'actif'
            ],
            
            // Région de Dakar - Niassènes
            [
                'nom' => 'Dahira Cheikh Ibrahim Niass Colobane',
                'ville' => 'Colobane',
                'region' => 'Dakar',
                'adresse' => 'Colobane Marché',
                'confrerie' => 'Niassène',
                'description' => 'Dahira niassène de Colobane',
                'imageUrl' => '',
                'nombreMembres' => 2800,
                'statut' => 'actif'
            ],
            
            // Région de Diourbel - Mourides
            [
                'nom' => 'Dahira Touba Mosquée',
                'ville' => 'Touba',
                'region' => 'Diourbel',
                'adresse' => 'Grande Mosquée de Touba',
                'confrerie' => 'Mouride',
                'description' => 'Grand dahira de la grande mosquée de Touba',
                'imageUrl' => '',
                'nombreMembres' => 25000,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Serigne Saliou',
                'ville' => 'Touba',
                'region' => 'Diourbel',
                'adresse' => 'Darou Miname, Touba',
                'confrerie' => 'Mouride',
                'description' => 'Dahira historique mouride',
                'imageUrl' => '',
                'nombreMembres' => 18000,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Khadimou Rassoul Mbacké',
                'ville' => 'Mbacké',
                'region' => 'Diourbel',
                'adresse' => 'Centre-ville Mbacké',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride de Mbacké',
                'imageUrl' => '',
                'nombreMembres' => 12000,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Serigne Mountakha',
                'ville' => 'Diourbel',
                'region' => 'Diourbel',
                'adresse' => 'Escale, Diourbel',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride moderne',
                'imageUrl' => '',
                'nombreMembres' => 8500,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Lamp Fall Bambey',
                'ville' => 'Bambey',
                'region' => 'Diourbel',
                'adresse' => 'Quartier Résidentiel, Bambey',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride de Bambey',
                'imageUrl' => '',
                'nombreMembres' => 6200,
                'statut' => 'actif'
            ],
            
            // Région de Diourbel - Tijanes
            [
                'nom' => 'Dahira Tijani Diourbel',
                'ville' => 'Diourbel',
                'region' => 'Diourbel',
                'adresse' => 'Quartier Ndorong, Diourbel',
                'confrerie' => 'Tijane',
                'description' => 'Principal dahira tijane de la région',
                'imageUrl' => '',
                'nombreMembres' => 4500,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Al Fath Bambey',
                'ville' => 'Bambey',
                'region' => 'Diourbel',
                'adresse' => 'Gare Routière, Bambey',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Bambey',
                'imageUrl' => '',
                'nombreMembres' => 3200,
                'statut' => 'actif'
            ],
            
            // Région de Thiès - Tijanes
            [
                'nom' => 'Dahira Malikiya Thiès',
                'ville' => 'Thiès',
                'region' => 'Thiès',
                'adresse' => 'Centre-ville Thiès',
                'confrerie' => 'Tijane',
                'description' => 'Grand dahira tijane de Thiès',
                'imageUrl' => '',
                'nombreMembres' => 9500,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Fayda Tijaniya Tivaouane',
                'ville' => 'Tivaouane',
                'region' => 'Thiès',
                'adresse' => 'Mosquée Seydi El Hadj Malick Sy',
                'confrerie' => 'Tijane',
                'description' => 'Dahira principal de Tivaouane',
                'imageUrl' => '',
                'nombreMembres' => 20000,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Malick Sy Mbour',
                'ville' => 'Mbour',
                'region' => 'Thiès',
                'adresse' => 'Quartier Gouye Mouride, Mbour',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Mbour',
                'imageUrl' => '',
                'nombreMembres' => 7200,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Ansarou Dine Rufisque',
                'ville' => 'Rufisque',
                'region' => 'Thiès',
                'adresse' => 'Keury Kao, Rufisque',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Rufisque',
                'imageUrl' => '',
                'nombreMembres' => 5800,
                'statut' => 'actif'
            ],
            
            // Région de Thiès - Mourides
            [
                'nom' => 'Dahira Mouride Thiès',
                'ville' => 'Thiès',
                'region' => 'Thiès',
                'adresse' => 'Quartier Randoulène, Thiès',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride de Thiès',
                'imageUrl' => '',
                'nombreMembres' => 4200,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Touba Mbour',
                'ville' => 'Mbour',
                'region' => 'Thiès',
                'adresse' => 'Saly Portudal, Mbour',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride de Mbour',
                'imageUrl' => '',
                'nombreMembres' => 3800,
                'statut' => 'actif'
            ],
            
            // Région de Thiès - Layennes
            [
                'nom' => 'Dahira Layenne Rufisque',
                'ville' => 'Rufisque',
                'region' => 'Thiès',
                'adresse' => 'Arafat, Rufisque',
                'confrerie' => 'Layenne',
                'description' => 'Dahira layenne de Rufisque',
                'imageUrl' => '',
                'nombreMembres' => 2800,
                'statut' => 'actif'
            ],
            
            // Région de Saint-Louis - Tijanes
            [
                'nom' => 'Dahira Tijaniya Saint-Louis',
                'ville' => 'Saint-Louis',
                'region' => 'Saint-Louis',
                'adresse' => 'Île de Saint-Louis',
                'confrerie' => 'Tijane',
                'description' => 'Dahira historique tijane',
                'imageUrl' => '',
                'nombreMembres' => 8500,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Podor Tijani',
                'ville' => 'Podor',
                'region' => 'Saint-Louis',
                'adresse' => 'Centre-ville Podor',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Podor',
                'imageUrl' => '',
                'nombreMembres' => 5200,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Dagana Malickite',
                'ville' => 'Dagana',
                'region' => 'Saint-Louis',
                'adresse' => 'Quartier Résidentiel, Dagana',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Dagana',
                'imageUrl' => '',
                'nombreMembres' => 4100,
                'statut' => 'actif'
            ],
            
            // Région de Saint-Louis - Qadiriya
            [
                'nom' => 'Dahira Qadiriya Saint-Louis',
                'ville' => 'Saint-Louis',
                'region' => 'Saint-Louis',
                'adresse' => 'Sor, Saint-Louis',
                'confrerie' => 'Qadiriya',
                'description' => 'Principal dahira qadiriya',
                'imageUrl' => '',
                'nombreMembres' => 6800,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Cheikh Sidiya Louga',
                'ville' => 'Louga',
                'region' => 'Louga',
                'adresse' => 'Centre-ville Louga',
                'confrerie' => 'Qadiriya',
                'description' => 'Dahira qadiriya de Louga',
                'imageUrl' => '',
                'nombreMembres' => 4500,
                'statut' => 'actif'
            ],
            
            // Région de Saint-Louis - Mourides
            [
                'nom' => 'Dahira Mouride Saint-Louis',
                'ville' => 'Saint-Louis',
                'region' => 'Saint-Louis',
                'adresse' => 'Pikine Saint-Louis',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride du nord',
                'imageUrl' => '',
                'nombreMembres' => 3200,
                'statut' => 'actif'
            ],
            
            // Région de Kaolack - Niassènes
            [
                'nom' => 'Dahira Faydatou Niassène Kaolack',
                'ville' => 'Kaolack',
                'region' => 'Kaolack',
                'adresse' => 'Quartier Léona, Kaolack',
                'confrerie' => 'Niassène',
                'description' => 'Grand dahira niassène',
                'imageUrl' => '',
                'nombreMembres' => 15000,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Ibrahim Niass Médina Baye',
                'ville' => 'Kaolack',
                'region' => 'Kaolack',
                'adresse' => 'Médina Baye, Kaolack',
                'confrerie' => 'Niassène',
                'description' => 'Dahira principal de Médina Baye',
                'imageUrl' => '',
                'nombreMembres' => 22000,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Niassène Kaffrine',
                'ville' => 'Kaffrine',
                'region' => 'Kaffrine',
                'adresse' => 'Centre-ville Kaffrine',
                'confrerie' => 'Niassène',
                'description' => 'Dahira niassène de Kaffrine',
                'imageUrl' => '',
                'nombreMembres' => 8500,
                'statut' => 'actif'
            ],
            
            // Région de Kaolack - Tijanes
            [
                'nom' => 'Dahira Tijani Kaolack',
                'ville' => 'Kaolack',
                'region' => 'Kaolack',
                'adresse' => 'Quartier Ndoffane, Kaolack',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Kaolack',
                'imageUrl' => '',
                'nombreMembres' => 6200,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Fatick Tijani',
                'ville' => 'Fatick',
                'region' => 'Fatick',
                'adresse' => 'Centre-ville Fatick',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Fatick',
                'imageUrl' => '',
                'nombreMembres' => 4800,
                'statut' => 'actif'
            ],
            
            // Région de Kaolack - Mourides
            [
                'nom' => 'Dahira Mouride Kaolack',
                'ville' => 'Kaolack',
                'region' => 'Kaolack',
                'adresse' => 'Quartier Dialègne, Kaolack',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride de Kaolack',
                'imageUrl' => '',
                'nombreMembres' => 5200,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Touba Kaffrine',
                'ville' => 'Kaffrine',
                'region' => 'Kaffrine',
                'adresse' => 'Quartier Escale, Kaffrine',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride de Kaffrine',
                'imageUrl' => '',
                'nombreMembres' => 3800,
                'statut' => 'actif'
            ],
            
            // Nouvelles régions et dahiras supplémentaires
            
            // Région de Ziguinchor - Tijanes
            [
                'nom' => 'Dahira Tijani Ziguinchor',
                'ville' => 'Ziguinchor',
                'region' => 'Ziguinchor',
                'adresse' => 'Quartier Kandialang',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Casamance',
                'imageUrl' => '',
                'nombreMembres' => 6500,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Oussouye Tijani',
                'ville' => 'Oussouye',
                'region' => 'Ziguinchor',
                'adresse' => 'Centre Oussouye',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane d\'Oussouye',
                'imageUrl' => '',
                'nombreMembres' => 2200,
                'statut' => 'actif'
            ],
            
            // Région de Kolda - Tijanes
            [
                'nom' => 'Dahira Kolda Tijani',
                'ville' => 'Kolda',
                'region' => 'Kolda',
                'adresse' => 'Quartier Sikilo, Kolda',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Kolda',
                'imageUrl' => '',
                'nombreMembres' => 4800,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Vélingara Tijani',
                'ville' => 'Vélingara',
                'region' => 'Kolda',
                'adresse' => 'Centre-ville Vélingara',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Vélingara',
                'imageUrl' => '',
                'nombreMembres' => 3100,
                'statut' => 'actif'
            ],
            
            // Région de Sédhiou - Tijanes
            [
                'nom' => 'Dahira Sédhiou Tijani',
                'ville' => 'Sédhiou',
                'region' => 'Sédhiou',
                'adresse' => 'Quartier Marsassoum',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Sédhiou',
                'imageUrl' => '',
                'nombreMembres' => 3800,
                'statut' => 'actif'
            ],
            
            // Région de Tambacounda - Tijanes et Mourides
            [
                'nom' => 'Dahira Tambacounda Tijani',
                'ville' => 'Tambacounda',
                'region' => 'Tambacounda',
                'adresse' => 'Quartier Plateau, Tambacounda',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de l\'est',
                'imageUrl' => '',
                'nombreMembres' => 5500,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Mouride Tambacounda',
                'ville' => 'Tambacounda',
                'region' => 'Tambacounda',
                'adresse' => 'Quartier Abattoir, Tambacounda',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride de l\'est',
                'imageUrl' => '',
                'nombreMembres' => 4200,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Kédougou Tijani',
                'ville' => 'Kédougou',
                'region' => 'Kédougou',
                'adresse' => 'Centre-ville Kédougou',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Kédougou',
                'imageUrl' => '',
                'nombreMembres' => 2800,
                'statut' => 'actif'
            ],
            
            // Région de Matam - Tijanes
            [
                'nom' => 'Dahira Matam Tijani',
                'ville' => 'Matam',
                'region' => 'Matam',
                'adresse' => 'Quartier Thiangaye, Matam',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Matam',
                'imageUrl' => '',
                'nombreMembres' => 4500,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Ranérou Tijani',
                'ville' => 'Ranérou',
                'region' => 'Matam',
                'adresse' => 'Centre Ranérou',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Ranérou',
                'imageUrl' => '',
                'nombreMembres' => 2100,
                'statut' => 'actif'
            ],
        ];

        foreach ($dahiras as $dahira) {
            Dahira::create($dahira);
        }
    }
}
