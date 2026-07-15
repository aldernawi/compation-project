<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return JsonResource::collection(
            $request->user()->notifications()->paginate(15)
        );
    }
}
