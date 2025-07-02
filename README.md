# Metro-Markets Price Service

A semi- production-ready microservice / module  for fetching and persisting the lowest competitor prices, built with Symfony, DDD, CQRS, Hexagonal  and Docker.

---

## 🚀 Quick Start

Make sure you have **Docker** and **Docker Compose** installed on your machine.

1. **Build and start containers**

   ```bash
   make up
   ```

2. **Prepare the database & cache**

   ```bash
   make db-prepare
   ```

    * Creates the database if it doesn’t exist
    * Runs schema migrations
    * Loads random dummy data (fixtures) into the all the entities 

3. **Verify scheduler setup**

   ```bash
   make list-scheduler
   ```

   You should see a recurring job every 60 seconds to start price fetch.

4. **Run the scheduler or individual queues**

    * Scheduler (foreground): `make start-scheduler-logs`
    * Fetch queue:            `make fetch-queue`
    * Process queue:          `make process-queue`

5. **Flush Redis data (if needed)**

   ```bash
   make flush-redis
   ```

6. **Run tests**

   ```bash
   make test
   ```

---

## ⚙️ Architecture Overview

This service follows Domain-Driven Design (DDD) and the CQRS  pattern, with a clean, hexagonal structure.
It is designed to be modular, testable, and maintainable, with a focus on business logic encapsulation

```
src/
├── Domain/            ← Core entities, value objects, factories, repository interfaces
├── Application/       ← Commands & Queries, DTOs, use-case handlers
├── Infrastructure/    ← Symfony configs, adapters (Redis, Doctrine), external SDKs Adapters
└── UI/                ← HTTP controllers, routes
```

* **Core principles**: SOLID, KISS, DRY, hexagonal (ports-and-adapters) and Design Patterns
* **Tech**: PHP 8.x, Symfony 7+, Messenger, Scheduler, Redis, MySQL

---

## 🔄 Workflow

A typical end-to-end price-fetch workflow:

1. **Scheduler Trigger**: A recurring message (`StartFetchCompetitorPricesMessage`) is enqueued every configured interval.
2. **Batch Dispatch**: `StartFetchCompetitorPricesHandler` loads all product IDs (for now; custom fetch strategies can be added later) in batches and dispatches a `FetchCompetitorPricesMessage` for each supported competitor of each product.
3. **Fetch Handler**: `FetchCompetitorPricesHandler` calls the dummy SDK (or real adapter) to retrieve all competitor prices for the given product.
4. **Processing**: Each fetched price list triggers `ProcessFetchedPriceHandler`, which:
     -  Update supported competitors if needed.
    - Maps raw responses to `PriceDTO` objects.
    - Uses `LowestPriceSelector` to pick the lowest price from the new list and invokes `CurrentLowestPriceProvider` to load the current existing lowest price (from cache or DB) for comparison.
    - Calls `CacheAndPersistLowestPriceUpdater` to:
        - Log the update.
        - Save or update the new lowest `Price` to the database.
        - Store the new lowest in Redis cache for O(1) reads.
5. **Query Endpoints**: Clients call API endpoints (`/api/price/{id}` or `/api/price`) through the synchronous query bus. Handlers read from cache first, falling back to DB if needed, and return `PriceDTO` JSON.
6. **Logging**: All operations are logged for traceability in `var/log/*`, including scheduled job execution, fetch attempts, and price updates.

---

---

## 🗂️ Project Structure

| Layer              | Responsibility                                   | Folder               |
|--------------------|--------------------------------------------------|----------------------|
| **Domain**         | Business logic                                   | `src/Domain`         |
| **Application**    | Use cases, DTOs, Commands & Queries              | `src/Application`    |
| **Infrastructure** | External services, persistence, cache            | `src/Infrastructure` |
| **UI**             | HTTP entrypoints, controllers, routes            | `src/UI`             |
| **DummySDK**       | Dummy third-party competitor SDK                 | `packages`           |
| **Config**         | Main Symfony config (services, routes, packages) | `config/`            |

---

## 🐘 FrankenPHP Runtime

This project leverages the modern PHP runtime **FrankenPHP**, which bundles Caddy and PHP in one, providing:

- **Integrated HTTP workers** running PHP threads directly (no PHP-FPM required)
- **Warm OPcache** for faster PHP execution
- **Automatic HTTPS** via Caddy’s built-in self-signed certificates
- **Reduced container layers**, eliminating the need for Nginx or extra proxies
- **Improved performance** and simplified deployment

FrankenPHP is enabled by default Via Docker setup, so you don’t need to install or configure separate web servers or PHP-FPM processes.

---

## 🛠️ Makefile Commands

```bash
make up                # Build + start all Docker containers
make db-create         # Create database if not exists
make db-migrate        # Run Doctrine migrations
make fixtures          # Load seed data into price table
make db-prepare        # db-create + db-migrate + fixtures
make start-scheduler-logs  # Run scheduler consumer in foreground
make stop-scheduler        # Stop only the scheduler consumer
make list-scheduler        # Show recurring scheduled messages
make fetch-queue           # Consume the fetch_transport queue
make process-queue         # Consume the process_transport queue
make queues                # Alias: fetch-queue + process-queue
make flush-redis           # FLUSH Redis DB (clear cache)
make test                  # Run PHPUnit tests
```

> **Tip:** Use `make help` to list all targets.

---

## 🔑 API Usage

All endpoints are protected by a static API key via header `X-AUTH-TOKEN`. 
You can find the API key in the `.env` file.

### Endpoints

| Method | URL               | Description                           |
| ------ | ----------------- | ------------------------------------- |
| `GET`  | `/api/price/{id}` | Get lowest price for a single product |
| `GET`  | `/api/price`      | Get lowest price for all products     |

**Headers**:

```
X-AUTH-TOKEN: <YOUR_API_KEY>
```

Use the provided Postman collection to test the API.

---

## ⭐ Best Practices & Benefits

1. **Encapsulated Domain**: No business logic leaks into infrastructure or UI.
2. **Flexible CQRS**: Separate read/write models with synchronous query bus (`sync://`) and asynchronous command bus.
3. **Ports & Adapters**: Easy to swap Redis, Doctrine, or add new competitor SDKs or APIs without affecting core logic.
4. **Scheduler + Messenger**: Scalable batch processing with retry, fault tolerance, and separate streams.
5. **Testable & Maintainable**: Full unit tests for adapters, services, and providers.

---

## 🎯 Design Principles

* **DDD**: Entities, ValueObjects, Aggregates
* **CQRS**: Commands vs Queries, separate buses
* **SOLID**: Single Responsibility, Open/Closed, Liskov, Interface Segregation, Dependency Inversion
* **KISS & DRY**: Keep it simple, avoid duplication
* **Hexagonal Architecture**: Ports & Adapters

This approach decouples domain logic from framework details, making the service reusable, testable, and easy to migrate or plug into larger systems.

---

