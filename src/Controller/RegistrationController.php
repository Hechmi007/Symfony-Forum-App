<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="register_page")
     */
	public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        	$password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
        	$user->setPassword($password);

        	$entityManager = $this->getDoctrine()->getManager();
            	$entityManager->persist($user);
            	$entityManager->flush();

		return $this->redirectToRoute('home_page');
        }

        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }
}
