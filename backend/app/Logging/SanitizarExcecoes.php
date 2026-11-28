<?php

namespace App\Logging;

use App\Logging\SanitizarExcecoes;
use Illuminate\Log\Logger;
use Monolog\LogRecord;
use Throwable;

class SanitizarExcecoes
{
    public function __invoke(Logger $logger): void
    {
        $logger->getLogger()->pushProcessor(
            function (LogRecord $record): LogRecord {

                $exception = $record->context['exception'] ?? null;

                // Preserva registros comuns.
                if (!$exception instanceof Throwable) {
                    return $record;
                }

                // Mantem somente informacoes tecnicas permitidas.
                return $record->with(
                    message: 'Excecao interna registrada.',
                    context: [
                        'tipo' => get_class($exception),
                        'arquivo' => basename($exception->getFile()),
                        'linha' => $exception->getLine(),
                    ],
                    extra: []
                );
            }
        );
    }
}