<?php
    /**
     * Mirrors structure of ProductImages
     */
    namespace App;

    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;

    class Review extends Model
    {
        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'title',
            'product_id',
            'name',
            'description',
            'date',
            'grade'
        ];

        protected $appends = array('formattedDate');

        /**
         * Related product
         * 
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function product()
        {
            return $this->belongsTo(Product::class)
                        ->remember(self::getDefaultCacheTime());
        }

        public function getFormattedDateAttribute()
        {
            return Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('M d, Y');
        }
    
    }