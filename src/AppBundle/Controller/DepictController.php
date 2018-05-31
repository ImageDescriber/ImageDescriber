<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entity;
use AppBundle\Entity\Depict;

use AppBundle\Form\DepictType;
use AppBundle\Repository\DepictRepository;
use AppBundle\Repository\EntityRepository;
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

class DepictController extends FOSRestController
{
    /**
     * @Rest\Get("/depicts")
     *
     * @QueryParam(name="qwdDepict", requirements="\d+", default="", description="Identifier of Wikidata element")
     * @QueryParam(name="qwdEntity", requirements="\d+", default="", description="Identifier of Wikidata element")
     * @QueryParam(name="profile",  nullable=true, description="Search profile to apply")
     *
     * @Doc\ApiDoc(
     *     section="Depicts",
     *     resource=true,
     *     description="Get the list of all depicts",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getDepictsAction(Request $request, ParamFetcher $paramFetcher)
    {
        /* @var $repositoryDepict DepictRepository */
        /* @var $repositoryEntity EntityRepository */

        $qwdDepict = $paramFetcher->get('qwdDepict');
        $qwdEntity = $paramFetcher->get('qwdEntity');

        $repositoryDepict = $this->getDoctrine()->getManager()->getRepository('AppBundle:Depict');
        $repositoryEntity = $this->getDoctrine()->getManager()->getRepository('AppBundle:Entity');

        $query = [];

        if($qwdDepict != "") {
            $query["qwd"] = $qwdDepict;
        }
        if($qwdEntity != "") {
            $entity = $repositoryEntity->findOneBy(array("qwd" => $qwdEntity));
            if($entity != null) {
                $query["entity"] = $entity['id'];
            }
        }

        $depicts = $repositoryDepict->findBy($query, array('createDate' => 'DESC'));
        /* @var $depicts Depict[] */


        if($paramFetcher->get('profile') == '') {
            $profile = ["id", "depict"];
        } else {
            $profile = explode(',', $paramFetcher->get('profile'));
        }

        return new JsonResponse(json_decode($this->get('jms_serializer')->serialize($depicts, 'json', SerializationContext::create()->enableMaxDepthChecks()->setGroups($profile))));
    }

    /**
     * @Rest\Get("/depicts/{id}")
     * @Rest\View
     *
     * @Doc\ApiDoc(
     *     section="Depicts",
     *     resource=true,
     *     description="Return one depict",
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
    public function getDepictAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $depict = $em->getRepository('AppBundle:Depict')->find($request->get('id'));
        /* @var $depict Depict */

        if (empty($depict)) {
            return new JsonResponse(['message' => 'Depict not found'], Response::HTTP_NOT_FOUND);
        }

        return $depict;
    }

    /**
     * @Rest\Post("/depicts")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="Depicts",
     *     resource=true,
     *     description="Create a new depict entry",
     *     requirements={
     *         {
     *             "name"="entity",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The identifier of the entity."
     *         },
     *         {
     *             "name"="status",
     *             "dataType"="string",
     *             "requirement"="",
     *             "description"="Status of the entity"
     *         },
     *         {
     *             "name"="ipUser",
     *             "dataType"="string",
     *             "requirement"="",
     *             "description"="IP of the user sending the request"
     *         },
     *         {
     *             "name"="HTTP_USER_AGENT",
     *             "dataType"="text",
     *             "requirement"="",
     *             "description"="User agent of the request"
     *         }
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function postDepictAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $depict = new Depict();
        $form = $this->createForm(DepictType::class, $depict);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($depict);
            $em->flush();
            return $depict;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/depicts/{id}")
     * @Doc\ApiDoc(
     *     section="Depicts",
     *     resource=true,
     *     description="Update an existing depict",
     *     requirements={
     *         {
     *             "name"="entity",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The identifier of the entity."
     *         },
     *         {
     *             "name"="status",
     *             "dataType"="string",
     *             "requirement"="",
     *             "description"="Status of the entity"
     *         },
     *         {
     *             "name"="ipUser",
     *             "dataType"="string",
     *             "requirement"="",
     *             "description"="IP of the user sending the request"
     *         },
     *         {
     *             "name"="HTTP_USER_AGENT",
     *             "dataType"="text",
     *             "requirement"="",
     *             "description"="User agent of the request"
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function updateDepictAction(Request $request)
    {
        return $this->updateDepict($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/depicts/{id}")
     * @Doc\ApiDoc(
     *     section="Depicts",
     *     resource=true,
     *     description="Update an existing depict",
     *     requirements={
     *         {
     *             "name"="entity",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The identifier of the entity."
     *         },
     *         {
     *             "name"="status",
     *             "dataType"="string",
     *             "requirement"="",
     *             "description"="Status of the entity"
     *         },
     *         {
     *             "name"="ipUser",
     *             "dataType"="string",
     *             "requirement"="",
     *             "description"="IP of the user sending the request"
     *         },
     *         {
     *             "name"="HTTP_USER_AGENT",
     *             "dataType"="text",
     *             "requirement"="",
     *             "description"="User agent of the request"
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function patchDepictAction(Request $request)
    {
        return $this->updateDepict($request, false);
    }

    private function updateDepict(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $depict = $em->getRepository('AppBundle:Depict')->find($request->get('id'));
        /* @var $depict Depict */
        if (empty($depict)) {return new JsonResponse(['message' => 'Depict not found'], Response::HTTP_NOT_FOUND);}

        $form = $this->createForm(DepictType::class, $depict);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($depict);
            $em->flush();
            return $depict;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/depicts/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="Depict",
     *     resource=true,
     *     description="Remove a depict",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The unique identifier of the depict.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function removeDepictAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $depict = $em->getRepository('AppBundle:Depict')->find($request->get('id'));
        /* @var $depict Depict */

        if ($depict) {
            $em->remove($depict);
            $em->flush();
        }
    }
}
