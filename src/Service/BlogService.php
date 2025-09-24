<?php

namespace App\Service;

use App\Entity\Record;
use App\Entity\User;
use App\Repository\RecordRepository;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class BlogService
{
    const AMOUNT_OF_ITEMS = 30; // константа, отвечающая за количество записей на странице
    private $recordRepository;

    public function __construct(RecordRepository $recordRepository)
    {
        $this->recordRepository = $recordRepository;
    }

    /**
     * Взять все записи с пагинацией, и по поиску (если не пуст).
     * @param string $title Поисковая строка (для сортировки по названию заголовка).
     * @param int $page Номер страницы.
     * @param int $size Количество записей на одной странице.
     * @return array Данные (сами записи в формате массива из БД).
     */
    public function getRecords(string $title, int $page, int $size): array
    {
        return $this->recordRepository->getRecordByTitle($title, $page, $size);
    }

    public function createRecord(array $data, mixed $currentUser): JsonResponse {
        try {
            $this->recordRepository->createRecord($data, $currentUser);

            return new JsonResponse([201, "Успешное сохранение!"]);
        } catch (Exception $e) {
            return new JsonResponse([400, $e->getMessage()]);
        }
    }

    public function deleteRecord(string $id): bool
    {
        return $this->recordRepository->deleteRecordById($id);
    }
}