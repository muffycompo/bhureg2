@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">

            @include('bhu._partials._alert')

            <div class="panel panel-default">
                <div class="panel-heading">New Academic Transcript</div>

                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-5 col-xs-5">
                            {!! Form::open(['route' => 'admin.post_new_transcript', 'method' => 'POST', 'role' => 'form']) !!}

                            <div class="form-group">
                                <label for="matriculation_number">Matriculation #</label>
                                {!! Form::text('matriculation_number','',['class' => 'form-control', 'id' => 'matriculation_number']) !!}
                                @if ($errors->has('matriculation_number'))
                                    <span class="help-block text-danger">
                                        <p class="text-danger">{{ $errors->first('matriculation_number') }}</p>
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">
                                <span class="glyphicon glyphicon-backward"></span>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary pull-right" style="margin-bottom: 10px;">
                                <span class="glyphicon glyphicon-ok-circle"></span>
                                Generate Transcript
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
