<?php

namespace Core\Response;

use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;

class ViewResponse extends Response
{
    public function __construct(
        int $status = 200,
        array $headers = [],
        string $viewPath,
        $data,
        string $version = '1.1',
        ?string $reason = null
    ) {
        $headers['Content-Type'] = 'text/html; charset=utf-8';

        $body = $this->renderView($viewPath, $data);

        parent::__construct($status, $headers, $body, $version, $reason);
    }

    protected function renderView($viewPath, $data)
    {
        if (!file_exists($viewPath)) {
            throw new InvalidArgumentException('View does not exist.');
        }

        ob_start();
        extract($data);
        require $viewPath;
        $body = ob_get_clean();
        return $body;
    }
}