<?php

declare(strict_types=1);

namespace SimplePayment\Core\Infrastructure\Delivery;

use SimplePayment\Core\Application\CreateUser;
use SimplePayment\Core\Application\FetchUserById;
use SimplePayment\Framework\Id\Domain\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use function assert;
use function is_array;

class UserController extends AbstractController
{
    /**
     * @Route("/user", methods={"POST"})
     */
    public function store(Request $request, MessageBusInterface $bus, SerializerInterface $serializer): Response
    {
        $requestBody = (string) $request->getContent();

        $command = $serializer->deserialize($requestBody, CreateUser::class, 'json');
        assert($command instanceof CreateUser);

        $result = $bus->dispatch($command);

        $handled = $result->last(HandledStamp::class);
        assert($handled instanceof HandledStamp);

        $generatedId = $handled->getResult();
        assert($generatedId instanceof Id);

        return new JsonResponse(['id' => $generatedId->toString()], Response::HTTP_CREATED);
    }

    /**
     * @Route("/user/{id}", methods={"GET"})
     */
    public function find(Request $request, MessageBusInterface $bus): Response
    {
        $id = $request->attributes->get('id', '');

        $command = new FetchUserById($id);

        $result = $bus->dispatch($command);

        $handled = $result->last(HandledStamp::class);
        assert($handled instanceof HandledStamp);

        $userAsArray = $handled->getResult();
        assert(is_array($userAsArray));

        return new JsonResponse($userAsArray);
    }
}
