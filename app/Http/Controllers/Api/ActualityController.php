<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActualityCollection;
use App\Models\Actuality;
use Illuminate\Http\Request;

class ActualityController extends Controller
{
    public function index()
    {
        $actualities = Actuality::orderByRaw('COALESCE(published_at, created_at) DESC')->get();
        
        if ($actualities) {
            return $this->success(ActualityCollection::collection($actualities));
        }

        return $this->fail();
    }
    
    public function show(Actuality $actuality)
    {
        if ($actuality->exists()) {
            return $this->success(new ActualityCollection($actuality));
        }

        return $this->fail();
    }
}