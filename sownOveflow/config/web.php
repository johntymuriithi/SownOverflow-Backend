<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest'], // MY DEFAULT
        ],
        // only this
//        'corsFilter' => [
//            'class' => \yii\filters\Cors::class,
//            'cors' => [
//                'Origin' => ['*'], // Adjust as needed
//                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
//                'Access-Control-Allow-Credentials' => true,
//                'Access-Control-Max-Age' => 3600, // Cache for 1 hour
//                'Access-Control-Allow-Headers' => ['Content-Type', 'Authorization'],
//            ],
//        ],
        'response' => [
            // ...
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                $response->headers->set('Access-Control-Allow-Origin',  '*');
                $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS'); // Allow all common HTTP methods
                $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With'); // Allow 'Content-Type' and 'Authorization' headers
            },
        ],

        'request' => [
            'cookieValidationKey' => '6ZoDxNQWJLagz3dhQzchvQ1966rRhudE',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false, // Set to true if you want to use cookie-based auth
            'enableSession' => false, // For stateless authentication like JWT
            'loginUrl' => null, // No redirect for API requests
        ],
        'errorHandler' => [
            // Set a custom error handler if not using site/error
            'errorAction' => null,
        ],
        'mailer' => [
            'class' => 'yii\symfonymailer\Mailer',
            'transport' => [
                'scheme' => 'smtp',
                'host' => 'smtp-relay.brevo.com',
                'username' => '75362d001@smtp-brevo.com',
                'password' => '2hM3OLNErSp15RJT',
                'port' => 587,
                'encryption' => 'tls',
            ],
            'useFileTransport' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'hostInfo' => 'https://e8b0-41-90-101-26.ngrok-free.app/', // very important for frontend
            'rules' => [
                'POST user/signup' => 'user/signup',
                'POST user/login' => 'user/login',
                'POST add/category' => 'categories/categoryadd',
                'POST post/question' => 'questions/questionpost',
                'POST post/answer' => 'answers/answerpost',
                'POST answer/edit' => 'answers/answeredit',
                'POST answer/delete' => 'answers/answerdelete',

                'GET users/total' => 'user/userstotal',
                'GET show/categories' => 'categories/showcategory',
                'GET show/questions' => 'questions/showquestions',
                'POST question/edit' => 'questions/questionedit',
                'GET show/answers' => 'answers/showanswers',
                'GET show/questions/byCategory' => 'analysis/getquestions',
                'GET show/siteinfo' => 'analysis/siteinfo',

                'GET show/user/answers' => 'analysis/getquestionsansweredbyuser',
                'GET show/user/questions' => 'analysis/getquestionsbyuser',


                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
