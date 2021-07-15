<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Emprunt;
use App\Entity\Emprunteur;
use App\Entity\Genre;
use App\Entity\Livre;
use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        // Création d'une instance de faker localisée en
        // français (fr) de France (FR).
        $this->faker = FakerFactory::create('fr_FR');
    }

    public static function getGroups(): array
    {
        // Cette fixture fait partie du groupe "prod".
        // Cela permet de cibler seulement certains fixtures
        // quand on exécute la commande doctrine:fixtures:load.
        // Pour que la méthode statique getGroups() soit prise
        // en compte, il faut que la classe implémente
        // l'interface FixtureGroupInterface.
        return ['test'];
    }

    public function load(ObjectManager $manager)
    {
        // Appel des fonctions qui vont créer les objets dans la BDD.
        // La fonction loadAdmins() ne renvoit pas de données mais les autres
        // fontions renvoit des données qui sont nécessaires à d'autres fonctions.
        $this->loadAdmin($manager, 4);
        $genres = $this->loadGenres($manager);
        $auteurs = $this->loadAuteurs($manager, 500);
        $livres = $this->loadLivres($manager, $auteurs, $genres);
        $emprunteurs = $this->loadEmprunteurs($manager, 100);
        // $emprunts = $this->loadEmprunts($manager, $emprunteurs, $livres);

        // Exécution des requêtes.
        // C-à-d envoi de la requête SQL à la BDD.
        $manager->flush();
    }

//-------------------------------------------------------------------------------------------------------------//
                                      // ADMIN //
//-------------------------------------------------------------------------------------------------------------//

    public function loadAdmin(ObjectManager $manager, int $count)
    {
        // création d'un user avec des données constantes
        // ici il s'agit du compte admin
        $user = new User();
        $user->setEmail('admin@example.com');
        // hashage du mot de passe
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        for ($i = 1; $i < $count; $i++) {
            // Création d'un nouveau user.
            $user = new User();
            $user->setEmail($this->faker->email());
            // Hachage du mot de passe.
            $password = $this->encoder->encodePassword($user, '123');
            $user->setPassword($password);
            // Le format de la chaîne de caractères ROLE_FOO_BAR_BAZ
            // est libre mais il vaut mieux suivre la convention
            // proposée par Symfony.
            $user->setRoles(['ROLE_EMPRUNTEUR']);

            // Demande d'enregistrement d'un objet dans la BDD.
            $manager->persist($user);
        }
    }

//-------------------------------------------------------------------------------------------------------------//
                                      // GENRES //
//-------------------------------------------------------------------------------------------------------------//

    public function loadGenres(ObjectManager $manager)
    {
        $livreGenres = [
            1 => [
                'nom'=>'poésie'
            ],
            2 => [
                'nom'=>'roman historique',
            ],
            3 => [
                'nom'=>'roman d\'amour',
            ],
            4 => [
                'nom'=>'roman d\'aventure',
            ],
            5 => [
                'nom'=>'science-fiction',
            ],
            6 => [
                'nom'=>'fantasy',
            ],
            7 => [
                'nom'=>'biographie',
            ],
            8 => [
                'nom'=>'témoignage',
            ],
            9 => [
                'nom'=>'théâtre',
            ],
            10 => [
                'nom'=>'essai',
            ],
            11 => [
                'nom'=>'journal intime'
            ],
            12 => [
                'nom'=>'nouvelle'
            ],
            13 => [
                'nom'=>'conte'
            ],
        ];

        foreach($livreGenres as $key => $value){
            $genre = new Genre();
            $genre->setNom($value['nom']);
            $manager->persist($genre);
        }
    }

//-------------------------------------------------------------------------------------------------------------//
                                      // AUTEURS //
//-------------------------------------------------------------------------------------------------------------//

    public function loadAuteurs(ObjectManager $manager, int $count)
    {
        $auteurIdentity = [
            1 => [
                'nom'=>'auteur inconnu',
                'prenom'=>''
            ],
            2 => [
                'nom'=>'Cartier',
                'prenom'=>'Hugues'
            ],
            3 => [
                'nom'=>'Lambert',
                'prenom'=>'Armand'
            ],
            4 => [
                'nom'=>'Moitessier',
                'prenom'=>'Thomas'
            ]
        ]; 

        foreach($auteurIdentity as $key => $value){
            $auteur = new Auteur();
            $auteur->setNom($value['nom']);
            $auteur->setPrenom($value['prenom']);
            $manager->persist($auteur);

            $auteurs[] = $auteur;
        } 
        
        for($i=0;$i<500;$i++){

            $auteur = new Auteur();
            $auteur->setNom($this->faker->lastname());
            $auteur->setPrenom($this->faker->firstname());
            $manager->persist($auteur);
            
            $auteurs[] = $auteur;

        }

        // On retourne le tableau Auteurs afin de pouvoir l'utiliser dans d'autres fonctions.
        return $auteurs;
    }

//-------------------------------------------------------------------------------------------------------------//
                                      // EMPRUNTEURS //
//-------------------------------------------------------------------------------------------------------------//

    public function loadEmprunteurs(ObjectManager $manager, int $count)
    {
        // Création d'un tableau qui contiendra les students qu'on va créer.
        // La fonction va pouvoir renvoyer ce tableau pour que d'autres fonctions
        // de création d'objets puissent les utiliser.
        $emprunteurs = [];

        // Création d'un nouveau user.
        $user = new User();
        $user->setEmail('student@example.com');
        // Hachage du mot de passe.
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        // Le format de la chaîne de caractères ROLE_FOO_BAR_BAZ
        // est libre mais il vaut mieux suivre la convention
        // proposée par Symfony.
        $user->setRoles(['ROLE_EMPRUNTEUR']);

        // Demande d'enregistrement d'un objet dans la BDD.
        $manager->persist($user);

        // Création d'un nouveau emprunteur.
        $emprunteur = new EMPRUNTEUR();
        $emprunteur->setPrenom('foo');
        $emprunteur->setNom('foo');
        $emprunteur->setTel('123456789');
        $emprunteur->setActif(true);
        $emprunteur->setDateCreation(\DateTime::createFromFormat('Y-m-d H:i:s', '2010-01-01 00:00:00'));
        $emprunteur->setDateModification(null);
        // Association d'un student et d'un user.
        $emprunteur->setUser($user);

        // Demande d'enregistrement d'un objet dans la BDD
        $manager->persist($emprunteur);

        // Ajout du premier emprunteur créé à la liste.
        $emprunteurs[] = $emprunteur;

        // Création de students avec des données aléatoires.
        // On démarre la boucle for avec $i = 1 et non $i = 0
        // car on a « déjà fait le premier tour » de la boucle
        // quand on a créé notre premier student ci-dessus.
        // Si le développeur demande N students, il faut retrancher
        // le student qui a été créé ci-dessus et en créer N-1
        // dans la boucle for.
        for ($i = 1; $i < $count; $i++) {

            // Création d'un nouveau user.
            $user = new User();
            $user->setEmail($this->faker->email());
            // Hachage du mot de passe.
            $password = $this->encoder->encodePassword($user, '123');
            $user->setPassword($password);
            // Le format de la chaîne de caractères ROLE_FOO_BAR_BAZ
            // est libre mais il vaut mieux suivre la convention
            // proposée par Symfony.
            $user->setRoles(['ROLE_EMPRUNTEUR']);

            // Demande d'enregistrement d'un objet dans la BDD
            $manager->persist($user);

            // Création d'un nouveau emprunteur.
            $emprunteur = new EMPRUNTEUR();
            $emprunteur->setPrenom($this->faker->firstname());
            $emprunteur->setNom($this->faker->lastname());
            $emprunteur->setTel($this->faker->phoneNumber());
            $emprunteur->setActif(true);
            $emprunteur->setDateCreation($this->faker->dateTimeThisDecade());
            // Récupération de la date de début.
            $dateCreation = $emprunteur->getDateCreation();
            // Création d'une date de fin à partir de la date de début.
            $dateModification = \DateTime::createFromFormat('Y-m-d H:i:s', $dateCreation->format('Y-m-d H:i:s'));
            // Ajout d'un interval de 4 mois à la date de début.
            $dateModification->add(new \DateInterval('P4M'));
            $emprunteur->setdateModification($dateModification);

            // Association d'un emprunteur et d'un user.
            $emprunteur->setUser($user);

            // Demande d'enregistrement d'un objet dans la BDD.
            $manager->persist($emprunteur);

            // Ajout du emprunteur créé à la liste.
            $emprunteurs[] = $emprunteur;
        }

        // Renvoi de la liste des emprunteurs créés.
        return $emprunteurs;
    
    }

//-------------------------------------------------------------------------------------------------------------//
                                      // LIVRES //
//-------------------------------------------------------------------------------------------------------------//

    public function loadLivres($manager, $auteurs, $genres) 
    {

    }

//-------------------------------------------------------------------------------------------------------------//
                                      // EMPRUNT //
//-------------------------------------------------------------------------------------------------------------//

}
