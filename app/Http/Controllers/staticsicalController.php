<?php

namespace App\Http\Controllers;

use App\Models\Staticsical;
use Illuminate\Http\Request;

class staticsicalController extends Controller
{
    //

    private $staticsical;

    public function __construct(Staticsical $staticsical)
    {
        $this->staticsical = $staticsical;
    }

    public function index(){
        $staticsical = $this->staticsical->get();
        return response([
            'staticsical' => $staticsical
        ]);
    }
}
