<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Weidner\Goutte\GoutteFacade;

class ScrapingController extends Controller
{
    public function index() {



        $company_list = array();
        $title_list = array();
        $url_list = array();

        foreach (range(1,20) as $num){
            // タウンワークのWebサイトをスクレイピング
            $goutte = GoutteFacade::request('GET', 'https://townwork.net/oosaka/ct_ma06101/?page=' . $num);

            // 会社名を取得
            $goutte->filter('.job-lst-main-ttl-txt')->each(function ($node) use (&$company_list){
                $company_list[] = $node->text();
            });

            // タイトルを取得
            $goutte->filter('.job-lst-main-txt-lnk')->each(function ($node) use (&$title_list){
                $title_list[] = $node->text();
            });

            // URLを取得
            $goutte->filter('.job-lst-main-cassette-wrap .job-lst-box-wrap > a')->each(function ($node) use (&$url_list){
                $url_list[] = $node->attr('href');
            });

        }

        for ($i = 0; $i < count($company_list); $i++){
            $data[$i]['company'] = $company_list[$i];
            $data[$i]['title'] = $title_list[$i];
            $data[$i]['url'] = $url_list[$i];
            $data[$i]['created_at'] = Carbon::now();  // 現在の時刻を取得
            $data[$i]['updated_at'] = Carbon::now();
        }


        $time_start = microtime(true);

//        Job::truncate();

        // createメソッド
//        for ($i = 0; $i < count($data); $i++){
//            $model = new Job;
//            $model->create($data[$i]);
//        }

        // saveメソッド
//        for ($i = 0; $i < count($data); $i++){
//            $model = new Job;
//            $model->company = $data[$i]['company'];
//            $model->title = $data[$i]['title'];
//            $model->url = $data[$i]['url'];
//            $model->created_at = $data[$i]['created_at'];
//            $model->updated_at = $data[$i]['updated_at'];
//            $model->save($data[$i]);
//        }

        // insertメソッド
//        $model = new Job;
//        $model->insert($data);

        // updateOrCreateメソッド
        for ($i = 0; $i < count($data); $i++){
            $model = new Job;
            $model->company = $data[$i]['company'];
            $model->title = $data[$i]['title'];
            $model->url = $data[$i]['url'];
            $model->created_at = $data[$i]['created_at'];
            $model->updated_at = $data[$i]['updated_at'];
            $model->updateOrCreate(['company' => $data[$i]['company']], $data[$i]);
        }

        $time = microtime(true) - $time_start;

        return "{$time} 秒";
    }
}
