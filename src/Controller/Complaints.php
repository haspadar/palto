<?php

namespace Palto\Controller;

use Palto\Ads;
use Palto\Debug;
use Palto\Filter;
use Palto\Flash;

class Complaints extends Controller
{
    public function ignoreComplaint(): array
    {
        $id = intval($this->getDispatcher()->getRouter()->getQueryParameter('id'));
        if ($id) {
            \Palto\Complaints::ignoreComplaint($id);
            $complaint = \Palto\Complaints::getComplaint($id);
            $ad = Ads::getById($complaint['ad_id']);
            Flash::add(json_encode([
                'message' => 'Жалоба #' . $id . ' на <a href="' . $ad->generateUrl() . '" target="_blank">объявление</a> проигнорировано',
                'type' => 'success'
            ]));

            return ['success' => true];
        }

        return ['error' => 'Не указан ID'];
    }

    public function ignoreComplaints(): array
    {
        $idsString = Filter::get($this->getDispatcher()->getPutParameter('ids'));
        $ids = Filter::getIntArray(explode(',', $idsString));
        if ($ids) {
            \Palto\Complaints::ignoreComplaints($ids);
            Flash::add(json_encode([
                'message' => 'Жалобы ##' . $idsString . ' проигнорированы',
                'type' => 'success'
            ]));

            return ['success' => true];
        }

        return ['error' => 'Не указан ID'];
    }

    public function removeAd(): array
    {
        $id = intval($this->getDispatcher()->getRouter()->getQueryParameter('id'));
        if ($id) {
            try {
                \Palto\Complaints::removeAd($id);
                $complaint = \Palto\Complaints::getComplaint($id);
                $ad = Ads::getById($complaint['ad_id']);
                Flash::add(json_encode([
                    'message' => '<a href="' . $ad->generateUrl() . '" target="_blank">Объявление</a> с жалобой #' . $id . ' удалено',
                    'type' => 'success'
                ]));
                return ['success' => true];
            } catch (\Exception $e) {
                return ['error' => $e->getTraceAsString()];
            }
        }

        return ['error' => 'Не указан ID'];
    }

    public function removeAds(): array
    {
        $idsString = Filter::get($this->getDispatcher()->getPutParameter('ids'));
        $ids = Filter::getIntArray(explode(',', $idsString));
        if ($ids) {
            \Palto\Complaints::removeAds($ids);
            Flash::add(json_encode([
                'message' => 'Объявления с жалобами ##' . $idsString . ' удалены',
                'type' => 'success'
            ]));

            return ['success' => true];
        }

        return ['error' => 'Не указан ID'];
    }
}