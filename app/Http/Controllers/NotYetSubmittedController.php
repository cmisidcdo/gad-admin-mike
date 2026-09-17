<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotYetSubmittedController extends Controller
{
    /**
     * Display the Not Yet Submitted users page.
     * Only accessible by Administrator (user_role_id = 1).
     */
    public function index()
    {
        if ((int) auth()->user()->user_role_id === 1) {
            return view('not-yet-submitted');
        }

        return abort(403, 'Unauthorized access.');
    }
}
