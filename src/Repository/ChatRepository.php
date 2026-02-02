<?php

namespace App\Repository;

use App\Entity\Chat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chat>
 */
class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    //    /**
    //     * @return Chat[] Returns an array of Chat objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Chat
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Busca chats que contienen al usuario especificado (únicamente chats donde el usuario participa)
     *
     * @param mixed $user User entity or user id
     * @return Chat[]
     */
    public function findChatsByUser($user): array
    {
        $qb = $this->createQueryBuilder('c')
            ->innerJoin('c.usuariosChat', 'uc')
            ->innerJoin('uc.usuario', 'u')
            ->andWhere('u.id = :uid')
            ->setParameter('uid', $user instanceof \App\Entity\User ? $user->getId() : $user)
            ->orderBy('c.fechaCreacion', 'DESC')
            ->setMaxResults(50);

        return $qb->getQuery()->getResult();
    }
}

