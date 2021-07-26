<?php

namespace RestBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use RestBundle\Document\Category;
use RestBundle\Document\SCategory;
use RestBundle\Document\SSCategory;
use RestBundle\Document\Image;


class CategoryController extends Controller
{
    /**
     * @POST("/category")
	 * body-parm: name(String), urlBaniere(String)
	 * created a new category
     */
    public function postCategoryAction(Request $request)
    {
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Category');
	  $repository1 = $dm->getRepository('RestBundle:Image');
	  
	  
	  // checking if the category is already exist
	  $category1 = $repository->findOneByName($request->get('name'));
	  if(!empty($category1)){
			$formatted = [
               'statut' => '404'
            ];
			
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   }
	   
	   // else we can add the category
	   $image = $repository1->findOneById($request->get('urlBaniere'));
	   $image->setIsUsed(true);
	   $dm->flush(); 

	   $image = $repository1->findOneById($request->get('urlIcone'));
	   $image->setIsUsed(true);
	   $dm->flush();
		
	   $category= new Category();
	   $category->setName($request->get('name'));
	   $category->setUrlBaniere($request->get('urlBaniere'));
	   $category->setUrlIcone($request->get('urlIcone'));
	   $dm->persist($category);
       $dm->flush();
	   
	   $formatted = [
           'statut' => '200'
       ];
	   
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}
	
	/**
     * @POST("/category/update")
	 * body-parm: name(String), urlBaniere(String)
	 * modify a category
     */
    public function updateCategoryAction(Request $request)
    {
		$dm = $this->get('doctrine_mongodb')->getManager();
	    //check if the caracteristic exist
		$repository = $dm->getRepository('RestBundle:Category');
		$repository1 = $dm->getRepository('RestBundle:Image');
		$category = $repository->findOneById($request->get('idCategory'));
		$image = $repository1->findOneById($request->get('urlBaniere'));
		//$imageIcone = $repository1->findOneById($request->get('urlIcone'));
		if(empty($category)){
			$formatted = [
               'statut' => '404'
            ];
			return new JsonResponse($formatted);
		}
		
	   // if it exist we can update
	   $oldLogoId= $category->getUrlBaniere();
	   $oldIconeId= $category->getUrlIcone();
	   if($oldLogoId != $request->get('urlBaniere')){
		    $oldImage = $repository1->findOneById($oldLogoId);
		    $dm->remove($oldImage);
			$dm->flush();
	   }

	   if($oldIconeId != $request->get('urlIcone')){
		    $oldImage = $repository1->findOneById($oldIconeId);
		    $dm->remove($oldImage);
			$dm->flush();
	   }

	    $image->setIsUsed(true);
	    $dm->flush(); 
	   
	    $category->setName($request->get('name'));
	    $category->setUrlBaniere($request->get('urlBaniere'));
	    $category->setUrlIcone($request->get('urlIcone'));
		$dm->flush();
	    $formatted = [
			'statut' => '200'
		];
			
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted);
	}
	
	/**
     * @POST("/category/delete")
	 * post-parm:  name(string), unities(array of string)
	 * delete a category
     */
	public function deleteCategoryAction(Request $request){
	 
		$dm= $this->get('doctrine_mongodb')->getManager();
	    $repository = $dm->getRepository('RestBundle:Category');
		$repository1 = $dm->getRepository('RestBundle:Image');
		
		//check if the user exist
		$category = $repository->findOneById($request->get('idCategory'));
		if(empty($category)){
			$formatted = [
				'statut' => '404'
			];	
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
		}
		
		// else you can delete the Asccategory
		$idLogo= $category->getUrlBaniere();
	    $image = $repository1->findOneById($idLogo);
	    $idIcone= $category->getUrlIcone();
	    $imageIcone = $repository1->findOneById($idIcone);

	    $dm->remove($image);
	    $dm->remove($imageIcone);
	    $dm->flush();
		
		$dm->remove($category);
		$dm->flush();
		$formatted = [
            'statut' => '200'
        ];
		 
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	}
	
	/**
     * @POST("/category/scategory")
	 * body-parm : name(String), urlBaniere(String)
	 * url-parm :id_category(string)
	 * created a new subcategory inside one categories 
     */
    public function postSCategoryAction(Request $request)
    {
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Category');
	  $repository1 = $dm->getRepository('RestBundle:SCategory');
	  $repository2 = $dm->getRepository('RestBundle:Image');
	  
	  // checking if the category  exist
	  $category = $repository->findOneByName($request->get('name_category'));
	  if(empty($category)){
			$formatted = [
               'statut' => '404'
            ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   }
	   
	   // else we can add the subcategory
	    
	   $scategories= $category->getCategories();
	   $scategory= new SCategory();
	   $scategory->setName($request->get('name'));
	   $scategory->setUrlBaniere($request->get('urlBaniere'));
	   
		
		//verify if the subcategory did not exist already 
	    foreach ($scategories as $scategory1) {
	       if ($scategory1->getName() == $request->get('name')){
		       $myscategory = $scategory1;
		   }
	   
	    }
		
	    if(!empty($myscategory)){
			$formatted = [
               'statut' => '404'
            ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   }
	   
		// else
		$image = $repository2->findOneById($request->get('urlBaniere'));
	    $image->setIsUsed(true);
	    $dm->flush(); 
	   
		$category->addCategory($scategory);
	    $dm->persist($scategory);
        $dm->flush();
	 
	   $formatted = [
           'statut' => '200'
       ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted); 
	}
	
	/**
     * @POST("/category/scategory/{id_category}/{id_scategory}")
	 * body-parm : name(String), urlBaniere(String)
	 * url-parm :id_category(string)
	 * created a new sub subcategory inside one categories 
     */
    public function modifySCategoryAction($id_category,$id_scategory,Request $request)
    {
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Category');
	 
	  // checking if the category  exist
	  $category = $repository->findOneById($id_category);
	  if(empty($category)){
			$formatted = [
               'statut' => '404'
            ];
		return new JsonResponse($formatted);
	   }
	   
	   // else we can nodify the subcategory
	   $scategories= $category->getCategories();
	   //$scategory= new SCategory();
	   
		//verify if the subcategory  exist 
	   foreach ($scategories as $scategory1) {
	       if ($scategory1->getId() == $id_scategory){
		       $myscategory = $scategory1;
		   }
	   }
		
	    if(empty($myscategory)){
			$formatted = [
               'statut' => '404'
            ];
		return new JsonResponse($formatted);
	   }
	   
		// else we can modify
		$myscategory->setName($request->get('name'));
	    $myscategory->setUrlBaniere($request->get('urlBaniere'));
        $dm->flush();
		$formatted = [
           'statut' => '200'
		];
		return new JsonResponse($formatted); 
	}
	
	
	/**
     * @POST("/category/scategory/delete")
	 * body-parm : 
	 * url-parm :id_category(string)
	 * delete a sub category 
     */
    public function deleteSCategoryAction(Request $request)
    {
		
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Category');
	  $repository1 = $dm->getRepository('RestBundle:Image');
	 
	  // checking if the category  exist
	  $category = $repository->findOneByName($request->get('nameCategory'));
	  
	  if(empty($category)){
			$formatted = [
               'statut' => '404'
            ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	  }
	  
	   // else we can delete the subcategory
	   $scategories= $category->getCategories();
	   $scategory= new SCategory();
	   
	   //verify if the subcategory  exist 
	   foreach ($scategories as $scategory1) {
	       if ($scategory1->getId() == $request->get('idSCategory')){
		       $myscategory = $scategory1;
		   }
	   }
		
	   if(empty($myscategory)){ 
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
	   }
	   
	   $idLogo = $myscategory->getUrlBaniere();
	   $image = $repository1->findOneById($idLogo);
	   $dm->remove($image);
	   $dm->flush();
	   
	   $category->removeCategory($myscategory);
	   // else we can delete
	   //$dm->remove($myscategory);
		
       $dm->flush();
	   $formatted = [
           'statut' => '200'
	   ];
	   
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted); 
	}
	
	/**
     * @POST("/category/sscategory/delete")
	 * body-parm : 
	 * url-parm :
	 * delete a sub category 
     */
    public function deleteSSCategoryAction(Request $request)
    {
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Category');
	  $repository1 = $dm->getRepository('RestBundle:Image');
	 
	  // checking if the category  exist
	  $category = $repository->findOneByName($request->get('nameCategory'));
	  
	  if(empty($category)){
			$formatted = [
               'statut' => '4041'
            ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   }
	   
	   // else we can select the subcategory
	   $scategories= $category->getCategories(); 
	   foreach ($scategories as $scategory1) {
	       if ($scategory1->getName() == $request->get('nameSCategory')){
		       $myscategory = $scategory1;
		   }
	   }
		
	   if(empty($myscategory)){
			$formatted = [
               'statut' => '4042'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
			
	   }
	   
	   // else we can select the subcategorie
	   $sscategories= $myscategory->getCategories();
	   foreach ($sscategories as $sscategory1) {
	       if ($sscategory1->getId() == $request->get('idSSCategory')){
		       $mysscategory = $sscategory1;
		   }
	   }
	   
	   $idLogo =  $mysscategory->getUrlBaniere();
	   $image = $repository1->findOneById($idLogo);
	   $dm->remove($image);
	   $dm->flush();
	   
	   $myscategory->removeCategory($mysscategory);
       $dm->flush();
	   $formatted = [
           'statut' => '200'
	   ];
	   header('Access-Control-Allow-Origin: *');
	   return new JsonResponse($formatted); 
	}
	
	/**
     * @POST("/category/scategory/update")
	 * body-parm : 
	 * url-parm :
	 * update a sub category 
     */
    public function updateSCategoryAction(Request $request)
    {
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Category');
	  $repository1 = $dm->getRepository('RestBundle:Image');
	  $image = $repository1->findOneById($request->get('urlBaniere'));
	 
	  // checking if the category  exist
	  $category = $repository->findOneByName($request->get('nameCategory'));
	  
	  if(empty($category)){
			$formatted = [
               'statut' => '404'
            ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   }
	   
	   // else we can create the subcategory
	   $scategories= $category->getCategories();
	   $scategory= new SCategory();
	   
	   //verify if the subcategory  exist 
	   foreach ($scategories as $scategory1) {
	       if ($scategory1->getId() == $request->get('idSCategory')){
		       $myscategory = $scategory1;
		   }
	   }
		
	    if(empty($myscategory)){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
	   }
	  
	   
	   // add the modified version of the deleted subcategory
	   // if it exist we can update
	   $oldLogoId= $myscategory->getUrlBaniere();
	   if($oldLogoId != $request->get('urlBaniere')){
		    $oldImage = $repository1->findOneById($oldLogoId);
		    $dm->remove($oldImage);
			$dm->flush();
	   }
	   $image->setIsUsed(true);
	   $dm->flush();
	   $category->removeCategory($myscategory);
	   
	   $newScategory= new SCategory();
	   $newScategory->setName($request->get('name'));
	   $newScategory->setUrlBaniere($request->get('urlBaniere'));
	   $category->addCategory($newScategory);
	    
       $dm->flush();
		$formatted = [
           'statut' => '200'
		];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted); 
	}
	
	/**
     * @POST("/category/sscategory/update")
	 * body-parm : 
	 * url-parm :
	 * update a sub sub category 
     */
    public function updateSSCategoryAction(Request $request)
    {
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Category');
	 
	  // checking if the category  exist
	  $category = $repository->findOneByName($request->get('nameCategory'));
	  
	  if(empty($category)){
			$formatted = [
               'statut' => '404'
            ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	  }
	   
	   // else we can modify the ssubcategory
	   $scategories= $category->getCategories();
	   $scategory= new SCategory();
	   
	   //recuperation de la sous-category 
	   foreach ($scategories as $scategory1) {
	       if ($scategory1->getName() == $request->get('nameSCategory')){
		       $myscategory = $scategory1;
		   }
	   }
	    if(empty($myscategory)){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
	   } 
	   
	   // recuperation de la sous sous-category 
	   $mysscategories= $myscategory-> getCategories();
	   foreach ($mysscategories as $sscategory1) {
	       if ($sscategory1->getId() == $request->get('idSSCategory')){
		       $mysscategory = $sscategory1;
		   }
	   }
	   
	   if(empty($mysscategory)){
			$formatted = [
               'statut' => '404'
            ];
			header('Access-Control-Allow-Origin: *');
			return new JsonResponse($formatted);
	   } 
	   
	   // suppression de la sous sous-category
	   $myscategory->removeCategory($mysscategory);
	   
	   //ajout de la version modifier
	   $sscategory= new SSCategory();
	   $sscategory->setName($request->get('name'));
	   $myscategory->addCategory($sscategory);
	   $dm->persist($sscategory);
       $dm->flush();
	   $formatted = [
           'statut' => '200'
       ];
	  header('Access-Control-Allow-Origin: *');
	  return new JsonResponse($formatted);
	}
	
	/**
     * @POST("/category/sscategory")
	 * post-parm : nom_category(string), nom_scategory(String), name(Sting)
	 * created a subsubcategories inside a sub cateorie // 
     */
     public function postSSCategoryAction(Request $request)
    {
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Category');
	  
	  // checking if the category  exist
	  $category = $repository->findOneByName($request->get('nameCategory'));
	  if(empty($category)){
			$formatted = [
               'status' => '4041'
            ];
		  header('Access-Control-Allow-Origin: *');
		  return new JsonResponse($formatted);
	   }
	   
	   $scategories = $category->getCategories();
	   $myscategory;
	   foreach ($scategories as $scategory1) {
	       if ($scategory1->getName()== $request->get('nameSCategory')){
		       $myscategory = $scategory1;
		   } 
	   }
	   
	   //verify if the ssubcategory did not exist already 
	    $mysscategories= $myscategory-> getCategories();
	    foreach ($mysscategories as $sscategory1) {
	       if ($sscategory1->getName() == $request->get('name')){
		       $mysscategory = $scategory1;
		   }
	   
	    }
		
	    if(!empty($mysscategory)){
			$formatted = [
               'statut' => '404'
            ];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($formatted);
	   }
	   
	   //else
	   $sscategory= new SSCategory();
	   $sscategory->setName($request->get('name'));
	   $myscategory->addCategory($sscategory);
	   $dm->persist($sscategory);
       $dm->flush();
	   
	   $formatted = [
           'statut' => '200'
       ];
	  header('Access-Control-Allow-Origin: *');
	  return new JsonResponse($formatted);  
	}
	
	
	
	/**
     * @GET("/category/all")
	 * return all the categories
     */
    public function getAllCategory1Action(Request $request)
    {
	  $dm= $this->get('doctrine_mongodb')->getManager();
	  $repository = $dm->getRepository('RestBundle:Category');
	  $categories = $repository->findAll();
	   $formatted = [];
	   $formattedScat = [];
	   $response=[];
        foreach ($categories as $category) {
		
		    $scategories = $category->getCategories();
			$formattedScat=[];
			foreach ($scategories as $scategori ){
				$formattedsscat=[];
				foreach ($scategori-> getCategories() as $sscategori ){
					$formattedsscat[] = [
					'id' => $sscategori->getId(),
					'name'=> $sscategori->getName()
					];
				}
				
				
				$formattedScat[] = [
				'id' => $scategori->getId(),
				'name'=> $scategori->getName(),
				'urlBaniere'=> $scategori->getUrlBaniere(),
				'category'=> $formattedsscat,
				];
				
				
            
			}
			
            $formatted[] = [
               'id' => $category->getId(),
               'name' => $category->getName(),
               'urlBaniere' => $category->getUrlBaniere(),
               'urlIcone'=> $category->getUrlIcone(),
			   'category' => $formattedScat
            ];
			
			
        }
		$response=[
			'statut' => '200',
			'data'=> $formatted
		];
	  header('Access-Control-Allow-Origin: *');
	  return new JsonResponse($response);
	}
	
	/**
     * @GET("/category/{nom_categorie}")
	 * return a categorie with his their subcategories
     */
    public function getCategoryByIdAction($nom_categorie,Request $request)
    {
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:Category');
		$category = $repository->findOneByName($nom_categorie);
		$formatted = [];
		$formattedScat = [];
		$response=[];
        $scategories = $category->getCategories();

		foreach ($scategories as $scategori ){
			$formattedsscat=[];
			foreach ($scategori-> getCategories() as $sscategori ){
				$formattedsscat[] = [
					'id' => $sscategori->getId(),
					'name'=> $sscategori->getName()
					];
			}
				
				$formattedScat[] = [
				'id' => $scategori->getId(),
				'name'=> $scategori->getName(),
				'urlBaniere'=> $scategori->getUrlBaniere(),
				'category'=> $formattedsscat,
				];
				
				
            
		}
			
            $formatted = [
               'id' => $category->getId(),
               'name' => $category->getName(),
               'urlBaniere' => $category->getUrlBaniere(),
			   'category' => $formattedScat
            ];
			
			
        
		$response=[
			'statut' => '200',
			'data'=> $formatted
		];
		
	  header('Access-Control-Allow-Origin: *');
	  return new JsonResponse($response);
	}
	
	/**
     * @GET("/category/{nom_categorie}/{nom_scategorie}")
	 * return the subcategorie of a subcategorie
     */
    public function getSCategoryByIdAction($nom_categorie,$nom_scategorie,Request $request)
    {
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:Category');
		$category = $repository->findOneByName($nom_categorie);
		$formatted = [];
		$formattedScat = [];
		$response=[];
        $scategories = $category->getCategories();
		
		//retrieving of the subcategorie 
	   foreach ($scategories as $scategorie) {
	       if ($scategorie->getName() == $nom_scategorie){
		       $myscategory = $scategorie;
		   }
	   }
	   
		$sscategories= $myscategory->getCategories();
		$formattedsscat=[];
		foreach ($sscategories as $sscategori ){
			$formattedsscat[] = [
				'id' => $sscategori->getId(),
				'name'=> $sscategori->getName()
			];        
		}
			
		$response=[
			'statut' => '200',
			'data'=> $formattedsscat
		];
		
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	}
	
	
	/**
     * @GET("/category/scategory/{id_category}")
	 * url-parm: id_category
	 * return all the subcategories of a categories 
     */
	  public function getSubCategoryAction($id_category, Request $request)
      {
	 
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:Category');
		$category = $repository->findOneById($id_category);
		$scategories= $category->getCategories();
		$formatted = [];
		foreach ($scategories as $scategori ){
				$formatted[] = [
				'id' => $scategori->getId(),
				'name'=> $scategori->getName(),
				'urlBaniere' => $scategori->getUrlBaniere()
				];
		}
		
		$response =[
			'statut'=> '200',
			'response'=> $formatted
		];
		return new JsonResponse($response);
	 
	 }
	 
	 /**
     * @GET("/category/sscategory/{nom_category}/{nom_scategory}")
	 * url-parm: id_category
	 * return all the subcategories of a sub-categories 
     */
	 public function getSubSubCategoryAction($nom_category,$nom_scategory){
		$dm= $this->get('doctrine_mongodb')->getManager();
		$repository = $dm->getRepository('RestBundle:Category');
		$category = $repository->findOneByName($nom_category);
		$scategories= $category->getCategories();
		$formatted = [];
		$myscategory= null;
		foreach ($scategories as $scategory ){ 
			 if($scategory->getName()== $nom_scategory){
				$myscategory = $scategory;
			 }		
		}
		
		$sscategories = $myscategory->getCategories();
		foreach($sscategories as $sscategory){
			$formatted[] = [
				'id' => $sscategory->getId(),
				'name'=> $sscategory->getName()
			];
		}
		
		$response =[
			'statut'=> '200',
			'data'=> $formatted
		];
		header('Access-Control-Allow-Origin: *');
		return new JsonResponse($response);
	 
	 }
	 
	 
	 
	 
	 /**
      * @POST("/category/file")
      */
     public function postFileAction(Request $request)
    {
	  $file = $request->files->get('photo');
	  //var_dump($file);
	     $response =[
			'name'=> $file->getClientOriginalName(),
			'type'=> $file->getMimeType()
		];
		$directory='C:/wamp/www/maayi';
		$file1 = $file->move($directory, $file->getClientOriginalName());
	    return new JsonResponse($response);
	}
}	
