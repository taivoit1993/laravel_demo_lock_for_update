<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Routing\Controller as BaseController;
class ProductController extends BaseController
{
  public function index(){
    return Product::all();
  }
}