<?php
declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Helper;

use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Router\Dispatched;
use Lengbin\Helper\YiiSoft\Arrays\ArrayHelper;

class AnnotationHelper
{
    /**
     * 获得 路由 path 信息
     *
     * @param Dispatched|null $dispatched
     *
     * @return array|callable|string
     */
    public static function getRouter(Dispatched $dispatched = null)
    {
        if ($dispatched === null) {
            $request = CommonHelper::getRequest();
            $dispatched = $request->getAttribute(Dispatched::class);
        }
        $callback = $dispatched->handler->callback;
        return is_array($callback) ? $callback : ['', ''];
    }

    /**
     * 获得 类 注解
     *
     * @param string          $annotation
     * @param Dispatched|null $dispatched
     *
     * @return mixed|null
     */
    public static function getClassAnnotation(string $annotation, Dispatched $dispatched = null)
    {
        [$class, $method] = self::getRouter($dispatched);
        return AnnotationCollector::getClassAnnotation($class, $annotation);
    }

    /**
     * 获得 方法 注解
     *
     * @param string          $annotation
     * @param Dispatched|null $dispatched
     *
     * @return mixed|null
     */
    public static function getClassMethodAnnotation(string $annotation, Dispatched $dispatched = null)
    {
        [$class, $method] = self::getRouter($dispatched);
        $classMethodAnnotations = AnnotationCollector::getClassMethodAnnotation($class, $method);
        return ArrayHelper::getValue($classMethodAnnotations, $annotation);
    }

    /**
     * 获得 属性 注解
     *
     * @param string          $annotation
     * @param string          $property
     * @param Dispatched|null $dispatched
     *
     * @return mixed|null
     */
    public static function getClassPropertyAnnotation(string $annotation, string $property, Dispatched $dispatched = null)
    {
        [$class, $method] = self::getRouter($dispatched);
        $classMethodAnnotations = AnnotationCollector::getClassPropertyAnnotation($class, $property);
        return ArrayHelper::getValue($classMethodAnnotations, $annotation);
    }
}
