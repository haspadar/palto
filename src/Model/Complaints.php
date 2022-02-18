<?php

namespace Palto\Model;

class Complaints extends Model
{
    public static function add(array $complaint)
    {
        self::getConnection()->insert('complaints', $complaint);
    }

    public static function getActualComplaintsCount()
    {
        return self::getConnection()->queryFirstField("SELECT COUNT(*) FROM complaints WHERE response_time IS NULL AND ignore_time IS NULL");
    }

    public static function getActualComplaints()
    {
        return self::getConnection()->query("SELECT * FROM complaints WHERE response_time IS NULL AND ignore_time IS NULL");
    }

    public static function getComplaint(int $id)
    {
        return self::getConnection()->queryFirstRow('SELECT * FROM complaints WHERE id=%d', $id);
    }

    public static function updateResponseTime(array $ids)
    {
        self::getConnection()->update('complaints', [
            'response_time' => (new \DateTime())->format('Y-m-d H:i:s')
        ], "id IN %ld", $ids);
    }

    public static function updateIgnoreTime(array $ids)
    {
        self::getConnection()->update('complaints', [
            'ignore_time' => (new \DateTime())->format('Y-m-d H:i:s')
        ], "id IN %ld", $ids);
    }
}