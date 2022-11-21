<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\DataType;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Http\Controllers\Controller;
use TCG\Voyager\Database\Schema\SchemaManager;

class VoyagerNestedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dataType = $this->getDataType();

        if ($request->has('fix-tree')) {
            $dataType->model->fixTree();
        }

        $slug = $this->getSlug($request);
        $items = $dataType->model->withDepth()->get()->toTree();

        $view = view()->exists("voyager::$slug.nested")
            ? "voyager::$slug.nested"
            : "voyager::bread.nested";

        return Voyager::view($view, [
            'dataType' => $dataType,
            'items' => $items,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $dataType = $this->getDataType();
        $slug = $this->getSlug(request());

        $items = $dataType->model->descendantsAndSelf($id)
            ->toTree();

        $view = view()->exists("voyager::$slug.nested")
            ? "voyager::$slug.nested"
            : "voyager::bread.nested";

        return Voyager::view($view, [
            'dataType' => $dataType,
            'items' => $items,
            'root' => $id
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dataType = $this->getDataType();
        $requiredFileds = $dataType->requiredRows->pluck('field')->toArray();

        $validation = [];
        foreach ($requiredFileds as $filed) {
            $validation[$filed] = 'required';
        }

        $request->validate($validation);

        if ($parentId = $request->input('parent_id', false)) {
            try {
                $dataType->model->findOrFail($parentId);
                $requiredFileds[] = 'parent_id';
            } catch (\Exception $e) {
            }
        }

        try {
            $fileds = $request->only($requiredFileds);

            if (! $dataType->rows->where('field', 'slug')->isEmpty() && ! isset($fileds['slug'])) {
                $slug = trim($fileds['name']) === '' ? strtotime('now') . mt_rand(0, 10) : $fileds['name'];
                $fileds['slug'] = Str::slug($slug);
            }

            $dataType->model->create($fileds);

            return back()->with([
                'message'    => 'New item created successfully',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'message'    => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $dataType = $this->getDataType();

        $fileds = $request->only(
            $dataType->requiredRows->pluck('field')->toArray()
        );

        try {
            $dataType->model->where('id', $request->input('id'))->update($fileds);
            return back()->with([
                'message'    => 'Item updated successfully',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'message'    => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Update display order for the resource
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function order(Request $request)
    {
        $request->validate([
            'order' => 'required'
        ]);

        $dataType = $this->getDataType();
        $model = $dataType->model;
        $tree = json_decode($request->input('order'), true);

        if (trim($request->input('root')) === '') {
            $this->orderItems($dataType, $tree);
            return $model->rebuildTree($tree);
        }

        $root = $request->input('root');
        $tree = $tree[0]['children'] ?? [];

        $this->orderItems($dataType, $tree);
        return $model->rebuildSubtree($model->findOrFail($root), $tree);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'slug' => 'required'
        ]);

        try {
            $dataType = $this->getDataType();

            $model = $dataType->model
                ->findOrFail($request->input('id'));

            $model->delete();

            return back()->with([
                'message'    => 'Item deleted successfully',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'message'    => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * @param  Request $request
     * @return DataType
     */
    protected function getDataType()
    {
        $slug = $this->getSlug(request());

        $dataType = DataType::where('slug', $slug)->first();

        $this->authorize('browse', app($dataType->model_name));

        return $dataType;
    }

    /**
     * @param  mixed $dataType
     * @param  mixed $tree
     * @return void
     */
    protected function orderItems($dataType, $tree)
    {
        $orderColumn = $dataType->details->order_column ?? '';

        if ($orderColumn === '') {
            return false;
        }

        $table = $dataType->model->getTable();

        $columns = array_keys(collect(SchemaManager::describeTable($table))->toArray());

        try {
            if (! in_array($orderColumn, $columns)) {
                $orderColumn = 'sort_order';
                throw new \Exception("Invalid order column");
            }
        } catch (\Exception $e) {
            if (! in_array($orderColumn, $columns)) {
                return false;
            }
        }

        $query = [];
        $ids = Arr::flatten($tree);

        foreach ($ids as $order => $id) {
            $query[] = " WHEN id = " . q($id) . " THEN " . q($order) . " ";
        }

        if (empty($query)) {
            return false;
        }

        $query = "
            UPDATE {$table} SET {$orderColumn} = 
                CASE 
                ".implode(PHP_EOL, $query)."
                END
            WHERE id IN (".implode(', ', array_map('q', $ids)).")";

        return DB::unprepared($query);
    }
}
