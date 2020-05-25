#!/usr/bin/env bash

set -e

# Use STLINK_VERSION to select which stlink version is used
export STLINK_VERSION=2
export OPENOCD_ADAPTER_INIT="-c 'source [find interface/stlink-v${STLINK_VERSION}.cfg]' -c 'transport select hla_swd'"
export OPENOCD_CONFIG=/usr/share/openocd/scripts/target/stm32l0_dual_bank.cfg

# Reset ST probe
st-info --probe

# Run OPEN-OCD
__dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
bash ${__dir}/openocd.sh "$@" 2>&1
