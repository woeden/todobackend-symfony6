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

class TagController extends AbstractController
{
    /**
     * @Route("/tags/{id}", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Tag::class);

        $tag = $repository->find($id);
        if($tag === null)
        {
            throw new NotFoundHttpException();
        }

        $entityManager->remove($tag);
        $entityManager->flush();

        return $this->json($tag, 200, [], ['groups'=>Tag::READ]);
    }

    /**
     * @Route("/tags/{id}", name="get_tag", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getItem(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Tag::class);

        $tag = $repository->find($id);
        if($tag === null)
        {
            throw new NotFoundHttpException();
        }

        return $this->json($tag, 200, [], ['groups'=>Tag::READ]);
    }

    /**
     * @Route("/tags/{id}/todos", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getTodos(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Tag::class);

        $tag = $repository->find($id);

        return $this->json($tag->getTodos(), 200, [], ['groups'=>Todo::READ]);
    }

    /**
     * @Route("/tags/{id}", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function patchItem(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Tag::class);

        $tag = $repository->find($id);

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);


        if($tag === null)
        {
            throw new NotFoundHttpException();
        }



        $serializer->deserialize($request->getContent(), Tag::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $tag]);

        $entityManager->persist($tag);
        $entityManager->flush();

        return $this->json($tag, 200, [], ['groups'=>Tag::READ]);
    }

    /**
     * @Route("/tags/", methods={"GET"})
     */
    public function getAll(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Tag::class);

        $tags = $repository->findAll();

        return $this->json($tags, 200, [], ['groups'=>Tag::READ]);
    }

    /**
     * @Route("/tags/", methods={"POST"})
     */
    public function post(Request $request, ManagerRegistry $doctrine): Response
    {

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $tag = $serializer->deserialize($request->getContent(), Tag::class, 'json');

        $doctrine->getManager()->persist($tag);
        $doctrine->getManager()->flush();

        return $this->json($tag, 200, [], ['groups'=>Tag::READ]);
    }

    /**
     * @Route("/tags/", methods={"DELETE"})
     */
    public function deleteAll(ManagerRegistry $doctrine): Response
    {

        $tags = $doctrine->getRepository(Tag::class)->findAll();

        foreach ($tags as $tag){
            $doctrine->getManager()->remove($tag);
            $doctrine->getManager()->flush();
        }

        return $this->json($tags, 200, [], ['groups'=>Tag::READ]);
    }


}
