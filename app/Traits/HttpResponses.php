<?php

namespace App\Traits;

use Illuminate\Contracts\Support\MessageBag;

trait HttpResponses 
{
    public function response(array $data = [], string $message = '', int $status = 200)
    {   
        $reponseDatas = [];
        
        if($message) {
            $reponseDatas['message'] = $message;
        }

        if($data) {
            $reponseDatas['data'] = $data;
        }
            
        return response()->json($reponseDatas, $status);
    }


    public function errorResponse(string $message, int $status, array|MessageBag $errors = [])
    {
        $reponseDatas = [];

        if($message) {
            $reponseDatas['message'] = $message;
        }

        if($errors) {
            $reponseDatas['errors'] = $errors;
        }

        return response()->json($reponseDatas, $status);
    }
}