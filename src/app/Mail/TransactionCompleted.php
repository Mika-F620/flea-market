<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public $seller;
    public $product;

    public function __construct(Product $product, User $seller)
    {
        $this->seller = $seller;
        $this->product = $product;
    }

    public function build()
    {
        return $this->subject('取引が完了しました')
                    ->view('emails.transaction_completed')
                    ->with([
                        'sellerName' => $this->seller->name,
                        'productName' => $this->product->name,
                    ]);
    }
}
