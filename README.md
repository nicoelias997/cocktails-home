# Cocktails Home

A cocktail management app built with **Laravel 12, Vue 3, Inertia.js and MongoDB**. Features a **fully dynamic form system** driven by database schemas: the frontend always fetches field definitions from the API — no hardcoded fields in Vue components. Form schemas can also be **created and edited live from the UI** without touching any code.

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
git clone https://github.com/nicoelias997/cocktails-home cocktails-home
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

Open `.env` and verify/update:

```env
APP_URL=http://localhost:8000

MONGODB_URI=mongodb://localhost:27017
MONGODB_DATABASE=cocktails_home

SESSION_DRIVER=file
CACHE_STORE=file

SANCTUM_STATEFUL_DOMAINS=localhost,localhost:8000,127.0.0.1,127.0.0.1:8000
```

> **Note:** Do not set `DB_CONNECTION=mongodb`. Models declare `protected $connection = 'mongodb'` directly. Sessions and cache use `file` because Laravel's `database` driver requires SQL.

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

## Running locally

```bash
# Terminal 1 — backend
php artisan serve

# Terminal 2 — frontend (HMR)
npm run dev
```

---

## Project structure

```
app/
├── Http/
│   ├── Controllers/Api/
│   │   ├── CocktailController.php
│   │   └── FormSchemaController.php   # index, show, update
│   ├── Requests/
│   │   ├── ResourceRequest.php        # Abstract — applies schema validation on submit
│   │   ├── Cocktail/                  # StoreCocktailRequest, UpdateCocktailRequest
│   │   └── FormSchema/                # UpdateFormSchemaRequest
│   └── ...
├── Models/
│   ├── Cocktail.php
│   └── FormSchema.php
└── Services/
    ├── CocktailService.php
    └── FormSchemaService.php          # index(), getByName(), validate(), update()

resources/js/
├── Components/Dynamic/
│   ├── DynamicForm.vue        # Renders any schema as a form (no hardcoded fields)
│   ├── DynamicTable.vue       # Generic sortable/paginated table
│   └── FieldRepeater.vue      # Add/remove row lists (for repeater fields)
└── Pages/
    ├── Cocktail/
    │   ├── Index.vue           # List + detail modal (schema-driven)
    │   ├── Create.vue          # Dynamic form, fields from API
    │   └── Edit.vue            # Dynamic form, prefills from DB + schema
    └── Schema/
        ├── Index.vue           # List all form schemas
        └── Edit.vue            # Visual schema editor (add/edit/delete fields)
```

---

## API endpoints

All routes require Sanctum session authentication.

```
# Form schemas
GET    /api/schemas                List all schemas
GET    /api/schemas/{name}         Fetch schema sections (consumed by DynamicForm)
PUT    /api/schemas/{name}         Update schema sections from the UI editor

# Cocktails
GET    /api/cocktails              List — ?alcohol_type= ?sort= ?per_page= ?page=
POST   /api/cocktails              Create
GET    /api/cocktails/{id}         Show
PUT    /api/cocktails/{id}         Update
DELETE /api/cocktails/{id}         Delete
```

---

## Dynamic form system

The form schema for each resource is stored in MongoDB as a document in the `form_schemas` collection. Each document contains an ordered list of **sections**, each with **fields**:

```json
{
  "name": "cocktails",
  "active": true,
  "sections": [
    {
      "title": "Basic info",
      "fields": [
        { "key": "name",         "type": "text",   "label": "Name",         "required": true },
        { "key": "alcohol_type", "type": "select", "label": "Alcohol type",  "options": [...] }
      ]
    },
    {
      "title": "Details",
      "fields": [
        { "key": "attributes.glass",        "type": "text",     "label": "Glass" },
        { "key": "attributes.instructions", "type": "textarea", "label": "Instructions" },
        { "key": "attributes.ingredients",  "type": "repeater", "label": "Ingredients",
          "subfields": [
            { "key": "name",   "type": "text",   "label": "Ingredient" },
            { "key": "amount", "type": "text",   "label": "Amount" }
          ]
        }
      ]
    }
  ]
}
```

### Field key conventions

| Key format | Where stored | Editable from Schema UI? |
|------------|--------------|--------------------------|
| `name`, `alcohol_type`, `photo` | Top-level Cocktail document fields | Label/type only (key locked 🔒) |
| `attributes.*` | Inside the `attributes` sub-document | Fully editable |

### Editing schemas from the UI

Navigate to **Schemas** in the sidebar (or `/schemas`) to see all available schemas. Click **Edit fields** to open the visual editor where you can:

- Add, rename, reorder or delete fields
- Change field type (`text`, `textarea`, `number`, `select`, `checkbox`, `file`, `repeater`)
- Configure select options and repeater sub-fields inline
- Organize fields into named sections

Changes take effect on the next page load of the affected form — no code deployment needed.

---

## Adding a new model

1. Create `app/Models/YourModel.php` (MongoDB)
2. Create `app/Services/YourModelService.php`
3. Create `app/Http/Controllers/Api/YourModelController.php`
4. Create `StoreYourModelRequest` and `UpdateYourModelRequest` extending `ResourceRequest` (set `schemaName()` to your schema name)
5. Add CRUD routes in `routes/api.php` and web routes in `routes/web.php`
6. Seed a schema: add an entry in `FormSchemaSeeder` with `'name' => 'your_model'` and run `php artisan db:seed --class=FormSchemaSeeder`
7. Create `Pages/YourModel/Index.vue`, `Create.vue`, `Edit.vue` — `DynamicForm` and `DynamicTable` require no changes
8. Add a nav entry in `AuthenticatedLayout.vue`

The Schema Editor at `/schemas/your_model/edit` will work automatically for any schema in the database.

---

## Adding fields to an existing schema (code alternative)

Schema fields can be edited from the UI at `/schemas`. Alternatively, edit `database/seeders/FormSchemaSeeder.php` and re-run:

```bash
php artisan db:seed --class=FormSchemaSeeder
```

> The seeder uses `updateOrCreate` on `name`, so re-running is safe.

Supported field types: `text`, `textarea`, `number`, `select`, `checkbox`, `file`, `repeater`.
