<?php

namespace UserBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use UserBundle\Entity\Preference;
use UserBundle\Entity\User;

use UserBundle\Form\UserType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends FOSRestController
{
    /**
     * @Rest\Get("/users")
     * @Rest\QueryParam(
     *     name="token",
     *     description="User's token"
     * )
     * @Rest\View()
     *
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Get the list of all users",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getUsersAction(Request $request, ParamFetcher $paramFetcher)
    {
        $token = $paramFetcher->get('token');
        $em = $this->getDoctrine()->getManager();

        if($token != "") {
            $user = $em->getRepository('UserBundle:AccessToken')->findOneByToken($token)->getUser();
            /* @var $user User */

            return $user;
        } else {
            if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $users = $em->getRepository('UserBundle:User')->findAll();
                /* @var $users User[] */

                return $users;
            } else {
                throw $this->createAccessDeniedException('Unable to access this page!');
            }
        }
    }

    /**
     * @Rest\Get("/users/{id}")
     * @Rest\View()
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Return one user",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The user unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($request->get('id'));
        /* @var $user User */

        if(empty($user)) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        return $user;
    }

    /**
     * @Rest\Post("/users")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Create a new user",
     *     requirements={
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="Name of the user."
     *         },
     *         {
     *             "name"="email",
     *             "dataType"="email",
     *             "requirement"="",
     *             "description"="The email of the user."
     *         }
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function postUsersAction(Request $request)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new \FOS\UserBundle\Event\GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new \FOS\UserBundle\Event\FormEvent($form, $request);
            $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_registration_confirmed');
                $response = new \Symfony\Component\HttpFoundation\RedirectResponse($url);
            }

            $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::REGISTRATION_COMPLETED, new \FOS\UserBundle\Event\FilterUserResponseEvent($user, $request, $response));

            $view = $this->view($user, Response::HTTP_CREATED);

            return $this->handleView($view);
        }

        $view = $this->view($form, Response::HTTP_BAD_REQUEST);
        return $this->handleView($view);
    }

    /**
     * @Rest\View()
     * @Rest\Put("/users/{id}")
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Update an existing user",
     *     requirements={
     *         {
     *             "name"="Name",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="Name of the user."
     *         },
     *         {
     *             "name"="email",
     *             "dataType"="email",
     *             "requirement"="",
     *             "description"="The email of the user."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function updateUserAction(Request $request)
    {
        return $this->updateUser($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/users/{id}")
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Update an existing user",
     *     requirements={
     *         {
     *             "name"="Name",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="Name of the user."
     *         },
     *         {
     *             "name"="email",
     *             "dataType"="email",
     *             "requirement"="",
     *             "description"="The email of the user."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function patchUserAction(Request $request)
    {
        return $this->updateUser($request, false);
    }

    private function updateUser(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($request->get('id'));
        /* @var $user User */
        if (empty($user)) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        if($this->get('security.token_storage')->getToken()->getUser() == $user OR $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            if ($clearMissing) {
                $options = ['validation_groups'=>['Default', 'FullUpdate']];
            } else {
                $options = [];
            }

            $form = $this->createForm(UserType::class, $user);
            $form->submit($request->request->all(), $clearMissing);
            if ($form->isValid()) {
                if (!empty($user->getPlainPassword())) {
                    $encoder = $this->get('security.password_encoder');
                    $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
                    $user->setPassword($encoded);
                }

                $em->merge($user);
                $em->flush();
                return $user;
            } else {
                return $form;
            }
        } else {
            // User need to be the user requested or an admin
            throw $this->createAccessDeniedException('Unable to access this page!');
        }
    }

    /**
     * @Rest\Delete("/users/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Remove a user",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The user unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("has_role('IS_AUTHENTICATED_FULLY')")
     */
    public function removeUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($request->get('id'));
        /* @var $user User */

        if($user AND ($this->get('security.token_storage')->getToken()->getUser() == $user OR $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))) {
            $em->remove($user);
            $em->flush();
        } else {
            // User need to be the user requested or an admin
            throw $this->createAccessDeniedException('Unable to access this page!');
        }
    }
}
