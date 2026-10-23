<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        // Fetch the user's wishlist items along with the product details
        $wishlistItems = $user->wishlists()->with('product')->get();

        return view('user.wishlist', compact('wishlistItems'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $wishlist = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();

            return back()->with('success', 'Removed from wishlist.');
        } else {
            Wishlist::create(['user_id' => Auth::id(), 'product_id' => $request->product_id]);

            return back()->with('success', 'Added to wishlist!');
        }
    }
}
