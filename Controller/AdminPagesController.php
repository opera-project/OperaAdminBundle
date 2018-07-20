<?php

namespace Opera\AdminBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Opera\CoreBundle\Entity\Page;
use Opera\CoreBundle\Entity\Block;
use Symfony\Component\HttpFoundation\Request;
use Opera\CoreBundle\Repository\BlockRepository;
use Opera\CoreBundle\Cms\BlockManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminPagesController extends Controller
{   
    /**
     * @Route("/pages/{id}/layout", name="opera_admin_pages_blocks")
     * @Template
     */
    public function layout(Page $page, BlockRepository $blockRepository, Request $request)
    {
        return [
            'blocks_in_area' => $blockRepository->findForPageGroupedByAreas($page),
            'entity' => $request->query->get('entity'),
            'page' => $page,
            'block_controller' => get_class($this).'::block',
        ];
    }

    /**
     * @Route("/block/{id}", name="opera_admin_pages_block_edit")
     * @Template
     */
    public function block(Block $block, BlockManager $blockManager, Request $request)
    {
        $form = $blockManager->createAdminForm($block);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($block);
            $entityManager->flush();
        }
        
        return [
            'form' => $form->createView(),
            'block' => $block,
        ];
    }
}
