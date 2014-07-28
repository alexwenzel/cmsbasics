<?php namespace Alexwenzel\Cmsbasics\Controllers;

// use services\Flashmessage;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

abstract class Resource extends \BaseController {

    /**
     * Dependency
     * @var mixed
     */
    protected $dependency;

    /**
     * Name of the resource
     * @var string
     */
    protected $resource_name;

    /**
     * Constructor
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->dependency    = $settings['dependency'];
        $this->resource_name = $settings['resource_name'];
    }

    /**
     * Updates the "unique" rule and adds the resource ID.
     * @param  mixed  $id
     * @param  array  $rules
     * @return array
     */
    protected function _update_unique_rule($id, array $rules)
    {
        $updatedRules = [];

        foreach ($rules as $key => $values) {
            foreach (explode('|', $values) as $rule) {

                // prüfe auf die richtige regel
                if ( stripos($rule, 'unique:') === 0 ) {
                    $rule = $rule . ','.$id;
                }

                $updatedRules[$key][] = $rule;
            }
        }
        
        return $updatedRules;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        // fire event
        Event::fire($this->resource_name.'.index');

        // display listing
        return View::make($this->resource_name.'.index', [
            'items'         => $this->_index_items(),
            'resource_name' => $this->resource_name,
        ]);
    }

    /**
     * Collects the data for index view
     * @return Collection
     */
    protected function _index_items()
    {
        return $this->dependency->all();
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        // fire event
        Event::fire($this->resource_name.'.create');

        return View::make($this->resource_name.'.create', [
            'resource_name' => $this->resource_name,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @return Response
     */
    public function store()
    {
        // collect data
        $data = $this->_store_data();

        // create validator
        $validator = $this->_store_validator($data);

        // validate
        if ($validator->fails())
        {
            // call to action
            return $this->_store_fails($data, $validator);
        }

        // store data
        $model = $this->dependency->create($data);

        // fire event
        Event::fire($this->resource_name.'.store', array($model));

        // call to action
        return $this->_store_finished($data, $model);
    }

    /**
     * Returns the data, used for the store process.
     * @return array
     */
    protected function _store_data()
    {
        return Input::all();
    }

    /**
     * Returns the validator, used for the store process.
     * @return Illuminate\Validation\Validator
     */
    protected function _store_validator($data)
    {
        $classname = get_class($this->dependency);
        return Validator::make($data, $classname::$rules);
    }

    /**
     * Is executed, when the store validation fails.
     * @param  array $data
     * @param  Validator $validator
     * @return Response
     */
    protected function _store_fails($data, $validator)
    {
        return Redirect::back()->withErrors($validator)->withInput();
    }

    /**
     * Is executed, when the store process is finished.
     * @param  array $data
     * @param  mixed $model
     * @return Response
     */
    protected function _store_finished($data, $model)
    {
        return Redirect::route($this->resource_name.'.index');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $item = $this->dependency->findOrFail($id);

        // fire event
        Event::fire($this->resource_name.'.show', array($item));

        return View::make($this->resource_name.'.show', [
            'item' => $item,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $item = $this->dependency->findOrFail($id);

        // fire event
        Event::fire($this->resource_name.'.edit', array($item));

        return View::make($this->resource_name.'.edit', [
            'item' => $item,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        // look for the model
        $model = $this->dependency->findOrFail($id);

        // collect data
        $data = $this->_update_data();

        // create validator
        $validator = $this->_update_validator($id, $data);

        // validate
        if ($validator->fails())
        {
            // call to action
            return $this->_update_fails($data, $model, $validator);
        }

        // update data
        $model->update($data);

        // fire event
        Event::fire($this->resource_name.'.update', array($model));

        // call to action
        return $this->_update_finished($data, $model);
    }

    /**
     * Returns the data, used for the update process.
     * @return array
     */
    protected function _update_data()
    {
        return Input::all();
    }

    /**
     * Returns the data, used for the update process.
     * @param  string $id
     * @param  array $data
     * @return Illuminate\Validation\Validator
     */
    protected function _update_validator($id, $data)
    {
        // dependency namen suchen
        $classname = get_class($this->dependency);

        // update the "unique" rule
        $rules = $this->_update_unique_rule($id, $classname::$rules);

        return Validator::make($data, $rules);
    }

    /**
     * Is executed, when the update validation fails.
     * @param  array $data
     * @param  Model $model
     * @param  Validator $validator
     * @return Response
     */
    protected function _update_fails($data, $model, $validator)
    {
        return Redirect::back()->withErrors($validator)->withInput();
    }

    /**
     * Is executed, when the update process is finished.
     * @param  array $data
     * @param  Model $model
     * @return Response
     */
    protected function _update_finished($data, $model)
    {
        return Redirect::route($this->resource_name.'.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param  array|int  $ids
     * @return Response
     */
    public function destroy($ids)
    {
        // destroy data
        $this->dependency->destroy($ids);

        // fire event
        Event::fire($this->resource_name.'.destroy');

        // call to action
        return $this->_destroy_finished();
    }

    /**
     * Is executed, when the destroy process is finished.
     * @return Response
     */
    protected function _destroy_finished()
    {
        return Redirect::route($this->resource_name.'.index');
    }
}
