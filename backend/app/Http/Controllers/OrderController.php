<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use stdClass;

class OrderController extends BaseController
{
   public function index()
   {
      return response()->json(["message" => "abc"]);
   }

   public function store(Request $request)
   {
      DB::beginTransaction();
      try {
         $products = $request->products;
         foreach ($products as $item) {
            //check quantity
            $inventory = Inventory::where('product_id', $item['product_id'])
               ->where('quantity', '>=', $item['quantity'])->sharedLock()->first();
            if ($inventory) {
               Inventory::where('product_id', $item['product_id'])->update([
                  'quantity' => DB::raw('quantity - ' . $item['quantity'])
               ]);

               Order::create([
                  'product_id' => $item['product_id'],
                  'quantity' => $item['quantity']
               ]);
            } else {
               DB::rollBack();
               throw new Exception("Không đủ tồn kho!");
            }
         }
         DB::commit();
         return 1;
      } catch (Exception $e) {
         DB::rollBack();
         throw new Exception($e->getMessage());
      }
   }

   public function testPerforment()
   {
      DB::beginTransaction();
      try {
         $products = array();
         $spA = new stdClass();
         $spA->product_id = 1;
         $spA->quantity = 10;
         array_push($products, $spA);
         $spB = new stdClass();
         $spB->product_id = 2;
         $spB->quantity = 5;
         array_push($products, $spB);
         foreach ($products as $item) {
            //check quantity
            while (1) {
               $inventory = Inventory::where('product_id', $item->product_id)
                  ->where('quantity', '>=', $item->quantity)->first();
               if ($inventory) {
                  $isInventory = Inventory::where('product_id', $item->product_id)
                     ->where('updated_at', $inventory->updated_at)
                     ->update([
                        'quantity' => DB::raw('quantity - ' . $item->quantity)
                     ]);
                  if ($isInventory) {
                     Order::create([
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity
                     ]);
                     break;
                  }
               } else {
                  DB::rollBack();
                  throw new Exception("Không đủ tồn kho!");
               }
            }
         }
         DB::commit();
         return 1;
      } catch (Exception $e) {
         DB::rollBack();
         throw new Exception($e->getMessage());
      }
   }
}
