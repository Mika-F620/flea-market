<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'postal_code',
        'address',
        'building_name',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function purchasedProducts()
    {
        return $this->belongsToMany(Product::class, 'purchases', 'user_id', 'product_id')->withTimestamps();
    }

    public function purchases()
    {
        return $this->belongsToMany(Product::class, 'purchases', 'user_id', 'product_id')
            ->withPivot('payment_method', 'created_at', 'updated_at');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'rated_id'); // 'rated_id' は評価を受けたユーザーのID
    }

    public function givenRatings()
    {
        return $this->hasMany(Rating::class, 'rater_id');  // 購入者が出品者に対してした評価
    }

    // Userモデルに追加するメソッド
public function averageRating()
{
    // 出品者に対する評価
    $ratings = Rating::where('rated_id', $this->id)->pluck('score'); // rated_idがユーザーIDの評価を取得

    if ($ratings->isEmpty()) {
        return 0; // 評価がない場合は0を返す
    }

    return $ratings->avg(); // 平均スコアを返す
}


    // Userモデルにメソッドを追加

    public function tradingProducts()
    {
        return $this->hasMany(TradingProduct::class, 'user_id');
    }

}