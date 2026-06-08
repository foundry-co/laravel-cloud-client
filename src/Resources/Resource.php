<?php

namespace Foundry\LaravelCloudClient\Resources;

use Foundry\LaravelCloudClient\CloudClient;
use Foundry\LaravelCloudClient\Exceptions\AuthenticationException;
use Foundry\LaravelCloudClient\Exceptions\CloudApiException;
use Foundry\LaravelCloudClient\Exceptions\NotFoundException;
use Foundry\LaravelCloudClient\Exceptions\ValidationException;
use Foundry\LaravelCloudClient\Http\CloudRequest;
use Illuminate\Support\Collection;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

abstract class Resource
{
    public function __construct(protected CloudClient $connector) {}

    protected function send(CloudRequest $request): Response
    {
        try {
            return $this->connector->send($request);
        } catch (RequestException $e) {
            throw $this->mapException($e);
        }
    }

    protected function paginateAll(CloudRequest $request, callable $transform): Collection
    {
        if (! $request instanceof Paginatable) {
            throw new \InvalidArgumentException('Request must implement Paginatable to use paginateAll.');
        }

        $paginator = $this->connector->paginate($request);
        $items = collect();

        try {
            foreach ($paginator as $response) {
                $data = $response->json();
                $pageItems = collect($data['data'] ?? [])->map(fn ($item) => $transform([
                    'data' => $item,
                    'included' => $data['included'] ?? [],
                ]));
                $items = $items->merge($pageItems);
            }
        } catch (RequestException $e) {
            throw $this->mapException($e);
        }

        return $items;
    }

    private function mapException(RequestException $e): \Throwable
    {
        $response = $e->getResponse();
        $body = $response->json() ?? [];
        $message = $body['message'] ?? $e->getMessage();
        $errors = $body['errors'] ?? [];

        return match (true) {
            $response->status() === 401 => new AuthenticationException($message),
            $response->status() === 404 => new NotFoundException($message),
            $response->status() === 422 => new ValidationException($message, $errors),
            default => new CloudApiException($message),
        };
    }
}
