<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;




class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'size',
        'description',
        'image_url',
        'gallery_images',
        'price',
        'stock',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(ProductOrder::class);
    }

    protected function gallery(): Attribute
    {
        return Attribute::get(function (): Collection {
            return collect(array_merge([$this->image_url], $this->gallery_images ?? []))
                ->filter()
                ->unique()
                ->values();
        });
    }

    protected function storeLabels(): Attribute
    {
        return Attribute::get(function (): array {
            $labels = [];

            if ($this->created_at instanceof Carbon && $this->created_at->greaterThanOrEqualTo(now()->subDays(14))) {
                $labels[] = ['text' => 'Nuevo', 'class' => 'bg-emerald-500 text-white'];
            }

            if ($this->stock > 0 && $this->stock <= 3) {
                $labels[] = ['text' => 'Últimas unidades', 'class' => 'bg-amber-400 text-slate-950'];
            }

            if (($this->paid_orders_count ?? 0) >= 3 || ($this->orders_count ?? 0) >= 5) {
                $labels[] = ['text' => 'Más vendido', 'class' => 'bg-primary text-on-primary'];
            }

            return $labels;
        });
    }
}