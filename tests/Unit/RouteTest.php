<?php

namespace Tests\Unit;

use Illuminate\Routing\RouteCollection;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RouteTest extends TestCase
{
    use WithoutMiddleware;
    
    protected $testRoutes;
    
    public function setUp()
    {
        parent::setUp();
        $this->testRoutes = [
            'GET' => [
                'api/feed/settings/' => '',
                'api/settings/' => 'dagfjaqy3u232dsha23223'
            ],
            'PUT' => [
                'api/feed/settings/' => [
                    'path_params' => '',
                    'request_params' => [
                        'user' => [
                            'id' => 1
                        ],
                        'pages' => [
                            '21313211',
                            '343334343',
                            '5632r54323'
                        ]
                    ]
                ],
                'api/settings/' => [
                    'path_params' => '',
                    'request_params' => [
                        'user' => [
                            'id' => 1
                        ],
                        'player_id' => 'dagfjaqy3u232dsha23223',
                        'reminder_type' => 1,
                        'reminder_first_at' => '12:00',
                        'reminder_second_at' => ''
                    ]
                ]
            ],
            'DELETE' => [
                'api/settings/' => [
                    'path_params' => 'dagfjaqy3u232dsha23223',
                    'request_params' => [
                        'user' => [
                            'id' => 1
                        ],
                    ]
                ]
            ],
            'POST' => [
            ]
        ];
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRoutes()
    {
        foreach ($this->testRoutes as $method => $testRoutes) {
            switch ($method) {
                case 'GET':
                    foreach ($testRoutes as $path => $param) {
                        $response = $this->json($method, '/' . $path . $param);
                        $response->assertJsonStructure(['error']);
                    }
                    break;
                case 'PUT':
                case 'POST':
                case 'DELETE':
                    foreach ($testRoutes as $path => $params) {
                        $response = $this->json($method, '/' . $path . $params['path_params'], $params['request_params']);
                        dump($response->getContent());
                        $response->assertJsonStructure(['message']);
                    }
                    break;
            }
        }
    }
}
