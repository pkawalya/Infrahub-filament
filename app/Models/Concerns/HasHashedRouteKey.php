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
 *   - resolveRouteBinding() decodes the hash back to the real ID for lookups
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
     * Keep the route key name as the real DB column.
     * This ensures that any code doing Model::where(getRouteKeyName(), ...) works.
     * We do NOT return 'hashed_id' here because it's a virtual attribute.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    /**
     * Override getRouteKey to return the hashed ID for URL generation.
     * This is what Laravel uses when building URLs (e.g. route('resource.show', $model)).
     */
    public function getRouteKey(): string
    {
        return $this->hashed_id;
    }

    /**
     * Resolve the model from its hashed route key.
     * This is called by Laravel's route model binding AND Filament's record resolution.
     */
    public function resolveRouteBinding($value, $field = null): ?self
    {
        // If a specific field is requested (and it's a real column), query directly
        if ($field && $field !== 'id' && $field !== 'hashed_id') {
            return static::where($field, $value)->first();
        }

        // Try to decode as a hashed ID first
        $id = static::decodeHashId((string) $value);

        if ($id !== null) {
            return static::find($id);
        }

        // Fallback: try as raw integer (backward compatibility / plain IDs)
        if (is_numeric($value)) {
            return static::find((int) $value);
        }

        return null;
    }

    /**
     * Resolve for child route binding (e.g., nested resources).
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        return $this->{$childType}()->where($field ?? 'id', $value)->first();
    }
}
