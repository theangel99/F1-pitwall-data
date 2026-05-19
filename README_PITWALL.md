# PitWall - Formula 1 Data Application

A modern Formula 1 data web application built with Laravel 13, showcasing best practices in software architecture and code quality. This portfolio project demonstrates clean code principles, SOLID design, and comprehensive testing.

## 🏎️ Overview

PitWall is a full-featured F1 data platform that syncs real-time race data from the OpenF1 API, providing insights into drivers, races, sessions, lap times, and standings.

## 🛠️ Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | Laravel 13 (PHP 8.2+) |
| **Frontend** | Blade Templates + Alpine.js |
| **Database** | PostgreSQL (Supabase) |
| **Cache/Queue** | Redis + Laravel Horizon |
| **API** | OpenF1 API (https://openf1.org) |
| **Testing** | Pest PHP |
| **Styling** | Tailwind CSS |

## 📐 Architecture

This project follows **Clean Architecture** principles with strict separation of concerns:

```
┌─────────────┐
│ Controllers │  ← Thin, single responsibility
└──────┬──────┘
       │
┌──────▼──────┐
│  Services   │  ← All business logic
└──────┬──────┘
       │
┌──────▼──────┐
│Repositories │  ← All database queries
└──────┬──────┘
       │
┌──────▼──────┐
│   Models    │  ← Only relationships & scopes
└─────────────┘
```

### Key Patterns

- **Service Layer**: All business logic lives in service classes
- **Repository Pattern**: All database queries abstracted behind interfaces
- **DTOs**: All external API responses transformed to readonly Data Transfer Objects
- **Form Requests**: All validation in dedicated request classes
- **API Resources**: All JSON responses formatted via resource classes
- **Transformers**: Raw API data → DTOs (single responsibility)

## 🚀 Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer 2.x
- PostgreSQL 14+ (or Supabase account)
- Redis 6.x+
- Node.js 18+ (for frontend assets)

### Installation

1. **Install dependencies**
```bash
composer install
npm install
```

2. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configure `.env` file**
```env
# Database (PostgreSQL / Supabase)
DB_CONNECTION=pgsql
DB_HOST=your-supabase-host.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-password

# Redis (Cache & Queues)
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# OpenF1 API
OPENF1_BASE_URL=https://api.openf1.org/v1
OPENF1_CACHE_TTL=300
OPENF1_RATE_LIMIT_SLEEP_US=350000
```

4. **Run migrations**
```bash
php artisan migrate
```

5. **Start services**
```bash
# Start Laravel development server
php artisan serve

# Start queue worker (in separate terminal)
php artisan horizon

# Compile frontend assets (in separate terminal)
npm run dev
```

## 📊 Syncing OpenF1 Data

### Manual Sync Commands

```bash
# Sync everything for the current season
php artisan openf1:sync

# Sync specific data types
php artisan openf1:sync meetings
php artisan openf1:sync sessions
php artisan openf1:sync drivers
php artisan openf1:sync laps
php artisan openf1:sync positions
```

## 🧪 Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

## 🌐 Routes

### Web Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/` | Driver standings (current season) |
| GET | `/races` | Race calendar |
| GET | `/races/{race}` | Race detail & results |
| GET | `/drivers` | All drivers |
| GET | `/drivers/{driver}` | Driver profile & stats |

### API Routes

Prefix: `/api/v1` | Rate limit: 60 requests/minute

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/api/v1/drivers` | List all drivers |
| GET | `/api/v1/races` | List all races |
| GET | `/api/v1/standings/drivers` | Driver standings |

## 🎯 Code Quality Standards

This project enforces **strict code quality rules**:

### PSR-12 Coding Standard
- All files follow PSR-12
- No mixed types - always explicit

### Architecture Rules
✅ **DO:**
- Keep controllers thin (1 line of business logic max)
- Put all business logic in services
- Put all queries in repositories
- Use DTOs for external data
- Write PHPDoc for every public method

❌ **DON'T:**
- Put business logic in controllers or models
- Query databases directly in controllers
- Use `mixed` types
- Skip validation

## 📦 Database Schema

### Core Tables

- `seasons` - F1 seasons
- `constructors` - F1 teams
- `drivers` - F1 drivers
- `races` - Race meetings
- `sessions` - Practice/Qualifying/Race sessions
- `laps` - Lap timing data
- `positions` - Position tracking
- `pit_stops` - Pit stop data
- `race_results` - Final race results

All tables use UUID primary keys and proper foreign key constraints.

## 📝 License

This project is open-sourced software licensed under the MIT license.

## 🙏 Acknowledgments

- **OpenF1 API** - Free, open F1 data (https://openf1.org)
- **Laravel** - The PHP framework for web artisans
- **Supabase** - Open source Firebase alternative
- **Tailwind CSS** - Utility-first CSS framework

---

**Built with ❤️ and Laravel**
