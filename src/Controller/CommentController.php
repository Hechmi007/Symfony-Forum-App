<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
     * @Route("/post/{id}/comment", name="comment_page")
	 * @Method({"GET", "POST"})
     */
    public function add_comment(Request $request, $id)
    {
		$usr = $this->getUser();
		$comment = new Comment();
		$comment->setPostId($id);
		$comment->setUser($usr->getUsername());

		$form = $this->createForm(CommentType::class, $comment);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$comment = $form->getData();

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($comment);
			$entityManager->flush();

			return $this->redirectToRoute('show_post', ["id"=> $id]);
		}
		return $this->render('comment.html.twig', ["form"=>$form->createView(),]);
    }

	/**
	 * @Route("/remove/comment/{postId}/{id}", name="remove_comment")
	 */
	public function remove_comment($postId, $id)
	{
		$usr = $this->getUser();

		if ($usr != null)
			$usr = $usr->getUsername();

		$comment = $this->getDoctrine()
			->getRepository(Comment::class)
			->findOneBy(['postId'=>$postId, 'id'=>$id]);

		if ($comment->getUser() == $usr)
		{
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove($comment);
			$entityManager->flush();
			return $this->redirectToRoute("show_post", ["id"=> $postId]);
		}
		return $this->redirectToRoute('show_post', ["id"=> $postId]);
	}
}
