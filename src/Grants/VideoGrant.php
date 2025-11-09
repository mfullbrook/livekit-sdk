<?php

// SPDX-FileCopyrightText: 2024 LiveKit, Inc.
//
// SPDX-License-Identifier: Apache-2.0

namespace MFullbrook\Livekit\Grants;

/**
 * VideoGrant represents video-specific permissions in a LiveKit access token
 */
class VideoGrant
{
    /**
     * Create a new VideoGrant instance
     *
     * @param string|null $room Name of the room
     * @param string|null $roomJoin Name of the room to join
     * @param bool|null $roomCreate Permission to create a room
     * @param bool|null $roomList Permission to list rooms
     * @param bool|null $roomRecord Permission to record
     * @param bool|null $roomAdmin Permission to administer the room
     * @param bool|null $canPublish Permission to publish tracks
     * @param bool|null $canPublishData Permission to publish data
     * @param array<string>|null $canPublishSources List of track sources that can be published (e.g., ['camera', 'microphone'])
     * @param bool|null $canSubscribe Permission to subscribe to tracks
     * @param bool|null $canUpdateOwnMetadata Permission to update own metadata
     * @param bool|null $ingressAdmin Grant admin permissions
     * @param bool|null $hidden Whether the participant is hidden
     * @param string|null $recorder Recorder identity
     * @param bool|null $agent Grant agent permissions
     */
    public function __construct(
        public ?string $room = null,
        public ?string $roomJoin = null,
        public ?bool $roomCreate = null,
        public ?bool $roomList = null,
        public ?bool $roomRecord = null,
        public ?bool $roomAdmin = null,
        public ?bool $canPublish = null,
        public ?bool $canPublishData = null,
        public ?array $canPublishSources = null,
        public ?bool $canSubscribe = null,
        public ?bool $canUpdateOwnMetadata = null,
        public ?bool $ingressAdmin = null,
        public ?bool $hidden = null,
        public ?string $recorder = null,
        public ?bool $agent = null
    ) {
    }

    /**
     * Convert the video grant to an array suitable for JWT encoding
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
