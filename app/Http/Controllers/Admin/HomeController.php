<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use App\Models\Packets;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){

        $daily = DB::table('packets')->select('price')->where('id', 9)->first();
        $montly = DB::table('packets')->select('price')->where('id', 10)->first();
        $trainer = DB::table('packet_trainer')->select('price')->where('id', 6)->first();
        $review = DB::table('member_review')->join('member_information', 'member_information.id', '=', 'member_review.iduser')->where('member_review.status', 'Verified')->get();
        
        $cms = Cms::get();

        $package = Packets::where('is_active', 1)->orderBy('id', 'DESC')->get();

        foreach ($cms as $key => $value) {
            if ($value['section'] == "hero") {
                $content['hero'] = $value['content'];
            }

            if ($value['section'] == "jelajahi") {
                $content['jelajahi'] = $value['content'];
            }

            if ($value['section'] == "service") {
                $content['service'] = $value['content'];
            }

            if ($value['section'] == "join") {
                $content['join'] = $value['content'];
            }

            if ($value['section'] == "price") {
                $content['price'] = $value['content'];
            }

            if ($value['section'] == "about") {
                $content['about'] = $value['content'];
            }

            if ($value['section'] == "company") {
                $content['company'] = $value['content'];
            }
        }
        
        return view('home', [
            'daily' => $daily,
            'montly' => $montly,
            'trainer' => $trainer,
            'review' => $review,
            'content' => $content,
            'package' => $package,
        ]);
    }
}
