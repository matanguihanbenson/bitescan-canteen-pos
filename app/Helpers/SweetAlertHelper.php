<?php

if (!function_exists('sweetAlert')) {
    function sweetAlert()
    {
        return app(\App\Services\SweetAlertService::class);
    }
}

if (!function_exists('alert_success')) {
    function alert_success($title, $message = null, $options = [])
    {
        return \App\Services\SweetAlertService::success($title, $message, $options);
    }
}

if (!function_exists('alert_error')) {
    function alert_error($title, $message = null, $options = [])
    {
        return \App\Services\SweetAlertService::error($title, $message, $options);
    }
}

if (!function_exists('alert_warning')) {
    function alert_warning($title, $message = null, $options = [])
    {
        return \App\Services\SweetAlertService::warning($title, $message, $options);
    }
}

if (!function_exists('alert_info')) {
    function alert_info($title, $message = null, $options = [])
    {
        return \App\Services\SweetAlertService::info($title, $message, $options);
    }
}

if (!function_exists('toast_success')) {
    function toast_success($message, $options = [])
    {
        return \App\Services\SweetAlertService::successToast($message, $options);
    }
}

if (!function_exists('toast_error')) {
    function toast_error($message, $options = [])
    {
        return \App\Services\SweetAlertService::errorToast($message, $options);
    }
}

if (!function_exists('toast_warning')) {
    function toast_warning($message, $options = [])
    {
        return \App\Services\SweetAlertService::warningToast($message, $options);
    }
}

if (!function_exists('toast_info')) {
    function toast_info($message, $options = [])
    {
        return \App\Services\SweetAlertService::infoToast($message, $options);
    }
}

if (!function_exists('alert_confirm_delete')) {
    function alert_confirm_delete($title = 'Are you sure?', $message = 'This action cannot be undone!', $options = [])
    {
        return \App\Services\SweetAlertService::confirmDelete($title, $message, $options);
    }
}

if (!function_exists('alert_validation_error')) {
    function alert_validation_error($errors, $title = 'Validation Failed')
    {
        return \App\Services\SweetAlertService::formValidationError($errors, $title);
    }
}

if (!function_exists('alert_permission_denied')) {
    function alert_permission_denied($message = 'You do not have permission to perform this action.')
    {
        return \App\Services\SweetAlertService::permissionDenied($message);
    }
}

if (!function_exists('alert_record_created')) {
    function alert_record_created($record = 'Record')
    {
        return \App\Services\SweetAlertService::recordCreated($record);
    }
}

if (!function_exists('alert_record_updated')) {
    function alert_record_updated($record = 'Record')
    {
        return \App\Services\SweetAlertService::recordUpdated($record);
    }
}

if (!function_exists('alert_record_deleted')) {
    function alert_record_deleted($record = 'Record')
    {
        return \App\Services\SweetAlertService::recordDeleted($record);
    }
}

if (!function_exists('alert_record_not_found')) {
    function alert_record_not_found($record = 'Record')
    {
        return \App\Services\SweetAlertService::recordNotFound($record);
    }
}

if (!function_exists('alert_network_error')) {
    function alert_network_error($statusCode = null, $statusText = null)
    {
        return \App\Services\SweetAlertService::networkError($statusCode, $statusText);
    }
}

if (!function_exists('alert_session_expired')) {
    function alert_session_expired()
    {
        return \App\Services\SweetAlertService::sessionExpired();
    }
}

if (!function_exists('alert_operation_success')) {
    function alert_operation_success($operation = 'Operation')
    {
        return \App\Services\SweetAlertService::operationSuccess($operation);
    }
}

if (!function_exists('alert_operation_failed')) {
    function alert_operation_failed($operation = 'Operation')
    {
        return \App\Services\SweetAlertService::operationFailed($operation);
    }
}

if (!function_exists('alert_process_completed')) {
    function alert_process_completed($process = 'Process')
    {
        return \App\Services\SweetAlertService::processCompleted($process);
    }
}

if (!function_exists('alert_file_upload_success')) {
    function alert_file_upload_success($filename = null)
    {
        return \App\Services\SweetAlertService::fileUploadSuccess($filename);
    }
}

if (!function_exists('alert_file_upload_error')) {
    function alert_file_upload_error($message = null)
    {
        return \App\Services\SweetAlertService::fileUploadError($message);
    }
}

if (!function_exists('alert_export_completed')) {
    function alert_export_completed($type = 'Export')
    {
        return \App\Services\SweetAlertService::exportCompleted($type);
    }
}

if (!function_exists('alert_import_completed')) {
    function alert_import_completed($count = null)
    {
        return \App\Services\SweetAlertService::importCompleted($count);
    }
}

if (!function_exists('alert_database_error')) {
    function alert_database_error($message = null)
    {
        return \App\Services\SweetAlertService::databaseError($message);
    }
}
