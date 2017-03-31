@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">

            @include('bhu._partials._alert')

            <div class="panel panel-default">
                <div class="panel-heading">Change Password</div>

                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-5 col-xs-5">
                            {!! Form::open(['route' => 'admin.post_change_password', 'method' => 'POST', 'role' => 'form']) !!}

                            <div class="form-group">
                                <label for="password">New Password</label>
                                {!! Form::password('password',['class' => 'form-control', 'id' => 'password']) !!}
                                @if ($errors->has('password'))
                                    <span class="help-block text-danger">
                                        <p class="text-danger">{{ $errors->first('password') }}</p>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Confirm New Password</label>
                                {!! Form::password('password_confirmation',['class' => 'form-control', 'id' => 'password_confirmation']) !!}
                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <p class="text-danger">{{ $errors->first('password_confirmation') }}</p>
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">
                                <span class="glyphicon glyphicon-backward"></span>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary pull-right" style="margin-bottom: 10px;">
                                <span class="glyphicon glyphicon-ok-circle"></span>
                                Change Password
                            </button>

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
