<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ContentController extends Controller
{
    use HasApiResponses;

    public function toc(Request $request, string $type, string $version): JsonResponse
    {
        return $this->respondSuccess();
    }
}
