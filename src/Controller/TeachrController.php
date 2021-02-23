<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\JsonResponse; 
use App\Entity\Teachr;
use App\Entity\Statistics;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;





class TeachrController extends AbstractController{

   
    /** 
     * @Route("/api/insert_teachr", methods={"POST"})

    */
    public function createTeacher(Request $request){
        $em=$this->getDoctrine()->getManager();

        //instanciation d'un nouveau Teachr
        $teachr=new Teachr();
        // recuperation de la data du request body
        $requestBody=json_decode($request->getContent(),true);
        // attribution du non du teacher
        $teachr->setName($requestBody["name"]);

        $em->persist($teachr);
        // recuperation du counter

        
        $statisticsRepository=$em->getRepository(Statistics::class);

        
        // voir si il y a des insertions dans le compteur (est ce que des utilisateurs ont déja été ajouté?)
        $getInsertions=$statisticsRepository->findAll();
        // si non, je crée un nouveau objet et je lui donne 1 comme valuer
        if(count($getInsertions)==0){
            $counter=new Statistics();
            $counter->setCounter(1);
            $em->persist($counter);
        }
        // si il existe déja une insertion, je recupere la longeur du tableau, je recupere le dernier indice du tableau qui represente un objet de la classe Statistics et je lui incremente sa valeur par un 
        else{
            $getStatInsertionLength=count($getInsertions);
            $getLastId=$getInsertions[$getStatInsertionLength-1]->getId();

            $getLastCounter=$statisticsRepository->findOneBy(["id"=>$getLastId]);
             
            $getLastCounter->setCounter($getLastCounter->getCounter()+1);
            
            $em->persist($getLastCounter);
        }

        

        $em->flush();

        return $this->json(["message"=>"merci, le teachr est bien ajouté"]);

        
    } 

    /**
     * @Route("/api/teachers/getAll",methods={"GET"})
     */

     public function getAllTeachersr(NormalizerInterface $normalizer){
         $em=$this->getDoctrine()->getManager();

         $teachersRepo=$em->getRepository(Teachr::class);

         $teachers=$teachersRepo->findAll();
        
        //transformer le tableau d'objet en tableau associatif
        $normalizeTeachers=$normalizer->normalize($teachers,null,["groups"=>"read"]);

        
        //encode ce tableau en json pour retourner la reponse au client
         $jsonTeachers=json_encode($normalizeTeachers);

         $response=new Response($jsonTeachers,200,["Content-Type"=>"application/json"]);

         return $response;

        

     } 
     /**
      * @Route("/api/update_teacher/{id}",methods={"PUT"})
      */

     public function updateTeacher($id,Request $request, NormalizerInterface $normalizer){
        $em=$this->getDoctrine()->getManager();

        $teachersRepo=$em->getRepository(Teachr::class);

        $teacher=$teachersRepo->findOneBy(["id"=>$id]);

        $requestBody=json_decode($request->getContent(),true);

        $newName=$requestBody["name"];

        $teacher->setName($newName);

        $em->persist($teacher);
        $em->flush();
        $normalizeTeacher=$normalizer->normalize($teacher,null,["groups"=>"read"]);

        $jsonTeacher=json_encode($normalizeTeacher);

        $response=new Response($jsonTeacher,200,["Content-Type"=>"application/json"]);

        return $response;

        

        
     }


}


?>