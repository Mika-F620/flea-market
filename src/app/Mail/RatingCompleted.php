<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\User;
use App\Models\Rating;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RatingCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public $seller;
    public $product;
    public $rating;

    /**
     * 新しいメッセージインスタンスの生成
     *
     * @param User $seller 出品者
     * @param Product $product 商品
     * @param Rating $rating 評価情報
     * @return void
     */
    public function __construct(User $seller, Product $product, Rating $rating)
    {
        $this->seller = $seller;
        $this->product = $product;
        $this->rating = $rating;
    }

    /**
     * メールの構築
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('あなたの商品が評価されました')
                    ->view('emails.transaction_completed')
                    ->with([
                        'sellerName' => $this->seller->name,
                        'productName' => $this->product->name,
                        'ratingScore' => $this->rating->score,  // 評価スコアのみ
                    ]);
    }
}