@if($errors->all())

    @foreach($errors->all() as $message)
        <div class="alert alert-warning alert-dismissible mb-0 border-radius-0">
            {{ $message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endforeach

@elseif(session()->has('message'))

    <div class="alert alert-success alert-dismissible mb-0 border-radius-0">
        {{ session()->get('message') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    
@elseif(session()->has('error'))
    
    <div class="alert alert-danger alert-dismissible mb-0 border-radius-0">
        {{ session()->get('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    
@endif