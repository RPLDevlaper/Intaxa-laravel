<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function onSuccess($dataType, $data, $requestType = null)
    {
        if($requestType != null) {
            return response()->json([
                'status' => "$dataType $requestType",
                'code' => '200',
                'data' => $data
            ]);
        }
        if($data == null) {
            return response()->json([
                'status' => "$dataType Not Found",
                'code' => '404',
                'message' => "Data $dataType belum ada",
                'data' => $data
            ]);
        }
        return response()->json([
            'status' => "$dataType Founded",
            'code' => '200',
            'message' => "Data $dataType ditemukan",
            'data' => $data
        ]);
    }

    public function onError(\Exception $e)
    {
        if($e instanceof ClientException) {
            $nException = json_decode($e->getResponse()->getBody()->getContents(), true);
            if($nException) {
                $e = new \Exception($nException['reason'], $nException['code']);
            }
        }
        $exceptions = [
            'status' => 'Failed Error',
            'code' => $e->getCode(),
            'error' => $e->getMessage(),
        ];
        return response()->json($exceptions);
    }
}
