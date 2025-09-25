<?php

namespace App\Repository;

use App\Entity\Record;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Репозиторий для взятия сущностеи записей.
 * @extends ServiceEntityRepository<Record>
 */
class RecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Record::class);
    }

    /**
     * Взятие записей по поиску с пагинацией на страницы.
     * @param string $title Поиск по совпадению вхождений строки (ищет по заголовкам статей).
     * @param int $page Страница для пагинации.
     * @param int $size Количество элементов на странице (для пагинации).
     * @return array Массив данных из БД.
     */
    public function getRecordByTitle(string $title, int $page = 1, int $size = 30): array
    {
        $query = $this->createQueryBuilder('r');

        if ($title != null) {
            $query->where('r.title LIKE :search')
                ->setParameter('search', '%'.$title.'%');
        }

        return $query->setFirstResult(($page - 1) * $size)
            ->setMaxResults($size)
            ->getQuery()
            ->getResult();
    }

    /**
     * Функция для сохранения записи в бд по данным.
     * @param array $data Данные озаписи в формате массива.
     * @param mixed $currentUser Обьект текущего пользователя из компонента Security
     * @return void
     */
    public function createRecord(array $data, mixed $currentUser) {
        // создаем обьект записи и сохраняем в бд
        $record = new Record();
        $record->setTitle($data['title']);
        $record->setDescription($data['description']);
        $record->setAuthor($currentUser);
        $record->setDate(new DateTime());

        $this->getEntityManager()->persist($record);
        $this->getEntityManager()->flush();
    }

    /**
     * Удаление записи по ее id.
     * @param string $id Id записи.
     * @return bool Статус выполнения (True/False).
     */
    public function deleteRecordById(string $id): bool {
        $thisManager = $this->getEntityManager();

        if ($id != null) {
            $thisManager->remove($this->findOneBy(['id' => $id]));
            $thisManager->flush();

            return true;
        } else {
            return false;
        }
    }
}
