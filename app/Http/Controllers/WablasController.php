<?php

namespace App\Http\Controllers;

use App\Services\WablasService;
use Illuminate\Http\Request;

class WablasController extends Controller
{
    protected $wablasService;

    public function __construct(WablasService $wablasService)
    {
        $this->wablasService = $wablasService;
    }

    public function sendMessages(Request $request)
    {
        $request->validate([
            'messages' => 'required|array',
            'messages.*.phone' => 'required|string',
            'messages.*.message' => 'required|string',
        ]);

        $result = $this->wablasService->sendMessages($request->messages);

        return response()->json($result);
    }
}