<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        html {
            background: linear-gradient(114deg, #549C44 0%, #1E597C 100%);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            margin: 0;
            padding: 0;
            background: linear-gradient(114deg, #549C44 0%, #1E597C 100%);
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: #70bf44;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .lead-name {
            background-color: #f9fafb;
            padding: 20px 30px;
            border-bottom: 2px solid #e5e7eb;
        }

        .lead-name h2 {
            margin: 0;
            font-size: 20px;
            color: #111827;
        }

        .lead-name p {
            margin: 5px 0 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .content {
            padding: 30px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .info-table tr:last-child {
            border-bottom: none;
        }

        .info-table td {
            padding: 12px 0;
            vertical-align: top;
        }

        .info-table .label {
            font-weight: 600;
            color: #6b7280;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 60%;
        }

        .info-table .value {
            font-size: 15px;
            color: #111827;
        }

        .info-table .value a {
            color: #667eea;
            text-decoration: none;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }

        .footer {
            text-align: center;
            padding: 20px 30px;
            background-color: #f9fafb;
            color: #6b7280;
            font-size: 13px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <svg xmlns="http://www.w3.org/2000/svg" id="Layer_2" width="200" data-name="Layer 2" viewBox="0 0 154.83 25.57">
            <defs>
                <style>.cls-1 {
                        fill: #fff
                    }</style>
            </defs>
            <g id="Layer_1-2" data-name="Layer 1">
                <path
                    d="M11.83 18.24H3.19c-.18 0-.34-.15-.34-.34v-3.67c0-.19.15-.34.34-.34h7.48c.19 0 .34-.15.34-.34v-1.9c0-.19-.15-.34-.34-.34H3.19c-.18 0-.34-.15-.34-.34V7.42c0-.19.15-.34.34-.34h8.52c.18 0 .34-.15.34-.34V4.86c0-.19-.15-.34-.34-.34H.34c-.18 0-.34.15-.34.34v15.58c0 .18.15.34.34.34h11.5c.19 0 .34-.15.34-.34v-1.88c0-.19-.15-.34-.34-.34ZM53.99 4.54H42.61c-.19 0-.34.15-.34.34v15.58c0 .18.15.34.34.34h9.45c.08 0 .16-.04.21-.11l1.45-2.05c.12-.17 0-.4-.21-.4h-8.04c-.19 0-.34-.15-.34-.34v-3.67c0-.19.15-.34.34-.34h7.48c.19 0 .34-.15.34-.34v-1.9c0-.19-.15-.34-.34-.34h-7.48c-.19 0-.34-.15-.34-.34V7.42c0-.19.15-.34.34-.34h8.52c.19 0 .34-.15.34-.34V4.86c0-.19-.15-.34-.34-.34ZM88.76 7.72v9.87c0 .19.15.34.34.34h3.01c.19 0 .33.15.33.33v2.19c0 .18-.15.34-.34.34h-9.54c-.19 0-.34-.15-.34-.34v-2.18c0-.19.15-.34.34-.34h3.01c.18 0 .34-.15.34-.34V7.72c0-.19-.15-.34-.34-.34h-2.52c-.21 0-.32-.23-.21-.4l1.67-2.35c.05-.07.12-.11.21-.11h7.39c.19 0 .34.15.34.34v2.18c0 .19-.15.34-.34.34h-3c-.19 0-.34.15-.34.34ZM34.66 4.54h-2.33a.34.34 0 0 0-.31.21l-4.44 11.37c-.11.28-.52.28-.63 0L22.49 4.75a.335.335 0 0 0-.31-.21h-2.4c-.24 0-.4.24-.31.46l6.4 15.56c.05.13.17.21.31.21h2.08c.14 0 .26-.08.31-.21L34.97 5a.333.333 0 0 0-.31-.46M106.29 4.63h-6.01c-.22 0-.4.18-.4.4v15.45c0 .22.18.4.4.4h2.05c.22 0 .4-.18.4-.4v-4.42c0-.22.18-.4.4-.4h2.82c3.58 0 6.5-1.9 6.5-5.55v-.05c0-3.27-2.37-5.43-6.18-5.43Zm3.27 5.55c0 1.7-1.35 2.9-3.51 2.9h-2.92c-.22 0-.4-.18-.4-.4V7.64c0-.22.18-.4.4-.4h2.92c2.14 0 3.51 1 3.51 2.9v.05ZM149.32 18.28h-7.74c-.22 0-.4-.18-.4-.4V5.03c0-.22-.18-.4-.4-.4h-2.05c-.22 0-.4.18-.4.4v15.45c0 .22.18.4.4.4h10.59c.22 0 .4-.18.4-.4v-1.79c0-.22-.18-.4-.4-.4ZM130.71 18.36h-8.64c-.19 0-.34-.15-.34-.34v-3.67c0-.18.15-.34.34-.34h7.48c.18 0 .34-.15.34-.34v-1.9c0-.18-.15-.34-.34-.34h-7.48c-.19 0-.34-.15-.34-.34V7.54c0-.18.15-.34.34-.34h8.52c.18 0 .34-.15.34-.34V4.98c0-.19-.15-.34-.34-.34h-11.38c-.18 0-.34.15-.34.34v15.58c0 .19.15.34.34.34h11.5c.18 0 .34-.15.34-.34v-1.88c0-.19-.15-.34-.34-.34ZM70.94 14.07l-1.65 2.32c-.08.12-.08.27 0 .39l3.69 5.2c.05.07.12.11.21.11h3.49c.21 0 .33-.23.21-.4l-5.4-7.62a.337.337 0 0 0-.55 0M76.94 3h-3.49a.26.26 0 0 0-.21.11l-3.99 5.62c-.08.12-.08.27 0 .39l1.65 2.32c.13.19.41.19.55 0l5.7-8.04c.12-.17 0-.4-.21-.4M69.93 12.38c.17.25.17.57 0 .82l-1.77 2.49-6.92 9.78c-.05.07-.12.11-.21.11h-3.49c-.21 0-.33-.23-.21-.4L66 12.93a.24.24 0 0 0 0-.28L57.33.4c-.12-.17 0-.4.21-.4h3.49c.08 0 .16.04.21.11zM154.34 20.27c-.33.33-.72.49-1.19.49s-.86-.16-1.18-.49-.49-.72-.49-1.19.16-.85.49-1.18.72-.49 1.18-.49.86.16 1.19.49.49.72.49 1.18-.16.86-.49 1.19m-2.2-2.2q-.42.42-.42 1.02c0 .6.14.74.42 1.03q.42.42 1.02.42c.6 0 .74-.14 1.02-.42s.42-.62.42-1.03-.14-.74-.42-1.02q-.42-.42-1.02-.42c-.6 0-.74.14-1.02.42m.98.09c.23 0 .4.02.5.07.19.08.29.23.29.47 0 .16-.06.29-.18.36q-.09.06-.27.09c.14.02.25.08.32.18s.1.19.1.28v.27c0 .05 0 .08.02.1V20h-.29v-.25c0-.21-.06-.35-.17-.42q-.105-.06-.36-.06h-.26v.72h-.32v-1.84h.64Zm.35.29c-.08-.05-.21-.07-.39-.07h-.28v.67h.29c.14 0 .24-.01.31-.04.13-.05.19-.15.19-.29q0-.195-.12-.27"
                    class="cls-1"/>
            </g>
        </svg>
        <h1>New Lead</h1>
    </div>

    <div class="lead-name">
        <h2>{{ $lead->first_name }} {{ $lead->last_name }}</h2>
        <p>{{ $lead->practice->name }}</p>
    </div>

    <div class="content">
        <table class="info-table">
            @if($lead->email)
                <tr>
                    <td class="label">Email</td>
                    <td class="value"><a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a></td>
                </tr>
            @endif

            @if($lead->phone)
                <tr>
                    <td class="label">Phone</td>
                    <td class="value"><a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a></td>
                </tr>
            @endif

            <tr>
                <td class="label">Source</td>
                <td class="value">{{ ucfirst($lead->source?->value ?? 'Unknown') }}</td>
            </tr>

            @if($lead->lead_type)
                <tr>
                    <td class="label">Lead Type</td>
                    <td class="value">{{ $lead->lead_type }}</td>
                </tr>
            @endif

            <tr>
                <td class="label">Received</td>
                <td class="value">{{ $lead->lead_created_at?->setTimezone('America/Denver')->format('F j, Y \a\t g:i A T') }}</td>
            </tr>
        </table>

        @if($lead->data)
            @php
                $additionalData = collect($lead->data)->except([
                    'first_name', 'firstName', 'fname',
                    'last_name', 'lastName', 'lname',
                    'email', 'email_address', 'emailAddress',
                    'phone', 'phone_number', 'phoneNumber', 'mobile',
                    'created_at', 'createdAt', 'lead_created_at',
                    'lead_type', 'leadType', 'type'
                ]);
            @endphp

            @if($additionalData->isNotEmpty())
                <div class="section-title">Additional Information</div>
                <table class="info-table">
                    @foreach($additionalData as $key => $value)
                        <tr>
                            <td class="label">{{ ucwords(str_replace(['_', '-'], ' ', $key)) }}</td>
                            <td class="value">
                                @if(is_array($value))
                                    {{ implode(', ', $value) }}
                                @elseif(is_bool($value))
                                    {{ $value ? 'Yes' : 'No' }}
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif
        @endif
    </div>

    <div class="footer">
        This is an automated notification from Evexias lead management system.<br>
        Please do not reply to this email.
    </div>
</div>
</body>
</html>
