<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        //$repo = $this->getDoctrine() ->getRepository(Ad::class);
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
           'ads' => $ads
        ]);
    }




     /**
     * Permet de crée une annonce
     *
     * @Route("ads/new", name_="ads_create")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function create( Request $_request, EntityManagerInterface $manager){
        $ad = new Ad();
       

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($_request);


        if($form->isSubmitted() && $form->isValid()){
           // $manager = $this->getDoctrine()->getManager();
           foreach($ad->getImages() as $image) {
            $image->setAd($ad);
            $manager->persist($image);
        }

            $ad->setAuthor($this->getUser());

    

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()} <strong> a bien été enrefistrée ! "
            );
           

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
                
            ]);
            
        }


        return $this->render('ad/new.html.twig',[
            'form' => $form->createView()
        ]);
    }
    /**
     * Permet d'afficher le formulaire d'édition
     *
     * @Route("/ads/{slug}/edit", name="ads_edit")
     *  @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="cette annonce est pas vous ! vous ne pouvez pas la modifier")
     * @return Response
     */
    public function edit(Ad $ad,  Request $_request, EntityManagerInterface $manager){
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($_request);
        if($form->isSubmitted() && $form->isValid()){
            // $manager = $this->getDoctrine()->getManager();
            foreach($ad->getImages() as $image) {
             $image->setAd($ad);
             $manager->persist($image);
         }
     
 
             $manager->persist($ad);
             $manager->flush();
 
             $this->addFlash(
                 'success',
                 "Les modifications de l'annonce <strong>{$ad->getTitle()} <strong> ont  bien été enrefistrées ! "
             );
            
 
             return $this->redirectToRoute('ads_show', [
                 'slug' => $ad->getSlug()
                 
             ]);
             
         }
        
        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }




    /**
     * Permet d'afficher une seule annonece 
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="cette annonce est pas vous ! vous ne pouvez pas la modifier")
     * @Route("/ads/{slug}", name="ads_show")
     * @return Response
     */
    public function show(Ad $ad){

        //je recuper l'annonce qui correspond au slug !
        //$ad = $repo->findOneBySlug($slug);
        return $this->render('ad/show.html.twig',[
            'ad' => $ad
        ] );
    }
  
   /**
    * permet de supprimer 
    *
    *@Route("/ads/{slug}/delete", name="ads_delete")
    * @param Ad $ad
    * @param ObjectManager $manager
    * @return Response 
    */
    public function delete(Ad $ad,  EntityManagerInterface $manager){
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}<strong> a bien été supprimée !"
        );
        return $this->redirectToRoute("ads_index");

    }
  
}
