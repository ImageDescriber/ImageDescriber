<?php

namespace UserBundle\Controller;

use UserBundle\Entity\Preference;

use UserBundle\Form\PreferenceType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Nelmio\ApiDocBundle\Annotation as Doc;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use UserBundle\Repository\UserRepository;

class PreferenceController extends FOSRestController
{
    /**
     * @Rest\Get("/preferences")
     * @QueryParam(name="user", nullable=true, description="User is required")
     *
     * @Rest\View()
     *
     * @Doc\ApiDoc(
     *     section="Preferences",
     *     resource=true,
     *     description="Get the list of all preferences",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getPreferencesAction(Request $request, ParamFetcher $paramFetcher)
    {
        $user = $paramFetcher->get('user');

        $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:Preference');
        /* @var $repository UserRepository */
        if($user != "") {
            $preference = $repository->findOneBy(array("user" => $user));
            /* @var $preference Preference */
        } else {
            $preference = null;
        }
        return $preference;
    }

    /**
     * @Rest\Get("/preferences/{id}")
     * @Rest\View()
     * @Doc\ApiDoc(
     *     section="Preferences",
     *     resource=true,
     *     description="Return one preference",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The preference unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getPreferenceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $preference = $em->getRepository('UserBundle:Preference')->find($request->get('id'));
        /* @var $preference Preference */

        if (empty($preference)) {
            return new JsonResponse(['message' => 'Preference not found'], Response::HTTP_NOT_FOUND);
        }

        return $preference;
    }

    /**
     * @Rest\Post("/preferences")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="Preferences",
     *     resource=true,
     *     description="Create a new preference",
     *     requirements={
     *         {
     *             "name"="title",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the preference, can be a concatenation of the preference number and the testator."
     *         },
     *         {
     *             "name"="number",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The number (cote) of the preference."
     *         },
     *         {
     *             "name"="minute_date",
     *             "dataType"="date",
     *             "requirement"="Date",
     *             "description"="The date of the minute."
     *         },
     *         {
     *             "name"="preference_writing_date",
     *             "dataType"="date",
     *             "requirement"="Date",
     *             "description"="The writing date of the preference."
     *         },
     *         {
     *             "name"="preference_writing_place",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The writing place of the preference."
     *         },
     *         {
     *             "name"="testator",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The writing place of the preference."
     *         }
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function postPreferencesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $preference = new Preference();
        $form = $this->createForm(PreferenceType::class, $preference);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($preference);
            $em->flush();
            return $preference;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/preferences/{id}")
     * @Doc\ApiDoc(
     *     section="Preferences",
     *     resource=true,
     *     description="Update an existing preference",
     *     requirements={
     *         {
     *             "name"="title",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the preference, can be a concatenation of the preference number and the testator."
     *         },
     *         {
     *             "name"="number",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The number (cote) of the preference."
     *         },
     *         {
     *             "name"="minute_date",
     *             "dataType"="date",
     *             "requirement"="Date",
     *             "description"="The date of the minute."
     *         },
     *         {
     *             "name"="preference_writing_date",
     *             "dataType"="date",
     *             "requirement"="Date",
     *             "description"="The writing date of the preference."
     *         },
     *         {
     *             "name"="preference_writing_place",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The writing place of the preference."
     *         },
     *         {
     *             "name"="testator",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The writing place of the preference."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function updatePreferenceAction(Request $request)
    {
        return $this->updatePreference($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/preferences/{id}")
     * @Doc\ApiDoc(
     *     section="Preferences",
     *     resource=true,
     *     description="Update an existing preference",
     *     requirements={
     *         {
     *             "name"="title",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The title of the preference, can be a concatenation of the preference number and the testator."
     *         },
     *         {
     *             "name"="number",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The number (cote) of the preference."
     *         },
     *         {
     *             "name"="minute_date",
     *             "dataType"="date",
     *             "requirement"="Date",
     *             "description"="The date of the minute."
     *         },
     *         {
     *             "name"="preference_writing_date",
     *             "dataType"="date",
     *             "requirement"="Date",
     *             "description"="The writing date of the preference."
     *         },
     *         {
     *             "name"="preference_writing_place",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The writing place of the preference."
     *         },
     *         {
     *             "name"="testator",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The writing place of the preference."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function patchPreferenceAction(Request $request)
    {
        return $this->updatePreference($request, false);
    }

    private function updatePreference(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $preference = $em->getRepository('UserBundle:Preference')
            ->find($request->get('id'));
        /* @var $preference Preference */
        if (empty($preference)) {
            return new JsonResponse(['message' => 'Preference not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(PreferenceType::class, $preference);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($preference);
            $em->flush();
            return $preference;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/preferences/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="Preferences",
     *     resource=true,
     *     description="Remove a preference",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The preference unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function removePreferenceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $preference = $em->getRepository('UserBundle:Preference')->find($request->get('id'));
        /* @var $preference Preference */

        if ($preference) {
            $em->remove($preference);
            $em->flush();
        }
    }
}
