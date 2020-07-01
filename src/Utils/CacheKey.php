<?php

namespace SuperView\Utils;

/**
 * 缓存生成规则和部分关于缓存生成逻辑功能以及缓存读取
 */
class CacheKey
{
    /**
     * 通过反射组合参数生成key
     *
     * @param $method
     * @param $params
     * @param $virtualModel
     * @return string
     * @throws \Exception
     */
    public static function makeCachekey($method, $params, $virtualModel)
    {
        //反射获取方法默认参数以及默认值（优先使用传递的参数作为key，没有用默认值）
        $param = self::reflexMethod($virtualModel, $method);
        $depend = [];
        foreach ($param AS $parameter) {
            $position = $parameter['position'];
            $depend[$parameter['name']] = isset($params[$position]) ? $params[$position] : $parameter['defaultValue'];
        }

        //确保cachekey参数中的第一个为classid
        return self::custom($virtualModel, $method, $depend);
    }

    /**
     * 过滤字段生成最终缓存key(contentModel的limit小于15条，缓存key统一使用15条)
     *
     * @param $depend
     * @return mixed
     */
    public static function filterStr($depend, $modelAlias)
    {
        $model = self::getModel($modelAlias);
        $limit = \Sconfig::get('limit');
        $key = '';
        foreach ($depend as $k => $v) {
            if (!in_array($k, ['isPic', 'classid', 'limit'])) {
                if (is_array($v)) {
                    if (static::is_assoc($v)) {
                        foreach ($v as $ke => $val) {
                            $key .= '::' . $ke . '::' . (is_array($val) ? implode(',', $val) : $val);
                        }
                    } else {
                        $key .= '::' . implode(',', $v);
                    }
                } else {
                    $key .= '::' . $v;
                }
            }
            if ($k == 'limit') {
                if ($model === 'SuperView\Models\ContentModel') {
                    $limit = $v < $limit ? $limit : $v;
                    $key .= '::' . $limit;
                } else {
                    $key .= '::' . $v;
                }
            }
        }
        return $key;
    }

    /**
     * 判断关联数组
     *
     * @param $array
     * @return bool
     */
    public static function is_assoc($array)
    {
        $keys = array_keys($array);
        return $keys !== array_keys($keys);
    }

    /**
     * 确认支点
     *
     * @param $virtualModel
     * @return string
     */
    public static function confirm_type($virtualModel)
    {
        $all_types = \Sconfig::get('type');
        foreach ($all_types as $k => $v) {
            if (in_array($virtualModel, $v)) {
                return $k;
            }
        }
        return 'Other';
    }

    /**
     * 针对缓存key且返回
     *
     * @param $params
     * @param $model
     * @param $method
     * @param $cacheMinutes
     * @return mixed
     */
    public static function insertCahce($params, $model, $method, $cacheMinutes)
    {
        return \SCache::getCachekey($model, $method, $params, $cacheMinutes);
    }

    /**
     * 缓存key生成
     *
     * @param $modelAlias
     * @param $method
     * @param $param
     * @return string
     */
    public static function custom($modelAlias, $method, $param)
    {
        return ':' . self::confirm_type($modelAlias)
            . '::' . $modelAlias . '::'
            . $method
            . (isset($param['classid']) ? '::' . (is_array($param['classid']) ? implode(',', $param['classid']) : $param['classid']) : '')
            . self::filterStr($param, $modelAlias);
    }

    /**
     * 判断缓存是否存在
     *
     * @param $cacheKey
     * @return bool
     */
    public static function haveCache($cacheKey)
    {
        return \SCache::has($cacheKey);
    }

    /**
     * 定制方法生成缓存
     *
     * @param $data
     * @param $allCacheKey
     * @param int $cacheMinutes
     */
    public static function customMakeCache($data, $allCacheKey, $cacheMinutes = 120)
    {
        foreach ($data as $k => $v) {
            \SCache::put($allCacheKey[$k], $v, $cacheMinutes);
        }
    }

    /**
     * 返回缓存结果
     *
     * @param $allCacheKey
     * @return mixed
     *
     */
    public static function getAllCache($allCacheKey)
    {
        $result = array();
        foreach ($allCacheKey as $k => $v) {
            $result[$k] = \SCache::get($v) ?: [];
        }
        return $result;
    }

    /**
     * 获取custom所有参数及方法
     *
     * @param string $method 方法
     * @param array $arguments ＿＿call所有参数
     * @return mixed
     * @throws \Exception
     */
    public static function customAll($method, $arguments)
    {
        $res['key'] = array_shift($arguments);
        $res['modelAlias'] = array_shift($arguments);
        $methodParam = self::reflexMethod($res['modelAlias'], $method);
        $res['param'] = [];
        foreach ($methodParam AS $parameter) {
            $position = $parameter['position'];
            $res['param'][$parameter['name']] = isset($arguments[$position]) ? $arguments[$position] : $parameter['defaultValue'];
        }
        return $res;
    }

    /**
     * 通过参数 获取反射参数
     *
     * @param string $modelAlias 模型别名
     * @param string $method 方法名
     * @return \ReflectionParameter[]
     * @throws \Exception
     */
    public static function reflexMethod($modelAlias, $method)
    {
        $key = 'reflex::' . $modelAlias . '::' . $method;
        if (!\SCache::has($key)) {
            $model = self::getModel($modelAlias);
            if (!method_exists($model, $method)) {
                throw new \Exception("调用不存在方法 类:{$model} 方法: {$method}");
            }
            $ReflectionFunc = new \ReflectionMethod($model, $method);
            $methodParam = $ReflectionFunc->getParameters();
            $data = array();
            foreach ($methodParam AS $parameter) {
                $pa['name'] = $parameter->name;
                $pa['position'] = $parameter->getPosition();
                $pa['defaultValue'] = $parameter->isOptional() ? $parameter->getDefaultValue() : '';
                $data[] = $pa;
            }
            \SCache::forever($key, $data);
        }
        return \SCache::get($key);
    }

    /**
     * 返回模型类
     *
     * @param $modelAlias
     * @return mixed
     */
    private static function getModel($modelAlias)
    {
        $models = \SConfig::get('models');
        return array_key_exists($modelAlias, $models) ? $models[$modelAlias] : $models['content'];
    }

    /**
     * getOnly方法的特殊缓存key生成
     *
     * @param array $params 拼接的请求参数
     * @return string
     */
    public static function getOnlyCacheKey($params)
    {
        return ':getOnly::' . md5(json_encode($params));
    }
}
