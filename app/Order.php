<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes, Notifiable;

    /**
     * Always append those attributes
     * 
     * @var
     */
    protected $appends = [
        'order_source', 'first_name', 'last_name'
    ];

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id', 'confirmed', 'confirmed_at', 'deleted_at', 'mailed_at' , 'mailed', 'transaction_id'
    ];

    /**
     * The attributes that are casted to carbon dates
     * 
     * @var array
     */
    protected $dates = [
        'deleted_at', 'confirmed_at', 'mailed_at', 'refunded_at', 'api_requested_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'transaction_response',
        'cc_expiration_month',
        'cc_expiration_year',
        'payment_method',
        'transaction_id',
        'cc_expiration',
        'card_type',
        'cc_number',
        'cc_name',
        'cc_cvv',
    ];

    /**
     * Searchable columns
     * 
     * @var array
     */
    protected $searchableColumns = [
        'id', 'name', 'email', 'phone'
    ];

    /**
     * Get order source attribute
     * 
     * @return string
     */
    public function getOrderSourceAttribute() : string
    {
        return trim($this->gclid) == '' ? 'Other' : 'Adwords';
    }

    /**
     * Get shipping address
     * 
     * @return Address|null
     */
    public function getShippingAddressAttribute()
    {
        $foundShipping = $this->addresses->where('type', 'shipping')->first();

        return $foundShipping ?? $this->getBillingAddressAttribute();
    }

    /**
     * Get validated Address
     * 
     * @return Address|null
     */
    public function getValidatedAddressAttribute()
    {
        return $this->addresses->where('type', 'validated')->first();
    }

    /**
     * Get tax attribute
     * 
     * @param  float $value 
     * @return float
     */
    public function getTaxAttribute($value)
    {
        return round($value, 2);
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return ucwords(strtolower($this->attributes['name']));   
    }

    /**
     * Get First name attribute
     * 
     * @param  float $value 
     * @return float
     */
    public function getFirstNameAttribute()
    {
        $name = explode(' ', trim($this->attributes['name']));
        return $name[0] ?? '';
    }

    /**
     * Get First name attribute
     * 
     * @param  float $value 
     * @return float
     */
    public function getLastNameAttribute()
    {
        $name = explode(' ', trim($this->attributes['name']));

        $firstName = array_shift($name);
        $lastName = implode(' ', $name);

        return trim($lastName) === '' ? $firstName : $lastName;
    }

    /**
     * @return boolean
     */
    public function getConfirmedAttribute()
    {
        try {
            $confirmed = (int) $this->attributes['confirmed'] ?? $this->confirmed ?? 0;
            return (int) $confirmed === 1;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get billing address
     * 
     * @return Address|null
     */
    public function getBillingAddressAttribute()
    {
        return $this->addresses->where('type', 'billing')->first();
    }

    /**
     * @return boolean
     */
    public function getRefundedAttribute()
    {
        try {
            $refunded = (int) $this->attributes['refunded'] ?? $this->refunded ?? 0;
            return (int) $refunded === 1;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getCarrierAttribute()
    {
        if (isset($this->carriers) && ! $this->carriers->isEmpty()) {
            return $this->carriers->first();
        }

        return null;
    }

    /**
     * Mark order as mailed
     *
     * @return Order
     */
    public function markAsMailed()
    {
        $this->mailed = true;
        $this->mailed_at = date('Y-m-d H:i:s');
        $this->save();
        return $this;
    }

    /**
     * Mark order Shipped
     * 
     * @return Order
     */
    public function markAsShipped()
    {
        try {
            $this->order_status_id = 3;
            $this->save();
        } catch (\Exception $e) {
            
        }
        
        return $this;   
    }

    /**
     * Mark order as refunded
     *
     * @return self
     */
    public function markAsRefunded()
    {
        $this->refunded = true;
        $this->refunded_at = date('Y-m-d H:i:s');
        $this->order_status_id = 4;
        $this->save();
        return $this;
    }
    
    /**
     * Mark order as refunded
     *
     * @return self
     */
    public function markAsConfirmed()
    {
        $this->confirmed = true;
        $this->confirmed_at = date('Y-m-d H:i:s');
        $this->save();
        return $this;
    }

    /**
     * Mark order as reported
     *
     * @return self
     */
    public function markAsReported()
    {
        $this->reported = true;
        $this->reported_at = date('Y-m-d H:i:s');
        $this->save();
        return $this;
    }
    
    /**
     * Get ordered products
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)
                    ->withoutGlobalScopes()
                    ->withTimestamps()
                    ->withPivot(['quantity', 'price', 'options']);
    }

    /**
     * Get shipping option
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shipping()
    {
        return $this->belongsTo(Shipping::class);
    }

    /**
     * Get applied discount
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
    
    /**
     * Get customer
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get carriers
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carriers()
    {
        return $this->hasMany(Carrier::class);
    }

    /**
     * Get api responses
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function apiResponses()
    {
        return $this->hasMany(OrderApiResponse::class);
    }

    /**
     * Get all order addresses
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get all tracking numbers
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trackingNumbers()
    {
        return $this->hasMany(TrackingNumber::class);
    }

    /**
     * Get order status
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    /**
     * Get Nofraud transaction
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function nofraudTransaction()
    {
        return $this->hasOne(NofraudTransaction::class);
    }

    /**
     * Confirmed orders scope
     * 
     * @param  Builder $query
     * @return Builder       
     */
    public function scopeConfirmed($query)
    {
        return $query->where('confirmed', true)
            ->where('refunded', false)
            ->where('order_status_id', '!=', 4);
    }

    /**
     * Today scope
     * 
     * @param  Builder $query
     * @return Builder       
     */
    public function scopeToday($query)
    {
        return $query->where('confirmed', true)->whereDate('created_at', now());
    }

    /**
     * Confirmed orders scope
     * 
     * @param  Builder $query
     * @return Builder       
     */
    public function scopeRefunded($query)
    {
        return $query->where('refunded', true);
    }

    /**
     * Unconfirmed orders scope
     * 
     * @param  Builder $query
     * @return Builder       
     */
    public function scopeNotConfirmed($query)
    {
        return $query->where('confirmed', false);
    }

    /**
     * @return string
     */
    public function getRawCcNumberAttribute()
    {
        $ccNumber = isset($this->cc_number) ? $this->cc_number : '';

        if ($ccNumber === '') {
            return $ccNumber;
        }

        try {
            return decrypt($ccNumber);
        } catch (\Exception $exception) {
            return $ccNumber;
        }
    }

    /**
     * @return string
     */
    public function getExpirationDateAttribute()
    {
        $year = isset($this->cc_expiration_year) && strlen($this->cc_expiration_year) > 0
            ? $this->cc_expiration_year
            : '';

        $month = isset($this->cc_expiration_month) && strlen($this->cc_expiration_month) > 0
            ? $this->cc_expiration_month
            : '';

        if (strlen($year) === 2) {
            $year = "20{$year}";
        }

        return "{$year}-{$month}";
    }

    /**
     * @return string
     */
    public function getCardTypeAttribute()
    {
        if (! isset($this->attributes['card_type'])) {
            return 'Card';
        }

        if (trim($this->attributes['card_type']) === '') {
            return 'Card';
        }

        return trim($this->attributes['card_type']);
    }

    /**
     * @return string
     */
    public function getLastCCDigitsAttribute()
    {
        if (! isset($this->cc_number)) {
            return 'XXXX';
        }

        if (trim($this->cc_number) === '') {
            return 'XXXX';
        }
        
        try {
            $ccNumber = decrypt($this->cc_number);
            return strlen($ccNumber) > 4 ? substr($ccNumber, -4) : $ccNumber;
        } catch (\Exception $exception) {
            return substr($this->cc_number, -4);
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $searchTerm
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($query) use ($searchTerm){
                foreach ($this->searchableColumns as $column) {
                    $query = $query->orWhereRaw('LOWER('.$column.') LIKE ? ', ['%'.trim(strtolower($searchTerm)).'%']);
                }
            })
            ->orderByRaw('name LIKE ? DESC', ['%'.trim(strtolower($searchTerm)).'%']);
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForSlack($notification)
    {
        return config('services.slack.order_notification_webhook');
    }
}
