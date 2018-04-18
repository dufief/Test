<?php
/**
 * Created by PhpStorm.
 * User: lapiscine
 * Date: 09/04/2018
 * Time: 10:56
 */

namespace ArticleBundle\Controller;


use ArticleBundle\Entity\Mangas;
use ArticleBundle\Form\MangasType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class FormController extends Controller
{

    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
    public function createFormAction(Request $request)
    {

            // Create the variable $em for stock a entity Manager
            $em = $this->getDoctrine()->getManager();


            // Create this instance Mangas in this variable $article
            $article = new Mangas();


            // Create this variable $form for stock the structur abstract our form grace a l'entity AuteurType who is connects our entity Mangas
            $form= $this->createForm(MangasType::class, $article);


            // Verifying this présence of the request Post in Http
            if($request->isMethod('Post')){


                // Set up of the structure abstract form in the request
                $form->handleRequest($request);


                // Verifying form ( champs is not null or champs existing)
                if($form->isValid()){
                    $file = $article->getImg();

                    $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                    // moves the file to the directory where img_directory are stored
                    $file->move(
                        $this->getParameter('img_directory'),
                        $fileName);

                    $article->setImg($fileName);


                    // This request is submit of unité work
                    $em->persist($article);

                    //Envois de la requête dans la base de donnée
                    $em->flush();




                    return $this->redirectToRoute('article_homepage');
                }

                return new Response("Impossible de créer l'auteur");
            }

            //Création de la variable $vars pour stocké la structure du formulaire
            $vars['form'] = $form->createView();

            // envoie de la structure du formulaire dans notre vue
            return $this->render('@Article/Default/form.html.twig', $vars);
    }



    public function updateFormAction(Request $request, $id)
    {

        // Create the variable $em for stock a entity Manager
        $em = $this->getDoctrine()->getManager();

        $rep = $em->getRepository('ArticleBundle:Mangas');

        // Create this instance Mangas in this variable $article
        $article = $rep->find($id);


        // Create this variable $form for stock the structur abstract our form grace a l'entity MangasType who is connects our entity Mangas
        $form= $this->createForm(MangasType::class, $article);


        // Verifying this présence of the request Post in Http
        if($request->isMethod('Post')){


            // Set up of the structure abstract form in the request
            $form->handleRequest($request);


            // Verifying form ( champs is not null or champs existing)
            if($form->isValid()){


                $file = $article->getImg();

                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();



                // moves the file to the directory where img_directory are stored
                $file->move(
                    $this->getParameter('img_directory'),
                    $fileName);

                $article->setImg($fileName);

                // This request is submit of unit work
                $em->persist($article);

                //Envois de la requête dans la base de donnée
                $em->flush();




                return $this->redirectToRoute('article_homepage');
            }

            return new Response("Impossible de créer l'auteur");
        }

        //Création de la variable $vars pour stocké la structure du formulaire
        $vars['form'] = $form->createView();

        // envoie de la structure du formulaire dans notre vue
        return $this->render('@Article/Default/form.html.twig', $vars);
    }

    public function removeFormAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $rep = $em->getRepository('ArticleBundle:Mangas');

        $article = $rep->findOneById($id);

        $em->remove($article);
        $em->flush();

        $this->addFlash('delete', 'a pu');

        return $this->redirectToRoute("article_homepage");
    }
}