<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/create-test-user', name: 'create_test_user')]
    public function createTestUser(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Проверяем, нет ли уже пользователя с таким логином
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['login' => 'log']);

        if ($existingUser) {
            return new Response('Пользователь с логином "log" уже существует!');
        }

        // Создаем нового пользователя
        $user = new User();
        $user->setLogin('log');
        $user->setUsername('max');
        $user->setActive(true);

        // Хешируем пароль
        $hashedPassword = $passwordHasher->hashPassword($user, 'pas');
        $user->setPassword($hashedPassword);

        // Сохраняем в базу данных
        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('Тестовый пользователь создан!<br>Логин: log<br>Пароль: pas<br><a href="/login">Перейти к входу</a>');
    }
}
