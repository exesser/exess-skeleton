<?php declare(strict_types=1);

namespace ExEss\Cms\Http\EventSubscriber;

use ExEss\Cms\Helper\DataCleaner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CleanInputSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            // just before the firewall
            KernelEvents::REQUEST => ['cleanInput', 10],
        ];
    }

    public function cleanInput(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // dont clean input when doing flow updates, which can contain WYSIWYG
        // @todo fix this when route has been transferred
        if (\strpos($request->getUri(), '/Api/V8_Custom/Flow/') !== false) {
            return;
        }

        $body = $request->getContent();
        $cleaned = DataCleaner::cleanInput($body);

        if ($body === $cleaned) {
            return;
        }

        $request->initialize(
            $request->query->getIterator()->getArrayCopy(),
            $request->request->getIterator()->getArrayCopy(),
            $request->attributes->getIterator()->getArrayCopy(),
            $request->cookies->getIterator()->getArrayCopy(),
            $request->files->getIterator()->getArrayCopy(),
            $request->server->getIterator()->getArrayCopy(),
            $cleaned
        );
    }
}
