<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
        $allUsers = $userRepository->findAllSortByLastname();
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
        // dump($allUsers, $users, $superAdmins, $admins);
        return $this->render('back/user/list.html.twig', [
            'users' => $users,
            'superAdmins' => $superAdmins,
            'admins' => $admins
        ]);
    }


    // /**
    //  * @Route("/new", name="app_back_user_new", methods={"GET", "POST"})
    //  */
    // public function new(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    // {
    //     $user = new User();
    //     $form = $this->createForm(UserType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $passwordClear = $user->getPassword();
    //         $hashedPassword = $passwordHasher->hashPassword($user, $passwordClear);
    //         $user->setPassword($hashedPassword);

    //         // cette méthode fait le persist et le flush !
    //         $userRepository->add($user, true);

    //         return $this->redirectToRoute('app_back_user_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('back/user/new.html.twig', [
    //         'user' => $user,
    //         'form' => $form,
    //     ]);
    // }

    // /**
    //  * @Route("/{id}", name="app_back_user_show", methods={"GET"})
    //  */
    // public function show(User $user): Response
    // {
    //     return $this->render('back/user/show.html.twig', [
    //         'user' => $user,
    //     ]);
    // }

    // /**
    //  * @Route("/{id}/edit", name="app_back_user_edit", methods={"GET", "POST"})
    //  */
    // public function edit(Request $request, Security $security, User $user, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    // {
    //     // $this->denyAccessUnlessGranted('ROLE_ADMIN');
    //     $this->denyAccessUnlessGranted('USER_UPDATE', $user);

    //     // USER_UPDATE
    //     // si l'utilisateur que l'on édite a le role manager ou admin
    //     $form = $this->createForm(UserType::class, $user);
    //     // $form->remove('password');

    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // est ce qu'un mot de passe a été saisi

    //         $passwordClear = $form->get('password')->getData();
    //         if (! empty($passwordClear))
    //         {
    //             // si oui alors le hashé et le remplacer dans l'objet user
    //             $hashedPassword = $passwordHasher->hashPassword($user, $passwordClear);
    //             $user->setPassword($hashedPassword);
    //         }

    //         $userRepository->add($user, true);

    //         return $this->redirectToRoute('app_back_user_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('back/user/edit.html.twig', [
    //         'user' => $user,
    //         'form' => $form,
    //     ]);
    // }

    //   /**
    //  * @Route("/{id}", name="app_back_user_delete", methods={"POST"})
    //  */
    // public function delete(Request $request, User $user, UserRepository $userRepository): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
    //         $userRepository->remove($user, true);
    //     }

    //     return $this->redirectToRoute('app_back_user_index', [], Response::HTTP_SEE_OTHER);
    // }


}
