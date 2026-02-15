<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager
    ): Response {

        // Se crea una nueva instancia de usuario
        $user = new User();

        // Se construye el formulario de registro asociado a la entidad
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Si el formulario es válido, se procede al registro
        if ($form->isSubmitted() && $form->isValid()) {

            // Se encripta la contraseña antes de guardarla en base de datos
            $hashedPassword = $userPasswordHasher->hashPassword(
                $user,
                $user->getPassword()
            );

            $user->setPassword($hashedPassword);

            // Se guarda el usuario en base de datos
            $entityManager->persist($user);
            $entityManager->flush();

            // Se inicia sesión automáticamente tras el registro
            return $security->login($user);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
