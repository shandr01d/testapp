<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UsersController extends FOSRestController
{

    public function getUsersAction()
    {        
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findActive();
        
        $view = $this->view($users, 200);
        return $this->handleView($view);
    }
    
    public function getUserAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        
        if(!$user){
            throw $this->createNotFoundException( $this->get('translator')->trans('user.error.not_found', array(), 'validators') );
        }
        
        $view = $this->view($user, 200);
        return $this->handleView($view);
    }
    
    public function postUsersAction(Request $request)
    {
        return $this->processForm($request, new User());
    }
    
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function putUserAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $id = $this->getUser()->getId();
        }
        
        $user = $em->getRepository('AppBundle:User')->find($id);
        if(!$user){
            throw $this->createNotFoundException( $this->get('translator')->trans('user.error.not_found', array(), 'validators') );
        }
        
        return $this->processForm($request, $user, false);
    }
    
    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteUserAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);
        if(!$user){
            throw $this->createNotFoundException( $this->get('translator')->trans('user.error.not_found', array(), 'validators') );
        }
        
        $user->setIsActive(false);
        $em->persist($user);
        $em->flush();
        
        return View::create($user, 202);
    }
    
    private function processForm(Request $request, User $user, $new = true)
    {
        $statusCode = $new ? 201 : 204;
        
        $form = $this->createForm(new UserType(), $user);        
        $form->submit($request, $new);
                
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            if($new || !is_null($request->get('password'))){
                $factory = $this->container->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($form->get('password')->getData(), $user->getSalt());
                $user->setPassword($password);
                
                if(!$form->get('isActive')->getData()){
                    $user->setIsActive(true);
                }
                
                $role = $em->getRepository('AppBundle:Role')->findOneByRole('ROLE_USER');
                if(!$role){
                    throw $this->createNotFoundException( $this->get('translator')->trans('role.error.not_found', array(), 'validators') );
                }
                $user->addRole($role);
            }
            
            $em->persist($user);
            $em->flush();
            
            return View::create($user, $statusCode);
        }
                
        return View::create($form, 400);
    }
}
