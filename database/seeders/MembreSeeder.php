<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Membre;
use App\Models\Dahira;
use Carbon\Carbon;

class MembreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les dahiras existants
        $dahiras = Dahira::all();
        
        if ($dahiras->isEmpty()) {
            $this->command->info('Aucun dahira trouvé. Veuillez créer des dahiras d\'abord.');
            return;
        }

        $dahiraTest = $dahiras->first();

        // Créer des membres de test pour le premier dahira
        $membres = [
            [
                'nom' => 'Fall',
                'prenom' => 'Aminata',
                'email' => 'aminata.fall@test.sn',
                'telephone' => '+221771234567',
                'adresse' => 'Médina, Dakar',
                'genre' => 'féminin',
                'date_naissance' => '1985-03-15',
                'profession' => 'Enseignante',
                'statut' => 'actif',
                'commentaires' => 'Très active dans les activités du dahira',
                'date_inscription' => Carbon::now()->subMonths(6),
                'dahira_id' => $dahiraTest->id,
            ],
            [
                'nom' => 'Ndiaye',
                'prenom' => 'Moussa',
                'email' => 'moussa.ndiaye@test.sn',
                'telephone' => '+221772345678',
                'adresse' => 'Plateau, Dakar',
                'genre' => 'masculin',
                'date_naissance' => '1978-08-22',
                'profession' => 'Commerçant',
                'statut' => 'actif',
                'commentaires' => 'Membre fondateur du dahira',
                'date_inscription' => Carbon::now()->subYear(),
                'dahira_id' => $dahiraTest->id,
            ],
            [
                'nom' => 'Sarr',
                'prenom' => 'Fatou',
                'email' => 'fatou.sarr@test.sn',
                'telephone' => '+221773456789',
                'adresse' => 'Parcelles Assainies, Dakar',
                'genre' => 'féminin',
                'date_naissance' => '1992-12-03',
                'profession' => 'Étudiante',
                'statut' => 'actif',
                'commentaires' => 'Nouvelle membre, très motivée',
                'date_inscription' => Carbon::now()->subMonths(2),
                'dahira_id' => $dahiraTest->id,
            ],
            [
                'nom' => 'Diop',
                'prenom' => 'Omar',
                'email' => 'omar.diop@test.sn',
                'telephone' => '+221774567890',
                'adresse' => 'Almadies, Dakar',
                'genre' => 'masculin',
                'date_naissance' => '1980-05-17',
                'profession' => 'Ingénieur',
                'statut' => 'actif',
                'commentaires' => 'Responsable des questions techniques',
                'date_inscription' => Carbon::now()->subMonths(8),
                'dahira_id' => $dahiraTest->id,
            ],
            [
                'nom' => 'Ba',
                'prenom' => 'Khadija',
                'email' => 'khadija.ba@test.sn',
                'telephone' => '+221775678901',
                'adresse' => 'Guédiawaye, Dakar',
                'genre' => 'féminin',
                'date_naissance' => '1988-09-30',
                'profession' => 'Infirmière',
                'statut' => 'actif',
                'commentaires' => 'S\'occupe des questions de santé',
                'date_inscription' => Carbon::now()->subMonths(4),
                'dahira_id' => $dahiraTest->id,
            ],
            [
                'nom' => 'Sow',
                'prenom' => 'Ibrahima',
                'email' => 'ibrahima.sow@test.sn',
                'telephone' => '+221776789012',
                'adresse' => 'Pikine, Dakar',
                'genre' => 'masculin',
                'date_naissance' => '1975-01-12',
                'profession' => 'Chauffeur',
                'statut' => 'inactif',
                'commentaires' => 'En voyage pour le moment',
                'date_inscription' => Carbon::now()->subMonths(10),
                'dahira_id' => $dahiraTest->id,
            ],
            [
                'nom' => 'Diouf',
                'prenom' => 'Aissatou',
                'email' => 'aissatou.diouf@test.sn',
                'telephone' => '+221777890123',
                'adresse' => 'Rufisque, Dakar',
                'genre' => 'féminin',
                'date_naissance' => '1990-07-25',
                'profession' => 'Coiffeuse',
                'statut' => 'actif',
                'commentaires' => 'Très présente aux événements',
                'date_inscription' => Carbon::now()->subMonths(3),
                'dahira_id' => $dahiraTest->id,
            ],
            [
                'nom' => 'Thiam',
                'prenom' => 'Mamadou',
                'email' => 'mamadou.thiam@test.sn',
                'telephone' => '+221778901234',
                'adresse' => 'Mbao, Dakar',
                'genre' => 'masculin',
                'date_naissance' => '1983-11-08',
                'profession' => 'Mécanicien',
                'statut' => 'actif',
                'commentaires' => 'Aide pour les réparations',
                'date_inscription' => Carbon::now()->subMonths(7),
                'dahira_id' => $dahiraTest->id,
            ],
        ];

        foreach ($membres as $membreData) {
            Membre::create($membreData);
        }

        $this->command->info('8 membres de test créés avec succès pour le dahira: ' . $dahiraTest->nom);

        // Si il y a d'autres dahiras, créer quelques membres pour eux aussi
        if ($dahiras->count() > 1) {
            foreach ($dahiras->skip(1) as $dahira) {
                $autresMembres = [
                    [
                        'nom' => 'Cissé',
                        'prenom' => 'Mariama',
                        'email' => "mariama.cisse.{$dahira->id}@test.sn",
                        'telephone' => '+221779012345',
                        'adresse' => 'Centre-ville',
                        'genre' => 'féminin',
                        'date_naissance' => '1987-04-18',
                        'profession' => 'Secrétaire',
                        'statut' => 'actif',
                        'commentaires' => 'Membre actif',
                        'date_inscription' => Carbon::now()->subMonths(5),
                        'dahira_id' => $dahira->id,
                    ],
                    [
                        'nom' => 'Sylla',
                        'prenom' => 'Ousmane',
                        'email' => "ousmane.sylla.{$dahira->id}@test.sn",
                        'telephone' => '+221780123456',
                        'adresse' => 'Banlieue',
                        'genre' => 'masculin',
                        'date_naissance' => '1981-06-14',
                        'profession' => 'Agriculteur',
                        'statut' => 'actif',
                        'commentaires' => 'Très dévoué',
                        'date_inscription' => Carbon::now()->subMonths(6),
                        'dahira_id' => $dahira->id,
                    ],
                ];

                foreach ($autresMembres as $membreData) {
                    Membre::create($membreData);
                }
            }
            
            $this->command->info('2 membres additionnels créés pour chaque autre dahira.');
        }
    }
}
