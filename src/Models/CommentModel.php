<?php

namespace SuperView\Models;

class CommentModel extends BaseModel
{

    /**
     * 评论
     *
     * @param int $id   soft_id
     * @param int $limit
     * @param string $order
     * @return mixed
     */
    public function comment($id = 0, $limit = 0, $order = 'saytime')
    {
        return $this->dal['comment']->getComment($id, $limit, $order);
    }
}
