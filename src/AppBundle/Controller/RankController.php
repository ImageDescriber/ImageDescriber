<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entity;
use AppBundle\Entity\Rank;

use AppBundle\Form\RankType;
use AppBundle\Repository\EntityRepository;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;

class RankController extends FOSRestController
{
    /**
     * @Rest\Get("/ranks")
     * @Rest\View()
     *
     * @Doc\ApiDoc(
     *     section="Ranks",
     *     resource=true,
     *     description="Get the list of all ranks",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getRanksAction(Request $request, ParamFetcher $paramFetcher)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Rank');
        /* @var $repository EntityRepository */
        $ranks = $repository->findAll();
        /* @var $ranks Rank[] */
        return $ranks;
    }

    /**
     * @Rest\Get("/ranks/{id}")
     * @Rest\View
     *
     * @Doc\ApiDoc(
     *     section="Ranks",
     *     resource=true,
     *     description="Return one rank",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The unique identifier of the entity.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getRankAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rank = $em->getRepository('AppBundle:Rank')->find($request->get('id'));
        /* @var $rank Rank */

        if (empty($rank)) {
            return new JsonResponse(['message' => 'Rank not found'], Response::HTTP_NOT_FOUND);
        }

        return $rank;
    }

    /**
     * @Rest\Post("/ranks")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="Ranks",
     *     resource=true,
     *     description="Create a new rank entry",
     *     requirements={
     *         {
     *             "name"="value",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The value of the rank."
     *         }
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function postRankAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $rank = new Rank();
        $form = $this->createForm(RankType::class, $rank);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($rank);
            $em->flush();
            return $rank;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/ranks/{id}")
     * @Doc\ApiDoc(
     *     section="Ranks",
     *     resource=true,
     *     description="Update an existing rank",
     *     requirements={
     *         {
     *             "name"="value",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The value of the rank."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function updateRankAction(Request $request)
    {
        return $this->updateRank($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/ranks/{id}")
     * @Doc\ApiDoc(
     *     section="Ranks",
     *     resource=true,
     *     description="Update an existing rank",
     *     requirements={
     *         {
     *             "name"="value",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The value of the rank."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function patchRankAction(Request $request)
    {
        return $this->updateRank($request, false);
    }

    private function updateRank(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $rank = $em->getRepository('AppBundle:Rank')->find($request->get('id'));
        /* @var $rank Rank */
        if (empty($rank)) {return new JsonResponse(['message' => 'Rank not found'], Response::HTTP_NOT_FOUND);}

        $form = $this->createForm(RankType::class, $rank);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($rank);
            $em->flush();
            return $rank;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/ranks/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="Rank",
     *     resource=true,
     *     description="Remove a rank",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The unique identifier of the rank.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function removeRankAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rank = $em->getRepository('AppBundle:Rank')->find($request->get('id'));
        /* @var $rank Rank */

        if ($rank) {
            $em->remove($rank);
            $em->flush();
        }
    }
}
