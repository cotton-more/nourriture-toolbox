<?php namespace NourritureToolbox\Registrar\Models;


use Carbon\Carbon;
use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RegistrationApplication extends Model
{
    protected $dates = [
        'expired_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function(RegistrationApplication $model) {
            $model->setIncrementing(false);
            $model->setAttribute($model->getKeyName(), Uuid::uuid());
        });

        static::creating(function(RegistrationApplication $model) {
            $model->generateTicket();
        });
    }

    public function generateTicket()
    {
        $this->setAttribute('ticket', md5(random_bytes(10)));
    }

    /**
     * @param Builder $builder
     * @param string $email
     * @param string $ticket
     * @return Builder
     */
    public function scopeUnexpired(Builder $builder, $email = null, $ticket = null)
    {
        $builder->where('expired_at', '>', Carbon::now());

        if (null !== $email) {
            $builder->where('email', $email);
        }

        if (null !== $ticket) {
            $builder->where('ticket', $ticket);
        }

        return $builder;
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeExpired(Builder $builder)
    {
        $builder->whereNull('expired_at')->orWhere('expired_at', '<=', Carbon::now());

        return $builder;
    }
}