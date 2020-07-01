<?php

namespace SuperView\Models;

use SuperView\Dal\Dal;
use SuperView\Utils\CacheKey;
use SuperView\Utils\Page;

class BaseModel
{
    protected $dal;

    protected $virtualModel;

    protected $pageOptions;

    protected static $instances;

    protected static $fitter;

    protected $actionPage = false;

    public static $additional = false;

    private function __construct()
    {
        $this->dal = Dal::getInstance();
    }

    public static function getInstance($virtualModel = '')
    {
        // 每一个$virtualModel对应一个独立的实例
        if (empty(static::$instances[$virtualModel])) {
            static::$instances[$virtualModel] = new static();
            static::$instances[$virtualModel]->setVirtualModel($virtualModel);
        }

        return static::$instances[$virtualModel];
    }

    public function setAdditional($arr)
    {
        static::$additional = $arr;
    }

    protected function setVirtualModel($virtualModel)
    {
        $this->virtualModel = $virtualModel;
    }

    /**
     * 设置分页属性.
     *
     * @return void
     */
    public function setPageOptions($pageOptions)
    {
        if (is_array($this->pageOptions)) {
            $this->pageOptions = array_merge($this->pageOptions, $pageOptions);
        } else {
            $this->pageOptions = $pageOptions;
        }
    }

    /**
     * 设置列表返回字段
     *
     * @return void
     */
    public function setFilterOptions($filterOptions)
    {
        self::$fitter = $filterOptions;
    }

    /**
     * 设置是否使用page方法
     *
     */
    public function setActionPage()
    {
        $this->actionPage = true;
    }

    /**
     * 获取是否设置page方法
     *
     * @return bool
     */
    public function getActionPage()
    {
        return $this->actionPage;
    }

    /**
     * 重置当前Model的属性(目前包含分页属性). &&   列表过滤查询字段（basis，advance）
     *
     * @return void
     */
    public function reset()
    {
        $this->pageOptions = null;
        self::$fitter = 'info';
        $this->actionPage = false;
        static::$additional = false;
    }

    /**
     * 使用支持分页的方式返回数组, 包含'list', 'count', 'page'参数.
     *
     * @return array
     */
    public function returnWithPage($data)
    {
        //针对count方法只返回字符串
        if (is_string($data)) {
            return $data;
        }
        $data['list'] = $data['list'] ?? $data;
        $data['count'] = $data['count'] ?? 0;
        // 未设置分页url路由规则, 直接返回'list'包含数组.
        if (empty($this->pageOptions) || $this->pageOptions['route'] === false) {
            $response = empty($data['list']) ? [] : $data['list'];
        } else {
            $response = $data;
        }
        return $response;
    }

    /**
     * Generate cache key by params.
     *
     * @param $method
     * @param array $params
     * @return string
     * @throws \Exception
     */
//  //缓存名称方式重构
    public function makeCacheKey($method, $params = [])
    {
        //树缓存单独拧出
        if ($method == 'SuperView\Models\CategoryModel::all') {
            return ':TotalCategory';
        }
        return CacheKey::makeCachekey($method, $params, $this->virtualModel);
    }

    /**
     * 获取通过SuperView::page方法设置的page参数.
     *
     * @return string
     */
    public function getCurrentPage()
    {
        return isset($this->pageOptions['currentPage']) ? $this->pageOptions['currentPage'] : 1;
    }

    /**
     * 获取通过SuperView::filter方法设置的filter参数.
     *
     * @return string
     */
    public static function getFilter()
    {
        return isset(self::$fitter) ? self::$fitter : 'info';
    }

    /**
     * 添加列表包含信息：分类信息、url.
     *
     * @return array
     */
    public function addListInfo($data)
    {
        $categoryModel = CategoryModel::getInstance('category');
        if (isset($data['list'])) {
            foreach ($data['list'] as $key => $value) {
                if (!isset($value['classid']) || !isset($value['id'])) return $data;   //此判断针对getOnly部分方法如专题不需要走addlist方法的
                $category = $categoryModel->info($value['classid']);
                $data['list'][$key]['classname'] = $category['classname'];
                $data['list'][$key]['classpath'] = $category['classpath'];
            }
        }
        return $data;
    }
}
