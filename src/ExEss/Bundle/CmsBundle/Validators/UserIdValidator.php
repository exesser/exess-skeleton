<?php

namespace ExEss\Bundle\CmsBundle\Validators;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Bundle\CmsBundle\Entity\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UserIdValidator extends ConstraintValidator
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param mixed $value
     * @throws UnexpectedTypeException When the constraint is not of type UserId.
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UserId) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\UserId');
        }

        if ($this->em->getRepository(User::class)->find($value) === null) {
            $this->buildViolation($value, $constraint);
        }
    }

    /**
     * @param mixed $value
     */
    private function buildViolation($value, Constraint $constraint): void
    {
        $this->context->buildViolation($constraint->userNotValid)
            ->setCode(UserId::USER_NOT_FOUND_ERROR)
            ->setParameter('{{ id }}', $value)
            ->addViolation();
    }
}
