<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tarea; // Asegúrate de importar tu entidad Tarea
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;


class TareaController extends AbstractController
{
    // #[Route('/tarea', name: 'app_tarea')]
    // public function index(): JsonResponse
    // {
    //     return $this->json([
    //         'message' => 'Welcome to your new controller!',
    //         'path' => 'src/Controller/TareaController.php',
    //     ]);
    // }

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
                'estado' => $tarea->isEstado(),
                'identificador' => $tarea->getId(),
            ];
        }

        return $this->json(['tareas' => $tareasArray]);
    }

    #[Route('/tarea', name: 'create_tarea', methods: ['POST'])]
    public function createTarea(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $tarea = new Tarea();
        $tarea->setNombre($data['nombre']);
        $tarea->setDescripcion($data['descripcion']);
        $tarea->setEstado($data['estado']); // Estado inicial por defecto, puedes cambiarlo según tus necesidades

        $entityManager->persist($tarea);
        $entityManager->flush();

        return $this->json([
            'message' => 'Tarea creada correctamente',
            'tarea' => [
                'nombre' => $tarea->getNombre(),
                'descripcion' => $tarea->getDescripcion(),
                'estado' => $tarea->isEstado(),
                'identificador' => $tarea->getId()
            ]
        ], JsonResponse::HTTP_CREATED); // Retorna un código de estado HTTP 201, que indica que el recurso fue creado exitosamente
    } 

    #[Route('/tarea/{id}', name: 'get_tarea', methods: ['GET'])]
    public function getTarea(ManagerRegistry $doctrine, int $id): JsonResponse
    {   
        $entityManager = $doctrine->getManager();
        $tarea = $entityManager->getRepository(Tarea::class)->find($id);

        if (!$tarea) {
            return $this->json(['message' => 'Tarea no encontrada'], 404);
        }

        $dataTarea = [
            'nombre' => $tarea->getNombre(),
            'descripcion' => $tarea->getDescripcion(),
            'estado' => $tarea->isEstado(),
            'id' => $tarea->getId()
        ];

        return $this->json(['data' => $dataTarea]);
    }

    #[Route('/tarea/{id}', name: 'delete_tarea', methods: ['DELETE'])]
    public function deleteTarea(ManagerRegistry $doctrine, int $id): JsonResponse
    {   
        $entityManager = $doctrine->getManager();
        $tarea = $entityManager->getRepository(Tarea::class)->find($id);

        if (!$tarea) {
            return $this->json(['message' => 'Tarea no encontrada no hay nada que eliminar'], 404);
        }

        $entityManager->remove($tarea);
        $entityManager->flush();


        return $this->json(['message' => "Tarea id: $id eliminada correctamente"]);
    }

}
