<?php
namespace Palto;

class Moderation
{
    public static function ignoreComplaint(\MeekroDB $db, int $id) 
    {
        $db->update('complaints', [
            'ignore_time' => (new \DateTime())->format('Y-m-d H:i:s')
        ], "id = %d", $id);
    }

    public static function sendRemovedComplaintMail(Palto $palto, int $id)
    {
        $complaint = self::getComplaint($palto->getDb(), $id);
        $subject = 'Your ad was removed';
        $body = 'Your <a target="_blank" href="'
            . $complaint['domain']
            . $complaint['page']
            . '">ad</a> was removed.<br><br>Your report: : "'
            . $complaint['message']
            . '"';
        $palto->sendEmail($complaint['email'], $subject, $body);
        $palto->getDb()->update('complaints', [
            'response_time' => (new \DateTime())->format('Y-m-d H:i:s')
        ], "id = %d", $id);
    }

    public static function getComplaint(\MeekroDB $db, $id) 
    {
        return $db->queryFirstRow('SELECT * FROM complaints WHERE id=%d', $id);
    }
    
    public static function removeComplaintUser(\MeekroDB $db, $id) 
    {
        $complaint = self::getComplaint($db, $id);
        if ($complaint['ad_id']) {
            $db->update('ads', [
                'deleted_time' => (new \DateTime())->format('Y-m-d H:i:s')
            ], "id = %d", $complaint['ad_id']);
        }
    }

    public static function getActualComplaints(\MeekroDB $db): array
    {
        return $db->query("SELECT * FROM complaints WHERE response_time IS NULL AND ignore_time IS NULL");
    }

    public static function getSmtpEmail(Palto $palto): string
    {
        return $palto->getEnv()['SMTP_EMAIL'];
    }

    public static function addComplaint(Palto $palto, array $complaint)
    {
        $subject = 'Пришла жалоба';
        $body = 'Текст жалобы: "'. $complaint['message'] . '"<br><a target="_blank" href="'
            . $complaint['domain']
            . '/moderate/'
            . '">Зайти в админку</a>';
        $palto->sendEmail(self::getSmtpEmail(), $subject, $body);
        $palto->getDb()->insert('complaints', $complaint);
    }

    private static function getIp(): string
    {
        return isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']
            ? $_SERVER['HTTP_X_FORWARDED_FOR']
            : (isset($_SERVER['REMOTE_ADDR'])
                ? $_SERVER['REMOTE_ADDR']
                : ''
            );
    }
}