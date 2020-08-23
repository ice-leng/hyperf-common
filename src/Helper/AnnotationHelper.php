<?php
declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Helper;

use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Annotation\AnnotationInterface;
use Hyperf\HttpServer\Router\Dispatched;
use Lengbin\Helper\YiiSoft\Arrays\ArrayHelper;

class AnnotationHelper
{
    /**
     * 获得 注解 对象
     *
     * @param string          $annotation
     * @param Dispatched|null $dispatched
     *
     * @return AnnotationInterface|null
     */
    public static function get(string $annotation, Dispatched $dispatched = null)
    {
        if ($dispatched === null) {
            $request = CommonHelper::getRequest();
            $dispatched = $request->getAttribute(Dispatched::class);
        }
        [$class, $method] = $dispatched->handler->callback;
        $classMethodAnnotations = AnnotationCollector::getClassMethodAnnotation($class, $method);
        return ArrayHelper::getValue($classMethodAnnotations, $annotation);
    }
}
