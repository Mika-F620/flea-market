<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class TransactionCompleted extends Mailable
{
    public $seller;
    public $product;
    public $rating; // 評価情報を追加

    public function __construct($seller, $product, $rating = null)
    {
        $this->seller = $seller;
        $this->product = $product;
        $this->rating = $rating; // 評価情報をセット
    }

    public function build()
    {
        return $this->view('emails.transaction_completed')
                    ->with([
                        'seller' => $this->seller,
                        'product' => $this->product,
                        'rating' => $this->rating, // 評価スコアやコメントをビューに渡す
                    ])
                    ->subject('取引が完了しました');
    }
}
