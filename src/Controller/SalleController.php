<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\SalleRepository;

use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Salle;

use App\Form\SalleType;

use Symfony\Component\HttpFoundation\Request;

class SalleController extends AbstractController
{
    #[Route('/Salle/Read_Back', name: 'Read_Back_Salle')]
    public function ReadBackSalle(SalleRepository $repo): Response
    {
        $Salles = $repo->findAll();

        return $this->render('Salle/ReadSalleBack.html.twig', [
            'Salles' => $Salles,
        ]);
    }

    #[Route('/Salle/Read_Front', name: 'Read_Front_Salle')]
    public function ReadFrontSalle(SalleRepository $repo): Response
    {
        $Salles = $repo->findAll();

        return $this->render('Salle/ReadSalleFront.html.twig', [
            'Salles' => $Salles,
        ]);
    }

    #[Route('/Salle/Read_Front/{id}', name: 'Read_Front_One_Salle')]
    public function showSalle(Salle $salle): Response
    {
        return $this->render('salle/ReadOneSalleFront.html.twig', [
            'salle' => $salle,
        ]);
    }

    #[Route('/Salle/Like/{id}', name: 'Like_Salle')]
    public function likeSalle(
        ManagerRegistry $doctrine,
        $id,
        Request $req
    ): Response {
        $em = $doctrine->getManager();
        $Salle = $doctrine->getRepository(Salle::class)->find($id);
        $Salle->setLikes($Salle->getLikes()+1);
        $em->persist($Salle);
        $em->flush();

        return $this->redirectToRoute('Read_Front_Salle');
    }

    #[Route('/Salle/Create', name: 'Create_Salle')]
    public function CreateSalle(
        ManagerRegistry $doctrine,
        Request $req
    ): Response {
        $em = $doctrine->getManager();
        $Salle = new Salle();
        $form = $this->createForm(SalleType::class, $Salle);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $req->files->get('salle')['image'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($uploads_directory, $filename);
            $Salle->setImage($filename);
            $em->persist($Salle);
            $em->flush();

            $this->addFlash('notice', 'Ajout avec success!');

            return $this->redirectToRoute('Read_Back_Salle');
        }

        return $this->renderForm('Salle/CreateSalle.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/Salle/Delete/{id}', name: 'Delete_Salle')]
    public function DeleteSalle(ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $repo = $doctrine->getRepository(Salle::class);
        $Salle = $repo->find($id);
        $em->remove($Salle);
        $em->flush();

        $this->addFlash('notice', 'Suppression avec success!');

        return $this->redirectToRoute('Read_Back_Salle');
    }

    #[Route('/Salle/Update/{id}', name: 'Update_Salle')]
    public function UpdateSalle(
        ManagerRegistry $doctrine,
        $id,
        Request $req
    ): Response {
        $em = $doctrine->getManager();
        $Salle = $doctrine->getRepository(Salle::class)->find($id);
        $form = $this->createForm(SalleType::class, $Salle);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $req->files->get('salle')['image'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($uploads_directory, $filename);
            $Salle->setImage($filename);
            $em->persist($Salle);
            $em->flush();

            $this->addFlash('notice', 'Modification avec success!');

            return $this->redirectToRoute('Read_Back_Salle');
        }

        return $this->renderForm('Salle/UpdateSalle.html.twig', [
            'form' => $form,
        ]);
    }
}
