<?php

namespace SuperView\Dal\Api;

/**
 * Utils Dal.
 */
class Utils extends Base
{
    /**
     * 获取软件相关词
     *
     * @param $softid
     * @return array|bool
     */
    public function relationWord($softid)
    {
        $params = [
            'softid' => $softid,
        ];
        return $this->getData('relationWord', $params);
    }

    /**
     * 友链
     *
     * @param $classid
     * @param $field
     * @param $order
     * @return array|bool|mixed
     */
    public function friendLink($classid, $field, $order)
    {
        $params = [
            'classid' => $classid,
            'field' => $field,
            'order' => $order,
        ];
        return $this->getData('friendLink', $params);
    }

}