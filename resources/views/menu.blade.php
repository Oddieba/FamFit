@extends('layouts.quest')

@section('title', 'Inicio | FamFit Quest')

@section('content')
    <header class="fq-page-heading">
        <div>
            <p class="fq-eyebrow">TU ESPACIO FAMFIT</p>
            <h1>¡Hola, {{ auth()->user()->name }}! <span aria-hidden="true"></span></h1>
            <p class="fq-muted">Pequeños hábitos, grandes logros. ¿Qué te gustaría hacer hoy?</p>
        </div>
       
    </header>
 <body>







    
 </body>
   
@endsection