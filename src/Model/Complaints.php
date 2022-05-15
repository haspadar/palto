<?php

namespace Palto\Model;

class Complaints extends Model
{
    protected string $name = 'complaints';

    public function getActualComplaintsCount()
    {
        return self::getDb()->queryFirstField("SELECT COUNT(*) FROM " . $this->name . " WHERE response_time IS NULL AND ignore_time IS NULL");
    }

    public function getActualComplaints()
    {
        return self::getDb()->query("SELECT * FROM " . $this->name . " WHERE response_time IS NULL AND ignore_time IS NULL");
    }

    public function getComplaint(int $id)
    {
        return self::getDb()->queryFirstRow('SELECT * FROM ' . $this->name . ' WHERE id=%d', $id);
    }

    public function updateResponseTime(array $ids)
    {
        self::getDb()->update($this->name, [
            'response_time' => (new \DateTime())->format('Y-m-d H:i:s')
        ], "id IN %ld", $ids);
    }

    public function updateIgnoreTime(array $ids)
    {
        self::getDb()->update($this->name, [
            'ignore_time' => (new \DateTime())->format('Y-m-d H:i:s')
        ], "id IN %ld", $ids);
    }
}