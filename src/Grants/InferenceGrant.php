<?php

namespace MFullbrook\Livekit\Grants;

/**
 * InferenceGrant represents inference-specific permissions in a LiveKit access token
 */
class InferenceGrant
{
    /**
     * Create a new InferenceGrant instance
     *
     * @param bool|null $perform Permission to perform inference
     */
    public function __construct(
        public ?bool $perform = null
    ) {
    }

    /**
     * Convert the inference grant to an array suitable for JWT encoding
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
