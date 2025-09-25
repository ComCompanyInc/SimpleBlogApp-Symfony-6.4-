<?php

namespace App\Service;

use App\Entity\Role;
use App\Entity\User;
use App\Enums\RoleEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Сервис для управления логинами и паролями.
 */
class SecurityService
{
    /**
     * Функция создания нового пользователя
     * @param array $data Массив с данными о пользователе для сохранения.
     * @param EntityManagerInterface $entityManager Обьект для управления сущностями.
     * @param UserPasswordHasherInterface $passwordHasher Обьект для хеширования пароля.
     * @return JsonResponse
     */
    public function createNewUser(array $data, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $username = $data['username'];
        $login = $data['login'];
        $password = $data['password'];
        $role = RoleEnum::ROLE_USER;

        if ($login != null && $password != null && $username != null) {
            // Проверяем, нет ли уже пользователя с таким логином
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['login' => $login]);

            if ($existingUser) {
                return new JsonResponse([400, 'Пользователь с таким логином уже существует!']);
            }

            // Создаем нового пользователя
            $user = new User();
            $user->setLogin($login);
            $user->setUsername($username);
            $user->setActive(true);

            // Хешируем пароль
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            // Сохраняем в базу данных
            $entityManager->persist($user);
            $entityManager->flush();

            $existingRole = $entityManager->getRepository(Role::class)->findOneBy(['name' => $role]);
            if (!$existingRole) {
                return new JsonResponse([400, 'Ошибка - роли для пользователя не существует (создайте ее)!']);
            } else {
                $existingRole->setUser($user);
            }

            return new JsonResponse([201, 'Пользователь успешно создан!']);
        } else {
            return new JsonResponse([400, 'Ошибка - не все поля заполнены!']);
        }
    }
}