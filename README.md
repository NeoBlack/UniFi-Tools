# UniFi-Tools

## Description

This is a test project that contains commands that interact with a UniFi controller.

## Install

```
git clone git@github.com:NeoBlack/UniFi-Tools.git
cd UniFi-Tools
composer install
```

## Configure

```
cp .env .env.local
# edit .env.local and adjust at least the variables:
# - CONTROLLER_USERNAME="<controller username>"
# - CONTROLLER_PASSWORD="<controller password>"
# If your controller is running on an different IP, change this here:
# - CONTROLLER_BASE_URL="https://192.168.1.1"
```

## Commands

This project contains this commands:

### Speed Test Results

This command reads the speed test history and print a table with the results.
The command has two options for start and end date.

```
./bin/console speedtest:results
./bin/console speedtest:results -f "2 days ago" -t "yesterday"
```

### Speed Test Check

This command checks the last speed test result regarding download rate, upload rate or latency.
The command has two options for start and end date like the result command, and three additional options for the check:

- minDownload Minimal Download Rate in MBit/s 
- minUpload Minimal Upload Rate in MBit/s
- maxLatency Maximum Latency in milliseconds

```
./bin/console speedtest:check bin/console speedtest:check --minDownload 480 --minUpload 90 --maxLatency 20
```