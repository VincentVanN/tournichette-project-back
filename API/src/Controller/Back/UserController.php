<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/back/users", name="app_back_user")
 */
class UserController extends AbstractController
{
    /**
     * List all users
     * @Route("", name="_list", methods="GET")
     */
    public function list(UserRepository $userRepository): Response
    {
        $allUsers = $userRepository->findAllSortBy('lastname');
        $users = [];
        $superAdmins = [];
        $admins = [];

        foreach ($allUsers as $currenUser)
        {
            if (in_array('ROLE_USER', $currenUser->getRoles())
                && !in_array('ROLE_SUPER_ADMIN', $currenUser->getRoles())
                && !in_array('ROLE_ADMIN', $currenUser->getRoles())) {
                $users[] = $currenUser;
            }

            if (in_array('ROLE_SUPER_ADMIN', $currenUser->getRoles())) {
                $superAdmins[] = $currenUser; 
            }

            if (in_array('ROLE_ADMIN', $currenUser->getRoles())) {
                $admins[] = $currenUser;
            }
        }

        return $this->render('back/user/list.html.twig', [
            'users' => $users,
            'superAdmins' => $superAdmins,
            'admins' => $admins
        ]);
    }

    /**
     * Create un new user
     * @Route("/new", name="_new", methods={"GET", "POST"})
     */
    public function create(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    {
        $user = new User();
        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()) {
            
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $role = $formUser->get('roles')->getData() !== null ? $formUser->get('roles')->getData() : 'ROLE_USER';
            $user->setRoles([$role]);
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_back_user_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/user/new.html.twig', [
            'user' => $user,
            'form' => $formUser
        ]);
    }

    /**
     * Show details of a user with given ID
     * @Route("/{id<\d+>}", name="_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('back/user/show.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Edit a user with given ID
     * @Route("/{id<\d+>}/edit", name="_edit", methods={"GET", "POST"})
     */
    public function edit(User $user, Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    {
        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted()) {

            $passwordClear = $formUser->get('password')->getData();

            if(!empty($passwordClear)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $passwordClear);
                $user->setPassword($hashedPassword);
            }

            
            $role = $formUser->get('roles')->getData() !== null ? $formUser->get('roles')->getData() : 'ROLE_USER';
            $user->setRoles([$role]);

            $userRepository->add($user, true);

            return $this->redirectToRoute('app_back_user_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/user/edit.html.twig', [
            'user' => $user,
            'form' => $formUser
        ]);
    }

      /**
     * @Route("/{id<\d+>}", name="_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_back_user_list', [], Response::HTTP_SEE_OTHER);
    }

}
