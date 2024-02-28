<?php

namespace App\Security\Voter;

use App\Entity\Address;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AddressVoter extends Voter
{
    const VIEW = 'VIEW';
    const EDIT = 'VIEW';

    const DELETE = 'DELETE';

    public function __construct(
        private readonly Security $security,
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof Address) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // ROLE_ADMIN can do anything
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a User object, thanks to `supports()`
        /** @var Address $address */
        $address = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView( $address, $user),
            self::EDIT => $this->canEdit( $address, $user),
            self::DELETE => $this->canDelete( $address, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(Address $address, User $user): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($address, $user)) {
            return true;
        }

        return $address->getOwner() === $user;
    }

    private function canEdit(Address $address, User $user): bool
    {
        return $address->getOwner() === $user;
    }
}