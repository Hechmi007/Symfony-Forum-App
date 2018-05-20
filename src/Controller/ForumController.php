<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ForumController extends AbstractController
{
	/**
	* @Route("/", name="home_page")
	* @Method({"GET"});
	*/
	public function index()
    	{
		 $posts = $this->getDoctrine()
        	->getRepository(Post::class)
        	->findAll();

    	if (!$posts){
        	return $this->render('no-post.html.twig');
	}
	return $this->render('home_page.html.twig', ["posts"=>$posts]);
    	}

	/**
	 * @Route("/admin", name="admin_page")
	 * @Method({"GET"})
	 */
	public function admin()
	{
		return new Response('<html><body>Admin page!</body></html>');
	}

	/**
	 * @Route("/post", name="post_page")
	 * @Method({"GET", "POST"})
	 */
	public function post(Request $request)
	{
		$post = new Post();
		$form = $this->createForm(PostType::class, $post);
		$usr = $this->getUser();
		$post->setUser($usr->getUserName());

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
        	
			$post = $form->getData();
	
			$entityManager = $this->getDoctrine()->getManager();
        	$entityManager->persist($post);
        	$entityManager->flush();

        	return $this->redirectToRoute('home_page');
    	}

		
		return $this->render('post_page.html.twig', ["form"=>$form->createView(),]);
	}

	/**
	 * @Route("/show-post/{id}", name="show_post")
	 * @Method({"GET"})
	 */
	public function showPost($id)
	{
		$usr = $this->getUser();
		if ($usr != null)
			$usr = $usr->getUsername();
		
		$post = $this->getDoctrine()
        	->getRepository(Post::class)
        	->find($id);
		$comments = $this->getDoctrine()
			->getRepository(Comment::class)
			->findBy(['postId'=> $id]);

    	if (!$post) {
        	throw $this->createNotFoundException(
            	'No post found, sorry'
        	);
   		}
		return $this->render('show_post.html.twig', ["post"=>$post, "comments"=>$comments, "usr"=>$usr]);
	}

	/**
	 * @Route("/remove/post/{id}", name="remove_post")
	 */
	public function removePost($id)
	{
		$usr = $this->getUser();

		if($usr != null)
		{
			$usr = $usr->getUsername();
		}

		$post = $this->getDoctrine()
			->getRepository(Post::class)
			->find($id);

		if ($post->getUser() == $usr)
		{
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove($post);
			$entityManager->flush();
			return $this->redirectToRoute('home_page');
		}
		return $this->redirectToRoute('show_post', ["id"=> $id]);
	}
}
