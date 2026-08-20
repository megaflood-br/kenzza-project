<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseComWebhookController extends Controller
{
    public function handle(Request $request)
    {
        return response('', 200);
    }
}
