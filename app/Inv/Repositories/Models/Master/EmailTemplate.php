<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

class EmailTemplate extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_email_template';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'id';

    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * Maintain created_by and updated_by automatically
     *
     * @var boolean
     */
    public $userstamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'template_code',
        'subject',
        'message',
        'cc',
        'bcc',
        'is_system',
        'status'
    ];

    /**
     * Scopes for active System Template list
     *
     * @param string $query
     * @param string $type
     * @return type
     */
    public function scopeActive($query, $type)
    {
        return $query->where('status', $type);
    }

    /**
     * Get email template w.r.t. email title
     *
     * @param string $mail_title
     *
     * @return array mail data
     *
     * @since 0.1
     */
    public static function getEmailTemplate($templateCode)
    {
        /**
         * Check Data is not blank
         */
        if (empty($templateCode)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        $arrTemplate = self::where('status','1')
                ->where('template_code', $templateCode)->first();

        return ($arrTemplate ?: false);
    }

    /**
     * Save or Update User defined templates
     *
     * @param Integer $template_id
     * @param array $arrData
     * @return Integer or Boolean
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function saveUserTemplates($template_id, $arrData = [])
    {

        // check data is array
        if (!is_array($arrData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        // Check Data is not blank
        if (empty($arrData)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        $objtemplate = self::updateOrCreate(['id' => (int) $template_id],
                $arrData);
        return ($objtemplate->id ?: false);
    }

    /**
     * Deleting User defined templates
     *
     * @param Integer $tempalte_id
     * @return boolean
     * @throws InvalidDataTypeExceptions
     */
    public static function deleteUserTemplate($tempalte_id)
    {
        /**
         * Check id is not an Integer
         */
        if (!is_int($tempalte_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $template_delete = self::where('id', (int) $tempalte_id)->delete();
        return $template_delete ? 1 : 0;
    }

    /**
     *
     * @param Integer $template_id
     * @return boolean
     */
    public static function updateTemplateStatus($template_id, $status)
    {
        if (!is_int($template_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        if ($status == 1) {
            $temp_status = 0;
        } else {
            $temp_status = 1;
        }
        $status_update = self::where('email_cat_id', (int) $template_id)
            ->update(['is_active' => $temp_status]);
        return ($status_update ? (int) $temp_status : false);
    }
}