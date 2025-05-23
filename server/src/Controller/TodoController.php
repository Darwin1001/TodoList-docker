<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends AbstractController
{
    private $todoRepository;

    public function __construct(TodoRepository $todoRepository)
    {
        $this->todoRepository = $todoRepository;
    }

    /**
     * @Route("/todos", methods={"GET"})
     */
    public function index(): Response
    {
        $todos = $this->todoRepository->findAll();
        return $this->json($todos);
    }

    /**
     * @Route("/todos", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $todo = new Todo();
        $todo->setTitle($data['title']);
        $todo->setCompleted(false);
        
        $this->todoRepository->save($todo);

        return $this->json($todo, Response::HTTP_CREATED);
    }

    /**
     * @Route("/todos/{id}", methods={"GET"})
     */
    public function show(int $id): Response
    {
        $todo = $this->todoRepository->find($id);
        if (!$todo) {
            return $this->json(['error' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($todo);
    }

    /**
     * @Route("/todos/{id}", methods={"PUT"})
     */
    public function update(Request $request, int $id): Response
    {
        $todo = $this->todoRepository->find($id);
        if (!$todo) {
            return $this->json(['error' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $todo->setTitle($data['title']);
        $todo->setCompleted($data['completed']);

        $this->todoRepository->save($todo);

        return $this->json($todo);
    }

    /**
     * @Route("/todos/{id}", methods={"DELETE"})
     */
    public function delete(int $id): Response
    {
        $todo = $this->todoRepository->find($id);
        if (!$todo) {
            return $this->json(['error' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        $this->todoRepository->remove($todo);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}