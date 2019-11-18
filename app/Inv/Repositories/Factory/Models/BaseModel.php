<?php

namespace App\Inv\Repositories\Factory\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use App\Inv\Repositories\Factory\Models\Relations\BelongsToMany;

class BaseModel extends Model
{
    /**
     * Indicates if the model should be userstamped.
     *
     * @var bool
     * @since 0.1
     */
    public $userstamps = true;

    /**
     * Indicates if the model should have device track or not
     *
     * @var boolean
     */
    public $devicetrack = false;

    /**
     * The name of the "created by" column.
     *
     * @var string
     * @since 0.1
     */
    const CREATED_BY = 'created_by';

    /**
     * The name of the "updated by" column.
     *
     * @var string
     * @since 0.1
     */
    const UPDATED_BY = 'updated_by';

    /**
     * The name of the "device" column
     *
     * @var string
     */
    const DEVICE = 'device';

    /**
     * Identifier to keep user id as null or 0 (zero)
     *
     * @var boolean
     */
    protected static $nullable_user = false;

    /**
     * Update created_by and updated_by fields, if set
     *
     * @param void
     * @return boolean
     * @since 0.1
     */
    protected function updateUserstamps()
    {
        if (!$this->userstamps) {
            return false;
        }

        if (!$this->isDirty(static::UPDATED_BY)) {
            $this->setUpdatedBy();
        }

        if (!$this->exists && !$this->isDirty(static::CREATED_BY)) {
            $this->setCreatedBy();
        }
    }

    /**
     * Update device fields, if set
     *
     * @param void
     * @return boolean
     */
    protected function updateDeviceTrack()
    {
        if (!$this->devicetrack) {
            return false;
        }

        if (!$this->isDirty(static::DEVICE)) {
            $this->setDevice();
        }

        if (!$this->exists && !$this->isDirty(static::DEVICE)) {
            $this->setDevice();
        }
    }

    /**
     * Update the model's update usertamp and timestamp.
     *
     * @return bool
     */
    public function touch()
    {
        $this->updateUserstamps();
        $this->updateDeviceTrack();

        parent::touch();
    }

    /**
     * Override the default updateTimestamps
     * Entry point to update created_by and updated_by
     *
     * @param void
     * @return void
     * @since 0.1
     */
    protected function updateTimestamps()
    {
        $this->updateUserstamps();
        $this->updateDeviceTrack();

        parent::updateTimestamps();
    }

    /**
     * Return user id if logged in, otherwise 0
     *
     * @param void
     * @return interger
     * @since 0.1
     */
    public function getUserId()
    {
        return (\Auth::user() ? \Auth::user()->id : (static::$nullable_user ? null
                : 0));
    }

    /**
     * Set value for created_by field
     *
     * @param void
     * @return void
     * @since 0.1
     */
    public function setCreatedBy()
    {
        $this->{static::CREATED_BY} = $this->getUserId();
    }

    /**
     * Set value for updated_by field
     *
     * @param void
     * @return void
     * @since 0.1
     */
    public function setUpdatedBy()
    {
        $this->{static::UPDATED_BY} = $this->getUserId();
    }

    /**
     * Set value for device field
     *
     * @param void
     * @return void
     */
    public function setDevice()
    {
        if (class_exists('Jenssegers\Agent\Agent')) {
            $agent      = app('Jenssegers\Agent\Agent');
            $agent->setUserAgent(request()->server('HTTP_USER_AGENT'));
            $devicetype = ($agent->isMobile() ? ($agent->isTablet() ? 'tablet' : 'mobile')
                    : 'computer');

            $this->{static::DEVICE} = $devicetype;
        }
    }

    /**
     * "created_by" mutator
     *
     * @param interger $value User Id
     * @return void
     * @since 0.1
     */
    public function setCreatedByAttribute($value)
    {
        $this->attributes['created_by'] = $value;
    }

    /**
     * "updated_by" mutator
     *
     * @param integer $value User Id
     * @return void
     * @since 0.1
     */
    public function setUpdatedByAttribute($value)
    {
        $this->attributes['updated_by'] = $value;
    }

    /**
     * "device" mutator
     *
     * @param string $value
     * @return void
     */
    public function setDeviceAttribute($value)
    {
        $this->attributes[static::DEVICE] = $value;
    }

    /**
     * Accessor of "created_by"
     *
     * @param mixed $value
     * @return integer
     * @since 0.1
     */
    public function getCreatedByAttribute($value)
    {
        return $value;
    }

    /**
     * Accessor of "updated_by"
     *
     * @param mixed $value
     * @return integer
     * @since 0.1
     */
    public function getUpdatedByAttribute($value)
    {
        return $value;
    }

    /**
     * Accessor of "device"
     *
     * @param mixed $value
     * @return string
     */
    public function getDeviceAttribute($value)
    {
        return $value;
    }

    /**
     * Get the name of the "created by" column.
     *
     * @return string
     */
    public function getCreatedByColumn()
    {
        return static::CREATED_BY;
    }

    /**
     * Get the name of the "updated by" column.
     *
     * @return string
     */
    public function getUpdatedByColumn()
    {
        return static::UPDATED_BY;
    }

    /**
     * Get the name of the "device" column
     *
     * @return string
     */
    public function getDeviceColumn()
    {
        return static::DEVICE;
    }

    /**
     * Get the date-time conversion string w.r.t. database driver used.
     *
     * @param string $field
     * @param boolean $returnDatePart
     * @param boolean $withRaw
     * @return string
     */
    public static function convertTz($field, $returnDatePart = true,
                                     $withRaw = false)
    {
        // Get the database driver.
        $dbDriver = Config::get('database.default');

        // Get the application timezone.
        $sourceTz = Config::get('app.timezone');

        // Get the expected timezone.
        $targetTz = Config::get('app.expected_timezone', $sourceTz);

        // $field sanitization.
        $fieldParts = explode(".", $field);
        if (count($fieldParts) > 1) {
            $dbPrefix = Config::get('database.connections.'.$dbDriver.'.prefix');
            $field    = $dbPrefix.str_ireplace($dbPrefix, '', $fieldParts[0]).'.'.$fieldParts[1];
        }

        // If source and target timezones are same, we simply use the date field
        // as there is no need to run an extra query when both are same.
        if ($sourceTz !== $targetTz) {
            // Create conversion string w.r.t. database driver used.
            switch ($dbDriver) {
                case 'mysql':
                    $field = "convert_tz(".$field.", '".$sourceTz."', '".$targetTz."')";
                    break;
                default:
                // No break is required here
            }
        }

        $resultStr = ($returnDatePart === true) ? 'date('.$field.')' : $field;

        return ($withRaw === false ? $resultStr : "\\DB::raw(\"".$resultStr."\")");
    }
}