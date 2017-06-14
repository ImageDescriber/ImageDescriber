<?php

namespace DataBundle\Controller;

use DataBundle\Entity\Entity;
use DataBundle\Exception\NoContentException;
use DataBundle\Exception\ResourceValidationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Nelmio\ApiDocBundle\Annotation as Doc;

class EntityController extends FOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/entities/qwd/{qwd}",
     *     name = "data_entity_qwd",
     *     requirements = {"qwd"="\d+"}
     * )
     * @Rest\View(statusCode = 200)
     * @Doc\ApiDoc(
     *     section="Entities",
     *     resource=true,
     *     description="Return one entity",
     *     requirements={
     *         {
     *             "name"="qwd",
     *             "dataType"="string",
     *             "requirement"="\d+",
     *             "description"="The identifier of wikidata",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         204="Returned when the content doesn't exist",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     *
     */
    public function qwdAction($qwd)
    {
        // ParamFetcher params can be dynamically added during runtime instead of only compile time annotations.
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository("DataBundle:Entity")->findOneByQwd($qwd);

        if($entity == null) {
            //throw new NoContentException("This content does not exist");
            return new JsonResponse(null);
        } else {
            return $entity;
        }
    }

    /**
     * @Rest\Get(
     *     path = "/entities/{id}",
     *     name = "data_entity_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(statusCode = 200)
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
    public function showAction(Entity $entity)
    {
        return $entity;
    }

    /**
     * @Rest\Get(
     *    path = "/entities",
     *    name = "data_entity_list"
     * )
     * @Rest\View(StatusCode = 200)
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
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository("DataBundle:Entity")->findAll();
    }

    /**
     * @Rest\Post(
     *    path = "/entities",
     *    name = "data_entity_create"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("entity", converter="fos_rest.request_body")
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
     */
    public function createAction(Entity $entity)
    {
        //dump($entity); die;
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return $this->view($entity, Response::HTTP_CREATED, ['Location' => $this->generateUrl('data_entity_show', ['id' => $entity->getId(), UrlGeneratorInterface::ABSOLUTE_URL])]);
    }

    /**
     * @Rest\Put(
     *    path = "/entities/{id}",
     *    name = "data_entity_update",
     *    requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 200)
     * @ParamConverter("newEntity", converter="fos_rest.request_body")
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
     */
    public function updateAction(Entity $entity, Entity $newEntity)
    {
        $entity->setQwd($newEntity->getQwd());
        $entity->setListDepicts($newEntity->getListDepicts());

        $this->getDoctrine()->getManager()->flush();

        return $entity;
    }

    /**
     * @Rest\Delete(
     *     path = "/entities/{id}",
     *     name = "data_entity_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(statusCode = 204)
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
     */
    public function deleteAction(Entity $entity)
    {
        $this->getDoctrine()->getManager()->remove($entity);
        $this->getDoctrine()->getManager()->flush();

        return;
    }
}
