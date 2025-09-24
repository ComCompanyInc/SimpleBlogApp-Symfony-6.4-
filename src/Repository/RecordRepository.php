<?php

namespace App\Repository;

use App\Entity\Record;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Record>
 */
class RecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Record::class);
    }

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
