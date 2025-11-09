<?php

namespace MFullbrook\Livekit;

use Google\Protobuf\Duration;
use Google\Protobuf\GPBEmpty;
use Livekit\CreateSIPDispatchRuleRequest;
use Livekit\CreateSIPInboundTrunkRequest;
use Livekit\CreateSIPOutboundTrunkRequest;
use Livekit\CreateSIPParticipantRequest;
use Livekit\DeleteSIPDispatchRuleRequest;
use Livekit\DeleteSIPTrunkRequest;
use Livekit\GetSIPInboundTrunkRequest;
use Livekit\GetSIPInboundTrunkResponse;
use Livekit\GetSIPOutboundTrunkRequest;
use Livekit\GetSIPOutboundTrunkResponse;
use Livekit\ListSIPDispatchRuleRequest;
use Livekit\ListSIPDispatchRuleResponse;
use Livekit\ListSIPInboundTrunkRequest;
use Livekit\ListSIPInboundTrunkResponse;
use Livekit\ListSIPOutboundTrunkRequest;
use Livekit\ListSIPOutboundTrunkResponse;
use Livekit\ListSIPTrunkRequest;
use Livekit\ListSIPTrunkResponse;
use Livekit\Pagination;
use Livekit\SIPClient as LivekitSIPClient;
use Livekit\SIPDispatchRuleInfo;
use Livekit\SIPDispatchRuleUpdate;
use Livekit\SIPInboundTrunkInfo;
use Livekit\SIPInboundTrunkUpdate;
use Livekit\SIPOutboundConfig;
use Livekit\SIPOutboundTrunkInfo;
use Livekit\SIPOutboundTrunkUpdate;
use Livekit\SIPParticipantInfo;
use Livekit\SIPTrunkInfo;
use Livekit\TransferSIPParticipantRequest;
use Livekit\UpdateSIPDispatchRuleRequest;
use Livekit\UpdateSIPInboundTrunkRequest;
use Livekit\UpdateSIPOutboundTrunkRequest;
use MFullbrook\Livekit\Grants\SIPGrant;
use Twirp\Context;

class SIPClient extends ServiceClient
{
    private LivekitSIPClient $client;

    protected AccessToken $accessToken;

    public function __construct(string $host, string $apiKey, string $apiSecret, array $httpOptions = [])
    {
        parent::__construct($host, $apiKey, $apiSecret, $httpOptions);

        $this->client = new LivekitSIPClient($this->host);
    }

    protected function authenticate(): array
    {
        if (empty($this->accessToken) || $this->accessToken->willExpireWithin(60)) {
            $this->accessToken = new AccessToken($this->apiKey, $this->apiSecret);
            $this->accessToken->sipGrant = new SIPGrant(admin: true);
        }

        return Context::withHttpRequestHeaders([], [
            'Authorization' => "Bearer {$this->accessToken->toJwt()}"
        ]);
    }

    public function createInboundTrunk(SIPInboundTrunkInfo $trunk): SIPInboundTrunkInfo
    {
        return $this->client->CreateSIPInboundTrunk(
            $this->authenticate(),
            new CreateSIPInboundTrunkRequest(['trunk' => $trunk])
        );
    }

    public function listDispatchRules(
        ?int $page = null, array $dispatch_rule_ids = [], array $trunk_ids = []
    ): ListSIPDispatchRuleResponse
    {
        return $this->client->ListSIPDispatchRule(
            $this->authenticate(),
            new ListSIPDispatchRuleRequest(
                compact('page', 'dispatch_rule_ids', 'trunk_ids')
            )
        );
    }

    public function listTrunks(?Pagination $page = null): ListSIPTrunkResponse
    {
        return $this->client->ListSIPTrunk(
            $this->authenticate(),
            new ListSIPTrunkRequest(['page' => $page])
        );
    }

    public function createOutboundTrunk(SIPOutboundTrunkInfo $trunk): SIPOutboundTrunkInfo
    {
        return $this->client->CreateSIPOutboundTrunk(
            $this->authenticate(),
            new CreateSIPOutboundTrunkRequest(['trunk' => $trunk])
        );
    }

    public function updateInboundTrunk(
        string $sip_trunk_id,
        ?SIPInboundTrunkInfo $replace = null,
        ?SIPInboundTrunkUpdate $update = null
    ): SIPInboundTrunkInfo
    {
        return $this->client->UpdateSIPInboundTrunk(
            $this->authenticate(),
            new UpdateSIPInboundTrunkRequest(
                compact('sip_trunk_id', 'replace', 'update')
            )
        );
    }

    public function updateOutboundTrunk(
        string $sip_trunk_id,
        ?SIPOutboundTrunkInfo $replace = null,
        ?SIPOutboundTrunkUpdate $update = null
    ): SIPOutboundTrunkInfo
    {
        return $this->client->UpdateSIPOutboundTrunk(
            $this->authenticate(),
            new UpdateSIPOutboundTrunkRequest(
                compact('sip_trunk_id', 'replace', 'update')
            )
        );
    }

    public function getInboundTrunk(string $sip_trunk_id): GetSIPInboundTrunkResponse
    {
        return $this->client->GetSIPInboundTrunk(
            $this->authenticate(),
            new GetSIPInboundTrunkRequest(['sip_trunk_id' => $sip_trunk_id])
        );
    }

    public function getOutboundTrunk(string $sip_trunk_id): GetSIPOutboundTrunkResponse
    {
        return $this->client->GetSIPOutboundTrunk(
            $this->authenticate(),
            new GetSIPOutboundTrunkRequest(['sip_trunk_id' => $sip_trunk_id])
        );
    }

    public function listInboundTrunks(
        ?Pagination $page = null,
        array $trunk_ids = [],
        array $numbers = []
    ): ListSIPInboundTrunkResponse
    {
        return $this->client->ListSIPInboundTrunk(
            $this->authenticate(),
            new ListSIPInboundTrunkRequest(
                compact('page', 'trunk_ids', 'numbers')
            )
        );
    }

    public function listOutboundTrunks(
        ?Pagination $page = null,
        array $trunk_ids = [],
        array $numbers = []
    ): ListSIPOutboundTrunkResponse
    {
        return $this->client->ListSIPOutboundTrunk(
            $this->authenticate(),
            new ListSIPOutboundTrunkRequest(
                compact('page', 'trunk_ids', 'numbers')
            )
        );
    }

    public function deleteTrunk(string $sip_trunk_id): SIPTrunkInfo
    {
        return $this->client->DeleteSIPTrunk(
            $this->authenticate(),
            new DeleteSIPTrunkRequest(['sip_trunk_id' => $sip_trunk_id])
        );
    }

    public function createDispatchRule(SIPDispatchRuleInfo $dispatch_rule): SIPDispatchRuleInfo
    {
        return $this->client->CreateSIPDispatchRule(
            $this->authenticate(),
            new CreateSIPDispatchRuleRequest(['dispatch_rule' => $dispatch_rule])
        );
    }

    public function updateDispatchRule(
        string $sip_dispatch_rule_id,
        ?SIPDispatchRuleInfo $replace = null,
        ?SIPDispatchRuleUpdate $update = null
    ): SIPDispatchRuleInfo
    {
        return $this->client->UpdateSIPDispatchRule(
            $this->authenticate(),
            new UpdateSIPDispatchRuleRequest(
                compact('sip_dispatch_rule_id', 'replace', 'update')
            )
        );
    }

    public function deleteDispatchRule(string $sip_dispatch_rule_id): SIPDispatchRuleInfo
    {
        return $this->client->DeleteSIPDispatchRule(
            $this->authenticate(),
            new DeleteSIPDispatchRuleRequest(['sip_dispatch_rule_id' => $sip_dispatch_rule_id])
        );
    }

    public function createParticipant(
        string $room_name,
        string $sip_call_to,
        ?string $sip_trunk_id = null,
        ?SIPOutboundConfig $trunk = null,
        ?string $sip_number = null,
        ?string $participant_identity = null,
        ?string $participant_name = null,
        ?string $participant_metadata = null,
        array $participant_attributes = [],
        ?string $dtmf = null,
        ?bool $play_dialtone = null,
        ?bool $hide_phone_number = null,
        array $headers = [],
        ?int $include_headers = null,
        ?Duration $ringing_timeout = null,
        ?Duration $max_call_duration = null,
        ?bool $krisp_enabled = null,
        ?int $media_encryption = null,
        ?bool $wait_until_answered = null,
        ?string $display_name = null
    ): SIPParticipantInfo
    {
        return $this->client->CreateSIPParticipant(
            $this->authenticate(),
            new CreateSIPParticipantRequest(
                compact(
                    'sip_trunk_id', 'trunk', 'sip_call_to', 'sip_number', 'room_name',
                    'participant_identity', 'participant_name', 'participant_metadata',
                    'participant_attributes', 'dtmf', 'play_dialtone', 'hide_phone_number',
                    'headers', 'include_headers', 'ringing_timeout', 'max_call_duration',
                    'krisp_enabled', 'media_encryption', 'wait_until_answered', 'display_name'
                )
            )
        );
    }

    public function transferParticipant(
        string $participant_identity,
        string $room_name,
        string $transfer_to,
        ?bool $play_dialtone = null,
        array $headers = [],
        ?Duration $ringing_timeout = null
    ): GPBEmpty
    {
        return $this->client->TransferSIPParticipant(
            $this->authenticate(),
            new TransferSIPParticipantRequest(
                compact('participant_identity', 'room_name', 'transfer_to', 'play_dialtone', 'headers', 'ringing_timeout')
            )
        );
    }
}