# Bookmark CLI

A fast PHP command-line tool to save, index, and search your favourite web pages entirely from your terminal.

## ✨ Features
- **Instant Search:** 1-2ms search responses using a local Redis cache.
- **Smart Scraping:** Automatically crawls and extracts HTML from unindexed sites on cache misses
- **Reliable Storage:** Keeps your data safe with local file storage and fuzzy text search via Elasticsearch.

## System Requirements
* **PHP** 8.1 or higher
* **Composer** (PHP dependency manager)
* **Redis Server** (Running on port `6379`)
* **Elasticsearch** v7.17+ (Running on port `9200` with Machine Learning disabled)

---

## 🛠️ Installation

1. Install globally via Composer using the official **Packagist Registry Listing**:
```
composer global require divyashriravichandran/bookmark-cli:dev-main
```

2. **Update your system PATH:** 

    Add the Composer global binaries to your shell profile (~/.zshrc or ~/.bashrc):

```
export PATH="$HOME/.composer/vendor/bin:$PATH"
```

## 🚀 Usage

1. **Start Services** 

    Make sure your local database services are running:
```
# Start Redis
brew services start redis

# Start Elasticsearch
ES_JAVA_HOME=$(brew --prefix)/opt/openjdk@17/libexec/openjdk.jdk/Contents/Home elasticsearch
```

2. **Available Terminal Commands**

    Run these commands in your terminal:
```
# Add and index a webpage
bookmark add <website-url>

# Search your bookmarks using fuzzy matching
bookmark search <keyword>

# Clear the Redis cache
redis-cli flushall
```