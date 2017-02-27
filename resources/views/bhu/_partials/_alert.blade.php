@if(session()->has('flash_message'))
    <div class="alert alert-{{ session('flash_type') }} alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ session('flash_message') }}
    </div>
@endif