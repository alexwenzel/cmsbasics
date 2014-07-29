<h1>create {{ $resource_name }}</h1>

{{ Form::open(['route'=>$resource_name.'.store', 'method'=>'post']) }}
@include($view_dir.'.form')
{{ Form::submit('create') }}
{{ Form::close() }}