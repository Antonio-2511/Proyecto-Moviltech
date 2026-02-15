<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Obtiene el error de autenticación si el login ha fallado
        $error = $authenticationUtils->getLastAuthenticationError();

        // Recupera el último email/usuario introducido
        $lastUsername = $authenticationUtils->getLastUsername();

        // Renderiza la vista del login pasando los datos necesarios
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony intercepta automáticamente esta ruta.
        // Nunca se ejecuta este código si el firewall está bien configurado.
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
