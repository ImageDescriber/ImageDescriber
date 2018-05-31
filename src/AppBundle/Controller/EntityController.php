<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entity;

use AppBundle\Form\EntityType;
use AppBundle\Repository\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class EntityController extends FOSRestController
{
    /**
     * @Rest\Get("/entities")
     *
     * @QueryParam(name="qwd", requirements="\d+", default="", description="Identifier of Wikidata element")
     * @QueryParam(name="random", requirements="false|true", default="false", description="Return a random item")
     * @QueryParam(name="keyword", description="Looks for specific items")
     * @QueryParam(name="depicts", requirements="false|true", default="false", description="Return the list of the entities with depicts")
     * @QueryParam(name="count", requirements="false|true", default="false", description="Return the number of results")
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Get the list of all entities",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getEntitiesAction(Request $request, ParamFetcher $paramFetcher)
    {
        $qwd = $paramFetcher->get('qwd');
        $random = $paramFetcher->get('random');
        $keyword = $paramFetcher->get('keyword');
        $depicts = $paramFetcher->get('depicts');
        $count = $paramFetcher->get('count');
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Entity');

        if($qwd != "" or $random == "true") {
            /* @var $repository EntityRepository */
            /* @var $entities Entity */
            if($qwd != "") {
                $response = $repository->findOneBy(array("qwd" =>$qwd));
            } elseif ($random == "true") {
                $response = $repository->find(rand(0, $repository->countEntities() - 1));
            }
        } else {
            $entities = $repository->findBy(array(), array(), 100);

            // Manage depict
            // Manage keyword

            if($count == "true") {
                $response = count($entities);
            } else {
                $response = $entities;
            }
        }


        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "entity"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($response, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Get("/entities/{id}")
     * @Rest\View
     *
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Return one entity",
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
    public function getEntityAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Entity')->find($request->get('id'));
        /* @var $entity Entity */

        if (empty($entity)) {
            return new JsonResponse(['message' => 'Entity not found'], Response::HTTP_NOT_FOUND);
        }

        return $entity;
    }

    /**
     * @Rest\Post("/entities")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Create a new entity",
     *     requirements={
     *         {
     *             "name"="qwd",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The identifier of the painting."
     *         },
     *         {
     *             "name"="image",
     *             "dataType"="url",
     *             "requirement"="",
     *             "description"="Url of the image of a painting."
     *         },
     *         {
     *             "name"="listDepicts",
     *             "dataType"="array",
     *             "requirement"="",
     *             "description"="List of the depicts of a painting."
     *         }
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function postEntityAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new Entity();
        $form = $this->createForm(EntityType::class, $entity);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();
            return $entity;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/entities/{id}")
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Update an existing entity",
     *     requirements={
     *         {
     *             "name"="qwd",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The identifier of the painting."
     *         },
     *         {
     *             "name"="image",
     *             "dataType"="url",
     *             "requirement"="",
     *             "description"="Url of the image of a painting."
     *         },
     *         {
     *             "name"="listDepicts",
     *             "dataType"="array",
     *             "requirement"="",
     *             "description"="List of the depicts of a painting."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function updateEntityAction(Request $request)
    {
        return $this->updateEntity($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/entities/{id}")
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Update an existing entity",
     *     requirements={
     *         {
     *             "name"="qwd",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The identifier of the painting."
     *         },
     *         {
     *             "name"="image",
     *             "dataType"="url",
     *             "requirement"="",
     *             "description"="Url of the image of a painting."
     *         },
     *         {
     *             "name"="listDepicts",
     *             "dataType"="array",
     *             "requirement"="",
     *             "description"="List of the depicts of a painting."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function patchEntityAction(Request $request)
    {
        return $this->updateEntity($request, false);
    }

    private function updateEntity(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Entity')->find($request->get('id'));
        /* @var $entity Entity */
        if (empty($entity)) {return new JsonResponse(['message' => 'Entity not found'], Response::HTTP_NOT_FOUND);}

        $form = $this->createForm(EntityType::class, $entity);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();
            return $entity;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/entities/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Remove an entity",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The unique identifier of the entity.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function removeEntityAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Entity')->find($request->get('id'));
        /* @var $entity Entity */

        if ($entity) {
            $em->remove($entity);
            $em->flush();
        }
    }
}
