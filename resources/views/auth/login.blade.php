@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading text-center">Students Course Registration Portal</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('regno') ? ' has-error' : '' }}">
                            <label for="regno" class="col-md-4 control-label">Matriculation #</label>

                            <div class="col-md-6">
                                <input id="regno" type="text" class="form-control" name="regno" value="{{ old('regno') }}" required autofocus>

                                @if ($errors->has('regno'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('regno') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('pin') ? ' has-error' : '' }}">
                            <label for="pin" class="col-md-4 control-label">PIN</label>

                            <div class="col-md-6">
                                <input id="pin" type="password" class="form-control" name="pin" required>

                                @if ($errors->has('pin'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('pin') }}</strong>
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
