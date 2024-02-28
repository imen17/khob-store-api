<?php

namespace App\Security\Voter;


use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;



# https://symfony.com/doc/current/security/voters.html#the-voter-interface
class UserVoter extends Voter
{
    const VIEW = 'VIEW';
    const EDIT = 'VIEW';

    public function __construct(
        private readonly Security $security,
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof User) {
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
        /** @var User $userEntity */
        $userEntity = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView( $userEntity, $user),
            self::EDIT => $this->canEdit( $userEntity, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(User $userEntity, User $user): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($userEntity, $user)) {
            return true;
        }

        return $userEntity === $user;
    }

    private function canEdit(User $userEntity, User $user): bool
    {
        return $userEntity === $user;
    }
}