# Rules Index

This file maps file glob patterns to rule files. Agents MUST read all matching rule files before editing any path.

| Globs | Rule File | Topic |
|-------|-----------|-------|
| `database/migrations/**` | [database.md](./database.md) | UUID, soft deletes, audit columns |
| `app/Models/**` | [models.md](./models.md) | BaseModel, fillable, UUID, SoftDeletes |
| `app/Http/Requests/**` | [requests.md](./requests.md) | FormRequest validation & authorization |
| `app/Http/Controllers/**` | [controllers.md](./controllers.md) | Resource pattern, DB::transaction, activity logging |
| `app/Filament/**` | [filament.md](./filament.md) | FilamentPHP resource, panel, UI conventions |
| `app/Policies/**` | [authorization.md](./authorization.md) | Gate, Policy, permission naming |
| `database/seeders/**` | [seeders.md](./seeders.md) | Permission & role seeding conventions |
| `routes/**` | [routes.md](./routes.md) | Web & API routing conventions |
| `tests/**` | [testing.md](./testing.md) | Feature test structure |
| `app/**` | [architecture.md](./architecture.md) | Overall project architecture & stack |
