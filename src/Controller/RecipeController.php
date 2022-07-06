<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    private $recipes;

    public function __construct(RecipeRepository $recipes)
    {
        $this->recipes = $recipes;
    }

    /**
     * Controller pour afficher les recettes
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recipe', name: 'app_recipe')]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $this->recipes->findAll(),
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    /**
     * Controller pour créer une recette
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return void
     */
    #[Route('/recipe/new', name: 'app_recipe_new', methods: ['GET','POST'])]
    public function newRecipe(Request $request, EntityManagerInterface $manager)
    {
        $recipe = new Recipe();

        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();

            return $this->redirectToRoute('app_recipe_new');
        }

        return $this->renderForm('recipe/new.html.twig', [
            'form' => $form,
        ]);

    }

    /**
     * Controller pour editer une recette
     */
    #[Route('/recipe/edit/{id}', name: 'app_recipe_edit', methods: ['GET', 'POST'])]
    public function editRecipe (
        Request $request,
        EntityManagerInterface $manager,
        Recipe $recipe
        )
    {
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();

            return $this->redirectToRoute('app_recipe');

        }

        return $this->render('recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/recipe/suppression/{id}', name: 'app_recipe_delete')]
    public function deleteRecipe(
        EntityManagerInterface $manager,
        Recipe $recipe
    ): Response
    {
        $manager->remove($recipe);
        $manager->flush();
       return $this->redirectToRoute('app_recipe');
    }

}
