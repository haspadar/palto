<?php

namespace Palto\Model;

use Palto\Debug;

class NestedSet extends Tree
{
    public function addAd(array $node)
    {
        $this->updateAdsCounts(1, $node);
    }

    public function removeAd(array $node)
    {
        $this->updateAdsCounts(-1, $node);
    }

    public function rebuildTree()
    {
        $this->rebuild(
            ['id' => 0, 'level' => 0, 'parent_id' => null],
            (object)['value' => 0, 'level' => 0]
        );
    }

    private function rebuild(array $node, \stdClass $iterator)
    {
        if ($iterator->level <= $node['level']) {
            $this->update(['left_id' => ++$iterator->value], $node['id']);
        } else {
            $this->update(['right_id' => ++$iterator->value, 'left_id' => ++$iterator->value], $node['id']);
        }

        $children = self::getDb()->query('SELECT * FROM ' . $this->name . ' WHERE parent_id ' . ($node['id'] ? '= %d' : ' IS NULL'), $node['id']);
        foreach ($children as $child) {
            $this->rebuild($child, $iterator);
        }

        $this->update(['right_id' => ++$iterator->value], $node['id']);
    }

    protected function updateAdsCounts(int $adsCount, array $node)
    {
        self::getDb()->debugMode();
        while ($node) {
            self::getDb()->query("UPDATE " . $this->name . " SET ads_count = ads_count + (%d) WHERE id = %d", $adsCount, $node['id']);
            $node = $node['parent_id']
                ? self::getDb()->queryFirstRow("SELECT id,title,parent_id FROM " . $this->name . " WHERE id=%d", $node['parent_id'])
                : [];
        }
    }
}