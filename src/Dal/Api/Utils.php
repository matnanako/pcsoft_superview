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
     * @param $home
     * @return array|bool|mixed
     */
    public function friendLink($classid, $field, $home)
    {
        $params = [
            'classid' => $classid,
            'field' => $field,
            'home' => $home,
        ];
        return $this->getData('friendLink', $params);
    }

}