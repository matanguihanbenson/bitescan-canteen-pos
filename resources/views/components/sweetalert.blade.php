@if(session()->has('sweetalert'))
    @php
        $alert = session('sweetalert');
        $type = $alert['type'] ?? 'info';
        $title = $alert['title'] ?? '';
        $message = $alert['message'] ?? null;
        $options = $alert['options'] ?? [];
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($type === 'toast')
                @php $toastType = $options['toast_type'] ?? 'info'; @endphp
                SweetAlert.{{ $toastType }}Toast('{{ $title }}', {
                    @if(isset($options['position'])) position: '{{ $options['position'] }}', @endif
                    @if(isset($options['timer'])) timer: {{ $options['timer'] }}, @endif
                });
            @elseif($type === 'form_validation_error')
                SweetAlert.formValidationError({!! json_encode($options['errors'] ?? []) !!}, '{{ $title }}');
            @elseif($type === 'network_error')
                SweetAlert.networkError({
                    status: {{ $options['status_code'] ?? 'null' }},
                    statusText: '{{ $message }}'
                });
            @elseif($type === 'session_expired')
                SweetAlert.sessionExpired();
            @elseif($type === 'permission_denied')
                SweetAlert.permissionDenied('{{ $message }}');
            @elseif($type === 'maintenance_mode')
                SweetAlert.maintenanceMode();
            @elseif($type === 'confirm_delete')
                SweetAlert.confirmDelete('{{ $title }}', '{{ $message }}');
            @else
                SweetAlert.{{ $type }}('{{ $title }}', {!! $message ? "'" . addslashes($message) . "'" : 'null' !!}, {
                    @if(isset($options['timer'])) timer: {{ $options['timer'] }}, @endif
                    @if(isset($options['showCloseButton'])) showCloseButton: {{ $options['showCloseButton'] ? 'true' : 'false' }}, @endif
                    @if(isset($options['allowOutsideClick'])) allowOutsideClick: {{ $options['allowOutsideClick'] ? 'true' : 'false' }}, @endif
                    @if(isset($options['confirmButtonText'])) confirmButtonText: '{{ $options['confirmButtonText'] }}', @endif
                });
            @endif
        });
    </script>
@endif
