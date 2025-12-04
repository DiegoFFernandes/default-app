@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <h6 class="text-muted">Olá seja bem vindo(a), {{ $user_auth->name }}!</h6>
                    </div>
                </div>
            </div>
            <!-- /.row -->
    </section>
@stop
