#!/bin/bash
echo "⚙️ Generating PHP code from Protobuf definitions..."
set -e
API_PROTOCOL=./protocol/protobufs
DESTINATION=./generated/
rm -rf $DESTINATION*

protoc \
    --plugin=protoc-gen-twirp_php=./twirp-plugin/protoc-gen-twirp_php \
    --twirp_php_out=$DESTINATION \
    --php_out=$DESTINATION \
    -I=$API_PROTOCOL \
    $API_PROTOCOL/logger/options.proto \
    $API_PROTOCOL/livekit_egress.proto \
    $API_PROTOCOL/livekit_room.proto \
    $API_PROTOCOL/livekit_webhook.proto \
    $API_PROTOCOL/livekit_ingress.proto \
    $API_PROTOCOL/livekit_models.proto \
    $API_PROTOCOL/livekit_agent.proto \
    $API_PROTOCOL/livekit_agent_dispatch.proto \
    $API_PROTOCOL/livekit_metrics.proto \
    $API_PROTOCOL/livekit_sip.proto \
    $API_PROTOCOL/livekit_analytics.proto \
    $API_PROTOCOL/agent/livekit_agent_session.proto
    
echo "✅ PHP code generation complete."

# Full list:
    # $API_PROTOCOL/livekit_egress.proto \
    # $API_PROTOCOL/livekit_room.proto \
    # $API_PROTOCOL/livekit_webhook.proto \
    # $API_PROTOCOL/livekit_ingress.proto \
    # $API_PROTOCOL/livekit_models.proto \
    # $API_PROTOCOL/livekit_agent.proto \
    # $API_PROTOCOL/livekit_agent_dispatch.proto \
    # $API_PROTOCOL/livekit_metrics.proto \
    # $API_PROTOCOL/livekit_sip.proto \
    # $API_PROTOCOL/livekit_analytics.proto \
    # $API_PROTOCOL/agent/livekit_agent_session.proto

composer dump-autoload