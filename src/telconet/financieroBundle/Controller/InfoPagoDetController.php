<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Form\InfoPagoDetType;

/**
 * InfoPagoDet controller.
 *
 */
class InfoPagoDetController extends Controller
{
    /**
     * Lists all InfoPagoDet entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('schemaBundle:InfoPagoDet')->findAll();

        return $this->render('schemaBundle:InfoPagoDet:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a InfoPagoDet entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoPagoDet')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPagoDet entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('schemaBundle:InfoPagoDet:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new InfoPagoDet entity.
     *
     */
    public function newAction()
    {
        $entity = new InfoPagoDet();
        $form   = $this->createForm(new InfoPagoDetType(), $entity);

        return $this->render('schemaBundle:InfoPagoDet:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new InfoPagoDet entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new InfoPagoDet();
        $form = $this->createForm(new InfoPagoDetType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('infopagodet_show', array('id' => $entity->getId())));
        }

        return $this->render('schemaBundle:InfoPagoDet:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing InfoPagoDet entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoPagoDet')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPagoDet entity.');
        }

        $editForm = $this->createForm(new InfoPagoDetType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('schemaBundle:InfoPagoDet:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing InfoPagoDet entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoPagoDet')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPagoDet entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new InfoPagoDetType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('infopagodet_edit', array('id' => $id)));
        }

        return $this->render('schemaBundle:InfoPagoDet:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a InfoPagoDet entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:InfoPagoDet')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoPagoDet entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('infopagodet'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
