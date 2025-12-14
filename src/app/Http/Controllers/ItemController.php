<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Like;
use App\Models\Category;
use App\Models\Comment;

class ItemController extends Controller
{

    /**
     * 商品一覧画面
     * @return view ビュー
     */
    public function index()
    {
        $items = Item::all();
        $likeItems = null;

        if (Auth::check()) {
            $user = Auth::user();
            $likeItems = $user->likeItems;
        }

        return view('index', [
            'items' => $items,
            'likeItems' => $likeItems,
        ]);
    }

    /**
    * 商品詳細画面
    * @return view ビュー
    */
    public function item($item_id)
    {
        $item = Item::with('comments.user', 'categories')->find($item_id);
        $category = $item->categories->first();
        $user = Auth::user();

        if ( ! is_null($category)){
            $categories = [
                'parentCategory' => Category::find($category->parent_id)->category ?? $category->category,
            ];
            if ($category->parent_id) {
                $categories['childCategory'] = $category->category;
            }
        }else{
            $categories = null ;
        }

        return view('item', [
            'item' => $item,
            'likesCount' => $item->likeUsers->count(),
            'categories' => $categories,
            'user' => $user,
            'userLiked' => $this->checkUserLiked($item),
            'userItem' => $item->user_id == Auth::id(),
            'commentsCount' => $item->comments->count(),
        ]);

    }

    /**
     * お気に入り追加機能
     * @return redirect リダイレクト
     */
    public function like($item_id)
    {
        $like = new Like();
        $like->user_id = Auth::id();
        $like->item_id = $item_id;
        $like->save();

        return redirect()->back();
    }

    /**
     * お気に入り解除機能
     * @return redirect リダイレクト
     */
    public function unlike($item_id)
    {
        Auth::user()->likeItems()->detach($item_id);
        return redirect()->back();
    }

    /**
     * コメントリスト画面
     */
    public function list($item_id)
    {
        $user = Auth::user();
        $item = Item::with('comments.user', 'categories')->find($item_id);
        $comments = $item->comments->all();

        return view('comment', [
            'user' => $user,
            'item' => $item,
            'comments' => $comments,
        ]);
    }

    /**
     * コメント追加機能
     *@return redirect リダイレクト
     */
    public function comment(CommentRequest $request, $item_id)
    {
        $userId = Auth::user()->id;
        $commentText = $request->input('comment');

        $comment = new Comment();
        $comment->user_id = $userId;
        $comment->item_id = $item_id;
        $comment->comment = $commentText;
        $comment->save();

        return redirect()->back();
    }

    /**
     * 検索機能
     * @return view ビュー
     */
    public function search(Request $request)
    {
        $searchText = $request->input('searchText');

        $items = Item::where('item', 'like', '%' . $searchText . '%')
        ->orWhere('detail', 'like', '%' . $searchText . '%')
        ->orWhereHas('categories', function ($query) use ($searchText) {
            $query->where('item', 'like', '%' . $searchText . '%')
                ->orWhereHas('parent', function ($parentQuery) use ($searchText) {
                    $parentQuery->where('item', 'like', '%' . $searchText . '%');
                });
        })
        ->get();

            $likeItems = null;

            if (Auth::check()) {
                $user = Auth::user();
                $likeItems = $user->likeItems;
                $likeItems = Item::where('item', 'like', '%' . $searchText . '%')
                ->orWhere('detail', 'like', '%' . $searchText . '%')
                ->orWhereHas('categories', function ($query) use ($searchText) {
                    $query->where('item', 'like', '%' . $searchText . '%')
                        ->orWhereHas('parent', function ($parentQuery) use ($searchText) {
                            $parentQuery->where('item', 'like', '%' . $searchText . '%');
                        });
                })
                ->get();
            }

            return view('index', [
                'items' => $items,
                'likeItems' => $likeItems
            ]);;
    }

    private function checkUserLiked($item)
    {
        return $item->likeUsers()->where('user_id', Auth::id())->exists();
    }
}
