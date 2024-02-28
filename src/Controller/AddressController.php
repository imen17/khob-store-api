<?php

namespace App\Controller;

use App\DTO\UpdateAddressDTO;
use App\Entity\Address;
use App\Security\Voter\AddressVoter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class AddressController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface    $serializer,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/addresses/{id}', methods: ["GET"])]
    public function getAddress(Address $address): JsonResponse
    {
        $this->denyAccessUnlessGranted(AddressVoter::VIEW, $address);
        return new JsonResponse(
            $this->serializer
                ->serialize(
                    $address,
                    "json",
                    [AbstractNormalizer::ATTRIBUTES => [
                        'id',
                        'addressLine',
                        'city',
                        'governorate'
                    ]
                    ]
                ),
            Response::HTTP_OK, [],
            true
        );
    }

    #[Route('/addresses', methods: ["PUT"])]
    public function addAddress(#[MapRequestPayload] UpdateAddressDTO $addressObject): Response
    {
        $this->denyAccessUnlessGranted(AddressVoter::EDIT, $this->getUser());
        $address = new Address();
        $address->setOwner($this->getUser())
            ->setAddressLine($addressObject->addressLine)
            ->setCity($addressObject->city)
            ->setGovernorate($addressObject->governorate);
        $this->entityManager->persist($address);
        $this->entityManager->flush();
        return new Response(
            null,
            Response::HTTP_CREATED
        );
    }

    #[Route('/addresses/{id}', methods: ["DELETE"])]
    public function deleteAddress(string $id): Response
    {
        $this->denyAccessUnlessGranted(AddressVoter::EDIT, $this->getUser());
        $address = $this->entityManager->getRepository(Address::class)->find($id);
        if (!$address)  throw new EntityNotFoundException();
        $this->entityManager->remove($address);
        $this->entityManager->flush();
        return new Response(
            null,
            Response::HTTP_CREATED
        );
    }


    #[Route('/addresses/{id}', methods: ["PATCH"])]
    public function updateAddress(#[MapRequestPayload] UpdateAddressDTO $addressObject, Address $address): Response
    {
        $this->denyAccessUnlessGranted(AddressVoter::EDIT, $this->getUser());
        $address->setAddressLine($addressObject->addressLine)
            ->setCity($addressObject->city)
            ->setGovernorate($addressObject->governorate);
        $this->entityManager->persist($address);
        $this->entityManager->flush();
        return new Response(
            null,
            Response::HTTP_ACCEPTED
        );
    }


}