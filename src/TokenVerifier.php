<?php

namespace MFullbrook\Livekit;


use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use MFullbrook\Livekit\Grants\ClaimGrants;
use MFullbrook\Livekit\Grants\InferenceGrant;
use MFullbrook\Livekit\Grants\SIPGrant;
use MFullbrook\Livekit\Grants\VideoGrant;

/**
 * TokenVerifier verifies and decodes LiveKit JWT tokens
 */
class TokenVerifier
{
    private const DEFAULT_CLOCK_TOLERANCE = 10;

    private string $apiKey;

    private string $apiSecret;

    /**
     * @param  string  $apiKey  API Key
     * @param  string  $apiSecret  API Secret
     */
    public function __construct(string $apiKey, string $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /**
     * Verify and decode a JWT token
     *
     * @param  string  $token  JWT token to verify
     * @param  int  $clockTolerance  Clock tolerance in seconds (default: 10)
     * @return ClaimGrants Decoded claims
     *
     * @throws \Exception if token is invalid
     */
    public function verify(string $token, int $clockTolerance = self::DEFAULT_CLOCK_TOLERANCE): ClaimGrants
    {
        JWT::$leeway = $clockTolerance;

        try {
            $decoded = JWT::decode($token, new Key($this->apiSecret, 'HS256'));

            // Verify issuer
            if (! isset($decoded->iss) || $decoded->iss !== $this->apiKey) {
                throw new \Exception('Invalid token issuer');
            }

            // Convert stdClass to ClaimGrants
            $claims = new ClaimGrants;

            if (isset($decoded->name)) {
                $claims->name = $decoded->name;
            }

            if (isset($decoded->kind)) {
                $claims->kind = $decoded->kind;
            }

            if (isset($decoded->metadata)) {
                $claims->metadata = $decoded->metadata;
            }

            if (isset($decoded->attributes)) {
                $claims->attributes = (array) $decoded->attributes;
            }

            if (isset($decoded->sha256)) {
                $claims->sha256 = $decoded->sha256;
            }

            if (isset($decoded->roomPreset)) {
                $claims->roomPreset = $decoded->roomPreset;
            }

            // Decode video grant
            if (isset($decoded->video)) {
                $videoGrant = new VideoGrant;
                foreach ((array) $decoded->video as $key => $value) {
                    if (property_exists($videoGrant, $key)) {
                        $videoGrant->$key = $value;
                    }
                }
                $claims->video = $videoGrant;
            }

            // Decode SIP grant
            if (isset($decoded->sip)) {
                $sipGrant = new SIPGrant;
                foreach ((array) $decoded->sip as $key => $value) {
                    if (property_exists($sipGrant, $key)) {
                        $sipGrant->$key = $value;
                    }
                }
                $claims->sip = $sipGrant;
            }

            // Decode inference grant
            if (isset($decoded->inference)) {
                $inferenceGrant = new InferenceGrant;
                foreach ((array) $decoded->inference as $key => $value) {
                    if (property_exists($inferenceGrant, $key)) {
                        $inferenceGrant->$key = $value;
                    }
                }
                $claims->inference = $inferenceGrant;
            }

            // Decode room config if present
            if (isset($decoded->roomConfig)) {
                // This would need to be deserialized from JSON to RoomConfiguration
                // For now, we'll skip this as it requires protobuf deserialization
                // $claims->roomConfig = ...
            }

            return $claims;
        } catch (\Exception $e) {
            throw new \Exception('Token verification failed: '.$e->getMessage());
        }
    }
}
