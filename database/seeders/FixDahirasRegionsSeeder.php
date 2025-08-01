<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dahira;

class FixDahirasRegionsSeeder extends Seeder
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
            // RÉGION DE DAKAR
            [
                'nom' => 'Dahira Touba Dakar',
                'ville' => 'Dakar',
                'region' => 'Dakar',
                'adresse' => 'Plateau, Dakar',
                'confrerie' => 'Mouride',
                'description' => 'Principal dahira mouride de la capitale sénégalaise, rassemblant des milliers de fidèles chaque semaine',
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
                'description' => 'Dahira mouride moderne du centre-ville de Dakar, actif dans l\'éducation religieuse',
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
                'description' => 'Grand dahira mouride de la banlieue dakaroise, très engagé socialement',
                'imageUrl' => '',
                'nombreMembres' => 6000,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Seydi El Hadji Malick',
                'ville' => 'Dakar',
                'region' => 'Dakar',
                'adresse' => 'Médina, Dakar',
                'confrerie' => 'Tijane',
                'description' => 'Principal dahira tijane de Dakar, héritier de la tradition de El Hadj Malick Sy',
                'imageUrl' => '',
                'nombreMembres' => 7200,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Fass Tijani',
                'ville' => 'Dakar',
                'region' => 'Dakar',
                'adresse' => 'Fass Delorme',
                'confrerie' => 'Tijane',
                'description' => 'Ancien dahira tijane historique du quartier de Fass, riche en traditions',
                'imageUrl' => '',
                'nombreMembres' => 4800,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Layenne Yoff',
                'ville' => 'Yoff',
                'region' => 'Dakar',
                'adresse' => 'Village traditionnel de Yoff',
                'confrerie' => 'Layenne',
                'description' => 'Dahira principal de la confrérie layenne, gardien des traditions ancestrales',
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
                'description' => 'Grand dahira layenne de Cambérène, centre spirituel important',
                'imageUrl' => '',
                'nombreMembres' => 8500,
                'statut' => 'actif'
            ],
            
            // RÉGION DE THIÈS
            [
                'nom' => 'Dahira Fayda Tijaniya Tivaouane',
                'ville' => 'Tivaouane',
                'region' => 'Thiès',
                'adresse' => 'Mosquée Seydi El Hadj Malick Sy',
                'confrerie' => 'Tijane',
                'description' => 'Dahira principal de Tivaouane, berceau de la Tijaniya au Sénégal',
                'imageUrl' => '',
                'nombreMembres' => 20000,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Malikiya Thiès',
                'ville' => 'Thiès',
                'region' => 'Thiès',
                'adresse' => 'Centre-ville Thiès',
                'confrerie' => 'Tijane',
                'description' => 'Grand dahira tijane de la ville de Thiès, très actif dans l\'enseignement',
                'imageUrl' => '',
                'nombreMembres' => 9500,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Malick Sy Mbour',
                'ville' => 'Mbour',
                'region' => 'Thiès',
                'adresse' => 'Quartier Gouye Mouride, Mbour',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de la côte, alliant tradition et modernité',
                'imageUrl' => '',
                'nombreMembres' => 7200,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Mouride Thiès',
                'ville' => 'Thiès',
                'region' => 'Thiès',
                'adresse' => 'Quartier Randoulène, Thiès',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride de Thiès, foyer d\'éducation et de spiritualité',
                'imageUrl' => '',
                'nombreMembres' => 4200,
                'statut' => 'actif'
            ],
            
            // RÉGION DE DIOURBEL
            [
                'nom' => 'Dahira Touba Mosquée',
                'ville' => 'Touba',
                'region' => 'Diourbel',
                'adresse' => 'Grande Mosquée de Touba',
                'confrerie' => 'Mouride',
                'description' => 'Grand dahira de la grande mosquée de Touba, cœur spirituel du mouridisme',
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
                'description' => 'Dahira historique mouride dédié à Serigne Saliou Mbacké',
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
                'description' => 'Dahira mouride de Mbacké, ville natale du fondateur Cheikh Ahmadou Bamba',
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
                'description' => 'Dahira mouride moderne de Diourbel, référence en éducation islamique',
                'imageUrl' => '',
                'nombreMembres' => 8500,
                'statut' => 'actif'
            ],
            
            // RÉGION DE SAINT-LOUIS
            [
                'nom' => 'Dahira Tijaniya Saint-Louis',
                'ville' => 'Saint-Louis',
                'region' => 'Saint-Louis',
                'adresse' => 'Île de Saint-Louis',
                'confrerie' => 'Tijane',
                'description' => 'Dahira historique tijane de l\'ancienne capitale, patrimoine spirituel',
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
                'description' => 'Dahira tijane du fleuve Sénégal, gardien des traditions halpular',
                'imageUrl' => '',
                'nombreMembres' => 5200,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Qadiriya Saint-Louis',
                'ville' => 'Saint-Louis',
                'region' => 'Saint-Louis',
                'adresse' => 'Sor, Saint-Louis',
                'confrerie' => 'Qadiriya',
                'description' => 'Principal dahira qadiriya du nord, héritier des traditions soufies anciennes',
                'imageUrl' => '',
                'nombreMembres' => 6800,
                'statut' => 'actif'
            ],
            
            // RÉGION DE KAOLACK
            [
                'nom' => 'Dahira Ibrahim Niass Médina Baye',
                'ville' => 'Kaolack',
                'region' => 'Kaolack',
                'adresse' => 'Médina Baye, Kaolack',
                'confrerie' => 'Niassène',
                'description' => 'Dahira principal de Médina Baye, centre mondial de la Faydah Tijaniya',
                'imageUrl' => '',
                'nombreMembres' => 22000,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Faydatou Niassène Kaolack',
                'ville' => 'Kaolack',
                'region' => 'Kaolack',
                'adresse' => 'Quartier Léona, Kaolack',
                'confrerie' => 'Niassène',
                'description' => 'Grand dahira niassène de Kaolack, foyer de rayonnement spirituel',
                'imageUrl' => '',
                'nombreMembres' => 15000,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Tijani Kaolack',
                'ville' => 'Kaolack',
                'region' => 'Kaolack',
                'adresse' => 'Quartier Ndoffane, Kaolack',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane traditionnel de Kaolack, centre commercial et spirituel',
                'imageUrl' => '',
                'nombreMembres' => 6200,
                'statut' => 'actif'
            ],
            
            // RÉGION DE FATICK
            [
                'nom' => 'Dahira Fatick Tijani',
                'ville' => 'Fatick',
                'region' => 'Fatick',
                'adresse' => 'Centre-ville Fatick',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Fatick, ancré dans les traditions sérères islamisées',
                'imageUrl' => '',
                'nombreMembres' => 4800,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Mouride Fatick',
                'ville' => 'Fatick',
                'region' => 'Fatick',
                'adresse' => 'Quartier Escale, Fatick',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride de Fatick, pont entre traditions sérères et mourides',
                'imageUrl' => '',
                'nombreMembres' => 3200,
                'statut' => 'actif'
            ],
            
            // RÉGION DE KAFFRINE
            [
                'nom' => 'Dahira Niassène Kaffrine',
                'ville' => 'Kaffrine',
                'region' => 'Kaffrine',
                'adresse' => 'Centre-ville Kaffrine',
                'confrerie' => 'Niassène',
                'description' => 'Dahira niassène de la nouvelle région de Kaffrine, dynamique et moderne',
                'imageUrl' => '',
                'nombreMembres' => 8500,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Touba Kaffrine',
                'ville' => 'Kaffrine',
                'region' => 'Kaffrine',
                'adresse' => 'Quartier Escale, Kaffrine',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride de Kaffrine, centre d\'attraction spirituelle',
                'imageUrl' => '',
                'nombreMembres' => 3800,
                'statut' => 'actif'
            ],
            
            // RÉGION DE LOUGA
            [
                'nom' => 'Dahira Cheikh Sidiya Louga',
                'ville' => 'Louga',
                'region' => 'Louga',
                'adresse' => 'Centre-ville Louga',
                'confrerie' => 'Qadiriya',
                'description' => 'Dahira qadiriya de Louga, référence de l\'érudition islamique au Sénégal',
                'imageUrl' => '',
                'nombreMembres' => 4500,
                'statut' => 'actif'
            ],
            [
                'nom' => 'Dahira Mouride Louga',
                'ville' => 'Louga',
                'region' => 'Louga',
                'adresse' => 'Quartier Keur Serigne Louga',
                'confrerie' => 'Mouride',
                'description' => 'Dahira mouride de Louga, actif dans l\'agriculture et l\'éducation',
                'imageUrl' => '',
                'nombreMembres' => 3600,
                'statut' => 'actif'
            ],
            
            // RÉGION DE ZIGUINCHOR
            [
                'nom' => 'Dahira Tijani Ziguinchor',
                'ville' => 'Ziguinchor',
                'region' => 'Ziguinchor',
                'adresse' => 'Quartier Kandialang',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Casamance, symbole de paix et de tolérance religieuse',
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
                'description' => 'Dahira tijane d\'Oussouye, dialogue inter-religieux en Casamance',
                'imageUrl' => '',
                'nombreMembres' => 2200,
                'statut' => 'actif'
            ],
            
            // RÉGION DE KOLDA
            [
                'nom' => 'Dahira Kolda Tijani',
                'ville' => 'Kolda',
                'region' => 'Kolda',
                'adresse' => 'Quartier Sikilo, Kolda',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Kolda, centre spirituel de la Haute Casamance',
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
                'description' => 'Dahira tijane de Vélingara, carrefour commercial et spirituel',
                'imageUrl' => '',
                'nombreMembres' => 3100,
                'statut' => 'actif'
            ],
            
            // RÉGION DE SÉDHIOU
            [
                'nom' => 'Dahira Sédhiou Tijani',
                'ville' => 'Sédhiou',
                'region' => 'Sédhiou',
                'adresse' => 'Quartier Marsassoum',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Sédhiou, gardien des traditions mandingues islamisées',
                'imageUrl' => '',
                'nombreMembres' => 3800,
                'statut' => 'actif'
            ],
            
            // RÉGION DE TAMBACOUNDA
            [
                'nom' => 'Dahira Tambacounda Tijani',
                'ville' => 'Tambacounda',
                'region' => 'Tambacounda',
                'adresse' => 'Quartier Plateau, Tambacounda',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de l\'est du Sénégal, pont vers l\'Afrique de l\'Ouest',
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
                'description' => 'Dahira mouride de l\'est, centre de développement rural et spirituel',
                'imageUrl' => '',
                'nombreMembres' => 4200,
                'statut' => 'actif'
            ],
            
            // RÉGION DE KÉDOUGOU
            [
                'nom' => 'Dahira Kédougou Tijani',
                'ville' => 'Kédougou',
                'region' => 'Kédougou',
                'adresse' => 'Centre-ville Kédougou',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Kédougou, ouverture vers la Guinée et le Mali',
                'imageUrl' => '',
                'nombreMembres' => 2800,
                'statut' => 'actif'
            ],
            
            // RÉGION DE MATAM
            [
                'nom' => 'Dahira Matam Tijani',
                'ville' => 'Matam',
                'region' => 'Matam',
                'adresse' => 'Quartier Thiangaye, Matam',
                'confrerie' => 'Tijane',
                'description' => 'Dahira tijane de Matam, tradition halpular et spiritualité islamique',
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
                'description' => 'Dahira tijane de Ranérou, au cœur du Fouta Toro spirituel',
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
