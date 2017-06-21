@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="text-center">
                <img src="{{ asset('images/bhu-logo.png') }}" style="height: 100px; margin-bottom: 20px;" alt="Bingham University Logo">
            </div>
            <div class="panel panel-default">
                <div class="panel-heading text-center">Admin Staff Portal</div>
                <div class="panel-body">
                    @if(session()->has('admin_error'))
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <p>{{ session('admin_error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{--<form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">--}}
                    {!! Form::open(['route' => 'admin.post_login', 'method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form']) !!}
                    {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                            <label for="username" class="col-md-4 control-label">Username</label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required autofocus>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="pin" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-log-in"></span>
                                    Login
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
