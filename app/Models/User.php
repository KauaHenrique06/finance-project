<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasUuidV7;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, HasMedia
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasUuidV7, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf'
    ];

    protected $append = ['profile_pic'];

    protected $with = ['media'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    // Create a collection with profile_pic name
    public function registerProfilePicCollection(): void
    {
        $this->addMediaCollection('profile_pic')->singleFile();
    }

    // Adjust metrics for save in database
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 300, 300) 
            ->format('webp')        
            ->quality(80)              
            ->nonQueued();
    }

    public function getProfilePicAttribute()
    {
        // Take based with relation and collection name
        $media = $this->getFirstMedia('profile_pic');

        if (!$media)
        {
            return null;
        }

        return [
            'id' => $media->id,
            'url' => $media->getUrl(),
        ];
    }

    public function forgotPassword(): HasMany {
        return $this->hasMany(ForgotPassword::class);
    }

}
