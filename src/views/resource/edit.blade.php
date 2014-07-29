<h1>edit {{ $resource_name }}</h1>

{{ Form::model($item, ['route'=>[$resource_name.'.update', $item->id], 'method'=>'put']) }}
@include($view_dir.'.form')
{{ Form::submit('edit') }}
{{ Form::close() }}