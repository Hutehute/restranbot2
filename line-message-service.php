<?php

class LineMessageService {

  private $accessToken;

  public function __construct($accessToken) {
     $this->accessToken = $accessToken;
  }

  public function postFlexMessage($token, $param) {
        $postJson = $this->createJsonParameter($token, $param);
        return $this->postMessage($postJson);
  }

  private function createJsonParameter($token, $list) {
    $messages = $this->generateFlexMessageContents($list);
    $result = ['replyToken'=>$token, 'messages'=>[$messages]];
    return json_encode($result);
  }

  private function postMessage($jsonParam) {
    $conn = curl_init();

    curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($conn, CURLOPT_POST, true);
    curl_setopt($conn, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->accessToken,'Content-type: application/json'));
    curl_setopt($conn, CURLOPT_URL,  'https://api.line.me/v2/bot/message/reply');
    curl_setopt($conn, CURLOPT_POSTFIELDS, $jsonParam);

    $result = curl_exec($conn);

    curl_close($conn);

    return $result;
  }

  private function generateFlexMessageContents($list) {
      $carouselItem = [];
      foreach ($list as $taberogu) {
           $carouselItem[] = $this->getFlexTemplate($taberogu);
      }
      $contents = ["type"=>"carousel","contents"=>$carouselItem];
      return ['type'=>'flex', 'altText'=>'search', 'contents'=>$contents];
  }

  private function getStarImg($rating, $seq) {
      if ($rating >= $seq) {
        return "https://scdn.line-apps.com/n/channel_devcenter/img/fx/review_gold_star_28.png";
      }
      return "https://scdn.line-apps.com/n/channel_devcenter/img/fx/review_gray_star_28.png";
  }

  private function getFlexTemplate($taberogu) {
   $ratingInt = round($taberogu->{'rating'});
   $distance = round($taberogu->{'distance'}*1000);
   return  [
     "type"=> "bubble",
     "hero"=> [
       "type"=> "image",
       "url"=> $taberogu->{'image_url'},
       "size"=> "full",
       "aspectRatio"=> "20:13",
       "aspectMode"=> "cover",
       "action"=> [
         "type"=> "uri",
         "uri"=> $taberogu->{'url'}
       ]
     ],
     "body"=> [
       "type"=> "box",
       "layout"=> "vertical",
       "contents"=> [
         [
           "type"=> "text",
           "text"=> $taberogu->{'name'},
           "weight"=> "bold",
           "size"=> "xl"
         ],
         [
           "type"=> "box",
           "layout"=> "baseline",
           "margin"=> "md",
           "contents"=> [
             [
               "type"=> "icon",
               "size"=> "sm",
               "url"=> $this->getStarImg($ratingInt, 1)
             ],
             [
               "type"=> "icon",
               "size"=> "sm",
               "url"=> $this->getStarImg($ratingInt, 2)
             ],
             [
               "type"=> "icon",
               "size"=> "sm",
               "url"=> $this->getStarImg($ratingInt, 3)
             ],
             [
               "type"=> "icon",
               "size"=> "sm",
               "url"=> $this->getStarImg($ratingInt, 4)
             ],
             [
               "type"=> "icon",
               "size"=> "sm",
               "url"=> $this->getStarImg($ratingInt, 5)
             ],
             [
               "type"=> "text",
               "text"=> $taberogu->{'rating'},
               "size"=> "sm",
               "color"=> "#999999",
               "margin"=> "md",
               "flex"=> 0
             ]
           ]
         ],
         [
           "type"=> "box",
           "layout"=> "vertical",
           "margin"=> "lg",
           "spacing"=> "sm",
           "contents"=> [
             [
               "type"=> "box",
               "layout"=> "baseline",
               "spacing"=> "sm",
               "contents"=> [
                 [
                   "type"=> "text",
                   "text"=> "種類",
                   "color"=> "#aaaaaa",
                   "size"=> "sm",
                   "flex"=> 1
                 ],
                 [
                   "type"=> "text",
                   "text"=> $taberogu->{'service'},
                   "wrap"=> true,
                   "color"=> "#666666",
                   "size"=> "sm",
                   "flex"=> 5
                 ]
               ]
             ],
             [
               "type"=> "box",
               "layout"=> "baseline",
               "spacing"=> "sm",
               "contents"=> [
                 [
                   "type"=> "text",
                   "text"=> "場所",
                   "color"=> "#aaaaaa",
                   "size"=> "sm",
                   "flex"=> 1
                 ],
                 [
                   "type"=> "text",
                   "text"=> $taberogu->{'street'}.' ('.$distance.'m)',
                   "wrap"=> true,
                   "color"=> "#666666",
                   "size"=> "sm",
                   "flex"=> 5
                 ]
               ]
             ]
             // [ 仕様変更により取得できなくなったので閉じる
             //   "type"=> "box",
             //   "layout"=> "baseline",
             //   "spacing"=> "sm",
             //   "contents"=> [
             //     [
             //       "type"=> "text",
             //       "text"=> "金額",
             //       "color"=> "#aaaaaa",
             //       "size"=> "sm",
             //       "flex"=> 1
             //     ],
             //     [
             //       "type"=> "text",
             //       "text"=> $taberogu->{'price'},
             //       "wrap"=> true,
             //       "color"=> "#666666",
             //       "size"=> "sm",
             //       "flex"=> 5
             //     ]
             //   ]
             // ]
           ]
         ]
       ]
     ],
     "footer"=> [
       "type"=> "box",
       "layout"=> "vertical",
       "spacing"=> "sm",
       "contents"=> [
         [
           "type"=> "button",
           "style"=> "link",
           "height"=> "sm",
           "action"=> [
             "type"=> "uri",
             "label"=> "食べログをみる",
             "uri"=> $taberogu->{'url'}
           ]
         ],
         [
           "type"=> "spacer",
           "size"=> "sm"
         ]
       ],
       "flex"=> 0
     ]
   ];
  }

}
