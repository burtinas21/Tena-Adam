<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Return a paginated, searchable list of users.
     * Used by the platform admin "Send Notification" modal to pick recipients.
     */
    public function index(Request $request)
    {
        $query = User::with('roles')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%' . $request->search . '%';
                $q->where(function ($inner) use ($search) {
                    $inner->where('first_name', 'like', $search)
                          ->orWhere('last_name',  'like', $search)
                          ->orWhere('email',       'like', $search)
                          ->orWhere('phone',       'like', $search);
                });
            })
            ->orderBy('first_name')
            ->orderBy('last_name');

        $users = $query->paginate($request->integer('per_page', 20));

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
                'total'        => $users->total(),
            ],
        ]);
    }
}
