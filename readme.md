# my cms basics for laravel

A package for Laravel, which I use to build repetitive resource Items.

## Model

Create a model for your new resource item.

The resource model should have a static ``$rules`` property, which contain all resource rules.

````php
class Post extends \Eloquent {

  protected $table = 'posts';
  protected $fillable = ['title', 'body'];

  public static $rules = [
    'title' => 'required',
    'body' => 'required',
  ];
}
````

## Controller

Create a new controller by extending the base resource controller. In the constructor you can specify some settings.

* ``dependency`` => your resource item's model
* ``resource_name`` => the identifier of your resource, the one you use at ``Route::resource()``
* ``view_dir`` => path to the rescource views

````php
use Alexwenzel\Cmsbasics\Controllers\Resource;

class PostsController extends Resource {

  public function __construct(Post $model)
  {
    parent::__construct([
      'dependency'    => $model,
      'resource_name' => 'posts',
      'view_dir'      => 'path.to.views',
    ]);
  }
}
````

Then register the controller in your routes.

````php
Route::resource('posts', 'PostsController');
````

### Customize behaviour

You cann customize the behaviour of the base resource controller by overriding specific methods

````php
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

### Events

The following Events are fired within the base resource controller:

**index**

````
[resource_name].index
````

**create**

````
[resource_name].create
````

**store**

````
[resource_name].store
````

Passes the newly created resource as the first argument.

**show**

````
[resource_name].show
````

Passes the requested resource as the first argument.

**edit**

````
[resource_name].edit
````

Passes the requested resource as the first argument.

**update**

````
[resource_name].update
````

Passes the updated resource as the first argument.

**destroy**

````
[resource_name].destroy
````

## View

This package comes with default views for all actions (index, create, show, edit).
 Publish the package views as a starting point.

````
php artisan view:publish alexwenzel/cmsbasics
````

Copy the package views to a new folder and customize them.

````php
class PostsController extends Resource {

  public function __construct(Post $model)
  {
    parent::__construct([
      'dependency'    => $model,
      'resource_name' => 'posts',
      'view_dir'      => 'path.to.views',
    ]);
  }
}
````
