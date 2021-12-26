<?php
namespace Palto;

use Palto\Model\Complaints;

class Moderation
{
    public static function ignoreComplaint(int $id)
    {
        Complaints::updateIgnoreTime($id);
    }

    public static function sendRemovedComplaintMail(int $id)
    {
        $complaint = self::getComplaint($id);
        $subject = 'Your ad was removed';
        $body = 'Your <a target="_blank" href="'
            . $complaint['domain']
            . $complaint['page']
            . '">ad</a> was removed.<br><br>Your report: : "'
            . $complaint['message']
            . '"';
        Email::send($complaint['email'], $subject, $body);
        Complaints::updateResponseTime($id);
    }

    public static function getComplaint($id)
    {
        return Complaints::getComplaint($id);
    }
    
    public static function removeComplaintUser($id)
    {
        $complaint = self::getComplaint($id);
        if ($complaint['ad_id']) {
            Ads::markAsDelete($complaint['ad_id']);
        }
    }

    public static function getActualComplaints(): array
    {
        return Complaints::getActualComplaints();
    }

    public static function getSmtpEmail(): string
    {
        return Config::get('SMTP_EMAIL');
    }

    public static function addComplaint(array $complaint)
    {
        $subject = 'Пришла жалоба';
        $body = 'Текст жалобы: "'. $complaint['message'] . '"<br><a target="_blank" href="'
            . $complaint['domain']
            . '/moderate/'
            . '">Зайти в админку</a>';

        Email::send(Config::get('SMTP_EMAIL'), $subject, $body);
        Complaints::add($complaint);
    }
}