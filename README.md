# Cocktails Home

A cocktail management app built with Laravel 12, Vue 3, Inertia.js and MongoDB. Features a dynamic form system driven by database schemas, allowing new models to be added without touching frontend components.

## Stack

- **Backend:** Laravel 12, PHP 8.2+, Laravel Sanctum
- **Frontend:** Vue 3, Inertia.js, Tailwind CSS, Vite
- **Database:** MongoDB

---

## Prerequisites

- PHP 8.2+
- [Composer](https://getcomposer.org/)
- Node.js 18+ and npm
- MongoDB 6+ running locally (default: `mongodb://localhost:27017`)
- PHP MongoDB extension (`ext-mongodb`)

### Installing the PHP MongoDB extension

```bash
pecl install mongodb
```

Then add `extension=php_mongodb` to your `php.ini`.

---

## Setup

### 1. Clone the repository

```bash
git clone <repo-url> cocktails-home
cd cocktails-home
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and add/update the following:

```env
APP_URL=http://localhost:8000

# MongoDB — do NOT set DB_CONNECTION=mongodb, models specify their connection explicitly
MONGODB_URI=mongodb://localhost:27017
MONGODB_DATABASE=cocktails_home

# Sessions and cache — file driver avoids SQL/MongoDB compatibility issues
SESSION_DRIVER=file
CACHE_STORE=file

# Sanctum — required for SPA authentication
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:8000,127.0.0.1,127.0.0.1:8000
```

> **Important:** do not set `DB_CONNECTION=mongodb`. Application models (`Cocktail`, `FormSchema`, `User`) declare `protected $connection = 'mongodb'` directly, so they connect to MongoDB regardless of the default connection. Sessions and cache use `file` because Laravel's `database` driver uses SQL-specific operations incompatible with MongoDB.

### 4. Run migrations

```bash
php artisan migrate
```

### 5. Create storage symlink (for uploaded photos)

```bash
php artisan storage:link
```

### 6. Seed the database

Creates a demo user, the cocktail form schema and 25 sample cocktails.

```bash
php artisan db:seed
```

**Demo credentials:**
- Email: `test@test.com`
- Password: `password`

---

## Running locally with indiviaul commands

```bash
# Backend only
php artisan serve

# Frontend only
npm run dev

# Build for production
npm run build

```

---

## Project structure

```
app/
├── Http/
│   ├── Controllers/Api/          # CocktailController, FormSchemaController
│   ├── Requests/
│   │   ├── ResourceRequest.php   # Abstract base — shared schema validation
│   │   ├── Cocktail/             # StoreCocktailRequest, UpdateCocktailRequest
│   │   └── FormSchema/           # UpdateFormSchemaRequest
│   └── ...
├── Models/
│   ├── Cocktail.php
│   └── FormSchema.php
└── Services/
    ├── CocktailService.php
    └── FormSchemaService.php     # getByName(), validate()

resources/js/
├── Components/Dynamic/
│   ├── DynamicForm.vue           # Renders sections from schema
│   ├── DynamicTable.vue          # Generic table with actions
│   └── FieldRepeater.vue         # Add/remove row lists
└── Pages/Cocktail/
    ├── Index.vue
    ├── Create.vue
    └── Edit.vue
```

---

## API endpoints

All routes require Sanctum session authentication.

```
GET    /api/schemas/{name}      Fetch form schema (e.g. /api/schemas/cocktails)

GET    /api/cocktails           List  — ?alcohol_type= ?sort= ?per_page= ?page=
POST   /api/cocktails           Create
GET    /api/cocktails/{id}      Show
PUT    /api/cocktails/{id}      Update
DELETE /api/cocktails/{id}      Delete
```

---

## Adding a new model

1. Create `app/Models/YourModel.php` (MongoDB)
2. Create `app/Services/YourModelService.php`
3. Create `app/Http/Controllers/Api/YourModelController.php`
4. Add routes in `routes/api.php`
5. Add a schema entry in `FormSchemaSeeder` with `name: 'your_model'`
6. Create `StoreYourModelRequest` and `UpdateYourModelRequest` extending `ResourceRequest`
7. Create `Pages/YourModel/Create.vue` and `Edit.vue` pointing `loadSchema` to `/api/schemas/your_model`

`DynamicForm`, `FieldRepeater` and `FormSchemaController` require no changes.

---

## Adding fields to an existing schema

Open `database/seeders/FormSchemaSeeder.php` and add a new field inside the relevant section. For example, to add an ABV field to cocktails:

```php
[
    'key'         => 'attributes.abv',
    'type'        => 'number',
    'label'       => 'ABV (%)',
    'placeholder' => 'e.g. 12.5',
    'min'         => 0,
    'max'         => 100,
],
```

Supported types: `text`, `textarea`, `number`, `select`, `checkbox`, `file`, `repeater`.

For a `select` field, include an `options` array:

```php
[
    'key'     => 'attributes.serving',
    'type'    => 'select',
    'label'   => 'Serving',
    'options' => [
        ['value' => 'straight', 'label' => 'Straight'],
        ['value' => 'on_the_rocks', 'label' => 'On the rocks'],
        ['value' => 'blended', 'label' => 'Blended'],
    ],
],
```

For a `repeater` field (list of rows), include `subfields`:

```php
[
    'key'       => 'attributes.steps',
    'type'      => 'repeater',
    'label'     => 'Steps',
    'subfields' => [
        ['key' => 'description', 'type' => 'text', 'label' => 'Step', 'flex' => 1],
    ],
],
```

### Re-running the seeder

The seeder uses `updateOrCreate` on the `name` field, so it is safe to re-run without creating duplicates:

```bash
php artisan db:seed --class=FormSchemaSeeder
```

The form will reflect the new fields immediately on the next page load — no Vue changes needed.
