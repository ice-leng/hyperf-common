<?php
declare(strict_types=1);

namespace Lengbin\Hyperf\Helper;

use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Utils\Context;
use Lengbin\Helper\YiiSoft\Arrays\ArrayHelper;
use Psr\Http\Message\ServerRequestInterface;

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
            $request = Context::get(ServerRequestInterface::class);
            $dispatched = $request->getAttribute(Dispatched::class);
        }
        [$class, $method] = $dispatched->handler->callback;
        $classMethodAnnotations = AnnotationCollector::getClassMethodAnnotation($class, $method);
        return ArrayHelper::getValue($classMethodAnnotations, $annotation);
    }
}
