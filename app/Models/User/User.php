<?php

namespace App\Models\User;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Traits\SearchableTrait;
use Config;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, SearchableTrait;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'UM_user';

    /**
     * The default order by
     *
     * @var string
     */
    protected $default_order_by = 'name';
    
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
     * The joins used by the model.
     *
     * @var array
     */
    protected $joins = array(
        0 => array(
            'type' => 'left',
            'table' => 'UM_user_group',
            'rel' => array(
                'one' => 'UM_user.group_id',
                'two' => 'UM_user_group.id'
            ),
            'columns' => array(
                'name' => 'group_name'
            ),
        ),
    );

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gender', 'name', 'dob', 'email', 'password', 'colour',
        'group_id', 'isActive', 'address', 'home_phone_number', 'mobile_phone_number',
        'office_phone_extension', 'ip_address', 'joined_at', 'left_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The rules used for the validation
     *
     * @var array
     */
    public static $rules = array(
        'name' => 'required|max:100',
        'dob' => 'date',
        'email' => 'unique:UM_user,email|required|max:100|email',
        'password' => 'min:6',
        'group_id' => 'required',
        'isActive' => 'required',
        'address' => 'max:255',
        'home_phone_number' => 'max:20',
        'mobile_phone_number' => 'max:20',
        'office_phone_extension' => 'max:20',
        'ip_address' => 'max:20',
        'joined_at' => 'date',
        'left_at' => 'date'
    );
    
    /**
     * The custom messages used for the validation
     *
     * @var array
     */
    public static $messages = array(
        //
    );
    
    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }

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
            $itemCountPerPage = $paginationConfig['itemCountPerPage'];

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
     * Returns the users schedule
     *
     * @return array
     */
    public static function getUsers()
    {
        $columns = array(
            'UM_user.id',
            'UM_user.name',
            'UM_user.isActive',
            'UM_user.colour',
            'UM_user_group.name as user_group_name',
        );

        $results = User::select($columns)
            ->join('UM_user_group','UM_user.group_id', '=', 'UM_user_group.id' , 'inner');

        $results->groupBy('UM_user.id');

        $results = $results->get()->toArray();

        return $results;
    }


}