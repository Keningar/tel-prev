<?php

namespace telconet\schemaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoContratoFormaPago;
use telconet\schemaBundle\Form\InfoContratoFormaPagoType;

/**
 * InfoContratoFormaPago controller.
 *
 */
class InfoContratoFormaPagoController extends Controller
{
    /**
     * Lists all InfoContratoFormaPago entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('schemaBundle:InfoContratoFormaPago')->findAll();

        return $this->render('schemaBundle:InfoContratoFormaPago:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a InfoContratoFormaPago entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoContratoFormaPago')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoContratoFormaPago entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('schemaBundle:InfoContratoFormaPago:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new InfoContratoFormaPago entity.
     *
     */
    public function newAction()
    {
        $entity = new InfoContratoFormaPago();
        $form   = $this->createForm(new InfoContratoFormaPagoType(), $entity);

        return $this->render('schemaBundle:InfoContratoFormaPago:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new InfoContratoFormaPago entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new InfoContratoFormaPago();
        $form = $this->createForm(new InfoContratoFormaPagoType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('infocontratoformapago_show', array('id' => $entity->getId())));
        }

        return $this->render('schemaBundle:InfoContratoFormaPago:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing InfoContratoFormaPago entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoContratoFormaPago')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoContratoFormaPago entity.');
        }

        $editForm = $this->createForm(new InfoContratoFormaPagoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('schemaBundle:InfoContratoFormaPago:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing InfoContratoFormaPago entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoContratoFormaPago')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoContratoFormaPago entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new InfoContratoFormaPagoType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('infocontratoformapago_edit', array('id' => $id)));
        }

        return $this->render('schemaBundle:InfoContratoFormaPago:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a InfoContratoFormaPago entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:InfoContratoFormaPago')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoContratoFormaPago entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('infocontratoformapago'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
