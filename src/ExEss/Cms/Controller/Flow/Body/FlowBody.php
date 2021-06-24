<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\Flow\Body;

use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\Http\Request\AbstractJsonBody;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FlowBody extends AbstractJsonBody
{
    public function getRoute(): ?array
    {
        return $this->arguments['route'];
    }

    public function getModel(): Model
    {
        return $this->arguments['model'];
    }

    public function getParentModel(): ?Model
    {
        return $this->arguments['parentModel'];
    }

    public function getAction(): FlowAction
    {
        return $this->arguments['action'];
    }

    public function getParams(): array
    {
        return $this->arguments['params'];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('route', null)
            ->setAllowedTypes('route', ['null', 'array'])
        ;
        $resolver
            ->setDefault('model', [])
            ->setAllowedTypes('model', ['array', Model::class])
            ->setNormalizer('model', function (Options $options, $value): Model {
                return \is_array($value) ? new Model($value) : $value;
            })
        ;
        $resolver
            ->setDefault('parentModel', null)
            ->setAllowedTypes('parentModel', ['null', 'array', Model::class])
            ->setNormalizer('parentModel', function (Options $options, $value): ?Model {
                return $value === null ? null : (\is_array($value) ? new Model($value) : $value);
            })
        ;
        $resolver
            ->setDefault('action', ['event' => FlowAction::EVENT_INIT])
            ->setAllowedTypes('action', ['array', 'object'])
            ->setNormalizer('action', function (Options $options, $value): FlowAction {
                return new FlowAction((array) $value);
            })
        ;
        $resolver
            ->setDefault('params', [])
            ->setAllowedTypes('params', ['array'])
        ;

        // this is sent but unused, ignore it
        $resolver
            ->setDefined('progress')
        ;
    }
}
