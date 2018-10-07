<?php

namespace Opera\AdminBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Opera\CoreBundle\Entity\Page;
use Opera\CoreBundle\Entity\Block;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Opera\CoreBundle\Repository\BlockRepository;
use Opera\CoreBundle\Cms\BlockManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Opera\AdminBundle\Form\NewBlockType;
use Opera\CoreBundle\Event\BlockUpdatedEvent;

class AdminPagesController extends Controller
{   
    /**
     * @Route("/pages/{id}/layout", name="opera_admin_pages_blocks")
     * @Template
     */
    public function layout(Page $page, BlockRepository $blockRepository, BlockManager $blockManager, Request $request)
    {
        $form = $this->createForm(NewBlockType::class, null, [
            'block_manager' => $blockManager,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $block = $blockManager->cleanBlockConfiguration($form->getData());
            $block->setPage($page);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($block);
            $entityManager->flush();
        }

        return [
            'blocks_in_area' => $blockRepository->findForPageGroupedByAreas($page),
            'entity' => $request->query->get('entity'),
            'page' => $page,
            'block_controller' => get_class($this).'::block',
            'form' => $form->createView(),
            'area' => isset($block) ? $block->getArea() : $request->get('area'),
        ];
    }

    /**
     * @Route("/pages/{id}/block-positions/{area}", methods="POST", name="update_positions")
     */
    public function updatePositions(Page $page, BlockRepository $blockRepository, Request $request, string $area)
    {
        foreach ($request->get('positions') as $blockId => $position) {
            $blockRepository->movePageBlockTo($page, $area, $blockId, $position);
        }

        return new Response();
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

            $this->get('event_dispatcher')->dispatch('opera.block.updated', new BlockUpdatedEvent($blockManager->getBlockType($block), $block));
        }
        
        return [
            'form' => $form->createView(),
            'block' => $block,
        ];
    }

    /**
     * @Route("/block/{id}/delete", name="opera_admin_pages_block_delete")
     */
    public function deleteBlock(Block $block)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($block);
        $entityManager->flush();

        return $this->redirectToRoute('opera_admin_pages_blocks', [
            'id' => $block->getPage()->getId(),
            'area' => $block->getArea(),
        ]);
    }
}
