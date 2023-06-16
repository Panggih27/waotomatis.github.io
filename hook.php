<?php 

// Webhooks are the same as auto-reply,
// but in webhook you can fetch data dynamically, you can do query to database before response.
  // this is an example of a webhook via PHP, you can do it in any language
 header('content-type: application/json');
 $data = json_decode(file_get_contents('php://input'), true);
 file_put_contents('whatsapp.txt', '[' . date('Y-m-d H:i:s') . "]\n" . json_encode($data) . "\n\n", FILE_APPEND);                                               $message = strtolower($data['message']); // ini menangkap pesan masuk
 $from = $data['from']; // ini menangkap nomor pengirim pesan
 $respon = false;
                                                    
                                                    
 function sayHello(){
 // you can query database and logic  here!
 // query database atau logika bisa dilakukan disini
                                                    
 return ["text" => 'Halloooo!'];
             }
function gambar(){
 // you can query database and logic  here!
 // query database atau logika bisa dilakukan disini
 return [
     'image' => ['url' => 'https://seeklogo.com/images/W/whatsapp-logo-A5A7F17DC1-seeklogo.com.png'],
     'caption' => 'Logo whatsapp!'
 ];
                                                    }
 function button(){
     // you can query database and logic  here!
     // query database atau logika bisa dilakukan disini
     
     // maximal 3 button
     $buttons = [
         ['buttonId' => 'id1', 'buttonText' => ['displayText' => 'BUTTON 1'], 'type' => 1], // button 1 // 
         ['buttonId' => 'id2', 'buttonText' => ['displayText' => 'BUTTON 2'], 'type' => 1], // button 2
         ['buttonId' => 'id3', 'buttonText' => ['displayText' => 'BUTTON 3'], 'type' => 1], // button 3
     ];
 
     $buttonMessage = [
         'text' => 'HOLA, INI ADALAH PESAN BUTTON', // pesan utama nya
         'footer' => 'ini pesan footer', // pesan footernya, 
         'buttons' => $buttons,
         'headerType' => 1 // biarkan default
     ];
 
     return $buttonMessage;
 }
 
 
 
 if($message === 'hai'){
     $respon = sayHello();
 } else if($message === 'gambar'){
     $respon = gambar();
 } else if($message === 'tes button'){
     $respon = button();
 }
 echo json_encode($respon);
?>