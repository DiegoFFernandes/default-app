@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <h2>Olá seja bem vindo(a), {{ $user_auth->name }}!</h2>
                    </div>
                </div>
            </div>
        </div>      
        <!-- /.row -->
    </section>
@stop


