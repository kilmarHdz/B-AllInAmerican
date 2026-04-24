<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Lead de Contacto</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f9; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f9; padding:32px 16px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%); padding:28px 32px; text-align:center;">
                            <h1 style="margin:0; color:#ffffff; font-size:22px; font-weight:600;">
                                📩 Nuevo Lead Recibido
                            </h1>
                            <p style="margin:8px 0 0; color:#bee3f8; font-size:13px;">
                                {{ $lead->created_at->format('d/m/Y — h:i A') }}
                            </p>
                        </td>
                    </tr>

                    {{-- Información del Padre/Tutor --}}
                    <tr>
                        <td style="padding:28px 32px 0;">
                            <h2 style="margin:0 0 16px; font-size:15px; color:#1e3a5f; text-transform:uppercase; letter-spacing:1px; border-bottom:2px solid #e2e8f0; padding-bottom:8px;">
                                👤 Padre / Tutor
                            </h2>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding:6px 0; color:#718096; font-size:13px; width:130px;">Nombre:</td>
                                    <td style="padding:6px 0; color:#2d3748; font-size:14px; font-weight:600;">{{ $lead->parent_name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0; color:#718096; font-size:13px;">Email:</td>
                                    <td style="padding:6px 0;">
                                        <a href="mailto:{{ $lead->parent_email }}" style="color:#2b6cb0; font-size:14px; text-decoration:none;">{{ $lead->parent_email }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0; color:#718096; font-size:13px;">Teléfono:</td>
                                    <td style="padding:6px 0;">
                                        <a href="tel:{{ $lead->parent_phone }}" style="color:#2b6cb0; font-size:14px; text-decoration:none;">{{ $lead->parent_phone }}</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Información del Niño --}}
                    <tr>
                        <td style="padding:24px 32px 0;">
                            <h2 style="margin:0 0 16px; font-size:15px; color:#1e3a5f; text-transform:uppercase; letter-spacing:1px; border-bottom:2px solid #e2e8f0; padding-bottom:8px;">
                                🧒 Información del Niño
                            </h2>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding:6px 0; color:#718096; font-size:13px; width:130px;">Nombre:</td>
                                    <td style="padding:6px 0; color:#2d3748; font-size:14px; font-weight:600;">{{ $lead->child_name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0; color:#718096; font-size:13px;">Edad:</td>
                                    <td style="padding:6px 0; color:#2d3748; font-size:14px;">{{ $lead->child_age }} años</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0; color:#718096; font-size:13px;">Diagnóstico:</td>
                                    <td style="padding:6px 0; color:#2d3748; font-size:14px;">{{ $lead->child_diagnosis }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Señales de Alerta --}}
                    @if(!empty($lead->alert_signs))
                    <tr>
                        <td style="padding:24px 32px 0;">
                            <h2 style="margin:0 0 16px; font-size:15px; color:#1e3a5f; text-transform:uppercase; letter-spacing:1px; border-bottom:2px solid #e2e8f0; padding-bottom:8px;">
                                ⚠️ Señales de Alerta
                            </h2>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                @foreach($lead->alert_signs as $sign)
                                <tr>
                                    <td style="padding:4px 0; color:#2d3748; font-size:14px;">
                                        <span style="color:#e53e3e; margin-right:6px;">•</span> {{ $sign }}
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                    @endif

                    {{-- Objetivos --}}
                    <tr>
                        <td style="padding:24px 32px 0;">
                            <h2 style="margin:0 0 16px; font-size:15px; color:#1e3a5f; text-transform:uppercase; letter-spacing:1px; border-bottom:2px solid #e2e8f0; padding-bottom:8px;">
                                🎯 Objetivos
                            </h2>
                            <p style="margin:0; color:#2d3748; font-size:14px; line-height:1.6; background-color:#f7fafc; padding:12px 16px; border-radius:8px; border-left:3px solid #2b6cb0;">
                                {{ $lead->goals }}
                            </p>
                        </td>
                    </tr>

                    {{-- Cómo nos encontró --}}
                    @if($lead->how_found_us)
                    <tr>
                        <td style="padding:24px 32px 0;">
                            <h2 style="margin:0 0 16px; font-size:15px; color:#1e3a5f; text-transform:uppercase; letter-spacing:1px; border-bottom:2px solid #e2e8f0; padding-bottom:8px;">
                                🔍 ¿Cómo nos encontró?
                            </h2>
                            <p style="margin:0; color:#2d3748; font-size:14px;">{{ $lead->how_found_us }}</p>
                        </td>
                    </tr>
                    @endif

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:28px 32px; margin-top:16px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#edf2f7; border-radius:8px; padding:16px;">
                                <tr>
                                    <td style="padding:16px; text-align:center;">
                                        <p style="margin:0 0 4px; color:#718096; font-size:12px;">
                                            Lead #{{ $lead->id }} · Estado: <strong style="color:#38a169;">{{ ucfirst($lead->status) }}</strong>
                                        </p>
                                        <p style="margin:0; color:#a0aec0; font-size:11px;">
                                            All-In American School · Sistema de Captación de Leads
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
