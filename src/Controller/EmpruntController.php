<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Form\EmpruntType;
use App\Repository\EmpruntRepository;
use App\Repository\EmprunteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface; // Nous appelons le bundle KNP Paginator
/**
 * @Route("/emprunt")
 */
class EmpruntController extends AbstractController
{
    /**
     * @Route("/", name="emprunt_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator,EmpruntRepository $empruntRepository, EmprunteurRepository $emprunteurRepository): Response
    {

            $emprunts = $empruntRepository->findAll();
            // Récupération du compte de l'utilisateur qui est connecté
            $user = $this->getUser();
    
            // On vérifie si l'utilisateur est un emprunteur 
            if ($this->isGranted('ROLE_EMPRUNTEUR')) {
                // Récupèration du profil emprunteur
                $user = $this->getUser();

                $emprunteur = $emprunteurRepository->findOneByUser($user);
                $emprunts = $emprunteur->getEmprunts();
                
                return $this->render('emprunt/index.html.twig', [
                'emprunts' => $emprunts
            ]);
    
            } elseif ($this->isGranted('ROLE_ADMIN'))  {
                // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri
                $donnees = $this->getDoctrine()->getRepository(Emprunt::class)->findBy([],['id' => 'ASC']);

                $emprunt = $paginator->paginate(
                    $donnees, // Requête contenant les données à paginer (ici nos articles)
                    $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                    15 // Nombre de résultats par page
                );

                return $this->render('emprunt/index.html.twig', [
                    'emprunts' => $emprunt
                ]);
            }
        
    }

    /**
     * @Route("/new", name="emprunt_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $emprunt = new Emprunt();
        $form = $this->createForm(EmpruntType::class, $emprunt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($emprunt);
            $entityManager->flush();

            return $this->redirectToRoute('emprunt_index');
        }

        return $this->render('emprunt/new.html.twig', [
            'emprunt' => $emprunt,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="emprunt_show", methods={"GET"})
     */

    public function show(Emprunt $emprunt, EmprunteurRepository $emprunteurRepository): Response
    {
        if ($this->isGranted('ROLE_EMPRUNTEUR')) {
            // L'utilisateur est un emprunteur
            
            // On récupère le compte de l'utilisateur authentifié
            $user = $this->getUser();

            // On récupère le profil emprunteur lié au compte utilisateur
            $emprunteur = $emprunteurRepository->findOneByUser($user);

            // On vérifie si la school year que l'utilisateur demande et la school year
            // auquel il est rattaché correspondent.
            // Si ce n'est pas le cas on lui renvoit un code 404
            if (!$emprunteur->getEmprunts()->contains($emprunt)) {
                throw new NotFoundHttpException();
            }
        }

        return $this->render('emprunt/show.html.twig', [
            'emprunt' => $emprunt,
        ]);
    }

    // public function show(Emprunt $emprunt): Response
    // {
    //     return $this->render('emprunt/show.html.twig', [
    //         'emprunt' => $emprunt,
    //     ]);
    // }

    /**
     * @Route("/{id}/edit", name="emprunt_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Emprunt $emprunt): Response
    {
        $form = $this->createForm(EmpruntType::class, $emprunt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('emprunt_index');
        }

        return $this->render('emprunt/edit.html.twig', [
            'emprunt' => $emprunt,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="emprunt_delete", methods={"POST"})
     */
    public function delete(Request $request, Emprunt $emprunt): Response
    {
        if ($this->isCsrfTokenValid('delete'.$emprunt->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($emprunt);
            $entityManager->flush();
        }

        return $this->redirectToRoute('emprunt_index');
    }
}
