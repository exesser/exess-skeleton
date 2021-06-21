<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\Sidebar;

use ExEss\Cms\Component\Sidebar\Factory\SidebarFactory;
use ExEss\Cms\Http\SuccessResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;

class ViewController
{
    private SidebarFactory $sidebarFactory;

    public function __construct(
        SidebarFactory $sidebarFactory
    ) {
        $this->sidebarFactory = $sidebarFactory;
    }

    /**
     * @Route("/Api/sidebar/{record_type}/{record_id}", methods={"GET"})
     * @ParamConverter("baseEntity")
     */
    public function __invoke(object $baseEntity): SuccessResponse
    {
        return new SuccessResponse($this->sidebarFactory->createSidebar($baseEntity));
    }
}
