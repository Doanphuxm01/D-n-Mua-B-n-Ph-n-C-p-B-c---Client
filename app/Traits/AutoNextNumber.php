<?php

/**
 * Created by ngankt2@gmail.com
 * Website: https://techhandle.net
 */
namespace App\Traits;
use Illuminate\Support\Facades\DB;
use MongoDB\Operation\FindOneAndUpdate;

trait AutoNextNumber
{
    /**
     * Increment the counter and get the next sequence
     *
     * @param $collection
     * @return mixed
     */
    private static function getID($collection)
    {
        $seq = DB::getCollection('_data_counters')->findOneAndUpdate(
            array('_id' => $collection),
            array('$inc' => array('seq' => 1)),
            array('new' => TRUE, 'upsert' => TRUE, 'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER)
        );

        return $seq->seq;
    }

    /**
     * Boot the AutoIncrementID trait for the model.
     *
     * @return void
     */
    public static function bootUseAutoIncrementID()
    {
        static::creating(function ($model) {
            $model->incrementing = FALSE;
            $model->{$model->getKeyName()} = self::getID($model->getTable());
        });
    }

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts()
    {
        return $this->casts;
    }
}