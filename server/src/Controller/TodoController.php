<?php

namespace App\Controller;

use App\Document\Todo;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class TodoController extends AbstractController
{
    private $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    #[Route('/todos', methods: ['GET'])]
    public function index(): Response
    {
        $todos = $this->dm->getRepository(Todo::class)->findAll();
        return $this->json($todos);
    }

    #[Route('/todos', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $todo = new Todo();
        $todo->setTitle($data['title']);
        $todo->setCompleted(false);
        
        $this->dm->persist($todo);
        $this->dm->flush();

        return $this->json($todo, Response::HTTP_CREATED);
    }

    #[Route('/todos/{id}', methods: ['GET'])]
    public function show(string $id): Response
    {
        $todo = $this->dm->getRepository(Todo::class)->find($id);
        if (!$todo) {
            return $this->json(['error' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($todo);
    }

    #[Route('/todos/{id}', methods: ['PUT'])]
    public function update(Request $request, string $id): Response
    {
        $todo = $this->dm->getRepository(Todo::class)->find($id);
        if (!$todo) {
            return $this->json(['error' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $todo->setTitle($data['title']);
        $todo->setCompleted($data['completed']);

        $this->dm->flush();

        return $this->json($todo);
    }

    #[Route('/todos/{id}', methods: ['DELETE'])]
    public function delete(string $id): Response
    {
        $todo = $this->dm->getRepository(Todo::class)->find($id);
        if (!$todo) {
            return $this->json(['error' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        $this->dm->remove($todo);
        $this->dm->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}