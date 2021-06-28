<?php

namespace ExEss\Cms\Users\Service;

use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use ExEss\Cms\Api\V8_Custom\Service\Security;
use ExEss\Cms\Component\Flow\Action\Arguments;
use ExEss\Cms\Component\Flow\Action\Command;
use ExEss\Cms\Component\Flow\Response\Model;
use ExEss\Cms\Dictionary\Model\Dwp;
use ExEss\Cms\Doctrine\Type\SecurityGroupType;
use ExEss\Cms\Entity\UserGuidanceRecovery;
use ExEss\Cms\Helper\DataCleaner;

class GuidanceRecoveryService
{
    private Security $security;

    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws DomainException When the route doesn't have the correct format.
     */
    public function saveGuidanceRecoveryData(array $data, Model $model, string $flowKey): void
    {
        $currentUser = $this->security->getCurrentUser();

        if (!$currentUser->hasMatchedUserGroup([SecurityGroupType::CUSTOMER])) {
            // we don't want to enable the guidance recovery for customers
            return;
        }

        if (!isset($data['linkTo'], $data['params'])) {
            throw new DomainException(
                'The guidance route doesn`t have the correct structure. `linkTo` and `params` are mandatory!'
            );
        };

        $model->offsetSet(Dwp::GUIDANCE_FLOW_ID, $flowKey);
        $data['params']['model'] = DataCleaner::getCleanedModel($model->toArray(), true);

        $this->updateRecoveryData($data);
    }

    public function resetGuidanceRecoveryData(): void
    {
        $this->updateRecoveryData(null);
    }

    public function getNavigateArguments(): ?\stdClass
    {
        return !empty($data = $this->getEntity()->getRecoveryData()) ? (object) $data : null;
    }

    private function getEntity(): UserGuidanceRecovery
    {
        $user = $this->security->getCurrentUser();
        if (!($recoveryData = $user->getGuidanceRecovery())) {
            $recoveryData = new UserGuidanceRecovery();
            $recoveryData->setId($user);

            $this->entityManager->persist($recoveryData);
            $this->entityManager->flush();
        }

        return $recoveryData;
    }

    private function updateRecoveryData(?array $data): void
    {
        $recoveryData = $this->getEntity();
        $recoveryData->setRecoveryData($data);
        $this->entityManager->persist($recoveryData);
        $this->entityManager->flush();
    }

    public function getCommand(): Command
    {
        $data = $this->getNavigateArguments();
        if (!isset($data->linkTo, $data->params)) {
            throw new DomainException(
                'The guidance route doesn`t have the correct structure. `linkTo` and `params` are mandatory!'
            );
        };

        $arguments = new Arguments();
        $arguments->linkTo = $data->linkTo;
        $arguments->params = $data->params;

        $command = new Command(Command::COMMAND_TYPE_NAVIGATE, $arguments);

        $command->setConfirmCommand(
            "Guidance recovery",
            "You have unfinished guidance, do you want to recover it?",
        );

        return $command;
    }
}
