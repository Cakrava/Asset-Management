<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        $histories = History::all();
        return view('histories.index', compact('histories'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $history = History::create([
            'user_id' => $user->id,
            'history_data' => $request->input('historydata'),
        ]);

        return response()->json([
            'message' => 'History berhasil disimpan',
            'data' => $history
        ], 201);
    }
public function show()
{
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }

    $histories = History::where('user_id', $user->id)->get();

    return view('page.user.history-user', compact('histories'));
}
}
