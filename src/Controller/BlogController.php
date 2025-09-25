<?php

namespace App\Controller;

use App\Service\BlogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Отвечает за обработку роутов относительно записей пользователей на сайте.
 */
class BlogController extends AbstractController
{
    private $blogService;
    private EntityManagerInterface $entityManager;

    public function __construct(BlogService $blogService, EntityManagerInterface $entityManager)
    {
        $this->blogService = $blogService;
        $this->entityManager = $entityManager;
    }

    /**
     * Домашняя страница с записями пользователей, доступная только после регистрации на сайте.
     * @param Request $request Хранит данные из запроса.
     * @param Security $security Обьект для взятия текущего пользователя из модуля Security.
     * @return Response .twig страница.
     */
    #[Route('/home', name: 'home')]
    public function home(Request $request, Security $security): Response
    {
        $title = $request->query->get("title") ?? "";
        $page = $request->query->get("page") ?? 1;
        $size = $request->query->get("size") ?? $this->blogService::AMOUNT_OF_ITEMS;
        
        $records = $this->blogService->getRecords($title, $page, $size);

        return $this->render('home.html.twig', [
            'records' => $records,
            'page' => $page,
            'size' => $size,
            'currentUser' => $security->getUser()
        ]);
    }

    /**
     * Удаление записи по ее Id.
     * @param Request $request Обьект для взятия Id записи из запроса
     * @return JsonResponse
     */
    #[Route('/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request): JsonResponse
    {
        $idRecord = $request->query->get("id");

        $response = null;
        if($this->blogService->deleteRecord($idRecord)) {
            $response = new JsonResponse([200, "Удаление совершено успешно!"]);
        } else {
            $response = new JsonResponse([400, "При удалении возникли проблемы"]);
        }

        return $response;
    }

    /**
     * Создание записи пользователем.
     * @param Request $request Обьект, хранящий данные в формате Json для сохранения.
     * @param Security $security Обьект для взятия текущего пользователя из модуля Security.
     * @return JsonResponse Ответ в формате json.
     */
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request, Security $security): JsonResponse
    {
        $data = $request->toArray(); // собираем json в массив

        return $this->blogService->createRecord($data, $security->getUser());
    }
}