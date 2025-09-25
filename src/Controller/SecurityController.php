<?php

namespace App\Controller;

use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Отвечает за обработку роутов логина и регистрации.
 */
class SecurityController extends AbstractController
{
    private $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Роут со входом
     * @param AuthenticationUtils $authenticationUtils Обьект аутентефикации из модуля Security
     * @return Response .twig страница
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Роут с выходом (по умолчанию создан для конфигурации разлогинивания в security.yaml)
     * @return void
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Страница регистрации.
     * @param EntityManagerInterface $entityManager Обьект менеджера БД.
     * @param Request $request Данные из запроса.
     * @param UserPasswordHasherInterface $passwordHasher Обьект для хеширования пароля.
     * @return JsonResponse Ответ в формате json
     */
    #[Route('/signIn', name: 'createUser', methods: ['POST'])]
    public function createTestUser(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = $request->toArray(); // собираем json в массив

        return $this->securityService->createNewUser($data, $entityManager, $passwordHasher);
    }

    /**
     * Страница регистрации.
     * @param Request $request Данные из запроса.
     * @return Response .twig страница
     */
    #[Route('/registration', name: 'registration')]
    public function registration(Request $request): Response
    {
        return $this->render('security/registration.html.twig', []);
    }
}
