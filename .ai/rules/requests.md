# FormRequest Conventions

## Wajib Untuk Setiap Modul Baru

Buat dua FormRequest: `Store{Module}Request` dan `Update{Module}Request` di `app/Http/Requests/`.

## Template

```php
class StoreWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('create', \App\Models\WorkOrder::class);
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'status'      => 'required|in:pending,in-progress,completed,cancelled',
            'priority'    => 'required|in:low,medium,high,critical',
            // foto (jika ada):
            'photo'       => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ];
    }
}
```

## Aturan

- `authorize()` WAJIB memeriksa permission menggunakan Policy dengan `$this->user()->can('{action}', \App\Models\ModelName::class)` atau `$this->user()->can('update', $this->route('model'))`. DILARANG MENGGUNAKAN GATE NAME STRING (seperti `'create work-order'`) KECUALI SUDAH DI-DEFINE.
- Gunakan `in:` untuk kolom enum/status, jangan biarkan string bebas
- Foto wajib divalidasi dengan `image|mimes:jpeg,png,webp|max:2048`
- `UpdateRequest` gunakan `sometimes` atau `unique` dengan `ignore` untuk uniqueness
