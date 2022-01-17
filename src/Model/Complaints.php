<?php

namespace Palto\Model;

class Complaints extends Model
{
    public static function add(array $complaint)
    {
        self::getDb()->insert('complaints', $complaint);
    }

    public static function getActualComplaints()
    {
        return self::getDb()->query("SELECT * FROM complaints WHERE response_time IS NULL AND ignore_time IS NULL");
    }

    public static function getComplaint(int $id)
    {
        return self::getDb()->queryFirstRow('SELECT * FROM complaints WHERE id=%d', $id);
    }

    public static function updateResponseTime(array $ids)
    {
        self::getDb()->update('complaints', [
            'response_time' => (new \DateTime())->format('Y-m-d H:i:s')
        ], "id IN %ld", $ids);
    }

    public static function updateIgnoreTime(array $ids)
    {
        self::getDb()->update('complaints', [
            'ignore_time' => (new \DateTime())->format('Y-m-d H:i:s')
        ], "id IN %ld", $ids);
    }
}