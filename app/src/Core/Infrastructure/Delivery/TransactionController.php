<?php

declare(strict_types=1);

namespace SimplePayment\Core\Infrastructure\Delivery;

use SimplePayment\Core\Application\TransferMoney;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use function assert;

class TransactionController extends AbstractController
{
    /**
     * @Route("/transaction", methods={"POST"})
     */
    public function transaction(Request $request, MessageBusInterface $bus, SerializerInterface $serializer): Response
    {
        $requestBody = (string) $request->getContent();

        $command = $serializer->deserialize($requestBody, TransferMoney::class, 'json');
        assert($command instanceof TransferMoney);

        $bus->dispatch($command);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
