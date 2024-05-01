<?php
namespace App\Controller;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
Use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\ArticleType;
use App\Entity\Category;
class IndexController extends AbstractController
{
   #[Route('/',name:'article_list')]
   public function home(EntityManagerInterface  $entityManager): Response
   {
       $articles = $entityManager->getRepository(Article::class)->findAll();
       return $this->render('articles/index.html.twig',['articles'=> $articles]);
   }

   #[Route('/new', name: 'new_article', methods:['GET','POST'])]
    public function new(PersistenceManagerRegistry $managerRegistry,Request $request)  {
      $article = new Article();
      $form = $this->createForm(ArticleType::class,$article);
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()) 
      { 
        $article = $form->getData();
        $entityManager =$managerRegistry->getManager();
        $entityManager->persist($article);
        $entityManager->flush();
        return $this->redirectToRoute('article_list');
    }
    return $this->render('articles/new.html.twig',['form' => $form->createView()]);
  }
     
 

     #[Route('/save', name: 'save-article')]
 public function save(PersistenceManagerRegistry $doctrine){
    $entityManager = $doctrine->getManager();
    $article = new Article();
    $article->setNom('Article 3');
    $article->setPrix(2080);
   
    $entityManager->persist($article);
    $entityManager->flush();
    return new Response('Article enregisté avec id '.$article->getId());
 }


 #[Route('/article/{id}', name:"article_show")]
 public function show(PersistenceManagerRegistry $managerRegistry,$id)  {
   $article=$managerRegistry->getRepository(Article::class)->find($id);
   return $this->render('articles/show.html.twig', array('article' => $article)); 
 }

  //Modifier un article
  #[Route('/article/edit/{id}',name:"edit_article",methods:['GET','POST'])]
  public function edit(PersistenceManagerRegistry $managerRegistry,Request $request,$id)  {
    $article = new Article();
    $article=$managerRegistry->getRepository(Article::class)->find($id);
    $form = $this->createForm(ArticleType::class,$article);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) 
    { 
      $entityManager = $managerRegistry->getManager(); 
      $entityManager->flush(); 
      return $this->redirectToRoute('article_list');
  }
  return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
}

#[Route('/article/delete/{id}', name: "delete_article")]
public function delete(PersistenceManagerRegistry $managerRegistry, Request $request, int $id): Response
{
    $article = $managerRegistry->getRepository(Article::class)->find($id);

    if (!$article) {
      
    }
    $entityManager = $managerRegistry->getManager();
    $entityManager->remove($article);
    $entityManager->flush();
    return $this->redirectToRoute('article_list');
}

#[Route('/category/newCat', name: 'new_category', methods:['GET','POST'])]
public function newCategory(PersistenceManagerRegistry $managerRegistry,Request $request) {
  $category = new Category();
  $form = $this->createForm(CategoryType::class,$category);
  $form->handleRequest($request);
  if($form->isSubmitted() && $form->isValid()) {
    $category = $form->getData();
    $entityManager=$managerRegistry->getManager();
    $entityManager->persist($category);
    $entityManager->flush();

  }
  return $this->render('categ/newCategory.html.twig',['form'=> $form->createView()]);
}
}

