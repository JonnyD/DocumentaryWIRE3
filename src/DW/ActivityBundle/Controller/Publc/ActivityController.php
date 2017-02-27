<?php

namespace DW\ActivityBundle\Controller\Publc;

use DW\ActivityBundle\Criteria\ActivityCriteria;
use DW\ActivityBundle\Enum\OrderBy;
use DW\ActivityBundle\Service\ActivityService;
use DW\BaseBundle\Enum\Order;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $criteria = new ActivityCriteria();
        $criteria->setSort([
            OrderBy::CREATED_AT => Order::ASC
        ]);

        $activityService = $this->getActivityService();
        $activity = $activityService->getAllActivityByCriteria($criteria);

        $groupNumber = 1;
        $previousType = $activity[0]->getType();
        $previousUserId = $activity[0]->getUser()->getId();
        $activity[0]->setGroupNumber($groupNumber);
        $activityService->save($activity[0], false);

        $count = 1;
        foreach ($activity as $act) {
            $increment = true;

            $type = $act->getType();
            $userId = $act->getUser()->getId();

            if ($type == 'joined') {
                if ($previousType == 'joined') {
                    if ($count == 20) {
                        $increment = true;
                        $count = 1;
                    } else {
                        $increment = false;
                        $count++;
                    }
                } else {
                    $increment = true;
                }
            }

            if ($type == 'comment') {
                $increment = true;
            }

            if ($type == 'like') {
                if ($previousType == 'like' && $previousUserId == $userId) {
                    $increment = false;
                } else {
                    $increment = true;
                }
            }

            $previousType = $type;
            $previousUserId = $userId;

            if ($increment) {
                $groupNumber++;
                $act->setGroupNumber($groupNumber);
            } else {
                $act->setGroupNumber($groupNumber);
            }

            $activityService->save($act, false);
        }

        $activityService->flush();

        var_dump($activity); die();


        return $this->render('ActivityBundle:Publc:list.html.twig', [
            'activity' => $activity
        ]);
    }

    /**
     * @return ActivityService
     */
    private function getActivityService()
    {
        return $this->get('dw.activity_service');
    }
}