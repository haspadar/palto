<?php

namespace Palto\Model;

class NestedSet extends Tree
{
    public function rebuildTree()
    {
        $this->rebuild(
            self::getDb()->queryFirstRow('SELECT * FROM ' . $this->name . ' WHERE parent_id IS NULL'),
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

        $children = self::getDb()->query('SELECT * FROM ' . $this->name . ' WHERE parent_id = %d', $node['id']);
        foreach ($children as $child) {
            $this->rebuild($child, $iterator);
        }

        $this->update(['right_id' => ++$iterator->value], $node['id']);
    }
}