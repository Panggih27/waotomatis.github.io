<?php

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Tag;

if (!function_exists('campaignFormatMessage')) {
    /**
     *
     * @param  Campaign  $campaign
     * @return \Illuminate\Http\Request|string|array|null
     */
    function campaignFormatMessage(Campaign $campaign)
    {
        $data = [
            'campaign' => $campaign->id,
            'sender' => $campaign->number->body,
        ];

        switch ($campaign->receivers->type) {
            case 'all':
                $data = array_merge($data, [
                    'receivers' => Contact::whereBelongsTo(auth()->user())->get()->map(function ($contact) use ($campaign) {
                        if (strpos($campaign->template->text, '{name}')) {
                            return [
                                'name' => $contact->name,
                                'number' => $contact->number,
                                'message' => str_replace('{name}', $contact->name, $campaign->template->text),
                            ];
                        } else {
                            return [
                                'name' => $contact->name,
                                'number' => $contact->number,
                                'message' => $campaign->template->text,
                            ];
                        }
                    })->toArray(),
                ]);
                break;
            case 'tag':
                $data = array_merge($data, [
                    'receivers' => Tag::with('contacts')->find($campaign->receivers->tag)->contacts->map(function ($contact) use ($campaign) {
                        if (strpos($campaign->template->text, '{name}')) {
                            return [
                                'name' => $contact->name,
                                'number' => $contact->number,
                                'message' => str_replace('{name}', $contact->name, $campaign->template->text),
                            ];
                        } else {
                            return [
                                'name' => $contact->name,
                                'number' => $contact->number,
                                'message' => $campaign->template->text,
                            ];
                        }
                    })->toArray(),
                ]);
                break;
            case 'contact':
                $data['receivers'] = collect($campaign->receivers->data)->map(function ($contact) use ($campaign) {
                    if (strpos($campaign->template->text, '{name}')) {
                        return [
                            'name' => $contact->name,
                            'number' => $contact->number,
                            'message' => str_replace('{name}', $contact->name, $campaign->template->text),
                        ];
                    } else {
                        return [
                            'name' => $contact->name,
                            'number' => $contact->number,
                            'message' => $campaign->template->text,
                        ];
                    }
                })->toArray();
                break;
            default:
                session()->flash('alert', [
                    'type' => 'danger',
                    'msg' => 'receiver type is not valid!'
                ]);
                return redirect(route('campaign.index'));
                break;
        }

        if (!is_null($campaign->template->template)) {
            $data['type'] = 'template';
            $data['body']['footer'] = $campaign->template->template->footer;

            if (count($campaign->template->template->templateButtons) > 1) {
                $typeButton1 = $campaign->template->template->templateButtons[0]->type;
                $typeButton2 = $campaign->template->template->templateButtons[1]->type;
                $data['body']['data'] = [
                    [
                        'index' => 1,
                        $typeButton1 . 'Button' => [
                            'displayText' => $campaign->template->template->templateButtons[0]->displayText,
                            ($typeButton1 == 'call' ? 'phoneNumber' : $typeButton1) => ($typeButton1 == 'call' ? '+' : '') . $campaign->template->template->templateButtons[0]->action
                        ]
                    ],
                    [
                        'index' => 2,
                        $typeButton2 . 'Button' => [
                            'displayText' => $campaign->template->template->templateButtons[1]->displayText,
                            ($typeButton2 == 'call' ? 'phoneNumber' : $typeButton2) => ($typeButton2 == 'call' ? '+' : '') . $campaign->template->template->templateButtons[1]->action
                        ]
                    ],
                ];
            } else {
                $typeButton1 = $campaign->template->template->templateButtons[0]->type;
                $data['body']['data'] = [
                    [
                        'index' => 1,
                        $typeButton1 . 'Button' => [
                            'displayText' => $campaign->template->template->templateButtons[0]->displayText,
                            $typeButton1 => $campaign->template->template->templateButtons[0]->action
                        ]
                    ]
                ];
            }
        } elseif (!is_null($campaign->template->media)) {

            $data['type'] = $campaign->template->media->type;
            $data['body']['data'] = [
                'image' => [
                    'url' => $campaign->template->media->url,
                ]
            ];
        } elseif (!is_null($campaign->template->button)) {
            $data['type'] = 'button';
            $data['body']['footer'] = $campaign->template->button->footer;
            $data['body']['data'] = collect($campaign->template->button->buttons)->map(function ($button) {
                return [
                    'index' => $button->index,
                    'buttonText' => [
                        'displayText' => $button->displayText
                    ],
                    'type' => 1,
                ];
            })->toArray();
        } else {
            $data['type'] = 'text';
            $data['body']['data'] = ['text' => $campaign->template->text];
        }

        return $data;
    }
}

if (!function_exists('extractWord')) {
    function extractWord(string $text, string $res): string
    {
        preg_match_all('/(?<!{){[^{}]+?}(?!})/', $text, $matches);

        $result = [];

        foreach ($matches[0] as $key => $item) {
            if (strpos($item, '|') > 0) {
                $exploded = explode('|', str_replace(['{', '}'], '', $item));
                $set = array_rand($exploded, 1);

                $result[] = $exploded[$set];
            } else {
                $result[] = $matches[0][$key];
            }
        }

        return str_replace($matches[0], $result, $res);
    }
}

if (!function_exists('getTile')) {
    function getTile($lat, $long, $zoom)
    {
        $xtile = floor((($long + 180) / 360) * pow(2, $zoom));
        $ytile = floor((1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / pi()) / 2 * pow(2, $zoom));

        return ['x' => $xtile, 'y' => $ytile];
    }
}

if (!function_exists('getLongLat')) {
    function getLongLat($xtile, $ytile, $zoom)
    {
        $n = pow(2, $zoom);
        $lon_deg = $xtile / $n * 360 - 180;
        $lat_deg = rad2deg(atan(sinh(pi() * (1 - 2 * $ytile / $n))));

        return ['lat' => $lat_deg, 'long' => $lon_deg];
    }
}

if (!function_exists('getBbox')) {
    function getBbox($lat, $long)
    {
        $zoom = 14;
        $width = 300;
        $height = 125;
        $tile_size = 256;

        $tile = getTile($lat, $long, $zoom);

        $xtile_s = ($tile['x'] * $tile_size - $width / 2) / $tile_size;
        $ytile_s = ($tile['y'] * $tile_size - $height / 2) / $tile_size;
        $xtile_e = ($tile['x'] * $tile_size + $width / 2) / $tile_size;
        $ytile_e = ($tile['y'] * $tile_size + $height / 2) / $tile_size;

        $s = getLongLat($xtile_s, $ytile_s, $zoom);
        $e = getLongLat($xtile_e, $ytile_e, $zoom);

        $bbox =  $s['long'] . ',' . $s['lat'] . ',' . $e['long'] . ',' . $e['lat'];
        return $bbox;
    }
}


?>