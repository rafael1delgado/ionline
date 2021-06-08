<?php

namespace App\Models\Parameters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RequestForms\RequestForm;

class PurchaseType extends Model
{
  protected $fillable = [ 'name' ];

  public function requestForms() {
      return $this->hasMany(RequestForm::class);
  }

    use HasFactory;
    protected $table = 'cfg_purchase_types';    

}
