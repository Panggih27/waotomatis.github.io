<?php


use Illuminate\Support\Facades\Route;

Route::get('generate', function (){
  \Illuminate\Support\Facades\Artisan::call('storage:link');
  echo 'ok';
});
?>