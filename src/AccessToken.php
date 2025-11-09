<?php

namespace MFullbrook\Livekit;

use Firebase\JWT\JWT;
use Livekit\RoomConfiguration;
use MFullbrook\Livekit\Grants\ClaimGrants;
use MFullbrook\Livekit\Grants\InferenceGrant;
use MFullbrook\Livekit\Grants\SIPGrant;
use MFullbrook\Livekit\Grants\VideoGrant;

/**
 * AccessToken generates JWT tokens for LiveKit
 */
class AccessToken
{
    private const int DEFAULT_TTL = 21600; // 6 hours in seconds

    private string $apiKey;

    private string $apiSecret;

    private ClaimGrants $grants;

    private ?string $_identity = null;

    private int $ttl;

    /**
     * Creates a new AccessToken
     *
     * @param  string|null  $apiKey  API Key, can be set in env LIVEKIT_API_KEY
     * @param  string|null  $apiSecret  API Secret, can be set in env LIVEKIT_API_SECRET
     * @param  array<string, mixed>  $options  Optional configuration
     *
     * @throws \InvalidArgumentException if API key or secret is not provided
     */
    public function __construct(?string $apiKey = null, ?string $apiSecret = null, array $options = [])
    {
        $apiKey = $apiKey ?? getenv('LIVEKIT_API_KEY') ?: null;
        $apiSecret = $apiSecret ?? getenv('LIVEKIT_API_SECRET') ?: null;

        if (! $apiKey || ! $apiSecret) {
            throw new \InvalidArgumentException('api-key and api-secret must be set');
        }

        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->grants = new ClaimGrants;
        $this->_identity = $options['identity'] ?? null;

        // Handle TTL: can be numeric (seconds) or string with unit
        $this->ttl = $this->parseTtl($options['ttl'] ?? self::DEFAULT_TTL);

        if (isset($options['name'])) {
            $this->grants->name = $options['name'];
        }

        if (isset($options['metadata'])) {
            $this->grants->metadata = $options['metadata'];
        }

        if (isset($options['attributes'])) {
            $this->grants->attributes = $options['attributes'];
        }
    }

    /**
     * Parse TTL value from various formats
     *
     * @param  int|string  $ttl  TTL in seconds or as a string (e.g., '6h', '2 days')
     * @return int TTL in seconds
     */
    private function parseTtl($ttl): int
    {
        if (is_int($ttl)) {
            return $ttl;
        }

        if (is_string($ttl)) {
            // Parse time strings like '6h', '2 days', '10m', etc.
            if (preg_match('/^(\d+)\s*(s|sec|second|seconds)$/i', $ttl, $matches)) {
                return (int) $matches[1];
            }
            if (preg_match('/^(\d+)\s*(m|min|minute|minutes)$/i', $ttl, $matches)) {
                return (int) $matches[1] * 60;
            }
            if (preg_match('/^(\d+)\s*(h|hour|hours)$/i', $ttl, $matches)) {
                return (int) $matches[1] * 3600;
            }
            if (preg_match('/^(\d+)\s*(d|day|days)$/i', $ttl, $matches)) {
                return (int) $matches[1] * 86400;
            }
            // If it's just a numeric string, treat as seconds
            if (is_numeric($ttl)) {
                return (int) $ttl;
            }
        }

        return self::DEFAULT_TTL;
    }

    /**
     * Video grant for this token
     */
    public ?VideoGrant $videoGrant {
        get => $this->grants->video;
        set => $this->grants->video = $value;
    }

    /**
     * SIP grant for this token
     */
    public ?SIPGrant $sipGrant {
        get => $this->grants->sip;
        set => $this->grants->sip = $value;
    }

    /**
     * Inference grant for this token
     */
    public ?InferenceGrant $inferenceGrant {
        get => $this->grants->inference;
        set => $this->grants->inference = $value;
    }

    /**
     * Participant name
     */
    public ?string $name {
        get => $this->grants->name;
        set => $this->grants->name = $value;
    }

    /**
     * Participant metadata
     */
    public ?string $metadata {
        get => $this->grants->metadata;
        set => $this->grants->metadata = $value;
    }

    /**
     * Participant attributes
     *
     * @var array<string, string>|null
     */
    public ?array $attributes {
        get => $this->grants->attributes;
        set => $this->grants->attributes = $value;
    }

    /**
     * Token kind
     */
    public ?string $kind {
        get => $this->grants->kind;
        set => $this->grants->kind = $value;
    }

    /**
     * SHA256 hash
     */
    public ?string $sha256 {
        get => $this->grants->sha256;
        set => $this->grants->sha256 = $value;
    }

    /**
     * Room preset name
     */
    public ?string $roomPreset {
        get => $this->grants->roomPreset;
        set => $this->grants->roomPreset = $value;
    }

    /**
     * Room configuration
     */
    public ?RoomConfiguration $roomConfig {
        get => $this->grants->roomConfig;
        set => $this->grants->roomConfig = $value;
    }

    /**
     * Participant identity
     */
    public ?string $identity {
        get => $this->_identity;
        set => $this->_identity = $value;
    }

    /**
     * Check if a token would expire within the given number of seconds
     *
     * @param  int  $seconds  Number of seconds to check
     * @return bool True if the token TTL is less than or equal to the given seconds
     */
    public function willExpireWithin(int $seconds): bool
    {
        return $this->ttl <= $seconds;
    }

    /**
     * Generates the JWT token
     *
     * @return string JWT encoded token
     *
     * @throws \InvalidArgumentException if identity is required but not set
     */
    public function toJwt(): string
    {
        // Validate that identity is set if joining a room
        if ($this->grants->video !== null && $this->grants->video->roomJoin && ! $this->identity) {
            throw new \InvalidArgumentException('identity is required for join but not set');
        }

        $now = time();

        // Build JWT payload
        $payload = [
            'iss' => $this->apiKey,
            'exp' => $now + $this->ttl,
            'nbf' => $now,
            'iat' => $now,
        ];

        // Add subject (identity) and JWT ID if set
        if ($this->identity) {
            $payload['sub'] = $this->identity;
            $payload['jti'] = $this->identity;
        }

        // Merge in the grants
        $payload = array_merge($payload, $this->grants->toArray());

        // Encode and return the JWT
        return JWT::encode($payload, $this->apiSecret, 'HS256');
    }
}
