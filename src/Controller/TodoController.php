<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Todo;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class TodoController extends AbstractController
{

    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }


    /**
     * @Route("/todos/{id}", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Todo::class);

        $todo = $repository->find($id);
        if($todo === null)
        {
            throw new NotFoundHttpException();
        }

        $entityManager->remove($todo);
        $entityManager->flush();

        return $this->json($todo, 200, [], ['groups'=>Todo::READ]);
    }

    /**
     * @Route("/todos/{id}", name="get_todo", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getItem(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Todo::class);

        $todo = $repository->find($id);
        if($todo === null)
        {
            throw new NotFoundHttpException();
        }

        return $this->json($todo, 200, [], ['groups'=>Todo::READ]);
    }

    /**
     * @Route("/todos/{id}", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function patchItem(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Todo::class);

        $todo = $repository->find($id);

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);


        if($todo === null)
        {
            throw new NotFoundHttpException();
        }



        $this->serializer->deserialize($request->getContent(), Todo::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $todo]);

        $entityManager->persist($todo);
        $entityManager->flush();

        return $this->json($todo, 200, [], ['groups'=>Todo::READ]);
    }

    /**
     * @Route("/todos/", methods={"GET"})
     */
    public function getAll(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Todo::class);

        $todos = $repository->findAll();

        return $this->json($todos, 200, [], ['groups'=>Todo::READ]);
    }




    /**
     * @Route("/todos/", methods={"POST"})
     */
    public function post(Request $request, ManagerRegistry $doctrine): Response
    {

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);

        $todo = $this->serializer->deserialize($request->getContent(), Todo::class, 'json');

        $doctrine->getManager()->persist($todo);
        $doctrine->getManager()->flush();

        return $this->json($todo, 200, [], ["groups"=>"todo:read"]);
    }

    /**
     * @Route("/todos/", methods={"DELETE"})
     */
    public function deleteAll(ManagerRegistry $doctrine): Response
    {

        $todos = $doctrine->getRepository(Todo::class)->findAll();

        foreach ($todos as $todo){
            $doctrine->getManager()->remove($todo);
            $doctrine->getManager()->flush();
        }

        return $this->json($todos, 200, [], ['groups'=>Todo::READ]);
    }

    /**
     * @Route("/todos/{id}/tags/", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function postTags(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Todo::class);
        $tagsRepository = $entityManager->getRepository(Tag::class);

        $todo = $repository->find($id);
        if($todo === null)
        {
            throw new NotFoundHttpException();
        }
        $tag = $tagsRepository->find(json_decode($request->getContent(), true)['id']);

        $todo->addTag($tag);

        $entityManager->persist($todo);
        $entityManager->flush();

        return $this->json($todo, 200, [], ['groups'=>Todo::READ]);
    }

    /**
     * @Route("/todos/{id}/tags/", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getTags(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Todo::class);

        $todo = $repository->find($id);

        if($todo === null)
        {
            throw new NotFoundHttpException();
        }

        return $this->json($todo->getTags(), 200, [], ['groups'=>Tag::READ]);
    }

    /**
     * @Route("/todos/{id}/tags/", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function deleteTags(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Todo::class);

        $todo = $repository->find($id);

        if($todo === null)
        {
            throw new NotFoundHttpException();
        }

        foreach ($todo->getTags() as $tag){
            $todo->removeTag($tag);
        }

        $entityManager->flush();

        return $this->json($todo->getTags(), 200, [], ['groups'=>Tag::READ]);
    }

    /**
     * @Route("/todos/{id}/tags/{idTag}", methods={"DELETE"}, requirements={"id"="\d+", "idTag"="\d+"})
     */
    public function deleteTag(ManagerRegistry $doctrine, int $id, int $idTag): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Todo::class);

        $todo = $repository->find($id);

        if($todo === null)
        {
            throw new NotFoundHttpException();
        }

        foreach ($todo->getTags() as $tag){

            if($tag->getId() === $idTag)
            {
                $todo->removeTag($tag);
            }
        }

        $entityManager->flush();

        return $this->json($todo->getTags(), 200, [], ['groups'=>Tag::READ]);
    }


}
