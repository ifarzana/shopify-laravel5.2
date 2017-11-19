<?php

namespace App\Models\Domain;

use Illuminate\Database\Eloquent\Model;

class DomainEmailTemplate extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'WH_domain_email_template';

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
    protected $fillable = ['name', 'send_email', 'include_invoice', 'subject', 'body', 'key'];

    /**
     * The rules used for the validation
     *
     * @var array
     */
    public static $rules = array(
        'name' => 'required',
        'send_email' => 'required|boolean',
        'include_invoice' => 'required|boolean',
        'subject' => 'required|max:255',
        'body' => 'required',
        'key' => 'required'
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
}
