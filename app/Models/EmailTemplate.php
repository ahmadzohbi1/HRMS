<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $guarded = [];

    protected $casts = [
        "id" => "integer",
        "title" => "string",
        "name" => "string",
        "type" => "string",
        "body" => "string",
    ];
}