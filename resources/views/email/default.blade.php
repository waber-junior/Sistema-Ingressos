@component('mail::message')
# Olá {{ $user->name }}!
 
<p>{{ $mesage }}</p>
 
@component('mail::button', ['url' => $url])
Visite o nosso site
@endcomponent
 
Obrigado,<br>
Casabella
@endcomponent