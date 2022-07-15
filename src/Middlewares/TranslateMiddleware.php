<?php

namespace Lengbin\Hyperf\Common\Middlewares;

use Hyperf\Contract\TranslatorInterface;
use Hyperf\Di\Annotation\Inject;
use Lengbin\Helper\YiiSoft\Arrays\ArrayHelper;
use Lengbin\Helper\YiiSoft\StringHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TranslateMiddleware implements MiddlewareInterface
{

    #[Inject]
    protected TranslatorInterface $translator;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $language = $request->getHeaderLine('language');
        if (StringHelper::isEmpty($language)) {
            $language = ArrayHelper::get($request->getQueryParams(), 'language');
        }
        if (!StringHelper::isEmpty($language)) {
            $this->translator->setLocale($language);
        }
        return $handler->handle($request);
    }
}
