# cms basics

## Controller

Create a new controller:

````
use Alexwenzel\Cmsbasics\Controllers\Resource;

class PostsController extends Resource {

    public function __construct(Post $model)
    {
        parent::__construct([
            'dependency'    => $model,
            'resource_name' => 'posts'
        ]);
    }
}
````

Register the controller:

````
Route::resource('posts', 'PostsController');
````

### Customization

````
protected function _index_items()
protected function _store_data()
protected function _store_validator($data)
protected function _store_fails($data, $validator)
protected function _store_finished($data, $model)
protected function _update_data()
protected function _update_validator($id, $data)
protected function _update_fails($data, $model, $validator)
protected function _update_finished($data, $model)
protected function _destroy_finished()
````

## Example Index View

````
<table>
@foreach ($items as $item)
<tr>
<td>{{ $item->title }}</td>
<td>{{ $item->body }}</td>
<td>{{ link_to_action('posts.edit', 'edit', [$item->id]) }}</td>
<td>
{{ Form::open(array('route'=>array('posts.destroy', $item->id), 'method'=>'delete')) }}
<button type="submit" class="btn btn-xs btn-danger">Delete</button>
{{ Form::close() }}
</td>
</tr>
@endforeach
</table>
````