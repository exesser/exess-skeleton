<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Sidebar;

use ExEss\Cms\Doctrine\Type\TranslationDomain;

abstract class AbstractBlueSidebar implements SidebarInterface
{
    abstract protected function getTitle(): string;

    abstract protected function getTitleContact(): string;

    public function jsonSerialize(): array
    {
        // @todo implement this
        return [
            'some' => 'data',
        ];

        return [
            'title' => $this->translator->trans($this->getTitle(), [], TranslationDomain::SIDEBAR),
            'titleContact' => $this->translator->trans($this->getTitleContact(), [], TranslationDomain::SIDEBAR),
            'name' => $account->name,
            'number' => $account->account_number_c,
            'type' => $account->account_type,
            'record_type' => $account->record_type,
            'id' => $account->id,
            'first_name' => '',
            'last_name' => '',
            'function' => '',
            'language' => '',
            'birthdate' => '',
            'arrowLink' => $this->getArrowLinks($account->id),
            'buttons' => $this->getButtons($account->id),
            'contractTotalLink' => $this->getContractTotalLink($account->id),
            'messages' => $this->getConditionalMessages($account),
        ];
    }

    private function getConditionalMessages(object $fatEntity): array
    {
        $conditionalMessages = $this->messageRepository->findBy([
            'domain' => TranslationDomain::SIDEBAR,
            'subject' => $fatEntity,
        ]);

        $messages = [];
        foreach ($conditionalMessages->getList() as $conditionalMessage) {
            $messages[] = $this->translator->trans($conditionalMessage->description, [], TranslationDomain::SIDEBAR);
        }

        return $messages;
    }

    private function getArrowLinks(string $id): array
    {
        return [
            'title' => $this->translator->trans('Overview', [], TranslationDomain::SIDEBAR),
            'linkTo' => 'focus-mode',
            'params' => [
                'mainMenuKey' => 'sales-marketing',
                'focusModeId' => 'account_cockpit_overview',
                'recordId' => $id,
            ],
        ];
    }

    private function getContractTotalLink(string $id): array
    {
        return [
            'icon' => 'icon-log',
            'linkTo' => 'focus-mode',
            'params' => [
                'mainMenuKey' => 'sales-marketing',
                'focusModeId' => 'account_cockpit_contracts',
                'recordId' => $id,
            ],
        ];
    }

    private function getButtons(string $id): array
    {
        $buttons[] = [
            'title' => $this->translator->trans('Details', [], TranslationDomain::SIDEBAR),
            'icon' => 'icon-bedrijf',
            'linkTo' => 'focus-mode',
            'params' => [
                'mainMenuKey' => 'sales-marketing',
                'focusModeId' => 'account_cockpit_details',
                'recordId' => $id,
            ],
        ];

        $buttons[] = [
            'title' => $this->translator->trans('Sales', [], TranslationDomain::SIDEBAR),
            'icon' => 'icon-winkelwagen',
            'linkTo' => 'focus-mode',
            'params' => [
                'mainMenuKey' => 'sales-marketing',
                'focusModeId' => 'account_cockpit_sales',
                'recordId' => $id,
            ],
        ];

        $buttons[] = [
            'title' => $this->translator->trans('Contracts', [], TranslationDomain::SIDEBAR),
            'icon' => 'icon-contract',
            'linkTo' => 'focus-mode',
            'params' => [
                'mainMenuKey' => 'sales-marketing',
                'focusModeId' => 'account_cockpit_contracts',
                'recordId' => $id,
            ],
        ];

        $buttons[] = [
            'title' => $this->translator->trans('Billing', [], TranslationDomain::SIDEBAR),
            'icon' => 'icon-euro',
            'linkTo' => 'focus-mode',
            'params' => [
                'mainMenuKey' => 'sales-marketing',
                'focusModeId' => 'account_cockpit_billing',
                'recordId' => $id,
            ],
        ];

        $buttons[] = [
            'title' => $this->translator->trans('Service', [], TranslationDomain::SIDEBAR),
            'icon' => 'icon-agent',
            'linkTo' => 'focus-mode',
            'params' => [
                'mainMenuKey' => 'sales-marketing',
                'focusModeId' => 'account_cockpit_service',
                'recordId' => $id,
            ],
        ];

        $buttons[] = [
            'title' => $this->translator->trans('Workflows', [], TranslationDomain::SIDEBAR),
            'icon' => 'icon-flowchart',
            'linkTo' => 'focus-mode',
            'params' => [
                'mainMenuKey' => 'sales-marketing',
                'focusModeId' => 'account_cockpit_workflows',
                'recordId' => $id,
            ],
        ];

        $buttons[] = [
            'title' => $this->translator->trans('Documents', [], TranslationDomain::SIDEBAR),
            'icon' => 'icon-mappen',
            'linkTo' => 'focus-mode',
            'params' => [
                'mainMenuKey' => 'sales-marketing',
                'focusModeId' => 'account_cockpit_documents',
                'recordId' => $id,
            ],
        ];

        return $buttons;
    }
}
