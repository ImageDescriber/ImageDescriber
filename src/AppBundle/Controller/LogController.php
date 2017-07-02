<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entity;
use AppBundle\Entity\Log;

use AppBundle\Form\LogType;
use AppBundle\Repository\EntityRepository;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;

class LogController extends FOSRestController
{
    /**
     * @Rest\Get("/logs")
     * @Rest\View()
     *
     * @QueryParam(name="qwd", requirements="\d+", default="", description="Identifier of Wikidata element")
     *
     * @Doc\ApiDoc(
     *     section="Logs",
     *     resource=true,
     *     description="Get the list of all logs",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getLogsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $qwd = $paramFetcher->get('qwd');

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Log');
        /* @var $repository EntityRepository */
        if($qwd != "") {
            $entity = $this->getDoctrine()->getManager()->getRepository('AppBundle:Entity')->findOneByQwd($qwd);
            $logs = $repository->findBy(array("entity" => $entity));
        } else {
            $logs = $repository->findAll();
        }

        /* @var $logs Log[] */
        return $logs;
    }

    /**
     * @Rest\Get("/logs/{id}")
     * @Rest\View
     *
     * @Doc\ApiDoc(
     *     section="Logs",
     *     resource=true,
     *     description="Return one log",
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
    public function getLogAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $log = $em->getRepository('AppBundle:Log')->find($request->get('id'));
        /* @var $log Log */

        if (empty($log)) {
            return new JsonResponse(['message' => 'Log not found'], Response::HTTP_NOT_FOUND);
        }

        return $log;
    }

    /**
     * @Rest\Post("/logs")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="Logs",
     *     resource=true,
     *     description="Create a new log entry",
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
    public function postLogAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $log = new Log();
        $form = $this->createForm(LogType::class, $log);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($log);
            $em->flush();
            return $log;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/logs/{id}")
     * @Doc\ApiDoc(
     *     section="Logs",
     *     resource=true,
     *     description="Update an existing log",
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
    public function updateLogAction(Request $request)
    {
        return $this->updateLog($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/logs/{id}")
     * @Doc\ApiDoc(
     *     section="Logs",
     *     resource=true,
     *     description="Update an existing log",
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
    public function patchLogAction(Request $request)
    {
        return $this->updateLog($request, false);
    }

    private function updateLog(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $log = $em->getRepository('AppBundle:Log')->find($request->get('id'));
        /* @var $log Log */
        if (empty($log)) {return new JsonResponse(['message' => 'Log not found'], Response::HTTP_NOT_FOUND);}

        $form = $this->createForm(LogType::class, $log);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($log);
            $em->flush();
            return $log;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/logs/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="Log",
     *     resource=true,
     *     description="Remove a log",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The unique identifier of the log.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function removeLogAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $log = $em->getRepository('AppBundle:Log')->find($request->get('id'));
        /* @var $log Log */

        if ($log) {
            $em->remove($log);
            $em->flush();
        }
    }
}
