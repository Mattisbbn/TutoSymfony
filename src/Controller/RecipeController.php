<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Recipe;
use App\Form\RecipeType;

final class RecipeController extends AbstractController
{


    #[Route(
    '/recette',
    name: 'recipe.index',
    )]
    public function index(Request $request,RecipeRepository $repository): Response
    {
        $recipes = $repository->findWithDurationLowerThan(10);

      

      
        return $this->render('recipe/index.html.twig',[ 'recipes' => $recipes ]);
    }



    #[Route(
    '/recette/{slug}-{id}',
    name: 'recipe.show',
    requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+']
    )]
    public function show(int $id,string $slug,RecipeRepository $repository): Response
    {
           $recipe = $repository->find($id);
           if($recipe->getSlug() !== $slug){
               return $this->redirectToRoute('recipe.show',[
                   'id' => $id,
                   'slug' => $recipe->getSlug()
               ],301);
           }
           return $this->render('recipe/show.html.twig', ['recipe' => $recipe]);
    }


    #[Route('/recettes/{id}/edit', "recipe.edit")]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em): Response{

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash("success","La recette à bien été mdifiée.");
            return $this->redirectToRoute("recipe.index");
        }


        return $this->render("recipe/edit.html.twig", ["recipe" => $recipe,"form"=> $form ]);
    }

    #[Route('/recettes/create', "recipe.create")]
    public function create( Request $request , EntityManagerInterface $em): Response{
         $recipe = new Recipe();

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setCreatedAt(new \DateTimeImmutable());
            $recipe->setUpdatedAt(new \DateTimeImmutable());
            $em->persist($recipe);
            $em->flush();
        }

        return $this->render("recipe/create.html.twig", ["form"=> $form ]);

    }
}
