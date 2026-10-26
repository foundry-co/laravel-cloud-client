<?php

namespace FoundryCo\LaravelCloudClient\Resources;

use FoundryCo\LaravelCloudClient\Concerns\CreatesRecords;
use FoundryCo\LaravelCloudClient\Concerns\DeletesRecords;
use FoundryCo\LaravelCloudClient\Concerns\FindsRecords;
use FoundryCo\LaravelCloudClient\Concerns\ListsRecords;
use FoundryCo\LaravelCloudClient\Concerns\UpdatesRecords;
use FoundryCo\LaravelCloudClient\Data\EnvironmentInstance;
use Illuminate\Support\Collection;

/**
 * @method Collection<int, EnvironmentInstance> list(?string $name = null, ?string $type = null, ?string $size = null, ?string $scalingType = null)
 * @method EnvironmentInstance find(string $id)
 * @method EnvironmentInstance create(string $name, string $type, string $size, string $scalingType, ?int $maxReplicas = null, ?int $minReplicas = null, ?bool $usesScheduler = null, ?bool $sleepWithApp = null, ?int $scalingCpuThresholdPercentage = null, ?int $scalingMemoryThresholdPercentage = null, ?int $visibilityTimeout = null, ?int $shutdownTimeout = null, ?array $backgroundProcesses = null)
 * @method EnvironmentInstance update(string $id, ?string $name = null, ?string $size = null, ?string $scalingType = null, ?int $maxReplicas = null, ?int $minReplicas = null, ?int $hibernationTimeout = null, ?bool $usesSleepMode = null, ?int $sleepTimeout = null, ?bool $usesScheduler = null, ?bool $sleepWithApp = null, ?bool $usesOctane = null, ?bool $usesInertiaSsr = null, ?int $scalingCpuThresholdPercentage = null, ?int $scalingMemoryThresholdPercentage = null, ?int $visibilityTimeout = null, ?int $shutdownTimeout = null, ?array $probes = null)
 * @method void delete(string $id)
 */
class InstancesResource extends Resource
{
    use CreatesRecords, DeletesRecords, FindsRecords, ListsRecords, UpdatesRecords;

    protected string $model = EnvironmentInstance::class;

    protected string $collection = '/environments/{parent}/instances';

    protected string $item = '/instances/{id}';

    protected array $creating = ['name', 'type', 'size', 'scaling_type'];

    public function sizes(): array
    {
        return $this->client->get('/instances/sizes')['data'] ?? [];
    }

    public function pause(string $id): EnvironmentInstance
    {
        return $this->hydrate($this->client->post("/instances/{$id}/pause"));
    }

    public function resume(string $id): EnvironmentInstance
    {
        return $this->hydrate($this->client->post("/instances/{$id}/resume"));
    }

    public function purge(string $id): EnvironmentInstance
    {
        return $this->hydrate($this->client->post("/instances/{$id}/purge"));
    }

    public function setDefault(string $id): EnvironmentInstance
    {
        return $this->hydrate($this->client->post("/instances/{$id}/default"));
    }

    public function failedJobs(string $id): Collection
    {
        return collect($this->client->get("/instances/{$id}/failed-jobs")['data'] ?? []);
    }

    public function deleteFailedJobs(string $id, ?array $jobIds = null, ?string $failedBefore = null): void
    {
        $this->client->delete("/instances/{$id}/failed-jobs", $this->withoutNulls([
            'jobIds' => $jobIds === null ? null : implode(',', $jobIds),
            'failedBefore' => $failedBefore,
        ]));
    }

    public function deleteFailedJob(string $id, string $jobId): void
    {
        $this->client->delete("/instances/{$id}/failed-jobs/{$jobId}");
    }

    public function retryFailedJob(string $id, string $jobId): void
    {
        $this->client->post("/instances/{$id}/failed-jobs/{$jobId}/retry");
    }
}
