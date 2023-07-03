<?php

namespace telconet\schemaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoContratoDatoAdicional;
use telconet\schemaBundle\Form\InfoContratoDatoAdicionalType;

/**
 * InfoContratoDatoAdicional controller.
 *
 */
class InfoContratoDatoAdicionalController extends Controller
{
    /**
     * Lists all InfoContratoDatoAdicional entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('schemaBundle:InfoContratoDatoAdicional')->findAll();

        return $this->render('schemaBundle:InfoContratoDatoAdicional:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a InfoContratoDatoAdicional entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoContratoDatoAdicional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoContratoDatoAdicional entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('schemaBundle:InfoContratoDatoAdicional:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new InfoContratoDatoAdicional entity.
     *
     */
    public function newAction()
    {
        $entity = new InfoContratoDatoAdicional();
        $form   = $this->createForm(new InfoContratoDatoAdicionalType(), $entity);

        return $this->render('schemaBundle:InfoContratoDatoAdicional:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new InfoContratoDatoAdicional entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new InfoContratoDatoAdicional();
        $form = $this->createForm(new InfoContratoDatoAdicionalType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('infocontratodatoadicional_show', array('id' => $entity->getId())));
        }

        return $this->render('schemaBundle:InfoContratoDatoAdicional:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing InfoContratoDatoAdicional entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoContratoDatoAdicional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoContratoDatoAdicional entity.');
        }

        $editForm = $this->createForm(new InfoContratoDatoAdicionalType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('schemaBundle:InfoContratoDatoAdicional:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing InfoContratoDatoAdicional entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoContratoDatoAdicional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoContratoDatoAdicional entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new InfoContratoDatoAdicionalType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('infocontratodatoadicional_edit', array('id' => $id)));
        }

        return $this->render('schemaBundle:InfoContratoDatoAdicional:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a InfoContratoDatoAdicional entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:InfoContratoDatoAdicional')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoContratoDatoAdicional entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('infocontratodatoadicional'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
