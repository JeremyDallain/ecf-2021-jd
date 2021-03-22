<?php

namespace App\Controller;

use DateTime;
use App\Entity\Event;
use App\Entity\Comment;
use App\Form\EventType;
use App\Form\CommentType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{

    protected $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    /**
     * @Route("/", name="home")
     */
    public function index(EventRepository $eventRepository)
    {

        $events = $eventRepository->findBy([], ['startedAt' => 'ASC']);

        // dd($events);
        return $this->render('event/index.html.twig', [
            'events' => $events
        ]);
    }

    /**
     * @Route("/show/{id}", name="event_show")
     */
    public function show($id, EventRepository $eventRepository, Request $request)
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            $this->addFlash('danger', "L'évênement demandé n'existe pas.");
            return $this->redirectToRoute('home');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setUser($this->getUser())
                ->setEvent($event)
                ->setCreatedAt(new DateTime);

            $this->em->persist($comment);
            $this->em->flush();

            $this->addFlash('success', 'Votre commentaire a été soumis avec succés');
            return $this->redirectToRoute('event_show', [
                'id' => $id
            ]);
        }

        return $this->render('event/show.html.twig', [
            'event' => $event,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/new", name="event_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request)
    {
        $user = $this->getUser();

        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //image par defaut 
            $event->setPicture('image_default.jpg');

            $picture = $form->get('picture')->getData();

            if ($picture !== null) {

                // le nom du fichier
                $file = 'img-' . mt_rand(1, 9999999) . '.' . $picture->guessExtension();

                // on deplace le fichier dans son dossier
                $picture->move(
                    $this->getParameter('images_directory'),
                    $file
                );
                // on rajoute le fichier dans l'objet article
                $event->setPicture($file);
            }

            $event->setUser($user);

            $this->em->persist($event);
            $this->em->flush();

            $this->addFlash('success', 'Évênement créé avec succés.');
            return $this->redirectToRoute('home');
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="event_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, $id, EventRepository $eventRepository)
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            $this->addFlash('danger', "L'évênement demandé n'existe pas.");
            return $this->redirectToRoute('home');
        }

        if ($event->getUser() !== $this->getUser()) {
            $this->addFlash('danger', "Vous n'etes pas le proprietaire de cet évênement, vous ne pouvez pas le modifier !");
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $picture = $form->get('picture')->getData();

            if ($picture !== null) {

                // le nom du fichier
                $file = 'img-' . mt_rand(1, 9999999) . '.' . $picture->guessExtension();

                // on deplace le fichier dans son dossier
                $picture->move(
                    $this->getParameter('images_directory'),
                    $file
                );
                // on rajoute le fichier dans l'objet article
                $event->setPicture($file);
            }

            $this->em->flush();
            $this->addFlash('success', 'Évênement modifié avec succés.');
            return $this->redirectToRoute('home');
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="event_delete", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, $id, EventRepository $eventRepository)
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            $this->addFlash('danger', "L'évênement demandé n'existe pas.");
            return $this->redirectToRoute('home');
        }

        if ($event->getUser() !== $this->getUser()) {
            $this->addFlash('danger', "Vous n'êtes pas le proprietaire de cet évênement, vous ne pouvez pas le supprimer !");
            return $this->redirectToRoute('home');
        }

        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            $this->em->remove($event);
            $this->em->flush();
            $this->addFlash('success', 'Évênement supprimé avec succés.');
        } else {
            $this->addFlash('danger', "Suppression de l'article impossible.");
        }

        return $this->redirectToRoute('home');
    }
}
