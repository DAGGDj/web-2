<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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
    public function books()
{
    return $this->belongsToMany(Book::class, 'borrowings')
                ->withPivot('id', 'borrowed_at', 'returned_at')
                ->withTimestamps();

}

public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    
    public function getActiveBorrowingsCount()
    {
        return $this->borrowings()
                    ->whereNull('returned_at')
                    ->count();
    }

    public function canBorrowMoreBooks()
    {
        return $this->getActiveBorrowingsCount() < 5;
    }

    public function getRemainingBorrowingSlots()
    {
        $remaining = 5 - $this->getActiveBorrowingsCount();
        return max(0, $remaining); 
    }


}
