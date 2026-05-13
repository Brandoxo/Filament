# CLAUDE.md — Bazar Admin Panel

> Agent and developer reference for the **Bazar** project.
> Read this file before modifying any order-related code.

---

## Project Overview

**Bazar** is a multi-tenant e-commerce admin panel built with **Laravel 12** and **Filament v5**. Each shop (`Shop`) owns its own `Customer` and `Order` records, linked by `shop_id`. The panel is accessed by shop operators and administrators to manage products, orders, customers, content (posts, looks, drops), and media.

---

## Stack

| Layer            | Technology                                  |
|------------------|---------------------------------------------|
| Framework        | Laravel 12 (PHP >= 8.2)                    |
| Admin panel      | Filament v5 (`filament/filament ^5.0`)      |
| RBAC             | `bezhansalleh/filament-shield ^4.2`         |
| Database         | MySQL (via Laragon on Windows)              |
| Storage          | AWS S3 (`league/flysystem-aws-s3-v3`)       |
| Asset bundler    | Vite                                        |
| Code style       | Laravel Pint                                |
| Test runner      | PHPUnit 11                                  |

---

## Repository Structure

```
app/
  Filament/
    Pages/
      Dashboard.php          # Custom dashboard — registers all header widgets
    Resources/               # Filament CRUD resources (Categories, Drops, Looks,
    │                        #   Posts, Products, Shops, Users)
    Widgets/
      BazarStatsOverview.php # Stats: total sales, items sold, new customers (sort 1)
      OrdersChartWidget.php  # Line chart: orders last 7 days (sort 2)
      PendingOrdersWidget.php# Table: pending orders only, auto-refreshes (sort 3)
      RecentOrdersWidget.php # Table: all orders with filters and status change (sort 4)
  Models/
    Order.php                # Order model — state machine constants + canTransitionTo()
    Customer.php
    Shop.php
    Product.php / ProductVariant.php
    Category.php / Look.php / Drop.php / Post.php / Page.php
    Favorite.php / UserAddress.php
    ShippingCarrier.php      # Paquetería — hasMany ShippingRate
    ShippingRate.php         # Tarifa de envío — belongsTo ShippingCarrier
    Coupon.php               # Cupón de descuento — TYPES + isValid() + calculateDiscount()
  Services/
    OrderStatusService.php   # ONLY authorised entry point for changing order status
database/
  migrations/
    2026_01_25_213410_create_orders_table.php
    2026_05_04_000001_add_payment_and_status_tracking_to_orders_table.php
```

---

## Essential Commands

```bash
# First-time setup (install, key, migrate, build assets)
composer setup

# Start all dev processes concurrently (server + queue + logs + vite)
composer dev

# Run tests
composer test

# Code style fix
./vendor/bin/pint

# Run a specific artisan command
php artisan <command>
```

---

## Database Schema — `orders` Table

| Column                    | Type              | Notes                                      |
|---------------------------|-------------------|--------------------------------------------|
| `id`                      | bigint PK         |                                            |
| `shop_id`                 | FK → shops        | Multi-tenancy anchor                       |
| `customer_id`             | FK → customers    |                                            |
| `order_number`            | string            | Human-readable, e.g. `ORD-001`            |
| `total_amount`            | decimal(10,2)     | Cast to `decimal:2` in model              |
| `status`                  | string            | Default `pending`. See state machine below |
| `payment_method`          | string, nullable  | Added 2026-05-04. Slug from `PAYMENT_METHODS` |
| `status_changed_at`       | timestamp, null   | Added 2026-05-04. Set by `OrderStatusService` |
| `delivery_mode`           | enum              | Added 2026-05-12. `parcel` or `personal`. See `DELIVERY_MODES` |
| `shipping_rate_id`        | FK → shipping_rates, null | Added 2026-05-12. `nullOnDelete`    |
| `tracking_number`         | string, nullable  | Added 2026-05-12. Courier tracking code     |
| `carrier_snapshot`        | json, nullable    | Added 2026-05-12. Carrier+rate data at order time |
| `coupon_id`               | FK → coupons, null | Added 2026-05-12. `nullOnDelete`          |
| `discount_amount`         | decimal(10,2)     | Added 2026-05-12. Discount applied. Cast to `decimal:2` |
| `coupon_snapshot`         | json, nullable    | Added 2026-05-12. Coupon data at order time |
| `shipping_address_snapshot` | json            | Customer address at purchase time          |
| `items_snapshot`          | json              | Line items + prices at purchase time       |
| `created_at` / `updated_at` | timestamps      |                                            |

---

## Order Management System

### State Machine

All valid order statuses and their allowed forward transitions are defined as constants on `App\Models\Order`.

```
pending    --> processing | cancelled
processing --> paid       | cancelled
paid       --> shipped    | cancelled
shipped    --> completed  | returned
completed  --> returned
cancelled  --> (terminal — no further transitions)
returned   --> (terminal — no further transitions)
```

**Status labels and colours** (used by Filament badge columns):

| Slug         | Label (ES)   | Filament colour |
|--------------|--------------|-----------------|
| `pending`    | Pendiente    | `warning`       |
| `processing` | En proceso   | `info`          |
| `paid`       | Pagado       | `success`       |
| `shipped`    | Enviado      | `primary`       |
| `completed`  | Completado   | `success`       |
| `cancelled`  | Cancelado    | `danger`        |
| `returned`   | Devuelto     | `gray`          |

**Payment method slugs** (`PAYMENT_METHODS`): `card`, `transfer`, `cash`, `paypal`, `mercadopago`, `other`.

### Checking allowed transitions

```php
$order->canTransitionTo('paid');   // true or false
```

`canTransitionTo()` performs a pure array lookup against `Order::TRANSITIONS[$this->status]`. It does **not** persist anything.

---

## RULE: Never update `status` directly

> **Do not call `$order->update(['status' => $newStatus])` anywhere in application code.**

Always go through `App\Services\OrderStatusService`. This service is the single authorised entry point for all status changes. It:

1. Validates the transition via `canTransitionTo()` and throws `ValidationException` on failure.
2. Updates both `status` and `status_changed_at` atomically inside `DB::transaction()`.
3. For bulk operations, uses `SELECT FOR UPDATE` (`lockForUpdate()`) to prevent race conditions.

**Single transition:**

```php
use App\Services\OrderStatusService;

$order = app(OrderStatusService::class)->transition($order, 'processing');
// Returns the refreshed Order model, or throws ValidationException.
```

**Bulk transition:**

```php
$result = app(OrderStatusService::class)->bulkTransition($records, 'cancelled');
// Returns: ['updated' => int, 'skipped' => int, 'errors' => string[]]
```

---

## Filament Widgets — Dashboard

Widgets are registered in `app/Filament/Pages/Dashboard.php` via `getHeaderWidgets()`. The dashboard renders a single-column layout at all breakpoints.

| Sort | Class                  | Description                                             |
|------|------------------------|---------------------------------------------------------|
| 1    | `BazarStatsOverview`   | KPI cards: sales, items sold, new customers             |
| 2    | `OrdersChartWidget`    | Line chart: order volume last 7 days                    |
| 3    | `PendingOrdersWidget`  | Pending orders table, FIFO sort, auto-refresh 30 s      |
| 4    | `RecentOrdersWidget`   | All orders table, paginated, filterable, status change   |

### PendingOrdersWidget

- Queries only `status = 'pending'`, eager-loads `customer`.
- Sorted by `created_at ASC` (oldest first — FIFO priority queue).
- Polls every **30 seconds** (`$pollingInterval = '30s'`).
- Row actions: "Procesar" (→ `processing`) and "Cancelar" (→ `cancelled`), both with confirmation modals.
- Bulk actions: "Marcar como En Proceso" and "Cancelar Seleccionadas" — both call `bulkTransition()` and emit differentiated success/warning notifications (errors capped at 5 lines).

### RecentOrdersWidget

- Queries all orders, eager-loads `customer`, sorted `created_at DESC`.
- Pagination options: 10 / 25 / 50 / 100 (default 10).
- Filters: status (`SelectFilter`), payment method (`SelectFilter`), date range (`Filter` with two `DatePicker`s).
- Row action "Cambiar Estado": opens a modal `Select` with all statuses. The action is **hidden automatically for terminal states** (`cancelled`, `returned`) via:
  ```php
  ->visible(fn (Order $record): bool => ! empty(Order::TRANSITIONS[$record->status]))
  ```
- Actual validation is always delegated to `OrderStatusService::transition()` — the modal itself does not validate business rules.

---

## Filament v5 — Action Namespace (Breaking Change)

In **Filament v5** all action classes live in `Filament\Actions`, **not** in `Filament\Tables\Actions` as they did in v3/v4.

```php
// CORRECT — Filament v5
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;

// WRONG — v3/v4 pattern, will cause class-not-found errors in v5
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
```

This applies to widget `->actions([])` and `->bulkActions([])` definitions. Table column and filter classes (`TextColumn`, `SelectFilter`, etc.) still come from `Filament\Tables\*`.

---

## Architecture Notes

- **Multi-tenancy**: enforced at the data layer via `shop_id` foreign key on `orders` and `customers`. There is no middleware-level scoping; queries must apply `where('shop_id', ...)` explicitly where required.
- **Snapshot pattern**: `shipping_address_snapshot` and `items_snapshot` on orders store a JSON copy of the customer address and product prices at the moment of purchase. This ensures historical order data is immutable even if the customer or product records change later.
- **Service layer**: business logic that touches order state lives exclusively in `app/Services/`. Do not place state-transition logic inside controllers, resources, or Livewire components.
- **RBAC**: permissions are managed by `filament-shield`. Role and permission tables are generated by the `create_permission_tables` migration. Do not hardcode role checks outside of Shield's gate definitions.

---

## Coding Conventions

- Currency is displayed in **MXN** (`->money('MXN')` on `TextColumn`).
- All user-facing labels in the admin panel are in **Spanish**.
- Models use `$guarded = []` (fully unguarded) rather than `$fillable` whitelists.
- `status` and `status_changed_at` are always updated together as a pair — never independently.
- Use `app(OrderStatusService::class)->transition()` via the service container; do not `new OrderStatusService()`.

---

## Files to Read First

When working on any order-related feature, read these files in order:

1. `app/Models/Order.php` — state machine constants and relationships
2. `app/Services/OrderStatusService.php` — all transition logic
3. `app/Filament/Widgets/PendingOrdersWidget.php` — pending queue UI
4. `app/Filament/Widgets/RecentOrdersWidget.php` — full order list UI
5. `app/Filament/Pages/Dashboard.php` — widget registration and layout

---

## Sensitive Areas — Do Not Modify Without Care

- `Order::TRANSITIONS` — changing this map affects all transition validation across the app.
- `OrderStatusService::bulkTransition()` — uses `lockForUpdate()`; removing this lock introduces race conditions under concurrent requests.
- `shipping_address_snapshot` / `items_snapshot` — these are immutable historical records; do not add logic that overwrites them after order creation.
