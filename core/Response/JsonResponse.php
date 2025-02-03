<?php

namespace Core\Response;

use GuzzleHttp\Psr7\Response;

class JsonResponse extends Response
{
    public function __construct(
        int $status = 200,
        array $headers = [],
        $body = null,
        string $version = '1.1',
        ?string $reason = null
    ) {
        $headers['Content-Type'] = 'application/json; charset=UTF-8';

        if ($body !== null) {
            $body = json_encode($body);
        }

        parent::__construct($status, $headers, $body, $version, $reason);
    }
}