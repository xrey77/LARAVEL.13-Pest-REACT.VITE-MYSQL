<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Product',
    title: 'Product',
    description: 'Product model schema',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'category', type: 'string', example: 'Electronics'),
        new OA\Property(property: 'descriptions', type: 'string', example: 'Gaming Laptop'),
        new OA\Property(property: 'qty', type: 'integer', example: 10),
        new OA\Property(property: 'unit', type: 'string', example: 'pcs'),
        new OA\Property(property: 'costprice', type: 'number', format: 'float', example: 800.00),
        new OA\Property(property: 'sellprice', type: 'number', format: 'float', example: 1000.00),
        new OA\Property(property: 'saleprice', type: 'number', format: 'float', example: 950.00),
        new OA\Property(property: 'productpicture', type: 'string', example: 'laptop.jpg'),
        new OA\Property(property: 'alertstocks', type: 'integer', example: 5),
        new OA\Property(property: 'criticalstocks', type: 'integer', example: 2),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class Product extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category',
        'descriptions',        
        'qty',
        'unit',
        'costprice',
        'sellprice',
        'saleprice',
        'productpicture',
        'alertstocks',
        'criticalstocks',
        'createdat',
        'updatedat'        
    ];

}
