<?php
declare(strict_types=1);

namespace App\Middleware;

use Cake\Database\Expression\QueryExpression;
use Cake\Http\ServerRequest;
use Cake\ORM\Locator\TableLocator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Dispositivos middleware
 */
class DispositivosMiddleware implements MiddlewareInterface
{
    /**
     * Process method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Server\RequestHandlerInterface $handler The request handler.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request instanceof ServerRequest) {
            if ($request->getParam('prefix') === 'Api') {
                if ($request->getHeaderLine('DeviceId')) {
                    /** @var \Authentication\IdentityInterface|\App\Model\Entity\Pessoa $identity */
                    $identity = $request->getAttribute('identity');

                    if ($identity) {
                        $tableLocator = new TableLocator();
                        $dispositivosTable = $tableLocator->get('Dispositivos');

                        /** @var \App\Model\Entity\Dispositivo $dispositivo */
                        $dispositivo = $dispositivosTable
                            ->find()
                            ->where(function (QueryExpression $expression) use ($request) {
                                $expression->eq('Dispositivos.id_dispositivo', $request->getHeaderLine('DeviceId'));

                                return $expression;
                            })
                            ->first();

                        if (empty($dispositivo)) {
                            $dispositivo = $dispositivosTable->newEntity([
                                'id_dispositivo' => $request->getHeaderLine('DeviceId'),
                            ]);
                        }

                        $dispositivo->pessoa_id = $identity->id;

                        if (
                            $dispositivo->isNew()
                            || $dispositivo->getOriginal('pessoa_id') != $dispositivo->pessoa_id
                        ) {
                            $dispositivo = $dispositivosTable->save($dispositivo);
                        }

                        $request = $request->withAttribute('dispositivo', $dispositivo);
                    }
                }
            }
        }

        return $handler->handle($request);
    }
}
