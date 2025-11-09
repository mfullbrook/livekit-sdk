<?php

namespace MFullbrook\Livekit\Grants;

use Livekit\RoomConfiguration;

/**
 * ClaimGrants represents the claims in a LiveKit access token
 */
class ClaimGrants
{
    /**
     * @var string|null Display name of the participant
     */
    public ?string $name = null;

    /**
     * @var VideoGrant|null Video-specific grants
     */
    public ?VideoGrant $video = null;

    /**
     * @var SIPGrant|null SIP-specific grants
     */
    public ?SIPGrant $sip = null;

    /**
     * @var InferenceGrant|null Inference-specific grants
     */
    public ?InferenceGrant $inference = null;

    /**
     * @var string|null Kind of the token
     */
    public ?string $kind = null;

    /**
     * @var string|null Metadata associated with the participant
     */
    public ?string $metadata = null;

    /**
     * @var array<string, string>|null Custom attributes
     */
    public ?array $attributes = null;

    /**
     * @var string|null SHA256 hash
     */
    public ?string $sha256 = null;

    /**
     * @var string|null Room preset name
     */
    public ?string $roomPreset = null;

    /**
     * @var RoomConfiguration|null Room configuration
     */
    public ?RoomConfiguration $roomConfig = null;

    /**
     * Convert the claims to an array suitable for JWT encoding
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $claims = array_filter(
            get_object_vars($this),
            function ($v) {
                return ! is_null($v);
            }
        );

        // Convert nested grant objects to arrays
        if (isset($claims['video'])) {
            $claims['video'] = $claims['video']->toArray();
        }

        if (isset($claims['sip'])) {
            $claims['sip'] = $claims['sip']->toArray();
        }

        if (isset($claims['inference'])) {
            $claims['inference'] = $claims['inference']->toArray();
        }

        // Convert RoomConfiguration to JSON string
        if (isset($claims['roomConfig'])) {
            $claims['roomConfig'] = $claims['roomConfig']->serializeToJsonString();
        }

        return $claims;
    }
}
