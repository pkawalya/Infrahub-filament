<?php

namespace App\Models\Concerns;

use Hashids\Hashids;

/**
 * Trait to hash model IDs in URLs.
 *
 * Instead of /app/cde-projects/25, URLs become /app/cde-projects/jR3kL9pW
 * This prevents enumeration attacks while keeping integer PKs in the database.
 *
 * Usage: Add `use HasHashedRouteKey;` to any model.
 *
 * How it works with Filament / Laravel route model binding:
 *   - getRouteKeyName() returns 'id' (real column) so direct DB queries never break
 *   - getRouteKey() returns the hashed ID so generated URLs are obfuscated
 *   - resolveRouteBindingQuery() decodes hashes before querying
 *   - resolveRouteBinding() decodes hashes back to integer PKs for record lookups
 */
trait HasHashedRouteKey
{
    /**
     * Get the Hashids instance for this model.
     * Uses APP_KEY + model class name as salt for unique hashes per model.
     */
    protected static function getHashids(): Hashids
    {
        static $instances = [];

        $class = static::class;
        if (!isset($instances[$class])) {
            $salt = config('app.key', 'infrahub-salt') . '|' . $class;
            $instances[$class] = new Hashids($salt, 32); // 32 char hashes
        }

        return $instances[$class];
    }

    /**
     * Encode the model's ID for use in URLs.
     */
    public function getHashedIdAttribute(): string
    {
        return static::getHashids()->encode($this->getKey());
    }

    /**
     * Decode a hashed ID back to the integer PK.
     * Returns null if the hash is invalid.
     */
    public static function decodeHashId(string $hash): ?int
    {
        $decoded = static::getHashids()->decode($hash);
        return $decoded[0] ?? null;
    }

    /**
     * Keep the route key name as the real DB column ('id').
     * This ensures that any code doing Model::where(getRouteKeyName(), ...) works.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    /**
     * Override getRouteKey to return the hashed ID for URL generation.
     * Laravel/Filament uses this when generating URLs like route('resource.show', $model).
     */
    public function getRouteKey(): string
    {
        return $this->hashed_id;
    }

    /**
     * Override the query builder for route binding.
     *
     * This is the KEY method — both Laravel's `resolveRouteBinding()` and
     * Filament's `resolveRecordRouteBinding()` flow through here.
     * We intercept the hashed value, decode it to the real integer ID,
     * and build a proper `WHERE id = <int>` query.
     */
    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        // If querying by a specific field that isn't 'id', pass through normally
        if ($field && $field !== 'id' && $field !== $this->getKeyName()) {
            return $query->where($field, $value);
        }

        // Try to decode as a hashed ID
        $id = static::decodeHashId((string) $value);

        if ($id !== null) {
            return $query->where($this->getQualifiedKeyName(), $id);
        }

        // Value is not a valid hash — return impossible condition
        // so the query returns no results (prevents enumeration via raw IDs)
        return $query->where($this->getQualifiedKeyName(), 0)->whereRaw('0 = 1');
    }

    /**
     * Resolve the model from its hashed route key.
     */
    public function resolveRouteBinding($value, $field = null): ?self
    {
        return $this->resolveRouteBindingQuery($this->newQuery(), $value, $field)->first();
    }

    /**
     * Resolve for child route binding (e.g., nested resources).
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        return $this->{$childType}()->where($field ?? 'id', $value)->first();
    }
}
