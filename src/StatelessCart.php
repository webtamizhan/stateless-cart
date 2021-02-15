<?php

namespace Webtamizhan\StatelessCart;

use Illuminate\Support\Facades\DB;
use Webtamizhan\StatelessCart\Exceptions\AuthenticationUserException;
use Webtamizhan\StatelessCart\Services\CartItem;

class StatelessCart
{
    /**
     * session name
     * @var string
     */
    private $cartSessionName;

    /**
     * Who's cart info
     * @var
     */
    private $user_id;

    public function __construct()
    {
        $this->cartSessionName = config('stateless-cart.cart_session_name');
        $this->setUserId();
    }

    /**
     * @param $user_id
     *
     * @return $this
     */
    public function setUserId()
    {
        $this->user_id = auth()->guard()->user()->id;
        if (empty($this->user_id)) {
            throw new AuthenticationUserException("Unable to find the authenticated user!");
        }

        return $this;
    }

    /**
     * Get Items from table
     * @return \Illuminate\Support\Collection
     */
    public function getItems()
    {
        if ($this->user_id) {
            $contents = DB::table(config('stateless-cart.database.table'))->where('user_id', $this->user_id)
                ->first();
            if (! $contents) {
                return new Collection();
            }
            $stored_items = unserialize($contents->items);
        } else {
            $stored_items = session()->get($this->cartSessionName);
            if (! $stored_items) {
                $stored_items = new Collection();
            }
        }
        $cartItems = new Collection();
        foreach ($stored_items as $stored_item) {
            $cartItems->put($stored_item->rowId, $stored_item);
        }

        return $cartItems;
    }

    /**
     * @param $item
     *
     * @return bool
     */
    private function isMulti($item)
    {
        if (! is_array($item)) {
            return false;
        }

        return is_array(head($item));
    }

    /**
     * Add cart items for user
     * @param       $id
     * @param null $name
     * @param null $qty
     * @param null $price
     * @param array $options
     *
     * @return \App\Service\CartItem|\App\Service\CartItem[]
     */
    public function add($id, $name = null, $qty = null, $price = null, array $options = [])
    {
        if ($this->isMulti($id)) {
            return array_map(function ($item) {
                return $this->add($item);
            }, $id);
        }
        if (is_array($id)) {
            $cartItem = CartItem::fromArray($id);
            $cartItem->setQuantity($id['qty']);
        } else {
            $cartItem = CartItem::fromAttributes($id, $name, $price, $options);
            $cartItem->setQuantity($qty);
        }

        $content = $this->getItems();

        if ($content->has($cartItem->rowId)) {
            $cartItem->qty += $content->get($cartItem->rowId)->qty;
        }

        $content->put($cartItem->rowId, $cartItem);

        $sessionItems = $content;
        $checkExist = DB::table(config('stateless-cart.database.table'))
            ->where('user_id', $this->user_id)
            ->first();
        if (! $checkExist) {
            DB::table(config('stateless-cart.database.table'))->insert([
                'user_id' => $this->user_id,
                'items' => serialize($content),
                'created_at' => date("Y-m-d H:i:s"),
            ]);
        } else {
            DB::table(config('stateless-cart.database.table'))
                ->where('user_id', $this->user_id)
                ->update([
                    'items' => serialize($content),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);
            $sessionItems = $content;
        }
        session()->put($this->cartSessionName, $sessionItems);

        return $cartItem;
    }

    public function update($rowId, $qty)
    {
        $cartItem = $this->get($rowId);

        if (is_array($qty)) {
            $cartItem->updateFromArray($qty);
        } else {
            $cartItem->qty = $qty;
        }

        $content = $this->getItems();

        if ($rowId !== $cartItem->rowId) {
            $content->pull($rowId);

            if ($content->has($cartItem->rowId)) {
                $existingCartItem = $this->get($cartItem->rowId);
                $cartItem->setQuantity($existingCartItem->qty + $cartItem->qty);
            }
        }

        if ($cartItem->qty <= 0) {
            $this->remove($cartItem->rowId);

            return;
        }
        $content->put($cartItem->rowId, $cartItem);

        if ($this->user_id) {
            DB::table(config('stateless-cart.database.table'))
                ->where('user_id', $this->user_id)
                ->update([
                    'items' => serialize($content),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }
        session()->put($this->cartSessionName, $content);

        return $cartItem;
    }

    /**
     * Get the contents of cart
     * @return \Illuminate\Support\Collection
     */
    public function content()
    {
        if (is_null($this->getItems())) {
            return new Collection([]);
        }

        return $this->getItems();
    }

    /**
     * Get the count
     * @return mixed
     */
    public function count()
    {
        $content = $this->getItems();

        return $content->sum('qty');
    }

    /**
     * Get the total amount of cart
     * @param null $decimals
     * @param null $decimalPoint
     * @param null $thousandSeperator
     *
     * @return string
     */
    public function total($decimals = 2, $decimalPoint = null, $thousandSeperator = null)
    {
        $content = $this->getItems();

        $total = $content->reduce(function ($total, CartItem $cartItem) {
            return $total + ($cartItem->qty * $cartItem->priceTax);
        }, 0);

        return $this->numberFormat($total, $decimals);
    }

    /**
     * @param int $decimals
     * @param string $decimalPoint
     * @param string $thousandSeperator
     *
     * @return string
     */
    public function subtotal($decimals = 2)
    {
        $content = $this->getItems();

        $subTotal = $content->reduce(function ($subTotal, CartItem $cartItem) {
            return $subTotal + ($cartItem->qty * $cartItem->price);
        }, 0);

        return $this->numberFormat($subTotal, $decimals);
    }

    /**
     * @param int $decimals
     *
     * @return string
     */
    public function tax($decimals = 2)
    {
        $content = $this->getItems();

        $tax = $content->reduce(function ($tax, CartItem $cartItem) {
            return $tax + ($cartItem->qty * $cartItem->tax);
        }, 0);

        return $this->numberFormat($tax, $decimals);
    }

    /**
     * @param $value
     * @param $decimals
     *
     * @return string
     */
    private function numberFormat($value, $decimals)
    {
        return number_format($value, $decimals, ',', '.');
    }

    /**
     * Get single item from cart
     * @param $rowId
     *
     * @return mixed|null
     */
    public function get($rowId)
    {
        $content = $this->getItems();

        if (! $content->has($rowId)) {
            return null;
        }

        return $content->get($rowId);
    }

    /**
     * Remove item from cart
     * @param $rowId
     */
    public function remove($rowId)
    {
        $cartItem = $this->get($rowId);

        $content = $this->getItems();

        $content->pull($cartItem->rowId);

        DB::table(config('stateless-cart.database.table'))
            ->where('user_id', $this->user_id)->update([
                'items' => serialize($content),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        session()->put($this->cartSessionName, $content);

        return;
    }

    /**
     * Delete the user cart and current session
     */
    public function destroy()
    {
        DB::table(config('stateless-cart.database.table'))
            ->where('user_id', $this->user_id)->delete();

        return;
    }

    public function swapSessionToUserCart()
    {
        if (! empty($this->user_id) && session()->has($this->cartSessionName)) {
            $contents = session()->get($this->cartSessionName);
            $checkExist = DB::table(config('stateless-cart.database.table'))
                ->where('user_id', $this->user_id)->first();
            if (! $checkExist) {
                DB::table(config('stateless-cart.database.table'))
                    ->insert([
                    'user_id' => $this->user_id,
                    'items' => serialize($contents),
                    'created_at' => date("Y-m-d H:i:s"),
                ]);
            } else {
                $existingContents = unserialize($checkExist->items);
                $newItems = new Collection();
                foreach ($existingContents as $existingContent) {
                    $newItems->put($existingContent->rowId, $existingContent);
                }
                foreach ($contents as $content) {
                    if ($newItems->has($content->rowId)) {
                        $content->qty += $newItems->get($content->rowId)->qty;
                    }
                    $newItems->put($content->rowId, $content);
                }
                DB::table(config('stateless-cart.database.table'))
                    ->where('user_id', $this->user_id)->update([
                    'items' => serialize($newItems),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);
            }
        }

        return;
    }
}
