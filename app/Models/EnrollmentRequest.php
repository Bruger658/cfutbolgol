<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollmentRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pendiente';
    public const STATUS_CONTACTED = 'contactado';
    public const STATUS_TRIAL_SCHEDULED = 'prueba_agendada';
    public const STATUS_ENROLLED = 'inscripto';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pendiente',
        self::STATUS_CONTACTED => 'Contactado',
        self::STATUS_TRIAL_SCHEDULED => 'Prueba agendada',
        self::STATUS_ENROLLED => 'Inscripto',
    ];

    protected $fillable = [
        'player_name',
        'birth_date',
        'guardian_email',
        'contact_phone',
        'category',
        'status',
        'contacted_at',
        'trial_scheduled_at',
        'enrolled_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'contacted_at' => 'datetime',
        'trial_scheduled_at' => 'datetime',
        'enrolled_at' => 'datetime',
    ];

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function whatsappUrl(): string
    {
        $phone = preg_replace('/\D+/', '', $this->contact_phone) ?: '';
        $message = rawurlencode("Hola {$this->player_name}, recibimos tu solicitud de inscripción a {$this->category} en Centro Fútbol Gol. Queremos coordinar una prueba de nivel.");

        return "https://wa.me/{$phone}?text={$message}";
    }
}