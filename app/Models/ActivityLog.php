<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    const FAILED = 0;
    const SUCCESS = 1;

    const JOB_COUNTRY = 0;
    const JOB_STATISTIC = 1;

    const TYPE_ERROR = 0;
    const TYPE_INFORMATION = 1;
    const TYPE_DEBUG = 2;


    /**
     * @param $jobStatus
     * @param $jobType
     * @param $messageType
     * @param $message
     * Common method for API services to log any information which might occur
     */
    public static function createLog($jobStatus, $jobType, $messageType, $message): bool
    {
        $log = new ActivityLog();
        $log->job_status = $jobStatus;
        $log->job_type = $jobType;
        $log->message_type = $messageType;
        $log->message = $message;
        return $log->save();
    }

}
