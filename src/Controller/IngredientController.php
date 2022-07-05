<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use Doctrine\Persistence\ObjectManager;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    private $ingredients;

    public function __construct(IngredientRepository $ingredient)
    {
        $this->ingredients = $ingredient;
    }

    #[Route('/ingredients', name: 'app_ingredient')]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $this->ingredients->findAll(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }

    #[Route('/ingredient/new', name: 'app_ingredient_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredient();

        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

             $this->addFlash(
                'notice',
                'Votre ingrédient à bien été crée.'
            );


            return $this->redirectToRoute('app_ingredient_new');
        }

        return $this->renderForm('ingredient/new.html.twig', [
            'form' => $form,
        ]);
    }
}
