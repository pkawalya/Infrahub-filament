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
     * Tell Laravel to use 'hashed_id' as the route key.
     */
    public function getRouteKeyName(): string
    {
        return 'hashed_id';
    }

    /**
     * Resolve the model from its hashed route key.
     */
    public function resolveRouteBinding($value, $field = null): ?self
    {
        if ($field && $field !== 'hashed_id') {
            return static::where($field, $value)->first();
        }

        // Try to decode the hash
        $id = static::decodeHashId($value);

        if ($id === null) {
            // Fallback: try as raw integer (for backward compatibility during transition)
            if (is_numeric($value)) {
                return static::find((int) $value);
            }
            return null;
        }

        return static::find($id);
    }

    /**
     * Resolve for child route binding (e.g., nested resources).
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        return $this->{$childType}()->where($field ?? 'id', $value)->first();
    }

    /**
     * Override getRouteKey to return the hashed ID.
     */
    public function getRouteKey(): string
    {
        return $this->hashed_id;
    }
}
