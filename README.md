# Bookmark CLI

A lightning-fast Command Line Interface (CLI) tool to scrape, cache, index, and search web bookmarks. Built with PHP, powered by Redis for sub-millisecond caching, and Elasticsearch for fuzzy full-text search.

---

## Features
* **Smart Scraping:** Live scraping with custom browser headers to prevent getting blocked by modern sites.
* **Dual-Layer Storage:** Persistent backups saved locally to a JSON file structure.
* **Hybrid Database Strategy:**
  * **Redis (RAM Cache):** Delivers lightning-fast `Cache Hit` reads in under 3 milliseconds.
  * **Elasticsearch (Search Index):** Provides full-text keyword indexing with built-in typo tolerance (fuzziness).

---

## System Requirements
* **PHP** 8.1 or higher
* **Composer** (PHP dependency manager)
* **Redis Server** (Running on port `6379`)
* **Elasticsearch** v7.17+ (Running on port `9200` with Machine Learning disabled)

---

## 🛠️ Installation And Usage


```
ES_JAVA_HOME=$(brew --prefix)/opt/openjdk@17/libexec/openjdk.jdk/Contents/Home elasticsearch
```

```
php cli.php add <website-url>
php cli.php search <keyword>
redis-cli flushall
```
