<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Borrowing extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos
    protected $fillable = ['user_id', 'book_id', 'borrowed_at', 'returned_at'];
    
    const LOAN_DAYS = 15;
    const FINE_PER_DAY = 0.50;

    // Relacionamento com User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento com Book
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function getDueDateAttribute()
    {
        return Carbon::parse($this->borrowed_at)->addDays(self::LOAN_DAYS);
    }

    
    public function getDaysLateAttribute()
    {
        $dueDate = $this->due_date;
        $returnDate = $this->returned_at ? Carbon::parse($this->returned_at) : now();
        
        if ($returnDate->greaterThan($dueDate)) {
            return $dueDate->diffInDays($returnDate);
        }
        
        return 0;
    }

    
    public function getIsOverdueAttribute()
    {
        return $this->days_late > 0;
    }

    
    public function getFineAmountAttribute()
    {
        return $this->days_late * self::FINE_PER_DAY;
    }

   
    public function getHasFineAttribute()
    {
        return $this->returned_at && $this->is_overdue;
    }

}
