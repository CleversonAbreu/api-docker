<?php 

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Product;

class ProductRepository
{

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function findAll(int $qtd) : LengthAwarePaginator
    {
        return $this->product->paginate($qtd);
    }

    public function findById(int $id) : Product
    {
        return $this->product->find($id);
    }

    public function insert(array $product)  : Product
    {
        return $this->product->create($product);
    }

    public function update(int $id, array $product) : int
    {
        return $this->product->whereId($id)->update($product);
    }

    public function delete(int $id) : bool
    {
        return $this->product->whereId($id)->delete();
    }
}