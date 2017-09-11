<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Marca;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\Security\Core\User\User;
/**
 * Marca controller.
 *
 * @Route("marcas")
 */
class MarcaController extends Controller
{
    /**
     * Lists all marca entities.
     *
     * @Route("/", name="marcas_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Marca::class);
        //Obtener empresa
        $currentuser = $this->get('security.token_storage')->getToken()->getUser();
        $empresa = $currentuser->getEmpresa();
        $marcas = $repository->findByEmpresa($empresa);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($marcas, $request->query->getInt('page', 1),10);
        
        return $this->render('marca/index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Creates a new marca entity.
     *
     * @Route("/new", name="marcas_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $marca = new Marca();
        $form = $this->createForm('AppBundle\Form\MarcaType', $marca);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $marca->setEmpresa($this->get('security.token_storage')->getToken()->getUser()->getEmpresa());
            $em->persist($marca);
            $em->flush();
            $this->addFlash('success', 'Guardado Correctamente');
            return $this->redirectToRoute('marcas_show', array('id' => $marca->getId()));
        }

        return $this->render('marca/new.html.twig', array(
            'marca' => $marca,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a marca entity.
     *
     * @Route("/{id}", name="marcas_show")
     * @Method("GET")
     */
    public function showAction(Marca $marca)
    {
        $deleteForm = $this->createDeleteForm($marca);

        return $this->render('marca/show.html.twig', array(
            'marca' => $marca,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing marca entity.
     *
     * @Route("/{id}/edit", name="marcas_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Marca $marca)
    {
        $deleteForm = $this->createDeleteForm($marca);
        $editForm = $this->createForm('AppBundle\Form\MarcaType', $marca);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('marcas_edit', array('id' => $marca->getId()));
        }

        return $this->render('marca/edit.html.twig', array(
            'marca' => $marca,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a marca entity.
     *
     * @Route("/{id}", name="marcas_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Marca $marca)
    {
        $form = $this->createDeleteForm($marca);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($marca);
            $em->flush();
        }

        return $this->redirectToRoute('marcas_index');
    }

    /**
     * Creates a form to delete a marca entity.
     *
     * @param Marca $marca The marca entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Marca $marca)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marcas_delete', array('id' => $marca->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
