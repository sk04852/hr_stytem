<?php
namespace App\Http\Controllers\Timelines\Traits;


trait TimelineTrait
{
    public function recordChanges($model, &$timeline_data, $relations = [])
    {
        if (empty($relations)) {
            foreach ($model->getDirty() as $index => $dirty_column) {
                $timeline_data['new_values'][$index] = $dirty_column;
                $timeline_data['old_values'][$index] = $model->getOriginal($index);
            }
        } else {
            foreach ($model->getDirty() as $index => $dirty_column) {
                if (array_key_exists($index, $relations)) {
                    $record_column_name = $relations[$index]['record_column_name'];
                    $timeline_data['new_values'][$index] = $relations[$index]['relation']::where($relations[$index]['identity_column'], '=', $dirty_column)->first()->$record_column_name;
                    $timeline_data['old_values'][$index] = $relations[$index]['relation']::where($relations[$index]['identity_column'], '=', $model->getOriginal($index))->first()->$record_column_name;
                } else {
                    $timeline_data['new_values'][$index] = $dirty_column;
                    $timeline_data['old_values'][$index] = $model->getOriginal($index);
                }
            }
        }
    }


    /**
     * @param $sync_response "Response of sync Method perform"
     * @param $model
     * @param $relation
     * @param $record_column_name
     * @param $timeline_data
     * @return void
     */
    public function recordSyncChanges($sync_response, $model, $relation, $record_column_name, &$timeline_data)
    {
        if (!empty($sync_response['attached']) || !empty($sync_response['detached']) || !empty($sync_response['updated'])) {
            $records = $model->$relation;
            $relation_model = $records[0]->getModel();
            if (!empty($sync_response['attached'])) {
                foreach ($sync_response['attached'] as $id) {
                    $temp_record = $records->where('id', $id)->first();
                    $timeline_data['new_values'][$relation][] = $temp_record->$record_column_name;
                    $timeline_data['old_values'][$relation] [] = null;
                }
            }


            if (!empty($sync_response['detached'])) {
                foreach ($sync_response['detached'] as $id) {
                    $temp_record = $relation_model->find($id);
                    $timeline_data['new_values'][$relation][] = null;
                    $timeline_data['old_values'][$relation] [] = $temp_record->$record_column_name;
                }
            }
        }
    }

    public function recordOneToManyDelete($model, $relation, $record_column_name, &$timeline_data)
    {
        $timeline_data['new_values'][$relation][] = null;
        $timeline_data['old_values'][$relation] [] = $model->$record_column_name;
    }

    public function recordOneToManyDeleteWithRelation($model, $relation, &$timeline_data, $relations_data = [])
    {
        foreach ($relations_data as $index => $relation_data){
            $record_column_name = $relation_data['record_column_name'];
            $timeline_data['new_values'][$relation][] = null;
            $timeline_data['old_values'][$relation][] = $relation_data['relation']::where($relation_data['identity_column'], '=', $model->getOriginal($index))->first()->$record_column_name;
        }
    }

    public function recordOneToManyNew($relation, $record_column_name, &$timeline_data)
    {
        $timeline_data['new_values'][$relation][] = $record_column_name;
        $timeline_data['old_values'][$relation][] = null;
    }

    public function recordOneToManyUpdate($model, $relation, &$timeline_data)
    {
        foreach ($model->getDirty() as $index => $dirty_column) {
            $timeline_data['new_values'][$relation][][$index] = $dirty_column;
            $timeline_data['old_values'][$relation][][$index] = $model->getOriginal($index);
        }
    }

    public function recordOneToManyUpdateWithRelation($model, $relation, &$timeline_data, $relations = [])
    {
        if (empty($relations)) {
            foreach ($model->getDirty() as $index => $dirty_column) {
                $timeline_data['new_values'][$relation][][$index] = $dirty_column;
                $timeline_data['old_values'][$relation][][$index] = $model->getOriginal($index);
            }
        } else {
            foreach ($model->getDirty() as $index => $dirty_column) {
                if (array_key_exists($index, $relations)) {
                    $record_column_name = $relations[$index]['record_column_name'];
                    if(is_null($dirty_column)){
                        $timeline_data['new_values'][$relation][][$index] = null;
                    }else{
                        $timeline_data['new_values'][$relation][][$index] = $relations[$index]['relation']::where($relations[$index]['identity_column'], '=', $dirty_column)->first()->$record_column_name;
                    }
                    if(is_null($model->getOriginal($index))){
                        $timeline_data['old_values'][$relation][][$index] = null;
                    }else{
                        $timeline_data['old_values'][$relation][][$index] = $relations[$index]['relation']::where($relations[$index]['identity_column'], '=', $model->getOriginal($index))->first()->$record_column_name;
                    }
                } else {
                    $timeline_data['new_values'][$relation][][$index] = $dirty_column;
                    $timeline_data['old_values'][$relation][][$index] = $model->getOriginal($index);
                }
            }
        }
    }
}

?>
