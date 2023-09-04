<?php
namespace Indeed\Ihc\Db;
if ( !defined( 'ABSPATH' ) ){
   exit;
}

class SearchUsers
{

    private $query                        = '';
    private $limit                        = 30;
    private $offset                       = 0;
    private $lid                          = -1;
    private $searchWord                   = '';
    private $role                         = '';
    private $order                        = '';
    private $advancedOrder                = '';
    private $levelStatus                  = '';
    private $specialSelect                = false;
    private $onlyDoubleEmailVerification  = false;
    private $approvelRequest              = false; /// this is the pending_user role

    public function __construct(){}

    public function setLimit( $limit=0 )
    {
        $this->limit = esc_sql( $limit );
        return $this;
    }

    public function setOffset( $offset=0 )
    {
        $this->offset = esc_sql( $offset );
        return $this;
    }

    public function setOrder( $order='' )
    {
        switch ( $order ){
            case 'display_name_asc':
              $this->order = ' u.display_name ASC ';
              break;
            case 'display_name_desc':
              $this->order = ' u.display_name DESC ';
              break;
            case 'user_login_asc':
              $this->order = ' u.user_login ASC ';
              break;
            case 'user_login_desc':
              $this->order = ' u.user_login DESC ';
              break;
            case 'user_email_asc':
              $this->order = ' u.user_email ASC ';
              break;
            case 'user_email_desc':
              $this->order = ' u.user_email DESC ';
              break;
            case 'ID_asc':
              $this->order = ' u.ID ASC ';
              break;
            case 'ID_desc':
              $this->order = ' u.ID DESC ';
              break;
            case 'user_registered_asc':
              $this->order = ' u.user_registered ASC ';
              break;
            case 'user_registered_desc':
              $this->order = ' u.user_registered DESC ';
              break;
            default:
              $this->order = '';
              break;
        }
        return $this;
    }

    public function setLid( $lid=-1 )
    {
        $this->lid = esc_sql( $lid );
        return $this;
    }

    public function setSearchWord( $searchWord='' )
    {
        $this->searchWord = esc_sql( $searchWord );
        return $this;
    }

    public function setRole( $role='' )
    {
        $this->role = esc_sql( $role );
        return $this;
    }

    public function setLevelStatus( $levelStatus='' )
    {
        $this->levelStatus = $levelStatus;
        return $this;
    }

    public function setSpecialSelect( $specialSelect='' )
    {
        $this->specialSelect = $specialSelect;
        return $this;
    }

    public function setAdvancedOrder( $advancedOrder='' )
    {
        $this->advancedOrder = $advancedOrder;
        return $this;
    }

    public function setOnlyDoubleEmailVerification( $onlyDoubleEmailVerification=false )
    {
        $this->onlyDoubleEmailVerification = $onlyDoubleEmailVerification;
        return $this;
    }

    public function setApprovelRequest( $approvelRequest=false )
    {
        $this->approvelRequest = $approvelRequest;
        return $this;
    }

    public function getResults()
    {
        global $wpdb;
        if ( !$this->query ){
            $this->query = $this->buildQuery();
        }
        //No query parameters required, Safe query. prepare() method without parameters can not be called
        $query = "SELECT DISTINCT u.ID, u.user_login, u.user_nicename, u.user_email, u.display_name, u.user_url,
                                  um.meta_value as roles, u.user_registered,
                                  IFNULL( GROUP_CONCAT( DISTINCT(ul.level_id), '|', ul.start_time, '|', ul.expire_time ), -1 ) as levels,
                                  umfn.meta_value as first_name,
                                  umln.meta_value as last_name
        ";
        $query .= $this->extraSelect();
        $query .= $this->query;
        $query .= $this->putGroup();

        if ( $this->limit ){
            $query .= " LIMIT {$this->limit} OFFSET {$this->offset} ";
        }

        return $wpdb->get_results( $query );
    }

    public function getCount()
    {
        global $wpdb;
        if ( !$this->query ){
            $this->query = $this->buildQuery();
        }
        //No query parameters required, Safe query. prepare() method without parameters can not be called
        $query = "SELECT COUNT(DISTINCT u.ID) ";
        $query .= $this->query;
        return $wpdb->get_var( $query );
    }


    private function buildQuery()
    {
        global $wpdb;

        $users = $wpdb->base_prefix . 'users';
        $userMeta = $wpdb->base_prefix . 'usermeta';
        $userLevels = $wpdb->prefix . 'ihc_user_levels';

        $query = " FROM $users u ";
        $query .= " INNER JOIN $userMeta um
                    ON um.user_id=u.ID
        ";

        if ( $this->searchWord ){
            $query .= "
                INNER JOIN $userMeta um2
                ON um2.user_id=u.ID
            ";
        }

        /// LEVELS
        if ( $this->advancedOrder || $this->levelStatus ){
            $query .= " INNER JOIN $userLevels ul
                        ON u.ID=ul.user_id
            ";
        } else {
            $query .= " LEFT JOIN $userLevels ul
                        ON u.ID=ul.user_id
            ";
        }

        $query .= " LEFT JOIN $userMeta umfn
                    ON u.ID=umfn.user_id
        ";
        $query .= " LEFT JOIN $userMeta umln
                    ON u.ID=umln.user_id
        ";
        $query .= $this->extraJoins();

        $query .= " WHERE 1=1 ";

        $query .= $this->searchWordConditions();
        $query .= $this->roleConditions();
        $query .= $this->levelConditions();
        $query .= $this->extraConditions();


        return $query;
    }

    private function extraSelect()
    {
        if ( !$this->specialSelect || $this->lid<0 || $this->lid == '' ){
            return '';
        }
        $query = " , ul.level_id as lid, ul.start_time as start_time, ul.expire_time as expire_time ";
        return $query;
    }

    private function extraJoins()
    {
        global $wpdb;
        $query = '';
        if ( $this->onlyDoubleEmailVerification ){
            $query .= " INNER JOIN {$wpdb->usermeta} umdev ON ul.user_id=umdev.user_id ";
        }
        return $query;
    }

    private function extraConditions()
    {
        $query = '';

        /// double email verification
        if ( $this->onlyDoubleEmailVerification ){
            $query .= " AND umdev.meta_key='ihc_verification_status' AND umdev.meta_value='-1' ";
        }

        $query .= " AND umfn.meta_key='first_name' ";
        $query .= " AND umln.meta_key='last_name' ";

        return $query;
    }

    private function putGroup()
    {
        $query = '  GROUP BY u.ID ';

        if ( $this->advancedOrder ){
            switch ( $this->advancedOrder ){
                case 'newSubscription':
                  $query .= " ORDER BY ul.start_time DESC ";
                  break;
                case 'goingToExpire':
                    $query .= " ORDER BY ul.expire_time ASC ";
                    break;
                case 'recentlyExpired':
                    $query .= " ORDER BY ul.expire_time DESC ";
                    break;
            }
        } else if ( $this->order ) {
            $query .= " ORDER BY {$this->order} ";
        }

        return $query;
    }

    private function searchWordConditions()
    {
        if ( !$this->searchWord ){
            return '';
        }
        $query = " AND ( ";
          $query .= " u.display_name LIKE '%{$this->searchWord}%' ";
          $query .= " OR ";
          $query .= " u.user_login LIKE '%{$this->searchWord}%' ";
          $query .= " OR ";
          $query .= " u.user_email LIKE '%{$this->searchWord}%' ";
          $query .= " OR ";
          $query .= " (um2.meta_key='first_name' AND um2.meta_value LIKE '%{$this->searchWord}%') ";
          $query .= " OR ";
          $query .= " (um2.meta_key='last_name' AND um2.meta_value LIKE '%{$this->searchWord}%') ";
          if (strpos($this->searchWord, ' ')!==FALSE){
              $pieces = str_replace( ' ', '|', $this->searchWord );
              $query .= " OR (um2.meta_key IN ('last_name','first_name') AND um2.meta_value REGEXP '$pieces') ";
          }
        $query .= " ) ";
        return $query;
    }

    private function roleConditions()
    {
        global $wpdb;
        $query = '';
        $roleKey = $wpdb->prefix . 'capabilities';

        /// without admin
        $query .= $wpdb->prepare(" AND ( um.meta_key=%s AND um.meta_value NOT LIKE '%administrator%' ) ", $roleKey );

        if ( $this->approvelRequest != '' ){
            $query .= $wpdb->prepare(" AND ( um.meta_key=%s AND um.meta_value LIKE '%pending_user%' ) ", $roleKey );
        }

        if ( $this->role == '' ){
            return $query;
        }

        if ( strpos( $this->role, ',' ) !== false ){
            $searchRoles = explode( ',', $this->role );
            $query .= " AND ( ( ";
            $countRoles = count( $searchRoles );
            for ( $i=0; $i<$countRoles; $i++ ){
                $query .= " um.meta_value LIKE '%{$searchRoles[$i]}%' ";
                if ( isset($searchRoles[$i+1]) ){
                    $query .= " OR ";
                }
            }
            $query .= $wpdb->prepare(" ) AND um.meta_key=%s ) ", $roleKey );
        } else {
            $query .= $wpdb->prepare(" AND ( um.meta_key=%s ", $roleKey );
            $query .= "AND um.meta_value LIKE '%{$this->role}%' ) ";
        }

        return $query;
    }

    private function levelConditions()
    {
        $query = '';
        if ( $this->lid > -1 && $this->lid != '' ){
            if ( strpos( $this->lid, ',' ) !== false ){
                $searchLids = explode( ',', $this->lid );
                $query .= " AND ( ";
                $countLevels = count( $searchLids );
                for ( $i=0; $i<$countLevels; $i++ ){
                    $query .= " ul.level_id={$searchLids[$i]} ";
                    if ( isset($searchLids[$i+1]) ){
                        $query .= " OR ";
                    }
                }
                $query .= " ) ";
            } else {
                $query .= " AND ( ul.level_id={$this->lid} ) ";
            }
        }

        if ( $this->levelStatus ){
              if ( strpos( $this->levelStatus, ',' ) !== false ){
                  $levelStatusArray = explode( ',', $this->levelStatus );
              } else {
                  $levelStatusArray = array( $this->levelStatus );
              }
              $countLevelStatus = count( $levelStatusArray );
              $query .= " AND ( ";
              for ( $i=0; $i < $countLevelStatus; $i++ ){
                $query .= " ( ";
                switch ( $levelStatusArray[ $i ] ){
                    case 'active':
                        $query .= " IFNULL( UNIX_TIMESTAMP( ul.expire_time ), 0 )>UNIX_TIMESTAMP( NOW() )
                                    AND IFNULL( UNIX_TIMESTAMP( ul.expire_time ), 0 )>0
                                    AND IFNULL( UNIX_TIMESTAMP( ul.start_time ), 0 )<UNIX_TIMESTAMP( NOW() ) ";
                        break;
                    case 'expired':
                        $query .= " IFNULL( UNIX_TIMESTAMP( ul.expire_time ), 0 )<UNIX_TIMESTAMP( NOW() )
                                    AND IFNULL( UNIX_TIMESTAMP( ul.expire_time ), 0 )>0
                                    AND IFNULL( UNIX_TIMESTAMP( ul.start_time ), 0 )<UNIX_TIMESTAMP( NOW() ) ";
                        break;
                    case 'hold':
                        $query .= " IFNULL( UNIX_TIMESTAMP( ul.expire_time ), 0 )=0 ";
                        break;
                    case 'expire_soon':
                        $query .= " IFNULL( UNIX_TIMESTAMP( ul.expire_time ), 0 )>UNIX_TIMESTAMP( NOW() )
                                    AND IFNULL( UNIX_TIMESTAMP( ul.start_time ), 0 )<UNIX_TIMESTAMP( NOW() )
                                    AND (
                                            ( IFNULL( UNIX_TIMESTAMP( ul.expire_time ), 0 ) - UNIX_TIMESTAMP( NOW() ) )
                                                * 100 /
                                            ( IFNULL( UNIX_TIMESTAMP( ul.expire_time ), 0 ) - IFNULL( UNIX_TIMESTAMP( ul.start_time ), 0 ) )
                                    ) < 10
                                    ";
                        break;
                }
                $query .= " ) ";
                if ( isset( $levelStatusArray[ $i + 1 ] ) ){
                    $query .= " OR ";
                }
            }
            $query .= " ) ";
        }

        if ( $this->advancedOrder ){
            switch ( $this->advancedOrder ){
                case 'newSubscription':
                    $query .= " AND IFNULL( UNIX_TIMESTAMP( ul.expire_time ), 0 )>UNIX_TIMESTAMP( NOW() ) ";
                    break;
                case 'recentlyExpired':
                    $query .= " AND IFNULL( UNIX_TIMESTAMP( ul.expire_time ), 0 )<UNIX_TIMESTAMP( NOW() )
                                AND IFNULL( UNIX_TIMESTAMP( ul.expire_time ), 0 )>0
                    ";
                    break;
                case 'goingToExpire':
                    $query .= " AND IFNULL( UNIX_TIMESTAMP( ul.expire_time ), 0 )>UNIX_TIMESTAMP( NOW() )
                                AND IFNULL( UNIX_TIMESTAMP( ul.start_time ), 0 )<UNIX_TIMESTAMP( NOW() )
                    ";
                    break;
            }
        }

        return $query;
    }

}
