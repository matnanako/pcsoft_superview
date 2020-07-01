<?php

namespace SuperView\Models;

use SuperView\Utils\Page;

class UtilsModel extends BaseModel
{

    /**
     * 获取分页
     */
    public function renderPage($route, $total, $perPage, $currentPage = null, $simple = false, array $options = [])
    {
        $page = new Page($route, $total, $perPage, $currentPage, $simple, $options);
        return $page->render();
    }

    /**
     * 获取软件相关词
     *
     * @param $softid
     * @return mixed
     */
    public function relationWord($softid = 0)
    {
        return $this->dal['utils']->relationWord($softid);
    }

    /**
     * 友链
     *
     * @param int $classid
     * @param array $field
     * @param string $home
     * @return mixed
     */
    public function friendLink($classid = 0, $field = [], $home = '')
    {
        return $this->dal['utils']->friendLink($classid, $field, $home);
    }
}
