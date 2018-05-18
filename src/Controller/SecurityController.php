<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
		$error = $authenticationUtils->getLastAuthenticationError();

		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render('security/login.html.twig', array(
        	'last_username' => $lastUsername,
        	'error'         => $error,
    	));
    }

	/**
	 * @Route("/remove/{username}", name="delete_user")
	 */
	public function deleteUser($username)
	{
		$usr = $this->getUser();
		if ($usr != null)
			$usr = $usr->getUsername();
		if ($usr == $username)
		{
			$profile = $this->getDoctrine()
				->getRepository(User::class)
				->findOneBy(['username'=> $username]);

			$entityManager = $this->getDoctrine()->getManager();
			$this->get('security.token_storage')->setToken(null);
			$entityManager->remove($profile);
			$entityManager->flush();
			return $this->render('bye.html.twig');
		}
		return $this->redirectToRoute('home_page');
	}
}
