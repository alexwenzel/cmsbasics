<h1>{{ $resource_name }}</h1>
<p>{{ link_to_action($resource_name.'.create', 'create') }}</p>
<table>
@foreach ($items as $item)
<tr>
<td>{{ $item->title }}</td>
<td>{{ $item->body }}</td>
<td>{{ link_to_action($resource_name.'.show', 'show', [$item->id]) }}</td>
<td>{{ link_to_action($resource_name.'.edit', 'edit', [$item->id]) }}</td>
<td>
{{ Form::open(array('route'=>array($resource_name.'.destroy', $item->id), 'method'=>'delete')) }}
<button type="submit" class="btn btn-xs btn-danger">Delete</button>
{{ Form::close() }}
</td>
</tr>
@endforeach
</table>