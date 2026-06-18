# Bookmark CLI

A PHP command-line tool for saving and indexing webpages. Features high-speed lookups via a Redis RAM cache, falling back to real-time HTML scraping, persistent JSON storage, and Elasticsearch indexing on a cache miss.


## System Requirements
* **PHP** 8.1 or higher
* **Composer** (PHP dependency manager)
* **Redis Server** (Running on port `6379`)
* **Elasticsearch** v7.17+ (Running on port `9200` with Machine Learning disabled)

---

## 🛠️ Installation And Usage


```
brew services start redis
ES_JAVA_HOME=$(brew --prefix)/opt/openjdk@17/libexec/openjdk.jdk/Contents/Home elasticsearch
```

```
php cli.php add <website-url>
php cli.php search <keyword>
redis-cli flushall
```
