<?php

namespace App\Http\Livewire\Pharmacies;

use Livewire\Component;
use App\Models\Pharmacies\Product;

class ProductDuedateBatchStock extends Component
{
    public $dispatch;
    public $products;
    public $product_id;
    public $array = [];
    public $due_date_batch;
    public $count;
    public $barcode;
    public $unity;

    public $query;
    public $product;
    public $selectedName;
    public $msg_too_many;

    public function resetx()
    {
        $this->query = '';
        $this->products = [];
        $this->product = null;
        $this->selectedName = null;
        $this->barcode = null;
        $this->due_date_batch = [];
        $this->count = null;
    }

    // public function mount()
    // {
    //     if($this->product) {
    //         $this->setProduct($this->product);
    //     }
    // }

    public function setProduct(Product $product)
    {
        $this->resetx();
        $this->product = $product;
        $this->selectedName = $product->name;

        if($this->product){
          $product = Product::with(['purchaseItems','receivingItems'])->find($this->product->id);
          $this->array = [];
          if ($product) {
  
            $this->barcode = $product->barcode;
            $this->unity = $product->unit;
            $this->count = "";
  
            // se obtienen lotes desde tabla batchs
            foreach ($product->batchs as $key => $batch) {
              $this->array[$batch->due_date->format('d-m-Y') . " - " . $batch->batch] = 0;
            }
  
            foreach ($product->batchs as $key => $batch) {
              $this->array[$batch->due_date->format('d-m-Y') . " - " . $batch->batch] = $batch->count;
            }
  
            // seteo de lotes
            foreach ($this->array as $key1 => $value) {
              //elimina valores en cero
              if ($value == 0) {
                unset($this->array[$key1]);
              }
              //seleccion, se obtiene cantidad
              if ($key1 == $this->due_date_batch) {
                $this->count = $value;
              }
            }
  
          }
        }
    }

    public function updatedQuery()
    {
        // $this->products = Product::where('name','LIKE','%'.$this->query.'%')
        //                         ->orderBy('name','Asc')
        //                         ->get();

        $this->products = Product::where('pharmacy_id',session('pharmacy_id'))
                                ->where('name','LIKE','%'.$this->query.'%')
                                ->where('stock','>',0)
                                ->orderBy('name', 'ASC')->get();

        /** Más de 50 resultados  */
        if(count($this->products) >= 25)
        {
            $this->products = [];
            $this->msg_too_many = true;
        }
        else {
            $this->msg_too_many = false;
        }
    }

    public function addSearchedProduct($productId){
        $this->searchedProduct= $productId;
        $this->emit('searchedProduct', $this->searchedProduct);
    }





    public function mount()
    {
      	// $this->products = Product::where('pharmacy_id',session('pharmacy_id'))
        //                           ->where('stock','>',0)
        //                           ->orderBy('name', 'ASC')->get();

        // if($this->product) {
        //   $this->setProduct($this->product);
        // }
    }

    public function foo(){
        $product = Product::where('pharmacy_id',session('pharmacy_id'))->where('barcode',$this->barcode)->first();
        if($product){
            $this->product_id = $product->id;
            $this->setProduct($product);
        }
    }

    public function render()
    {
      if($this->product){
        $product = Product::with(['purchaseItems','receivingItems'])->find($this->product->id);
        $this->array = [];
        if ($product) {

          $this->barcode = $product->barcode;
          $this->unity = $product->unit;
          $this->count = "";

          // se obtienen lotes desde tabla batchs
          foreach ($product->batchs as $key => $batch) {
            $this->array[$batch->due_date->format('d-m-Y') . " - " . $batch->batch] = 0;
          }

          foreach ($product->batchs as $key => $batch) {
            $this->array[$batch->due_date->format('d-m-Y') . " - " . $batch->batch] = $batch->count;
          }

          // seteo de lotes
          foreach ($this->array as $key1 => $value) {
            //elimina valores en cero
            if ($value == 0) {
              unset($this->array[$key1]);
            }
            //seleccion, se obtiene cantidad
            if ($key1 == $this->due_date_batch) {
              $this->count = $value;
            }
          }

        }
      }
      
        return view('livewire.pharmacies.product-duedate-batch-stock');
    }
}
