<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Emprunt;
use App\Entity\Livre;
use App\Entity\User;


use App\Repository\AuteurRepository;
use App\Repository\EmpruntRepository;
use App\Repository\EmprunteurRepository;
use App\Repository\GenreRepository;
use App\Repository\LivreRepository;
use App\Repository\UserRepository;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(
        UserRepository $userRepository,
        AuteurRepository $auteurRepository,
        LivreRepository $livreRepository,
        EmpruntRepository $empruntRepository,
        EmprunteurRepository $emprunteurRepository,
        GenreRepository $genreRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        //----------------------------------------------//
                      //USERS//
        //----------------------------------------------//
        exit();
        $users = $userRepository->findAll();
        dump($users);

        $users = $userRepository->find(1);
        dump($users);        

        $fooEmail = $userRepository->findOneBy(['email' => 'foo.foo@example.com']);
        dump($fooEmail);

        $emprunteur = $userRepository->findByRole('ROLE_EMPRUNTEUR');
        dump($emprunteur);
        
        //----------------------------------------------//
                      //LIVRES//
        //----------------------------------------------//

        $livres = $livreRepository->findAll();
        dump($livres);

        $livres = $livreRepository->find(1);
        dump($livres);

        $livres = $livreRepository->findByTitre('lorem');
        dump($livres);

        $livres = $livreRepository->findByAuteur(2);
        dump($livres);
        
        $livres = $livreRepository->findByGenre('roman');
        dump($livres);
        
                      //CREATIONS//

        $auteurs = $auteurRepository->findAll();
        $genres = $genreRepository->findAll(); 

        $newLivre = new Livre();
        $newLivre->setTitre('Totum autem id externum');
        $newLivre->setAnneeEdition(2020);
        $newLivre->setNombrePages(300);
        $newLivre->setCodeIsbn('9790412882714');
        $newLivre->setAuteur($auteurs[1]);
        $newLivre->addGenre($genres[5]);
        $entityManager->persist($newLivre);
    
        $entityManager->flush();
        
                      //MISE A JOUR//

        $genres = $genreRepository->findById(2);
        $genreID = $genreRepository->findById(5);

        $livre = $livreRepository->findById(2);
        $livre[0]->setTitre('Aperiendum est igitur');
        $livre[0]->removeGenre($genres[0]);
        $livre[0]->addGenre($genreID[0]);

        $entityManager->persist($livre[0]);
        $entityManager->flush();

                    //   SUPPRESSION//

        $livre = $livreRepository->findById(123);
        $entityManager->remove($livre[0]);
        $entityManager->flush();

        //----------------------------------------------//
                      //EMPRUNTEURS//
        //----------------------------------------------//

                      //LECTURE//
                      
        $emprunteurs = $emprunteurRepository->findAll();
        dump($emprunteurs);

        $emprunteurs = $emprunteurRepository->find(3);
        dump($emprunteurs);

        $emprunteur = $emprunteurRepository->findById(3);
        $user = $emprunteurRepository->findByUser($emprunteur);
        dump($emprunteur);

        $emprunteurs = $emprunteurRepository->findByNomOuPrenom("foo");
        dump($emprunteurs);

        $emprunteurs = $emprunteurRepository->findByTel('1234');
        dump($emprunteurs);

        $emprunteurs = $emprunteurRepository->findByDateCreation('2021-03-01 00:00:00');
        dump($emprunteurs);

        $emprunteurs = $emprunteurRepository->findByStatus(false);
        dump($emprunteurs);
        
        //----------------------------------------------//
                      //EMPRUNTS//
        //----------------------------------------------//

                      //LECTURE//

        $emprunts = $empruntRepository->findLastTen();
        dump($emprunts);

        $emprunts = $empruntRepository->findByEmprunteurId(2);
        dump($emprunts);

        $emprunts = $empruntRepository->findByLivreId(3);
        dump($emprunts);

        $emprunts = $empruntRepository->findByDateRetour('2021-01-01 00:00:00');
        dump($emprunts);

        $emprunts = $empruntRepository->findEmpruntsNonRendus();
        dump($emprunts);

        $emprunt = $empruntRepository->findOneByLivreIdEtDateRetour(3);
        dump($emprunt);
        
                      //CREATION//

        $emprunteurs = $emprunteurRepository->findAll();
        $livres = $livreRepository->findAll();

        $emprunt = new Emprunt();
        $emprunt->setDateEmprunt(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-12-01 16:00:00'));
        $emprunt->setDateRetour(NULL);
        $emprunt->setEmprunteur($emprunteurs[0]);
        $emprunt->setLivre($livres[0]);

        $entityManager->flush();
        dump($emprunt);

                      //MISE A JOUR//

        $emprunt = $empruntRepository->findById(3)[0];
        $emprunt->setDateRetour(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-05-01 10:00:00'));

        $entityManager->flush();
        dump($emprunt);

                      //SUPPRESSION//

        $emprunt = $empruntRepository->findById(42);
        $entityManager->remove($emprunt[0]);
        $entityManager->flush();

    }
}
