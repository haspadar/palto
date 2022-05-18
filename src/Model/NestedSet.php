<?php

namespace Palto\Model;

class NestedSet extends Tree
{
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
}