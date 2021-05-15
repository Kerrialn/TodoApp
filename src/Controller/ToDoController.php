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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use GuzzleHttp\Client;
use App\Service\Api;

class ToDoController extends AbstractController
{
    public function __construct(
        private ToDoRepository $toDoRepository,
        private Api $apiService
    ) {
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
            $uploadfile = $form->get("upload")->getData();
            if ($uploadfile && $uploadfile instanceof UploadedFile) {
                try{
                    $uploadfile->move(
                        $this->getParameter('imageUploadPath'), 
                        $uploadfile->getClientOriginalName()
                    );
                    $imagepath = $this->getParameter('imageUploadPath')."/".$uploadfile->getClientOriginalName();
                    $todo->setImage($imagepath);
                } catch (FileException $e) {
                    dump($e);
                }
            } 
            else {
                $todo->setImage(null);
            }

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager(); //getting the entity manager
            $entityManager->persist($todo); //staging area
            $entityManager->flush(); //saves it
            return $this->redirectToRoute('todos');
        }
        return $this->render('to_do/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/todos", name="todos")
     */
    public function read(): Response
    {        
        $client = new Client();
        $response = $client->request('GET', $this->apiService->weather("Prague"));


        $todos = $this->toDoRepository->findAll();
        return $this->render('to_do/index.html.twig', [
            'todos' => $todos,
            'weather' => $response->getBody()
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
