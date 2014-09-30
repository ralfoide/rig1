#!/bin/bash

RIGLOC=/usr/local/Rig/thumbnail/

export LD_PRELOAD=$RIGLOC/ffmpeg.so:$RIGLOC/libaviplay-0.7.so.0
$RIGLOC/rig_thumbnail "$@"
