<?php


namespace App\Http\Controllers\Cart;

use Exception;
use Illuminate\Support\Facades\Session;

class Cart
{
    const CART_NAME = 'cart';

    public function add($product, ?string $type = null)
    {
        $content = $this->content();
        $cartItem = new CartItem($product, $type);
        $content->put($cartItem->rowId, $cartItem);
        session()->put(self::CART_NAME, $content);
    }

    public function get($id)
    {
        $content = $this->content();
        if ($content->has($id)) {
            return $content->get($id);
        }

        $cartItem = $content->first(fn (CartItem $item) => (string)$item->id === (string)$id);
        if (is_null($cartItem))
            throw new Exception("The cart does not contain rowId {$id}.");

        return $cartItem;
    }

    public function update($id)
    {
        $content = $this->content();

        $cartItem = $this->get($id);

        $content->put($cartItem->rowId ?? $id, $cartItem);

        session()->put(self::CART_NAME, $content);
    }

    public function delete($id)
    {
        $content = $this->content();

        $cartItem = $this->get($id);
        $content->pull($cartItem->rowId ?? $id);

        session()->put(self::CART_NAME, $content);
    }

    public function content()
    {
        return session()->has(self::CART_NAME) ? session(self::CART_NAME) : collect();
    }

    public function isEmpty()
    {
        return sizeof($this->content()) == 0;
    }

    public function price()
    {
        $content = $this->content();

        return $content->reduce(function ($total, CartItem $cartItem) {
            return $total + $cartItem->price();
        }, 0);
    }

    public function discount()
    {
        $content = $this->content();

        return $content->reduce(function ($total, CartItem $cartItem) {
            return $total + $cartItem->discount();
        }, 0);
    }

    public function total($walletAmount = 0, $voucherAmount = 0 , $sendAmount = 0)
    {
        $content = $this->content();

        return (int)$content->reduce(function ($total, CartItem $cartItem) {
                return $total + $cartItem->total();
            }, 0) - $walletAmount - $voucherAmount + $sendAmount;
    }

    public function destroy()
    {
        session()->forget(self::CART_NAME);
    }
}
