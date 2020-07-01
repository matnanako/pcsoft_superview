<?php

namespace SuperView\Dal\Api;

/**
 * Comment Dal.
 */
class Comment extends Base
{

    public function getComment($id, $limit, $order)
    {
        $params = [
            'id' => intval($id),
            'limit' => intval($limit),
            'order' => $order,
        ];
        return $this->getData('comment', $params);
    }
}