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
        $livres = $this->loadLivres($manager, $auteurs, $genres, 1000);
        $emprunteurs = $this->loadEmprunteurs($manager, 104);
        $emprunts = $this->loadEmprunts($manager, $emprunteurs,$livres, 200);

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

        for ($i = 4; $i < $count; $i++) {
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
        $genres= [];

        // création des genres
        foreach($livreGenres as $key => $value){
            $genre = new Genre();
            $genre->setNom($value['nom']);

            $manager->persist($genre);

            $genres[] = $genre;
        }

        // On retourne le tableau Auteurs afin de pouvoir l'utiliser dans d'autres fonctions.
        return $genres;
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

        // création des 4 auteurs en données constantes
        foreach($auteurIdentity as $key => $value){
            $auteur = new Auteur();
            $auteur->setNom($value['nom']);
            $auteur->setPrenom($value['prenom']);
            $manager->persist($auteur);

            $auteurs[] = $auteur;
        } 
        // création des 500 auteurs en données aléatoires
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
        // Création d'un tableau qui contiendra les emprunteurs et les users qu'on va créer.
        // La fonction va pouvoir renvoyer ce tableau pour que d'autres fonctions
        // de création d'objets puissent les utiliser.
        $emprunteurs = [];

        // Création d'un premier user.
        $user = new User();
        $user->setEmail('foo.foo@example.com');
        // Hachage du mot de passe.
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        // Le format de la chaîne de caractères ROLE_FOO_BAR_BAZ
        // est libre mais il vaut mieux suivre la convention
        // proposée par Symfony.
        $user->setRoles(['ROLE_EMPRUNTEUR']);

        // Demande d'enregistrement d'un objet dans la BDD.
        $manager->persist($user);

        // Création d'un premier emprunteur.
        $emprunteur = new EMPRUNTEUR();
        $emprunteur->setPrenom('foo');
        $emprunteur->setNom('foo');
        $emprunteur->setTel('123456789');
        $emprunteur->setActif(true);
        $emprunteur->setDateCreation(\DateTime::createFromFormat('Y-m-d H:i:s', '2010-01-01 00:00:00'));
        $emprunteur->setDateModification(null);
        // Association d'un emprunteur et d'un user.
        $emprunteur->setUser($user);

        // Demande d'enregistrement d'un objet dans la BDD
        $manager->persist($emprunteur);

        // Création d'un second user.
        $user = new User();
        $user->setEmail('bar.bar@example.com');
        // Hachage du mot de passe.
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        // Le format de la chaîne de caractères ROLE_FOO_BAR_BAZ
        // est libre mais il vaut mieux suivre la convention
        // proposée par Symfony.
        $user->setRoles(['ROLE_EMPRUNTEUR']);

        // Demande d'enregistrement d'un objet dans la BDD.
        $manager->persist($user);

        // Création d'un second emprunteur.
        $emprunteur = new EMPRUNTEUR();
        $emprunteur->setPrenom('bar');
        $emprunteur->setNom('bar');
        $emprunteur->setTel('123456789');
        $emprunteur->setActif(true);
        $emprunteur->setDateCreation(\DateTime::createFromFormat('Y-m-d H:i:s', '2010-01-01 00:00:00'));
        $emprunteur->setDateModification(null);
        // Association d'un emprunteur et d'un user.
        $emprunteur->setUser($user);

        // Demande d'enregistrement d'un objet dans la BDD
        $manager->persist($emprunteur);

        // Création d'un troisième user.
        $user = new User();
        $user->setEmail('baz.baz@example.com');
        // Hachage du mot de passe.
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        // Le format de la chaîne de caractères ROLE_FOO_BAR_BAZ
        // est libre mais il vaut mieux suivre la convention
        // proposée par Symfony.
        $user->setRoles(['ROLE_EMPRUNTEUR']);

        // Demande d'enregistrement d'un objet dans la BDD.
        $manager->persist($user);

        // Création d'un troisième emprunteur.
        $emprunteur = new EMPRUNTEUR();
        $emprunteur->setPrenom('baz');
        $emprunteur->setNom('baz');
        $emprunteur->setTel('123456789');
        $emprunteur->setActif(true);
        $emprunteur->setDateCreation(\DateTime::createFromFormat('Y-m-d H:i:s', '2010-01-01 00:00:00'));
        $emprunteur->setDateModification(null);
        // Association d'un emprunteur et d'un user.
        $emprunteur->setUser($user);

        // Demande d'enregistrement d'un objet dans la BDD
        $manager->persist($emprunteur);

        // // Création de 100 emprunteurs avec des données aléatoires.
        // // Si le développeur demande N emprunteur, il faut retrancher
        // // l'emprunteur qui a été créé ci-dessus et en créer N-1
        // // dans la boucle for.
        for ($i = 4; $i < $count; $i++) {

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

    public function loadLivres($manager, Array $auteursParam, Array $genresParam, int $count) 
    {
        $livres = [];

        $auteur = $auteursParam[0];
        $genre = $genresParam[0];

        // Création d'un premier Book avec des données constantes.
        $livre = new Livre();
        
        $livre->setTitre('Lorem ipsum dolor sit amet');
        $livre->setAnneeEdition(2010);
        $livre->setNombrePages(100);
        $livre->setCodeIsbn('9785786930024');
        $livre->setAuteur($auteur);
        $livre->addGenre($genre);

        $manager->persist($livre);

        $livres[] = $livre;

        // Création d'un second livre avec des données constantes.
        $auteur = $auteursParam[1];
        $genre = $genresParam[1];

        $livre = new Livre();
        
        $livre->setTitre("Consectetur adipiscing elit");
        $livre->setAnneeEdition(2011);
        $livre->setNombrePages(150);
        $livre->setCodeIsbn('9783817260935');    
        $livre->setAuteur($auteur);
        $livre->addGenre($genre);


        $manager->persist($livre);
        
        $livres[] = $livre;

        // Création d'un troisième livre avec des données constantes.
        $auteur = $auteursParam[2];
        $genre = $genresParam[2];

        $livre = new Livre();

        $livre->setTitre('Mihi quidem Antiochum');
        $livre->setAnneeEdition(2012);
        $livre->setNombrePages(200);
        $livre->setCodeIsbn('9782020493727');
        $livre->setAuteur($auteur);
        $livre->addGenre($genre);

        $manager->persist($livre);

        $livres[] = $livre;

        // Création d'un quatrième livre avec des données constantes.
        $auteur = $auteursParam[3];
        $genre = $genresParam[3];

        $livre = new Livre();

        $livre->setTitre('Quem audis satis belle');
        $livre->setAnneeEdition(2013);
        $livre->setNombrePages(250);
        $livre->setCodeIsbn('9794059561353');
        $livre->setAuteur($auteur);
        $livre->addGenre($genre);

        $manager->persist($livre);

        $livres[] = $livre;

        // création des 1000 livres en données aléatoires
        for($i=0;$i<1000;$i++){

            // Choisir un auteur au hasard à chaque tour
            $randomAuteur = $this->faker->randomElement($auteursParam);
            // Choisir un genre au hasard à chaque tour
            $randomGenre = $this->faker->randomElement($genresParam);

            $livre = new Livre();

            $livre->setTitre($this->faker->sentence($nbWords = 4, $variableNbWords = true));
            $livre->setAnneeEdition($this->faker->year($max = 'now'));
            $livre->setNombrePages($this->faker->numberBetween($min = 40, $max = 1100));
            $livre->setCodeIsbn($this->faker->isbn13());
            $livre->setAuteur($randomAuteur);
            $livre->addGenre($randomGenre);

            $manager->persist($livre);

            $livres[] = $livre;
        }

        return $livres;
    }

//-------------------------------------------------------------------------------------------------------------//
                                      // EMPRUNTS //
//-------------------------------------------------------------------------------------------------------------//

public function loadEmprunts(ObjectManager $manager, Array $emprunteurs, $livres, int $count)
{
    $emprunts = [];

    //Création d'un premier emprunt a données constante
    $emprunt = new Emprunt();
    $emprunt->setDateEmprunt(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-02-01 10:00:00'));
    $dateEmprunt = $emprunt->getDateEmprunt();
    $dateModification = \DateTime::createFromFormat('Y-m-d H:i:s',  $dateEmprunt->format('Y-m-d H:i:s'));
    $dateModification->add(new \DateInterval('P1M'));
    $emprunt->setDateRetour($dateModification);
    $emprunt->setEmprunteur($emprunteurs[0]);
    $emprunt->setLivre($livres[0]);
    
    $manager->persist($emprunt);
    
    $emprunts[] = $emprunt;
    
    // //Création d'un second emprunt a données constante
    $emprunt = new Emprunt();
    $emprunt->setDateEmprunt(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-01-01 10:00:00'));
    $dateEmprunt = $emprunt->getDateEmprunt();
    $dateModification = \DateTime::createFromFormat('Y-m-d H:i:s',  $dateEmprunt->format('Y-m-d H:i:s'));
    $dateModification->add(new \DateInterval('P1M'));
    $emprunt->setDateRetour($dateModification);
    $emprunt->setEmprunteur($emprunteurs[1]);
    $emprunt->setLivre($livres[1]);
    
    $manager->persist($emprunt);
    
    $emprunts[] = $emprunt;
    
    // //Création d'un troisième emprunt a données constante
    $emprunt = new Emprunt();
    $emprunt->setDateEmprunt(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-04-01 10:00:00'));
    $dateEmprunt = $emprunt->getDateEmprunt();
    $dateModification = \DateTime::createFromFormat('Y-m-d H:i:s',  $dateEmprunt->format('Y-m-d H:i:s'));
    $dateModification->add(new \DateInterval('P1M'));
    $emprunt->setDateRetour($dateModification);
    $emprunt->setEmprunteur($emprunteurs[2]);
    $emprunt->setLivre($livres[2]);
    
    $manager->persist($emprunt);
    
    $emprunts[] = $emprunt;

    // création de 200 emprunts a données aléatoires
    for($i=0;$i<200;$i++){
        $modification = $this->faker->boolean($chanceOfGettingTrue = 50);
        $randomMonth = $this->faker->numberBetween($min = 1, $max = 7);
        $randomHour = $this->faker->numberBetween($min = 1, $max = 24);
        // choisir un emprunteur au hasard a chaque tour
        $randomEmprunteur = $this->faker->randomElement($emprunteurs);
        // choisir un livre au hasard a chaque tour
        $randomLivre = $this->faker->randomElement($livres);
        // Création des données aléatoires des données tests
        $emprunt = new Emprunt();
        $emprunt->setDateEmprunt($this->faker->dateTime($max = 'now', $timezone = null));
        $dateEmprunt = $emprunt->getDateEmprunt();
        $dateModification = \DateTime::createFromFormat('Y-m-d H:i:s',  $dateEmprunt->format('Y-m-d H:i:s'));

        $emprunt->setDateEmprunt($this->faker->dateTime($max = 'now', $timezone = null));

        if($modification){
            $dateEmprunt = $emprunt->getDateEmprunt();
            $DateRetour = \DateTime::createFromFormat('Y-m-d H:i:s', $dateEmprunt->format('Y-m-d H:i:s'));
            $DateRetour->add(new \DateInterval("P{$randomMonth}M"));
            $DateRetour->add(new \DateInterval("PT{$randomHour}H"));
            $emprunt->setDateRetour($DateRetour);
        }

        $emprunt->setEmprunteur($randomEmprunteur);
        $emprunt->setLivre($randomLivre);
        
        $manager->persist($emprunt);
        
        $emprunts[] = $emprunt;
    }
}    


}
