<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\EtudiantType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/etudiant')]
class EtudiantController extends AbstractController
{
    #[Route('/', name: 'etudiant')]
    public function index(): Response
    {
        return $this->render('etudiant/index.html.twig', [
            'controller_name' => 'EtudiantController',
        ]);
    }

    #[Route('/all/{page?1}/{nbr?12}', name: 'etudiant.list')]
    public function indexAll(ManagerRegistry $doctrine, $page, $nbr): Response{
        $repository = $doctrine->getRepository(Etudiant::class);
        $nbEtudiant = $repository->count([]);
        $nbPage = ceil($nbEtudiant / $nbr) ;
//        dd($nbPage);
        $etudiants = $repository->findBy([], [],$nbr,($page - 1) * $nbr );
        return $this->render('etudiant/index.html.twig', [
            'etudiants'=>$etudiants,
            'isPaginated'=>true,
            'nbPage' => $nbPage,
            'page'=>$page,
            'nbr' =>$nbr
        ]);
    }

    #[Route('/edit/{id?0}', name: 'etudiant.edit')]
    public function addEtudiant(Etudiant $etudiant = null, ManagerRegistry $doctrine, Request $request): Response
    {
        $new = false;

        if (!$etudiant) {
            $new = true;
            $etudiant = new Etudiant();
        }

        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            // si oui,
            // on va ajouter l'objet Etudiant dans la base de données
            $manager = $doctrine->getManager();
            $manager->persist($etudiant);
            $manager->flush();
            // message de succès
            if($new) {
                $message = " a été ajouté avec succès";
            } else {
                $message = " a été mis à jour avec succès";
            }
            $this->addFlash('success',$etudiant->getNom(). $message );
            // Rediriger verts la liste des étudiants
            return $this->redirectToRoute('etudiant.list');
        } else {
            //Sinon
            //On affiche notre formulaire
            return $this->render('etudiant/add-etudiant.html.twig', [
                'form' => $form->createView()
            ]);
        }

    }

    #[Route('/delete/{id}', name: 'etudiant.delete')]
    public function deleteEtudiant(Etudiant $etudiant = null, ManagerRegistry $doctrine): RedirectResponse {
        // Récupérer la etudiant
        if ($etudiant) {
            // Si la etudiant existe => le supprimer et retourner un flashMessage de succés
            $manager = $doctrine->getManager();
            // Ajoute la fonction de suppression dans la transaction
            $manager->remove($etudiant);
            // Exécuter la transacition
            $manager->flush();
            $this->addFlash('success', "L'etudiant a été supprimé avec succès");
        } else {
            //Sinon  retourner un flashMessage d'erreur
            $this->addFlash('error', "etudiant inexistant");
        }
        return $this->redirectToRoute('etudiant.list');
    }
}
