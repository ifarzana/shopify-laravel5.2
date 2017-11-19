<?php

namespace App\Models\Email;

//use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SearchableTrait;
use App\Traits\CacheQueryBuilderTrait;
use Config;

class EmailAccount extends Model
{
    /**
     * For Audit Log
     */
//    use AuditLogTrait;

    protected $auditLogEnabled = true;

    /**
     * For Caching all Queries.
     */
//    use CacheQueryBuilderTrait;

    use SearchableTrait;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'CONF_email_account';

    /**
     * The default order by
     *
     * @var string
     */
    protected $default_order_by = 'id';

    /**
     * The default order column
     *
     * @var string
     */
    protected $default_order = 'ASC';

    /**
     * The group by column
     *
     * @var string
     */
    protected $group_by = null;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['from_name', 'from_email', 'reply_to_name', 'reply_to_email', 'host', 'port', 'encryption', 'username', 'password', 'type'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The rules used for the validation
     *
     * @var array
     */
    public static $rules = array(
        'from_name' => 'required|max:255',
        'from_email' => 'required|max:255|email',
        'reply_to_name' => 'max:255',
        'reply_to_email' => 'max:255|email',
        'host' => 'required|max:255',
        'port' => 'digits_between:1,4',
        'encryption' => 'max:255',
        'username' => 'max:255',
        'type' => 'unique:CONF_email_account,type|required',
    );

    /**
     * The custom messages used for the validation
     *
     * @var array
     */
    public static $messages = array(
        //
    );

    /**
     * 'Fetch all' function
     *
     * @param $query Builder
     * @param $paginationData array
     * @param $search_by string
     * @param $where array
     * @return object
     */
    public function scopefetchAll(Builder $query, $paginationData = array(), $search_by, $where = array())
    {
        /*BEGIN SEARCH*/
        if($search_by != null) {
            $results = $this::search(urldecode($search_by));
        }else{
            $results = $this;
        }
        /*END SEARCH*/

        /*BEGIN WHERE*/
        if(!empty($where)) {
            $results = $results->where($where);
        }
        /*END WHERE*/

        /*BEGIN PAGINATION*/
        $paginated = isset($paginationData['paginated']) ? $paginationData['paginated']: false;

        if($paginated) {

            $paginationConfig = Config::get('pagination.pagination');

            $order_by         = $this->getOrderBy($paginationData);
            $order            = $paginationData['order'];
            $p                = $paginationData['p'];
            $itemCountPerPage = isset($paginationData['per_page']) ? $paginationData['per_page'] : $paginationConfig['itemCountPerPage'];

            $results = $results->orderBy($order_by, $order)->paginate($itemCountPerPage, ['*'], 'p');

            return $results;

        }else {
            if( (isset($paginationData['order_by'])) && (isset($paginationData['order'])) ) {
                $results = $results->orderBy($this->getOrderBy($paginationData), $paginationData['order']);
            }else{
                $results = $results->orderBy($this->default_order_by, $this->default_order);
            }
        }

        /*END PAGINATION*/

        return $results->get();
    }

    /**
     * Get settings function
     *
     * @param $marketing bool
     *
     * @return object
     */
    public static function getSettings($marketing = false)
    {
        $type = 'main';

        if($marketing == true) {
            $type = 'marketing';
        }

        $settings = EmailAccount::where('type', $type)->first();

        return $settings;
    }

}