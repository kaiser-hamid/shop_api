<?php

namespace App\Observers;

class ProductObserver
{
    public function created(Product $product)
    {
        $this->logHistory($product, 'create');
    }

    public function updated(Product $product)
    {
        $this->logHistory($product, 'update');
    }

    public function deleted(Product $product)
    {
        $this->logHistory($product, 'delete');
    }

    private function logHistory(Product $product, string $changeType)
    {
        $changes = $product->getDirty();
        
        foreach ($changes as $field => $newValue) {
            ProductHistory::create([
                'product_id' => $product->id,
                'field_name' => $field,
                'old_value' => $product->getOriginal($field),
                'new_value' => $newValue,
                'changed_by' => auth()->id(),
                'change_type' => $changeType
            ]);
        }
    }
}
