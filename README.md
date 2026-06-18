# Bookmark CLI

A fast PHP command-line tool to save, index, and search your favourite web pages entirely from your terminal.

## Key Architecture Benefits
- Zero Latency Lookups: Instant 1-2ms search responses powered directly by a local Redis RAM execution layer.

- Smart Fallbacks: Dynamically crawls unindexed targets using a local raw HTML scraper if a cache miss happens.

- Persistent Search Space: Keeps structured index collections mapped safely to local file storage and an Elasticsearch engine instance for fuzzy text parsing

## System Requirements
* **PHP** 8.1 or higher
* **Composer** (PHP dependency manager)
* **Redis Server** (Running on port `6379`)
* **Elasticsearch** v7.17+ (Running on port `9200` with Machine Learning disabled)

---

## 🛠️ Installation

You can install this engine globally from any terminal workspace using the official **Packagist Registry Listing**:

```
composer global require divyashriravichandran/bookmark-cli:dev-main
```

**Environment Configuration Path**

Ensure your system profile shell looks for global Composer binary variables. If you haven't yet, append this to your environment file (e.g., ~/.zshrc or ~/.bashrc):

```
export PATH="$HOME/.composer/vendor/bin:$PATH"
```

## Usage

1. **Start Infrastructure Dependencies**

Spin up your local database engines before running queries:

```
# Start Redis Engine
brew services start redis

# Start Elasticsearch Node
elasticsearch
```

2. **Available Terminal Commands**

Once installed, use the short global binary name directly from any directory folder on your Mac:

```
# Index and scrape a target webpage
bookmark add https://example.com

# Perform a high-speed fuzzy query search
bookmark search database

# Flush the local RAM data layer cache
redis-cli flushall
```