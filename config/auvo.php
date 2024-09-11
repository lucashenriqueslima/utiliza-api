<?php

// AUVO_API_URL=https://api.auvo.com.br/v2

// AUVO_API_KEY_INSPECTION=W4B7ASF3EjRpFcTLiNGRpcq0XWTxMBC
// AUVO_API_TOKEN_INSPECTION=W4B7ASF3EgMVc0hmBrLTbkAATOhEPGc

// AUVO_API_KEY_EXPERTISE=rcCr6y33EiDxtkSOU3FS57U3kqsaEfh
// AUVO_API_TOKEN_EXPERTISE=SlMDr6y33EhnFUb7Eo7SordjoHfzkIw

// AUVO_API_KEY_TRACKING=FuHS7qD3EhQdRwVm6bpR5oNLlOU7SN
// AUVO_API_TOKEN_TRACKING=LgnT7qD3Eg4J9FLfw5TR6C6IAWqmVBG

return [

    'api_url' => env('AUVO_API_URL', 'https://api.auvo.com.br/v2'),
    'inspection' => [
        'api_key' => env('AUVO_API_KEY_INSPECTION'),
        'api_token' => env('AUVO_API_TOKEN_INSPECTION'),
    ],
    'expertise' => [
        'api_key' => env('AUVO_API_KEY_EXPERTISE',),
        'api_token' => env('AUVO_API_TOKEN_EXPERT')
    ],
    'tracking' => [
        'api_key' => env('AUVO_API_KEY_TRACKING'),
        'api_token' => env('AUVO_API_TOKEN_TRACKING'),
    ],
];
