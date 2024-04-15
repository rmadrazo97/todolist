<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tarea; // Asegúrate de importar tu entidad Tarea
use Doctrine\Persistence\ManagerRegistry;

class TareaController extends AbstractController
{
    #[Route('/tarea', name: 'app_tarea')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TareaController.php',
        ]);
    }

    #[Route('/tareas', name: 'get_tareas', methods: ['GET'])]
    public function getTareas(ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $tareas = $entityManager->getRepository(Tarea::class)->findAll();
        $tareasArray = [];

        foreach ($tareas as $tarea) {
            $tareasArray[] = [
                'nombre' => $tarea->getNombre(),
                'descripcion' => $tarea->getDescripcion(),
                'estado' => $tarea->getEstado(),
                'identificador' => $tarea->getId(),
            ];
        }

        return $this->json(['tareas' => $tareasArray]);
    }

    #[Route('/tarea/{id}', name: 'get_tarea', methods: ['GET'])]
    public function getTarea(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $tarea = $entityManager->getRepository(Tarea::class)->find($id);

        if (!$tarea) {
            return $this->json(['error' => 'Tarea not found'], 404);
        }

        $tareaArray = [
            'nombre' => $tarea->getNombre(),
            'descripcion' => $tarea->getDescripcion(),
            'estado' => $tarea->getEstado(),
            'identificador' => $tarea->getId(),
        ];

        return $this->json(['tarea' => $tareaArray]);
    }


}
