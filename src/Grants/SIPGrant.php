<?php

namespace MFullbrook\Livekit\Grants;

/**
 * SIPGrant represents SIP-specific permissions in a LiveKit access token
 */
class SIPGrant
{
    /**
     * Create a new SIPGrant instance
     *
     * @param bool|null $admin Permission to manage SIP resources
     * @param bool|null $call Permission to make outbound calls
     */
    public function __construct(
        public ?bool $admin = null,
        public ?bool $call = null
    ) {
    }

    /**
     * Convert the SIP grant to an array suitable for JWT encoding
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter(
            get_object_vars($this),
            function ($v) {
                return ! is_null($v);
            }
        );
    }
}
