<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class SweetAlertService
{
    public static function success($title, $message = null, $options = [])
    {
        self::setAlert('success', $title, $message, $options);
    }

    public static function error($title, $message = null, $options = [])
    {
        self::setAlert('error', $title, $message, $options);
    }

    public static function warning($title, $message = null, $options = [])
    {
        self::setAlert('warning', $title, $message, $options);
    }

    public static function info($title, $message = null, $options = [])
    {
        self::setAlert('info', $title, $message, $options);
    }

    public static function toast($type, $message, $options = [])
    {
        self::setAlert('toast', $message, null, array_merge(['toast_type' => $type], $options));
    }

    public static function successToast($message, $options = [])
    {
        self::toast('success', $message, $options);
    }

    public static function errorToast($message, $options = [])
    {
        self::toast('error', $message, $options);
    }

    public static function warningToast($message, $options = [])
    {
        self::toast('warning', $message, $options);
    }

    public static function infoToast($message, $options = [])
    {
        self::toast('info', $message, $options);
    }

    public static function confirmDelete($title = 'Are you sure?', $message = 'This action cannot be undone!', $options = [])
    {
        self::setAlert('confirm_delete', $title, $message, $options);
    }

    public static function formValidationError($errors, $title = 'Validation Failed')
    {
        $errorMessages = [];
        
        if (is_array($errors) || is_object($errors)) {
            foreach ($errors as $field => $messages) {
                if (is_array($messages)) {
                    foreach ($messages as $message) {
                        $errorMessages[] = $message;
                    }
                } else {
                    $errorMessages[] = $messages;
                }
            }
        } else {
            $errorMessages[] = $errors;
        }

        self::setAlert('form_validation_error', $title, null, ['errors' => $errorMessages]);
    }

    public static function networkError($statusCode = null, $statusText = null)
    {
        $title = 'Network Error';
        $message = 'Unable to connect to the server. Please check your internet connection and try again.';

        if ($statusCode) {
            switch ($statusCode) {
                case 404:
                    $title = 'Not Found';
                    $message = 'The requested resource was not found.';
                    break;
                case 500:
                    $title = 'Server Error';
                    $message = 'An internal server error occurred. Please try again later.';
                    break;
                case 403:
                    $title = 'Access Denied';
                    $message = 'You do not have permission to perform this action.';
                    break;
                case 401:
                    $title = 'Unauthorized';
                    $message = 'Please log in to continue.';
                    break;
                case 422:
                    $title = 'Validation Error';
                    $message = 'Please check your input and try again.';
                    break;
                default:
                    $message = "Error {$statusCode}: " . ($statusText ?: 'Unknown error occurred');
            }
        }

        self::setAlert('network_error', $title, $message, ['status_code' => $statusCode]);
    }

    public static function sessionExpired()
    {
        self::setAlert('session_expired', 'Session Expired', 'Your session has expired. Please log in again to continue.');
    }

    public static function permissionDenied($message = 'You do not have permission to perform this action.')
    {
        self::setAlert('permission_denied', 'Access Denied', $message);
    }

    public static function maintenanceMode()
    {
        self::setAlert('maintenance_mode', 'System Maintenance', 'The system is currently under maintenance. Please try again later.');
    }

    public static function operationSuccess($operation = 'Operation')
    {
        self::success('Success!', "{$operation} completed successfully.");
    }

    public static function operationFailed($operation = 'Operation')
    {
        self::error('Failed!', "{$operation} failed. Please try again.");
    }

    public static function recordCreated($record = 'Record')
    {
        self::successToast("{$record} created successfully!");
    }

    public static function recordUpdated($record = 'Record')
    {
        self::successToast("{$record} updated successfully!");
    }

    public static function recordDeleted($record = 'Record')
    {
        self::successToast("{$record} deleted successfully!");
    }

    public static function recordNotFound($record = 'Record')
    {
        self::error('Not Found', "{$record} not found.");
    }

    public static function validationFailed($errors = null)
    {
        if ($errors) {
            self::formValidationError($errors);
        } else {
            self::error('Validation Failed', 'Please check your input and try again.');
        }
    }

    public static function unauthorizedAccess($action = 'perform this action')
    {
        self::permissionDenied("You are not authorized to {$action}.");
    }

    public static function databaseError($message = null)
    {
        self::error(
            'Database Error',
            $message ?: 'A database error occurred. Please try again later.'
        );
    }

    public static function fileUploadError($message = null)
    {
        self::error(
            'File Upload Error',
            $message ?: 'Failed to upload file. Please check the file size and format.'
        );
    }

    public static function fileUploadSuccess($filename = null)
    {
        $message = $filename ? "File '{$filename}' uploaded successfully!" : 'File uploaded successfully!';
        self::successToast($message);
    }

    public static function exportCompleted($type = 'Export')
    {
        self::successToast("{$type} completed successfully!");
    }

    public static function importCompleted($count = null)
    {
        $message = $count ? "Import completed! {$count} records processed." : 'Import completed successfully!';
        self::successToast($message);
    }

    public static function processStarted($process = 'Process')
    {
        self::infoToast("{$process} started...");
    }

    public static function processCompleted($process = 'Process')
    {
        self::successToast("{$process} completed successfully!");
    }

    public static function customAlert($type, $title, $message = null, $options = [])
    {
        self::setAlert($type, $title, $message, $options);
    }

    protected static function setAlert($type, $title, $message = null, $options = [])
    {
        Session::flash('sweetalert', [
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'options' => $options
        ]);
    }

    public static function getAlert()
    {
        return Session::get('sweetalert');
    }

    public static function hasAlert()
    {
        return Session::has('sweetalert');
    }

    public static function clearAlert()
    {
        Session::forget('sweetalert');
    }
}
