<?php

namespace App\Controller;

use App\Entity\ToDo;
use App\Form\ToDoFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ToDoRepository;
use Symfony\Component\HttpFoundation\Request;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ToDoController extends AbstractController
{

    private $toDoRepository;

    public function __construct(
        ToDoRepository $toDoRepository
    ) {
        $this->toDoRepository = $toDoRepository;
    }

    /**
     * @Route("/create", name="todo.create")
     */
    public function create(Request $request): Response
    {
        $todo = new ToDo();
        $form = $this->createForm(ToDoFormType::class, $todo);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original $task variable has also been updated
            $todo = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($todo);
            $entityManager->flush();

            return $this->redirectToRoute('todos');
        }
        return $this->render('to_do/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/todos", name="todos")
     */
    public function read(): Response
    {
        $todos = $this->toDoRepository->findAll();
        return $this->render('to_do/index.html.twig', [
            'todos' => $todos
        ]);
    }

    /**
     * @Route("/pdf", name="pdf.sample")
     */
    public function pdf(): RedirectResponse
    {
        $todos = $this->toDoRepository->findAll();
        $htmlTemplate = $this->renderView('to_do/sample.html.twig', [
            'title' => 'Hello world,: you are beautiful!',
            'todos' => $todos
        ]);

        $pdf = new Dompdf();
        $pdf->loadHtml($htmlTemplate);
        $pdf->render();
        $pdf->stream();

        return $this->redirectToRoute('todos');
    }
}
