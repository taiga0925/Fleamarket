<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellRequest;
use App\Models\Category;
use App\Models\Category_item;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class SellController extends Controller
{
    /**
     * 出店画面
     * @return view ビュー
     */
    public function index($item_id = null)
    {
        $selectedCategoryId = null;

        if ($item_id) {
            $item = Item::find($item_id);
            $selectedConditionId = $item->condition_id;
            $selectedCategoryId = $item->categories->first()->id;
        }

        $selectCategories = Category::whereNull('parent_id')->with('children')->get();

        $data = [
            'selectCategories' => $selectCategories,
            'item' => $item ?? null,
            'item_id' => $item_id ?? null,
        ];

        return view('sell', $data);
    }

    public function create(SellRequest $request)
    {
        $form = $request->all();

        if ($request->file != null) {

            $itemImagePath = $request->file->getClientOriginalName();
            $itemImagePath = $request->file->storeAs('public/images', $itemImagePath);

            $form['image'] = $request->file->getClientOriginalName();
        }

        $form['money'] = str_replace(',', '', $form['money']);
        $newItem = Item::create($form);

        $categoryItems = new Category_item();
        $categoryItems->item_id = $newItem->id;
        $categoryItems->category_id = $request->category_id;
        $categoryItems->save();

        return redirect()->back();
    }

}
