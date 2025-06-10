<?php

declare(strict_types=1);

namespace App\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JsonRequestTransformer
{
    public function transform(Request $request): void
    {
        $content = $request->getContent();

        if (empty($content)) {
            return;
        }

        if (!$this->isJsonRequest($request)) {
            return;
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('invalid json body: ' . json_last_error_msg());
        }

        $request->request->replace(is_array($data) ? $data : []);
    }

    private function isJsonRequest(Request $request): bool
    {
        return str_contains($request->headers->get('Content-Type', ''), 'application/json');
    }
}
