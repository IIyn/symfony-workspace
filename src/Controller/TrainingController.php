<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TrainingRepository;
use App\Entity\School;
use App\Entity\Training;
use App\Entity\Module;

class TrainingController extends AbstractController
{
	#[Route('/search_training', name: 'training_list')]
    public function list(EntityManagerInterface $em, TrainingRepository $trainingRepository, Request $request)
	{
	    // Get all modules for the filter checkboxes
	    $modules = $em->getRepository(Module::class)->findAll();

	    // Get selected module IDs from the request
	    $selectedModules = $request->query->all('modules');
	    
	    // Check if "Match Any Module" is selected
	    $matchAnyModule = $request->query->getBoolean('match_any_module', false);

	    if (empty($selectedModules)) {
	        // If no modules selected, show all trainings
	        $trainings = $trainingRepository->findAll();
	    } else {
	        if ($matchAnyModule) {
	            // If "Match Any Module" is selected, find trainings with at least one module
	            $trainings = $trainingRepository->findByAnyModule($selectedModules);
	        } else {
	            // Otherwise, find trainings with all selected modules
	            $trainings = $trainingRepository->findByModules($selectedModules);
	        }
	    }

	    return $this->render('training/list.html.twig', [
	        'trainings' => $trainings,
	        'selectedModules' => $selectedModules,
	        'modules' => $modules,
	        'matchAnyModule' => $matchAnyModule, // Pass the checkbox state to the view
	    ]);
	}

	#[Route('/manage_training/{id}', name: 'manage_training', methods: ['GET'])]
	public function manageTraining(int $id, EntityManagerInterface $entityManager, Request $request): Response
	{
		$training = $entityManager->getRepository(Training::class)->find($id);

		if (!$training) {
			throw $this->createNotFoundException('La formation demandée n\'existe pas.');
		}

		$moduleId = $request->query->get('remove');
		if ($moduleId) {
			$module = $entityManager->getRepository(Module::class)->find($moduleId);

			if ($module === null || !$training->getModules()->contains($module)) {
				throw $this->createNotFoundException('Le module demandé n\'est pas lié à la formation.');
			} else {
				$training->removeModule($module);
				$entityManager->remove($module);
				$entityManager->flush();

				return $this->redirectToRoute('manage_training', ['id' => $training->getId()]);
			}
		}

		return $this->render('training/manage.html.twig', [
			'training' => $training,
		]);
	}
}