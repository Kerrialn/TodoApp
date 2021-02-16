<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ToDoRepository;

class ToDoController extends AbstractController
{

    private $toDoRepository;

    public function __construct(
        ToDoRepository $toDoRepository
    )
    {
        $this->toDoRepository = $toDoRepository;
    }


    /**
     * @Route("/todo", name="to_do")
     */
    public function index(): Response
    {
        $todos = $this->toDoRepository->findAll(); 
        return $this->render('to_do/index.html.twig', [
            'todos' => $todos
        ]);
    }
}
