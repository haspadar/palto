<?php
namespace Palto;

use function Symfony\Component\String\b;

class Complaints
{
    public static function ignoreComplaints(array $ids)
    {
        (new Model\Complaints)->updateIgnoreTime($ids);
    }

    public static function ignoreComplaint(int $id)
    {
        (new Model\Complaints)->updateIgnoreTime([$id]);
    }

    public static function getComplaint($id)
    {
        return (new Model\Complaints)->getComplaint($id);
    }
    
    public static function removeAd($id)
    {
        $complaint = self::getComplaint($id);
        if ($complaint['ad_id']) {
            Ads::markAsDelete($complaint['ad_id']);
            self::sendUserMail($id);
        }

        (new Model\Complaints)->updateResponseTime([$id]);
    }

    public static function removeAds(array $ids)
    {
        foreach ($ids as $id) {
            $complaint = self::getComplaint($id);
            if ($complaint['ad_id']) {
                Ads::markAsDelete($complaint['ad_id']);
                self::sendUserMail($id);
            }
        }

        (new Model\Complaints)->updateResponseTime($ids);
    }

    public static function getActualComplaintsCount(): int
    {
        return (new Model\Complaints)->getActualComplaintsCount();
    }

    public static function getActualComplaints(): array
    {
        return (new Model\Complaints)->getActualComplaints();
    }

    public static function getSmtpEmail(): string
    {
        return Config::get('SMTP_EMAIL');
    }

    public static function addComplaint(array $complaint)
    {
        (new Model\Complaints)->add($complaint);
//        $subject = 'Пришла жалоба';
//        $body = 'Текст жалобы: "'. $complaint['message'] . '"<br><a target="_blank" href="'
//            . $complaint['domain']
//            . '/karman/complaints?cache=0'
//            . '">Зайти в админку</a>';
//
//        Email::send(Config::get('SMTP_EMAIL'), $subject, $body);
    }

    private static function sendUserMail(int $id)
    {
        $complaint = self::getComplaint($id);
        $subject = 'Your ad was removed';
        $body = sprintf(
            'Your <a target="_blank" href="%s">ad</a> was removed.<br><br>Your report: \"%s\".',
            $complaint['domain'] . $complaint['page'],
            $complaint['message']
        );
        Email::send($complaint['email'], $subject, $body);
    }
}