<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ToastsController extends Controller
{
    public function ajax(Request $request)
    {
        if ($request->ajax()) {

            $type = isset($request->type) ? $request->type : 'success';
            $close = isset($request->close) ? $request->close : true;
            $icon = isset($request->icon) ? $request->icon : true;
            $delay = isset($request->delay) ? $request->delay : 10000;
            $autohide = isset($request->autohide) ? $request->autohide : "true";
            $slot = $request->slot;
            $title = $request->title;

            $toasts = view("components.messages.toasts", compact('type', 'slot','close','icon','delay','autohide','title'))->render();
            return response()->json($toasts);
        }
    }
}
