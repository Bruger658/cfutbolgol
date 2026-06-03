<!doctype html>
<html lang="es">
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.5;">
    <h1>Nueva solicitud de inscripción</h1>
    <p>Se recibió una nueva solicitud desde el formulario web.</p>

    <ul>
        <li><strong>Jugador:</strong> {{ $enrollmentRequest->player_name }}</li>
        <li><strong>Fecha de nacimiento:</strong> {{ $enrollmentRequest->birth_date->format('d/m/Y') }}</li>
        <li><strong>Categoría:</strong> {{ $enrollmentRequest->category }}</li>
        <li><strong>Email del acudiente:</strong> {{ $enrollmentRequest->guardian_email }}</li>
        <li><strong>Teléfono:</strong> {{ $enrollmentRequest->contact_phone }}</li>
        <li><strong>Estado:</strong> {{ $enrollmentRequest->statusLabel() }}</li>
    </ul>

    <p>
        <a href="{{ $enrollmentRequest->whatsappUrl() }}">Contactar al interesado por WhatsApp</a>
    </p>

    @php
        $clubWhatsapp = preg_replace('/\D+/', '', (string) config('services.enrollment.club_whatsapp'));
        $clubMessage = rawurlencode('Nueva inscripción: '.$enrollmentRequest->player_name.' / '.$enrollmentRequest->category.' / '.$enrollmentRequest->contact_phone);
    @endphp

    @if ($clubWhatsapp)
        <p>
            <a href="https://wa.me/{{ $clubWhatsapp }}?text={{ $clubMessage }}">Registrar aviso por WhatsApp al club</a>
        </p>
    @endif
</body>
</html>