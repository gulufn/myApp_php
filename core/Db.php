<?php

namespace Core;

class Db
{
    /**
     * 插入一条用户数据
     *
     * @param  \PDO  $pdo
     * @param  array $data 数据类型为关联数组 字段名 => 值，如 ['email' => 'a@x.com', 'pwd' => 'xxx']
     * @return array $rdata
     */
    public static function insert_data_users(\PDO $pdo,array $data = []): array{
        // 允许插入的字段白名单
        $allowed = ['email', 'pwd', 'captcha', 'captcha_update_time', 'token', 'token_update_time', 'is_activated', 'register_time'];
        try {
            // 过滤掉不在白名单里的字段,array_flip()让数组变成关联数组，array_intersect_key(),取两个数组中的交集，以第一个为主
            $data = array_intersect_key($data, array_flip($allowed));
            if (empty($data)) {return ['code' => '0', 'msg' => '没有可插入的字段', 'data' => ''];}
            // ['email', 'pwd']，取$data里的key
            $cols = array_keys($data);                    
            // [':email', ':pwd']，把'key'变成':key'
            $placeholders = array_map(fn($c) => ":{$c}", $cols);  
            //开始拼接sql
            $sql = "INSERT INTO users (" . implode(',', $cols) . ") VALUES (" . implode(',', $placeholders) . ")";
            //预处理
            $statement = $pdo->prepare($sql);
            //循环给sql绑定值
            foreach ($data as $col => $val) {
                $statement->bindValue(":{$col}", $val);
            }
            //执行sql
            $statement->execute();
            $rdata=['sql'=>$sql,'params'=>$data];
            return ['code'=>'1','msg'=>'成功','data'=>$rdata];
        }catch (\PDOException $e) {
            $rdata=['sql'=>$sql,'error'=>$e->getMessage(),'sqlstate'=>$e->getCode(),'params'=>$fields];
            return ['code'=>'0','msg'=>'sql执行失败','data'=>$rdata];
        }
    }



    /**
     * 查询通过邮箱查询数据
     *
     * @param  \PDO   $pdo
     * @param  string $email  'a@x.comg'
     * @param  array  $fields ['id','email',......]
     * @return array  $rdata  
     */
    public static function select_data_by_meail_users(\PDO $pdo,string $email, array $fields = []): array{
        // 白名单，防止 SQL 注入
        $allowed = ['id', 'email', 'pwd', 'captcha', 'captcha_update_time','token','token_update_time','is_activated','register_time'];
        try {
            //取两个数组的交集
            $fields = array_intersect($fields, $allowed);
            //如果传入的数组是空数组，则return
            if (empty($fields)) {return ['code'=>'0','msg'=>'没有合法字段','data'=>''];}
            //把$fields数组里的元素用逗号连接
            $cols = implode(',', $fields);
            $sql = "SELECT {$cols} FROM users WHERE email = :email";
            //预处理
            $statement = $pdo->prepare($sql);
            //绑定值
            $statement->bindValue(':email', $email);
            //执行sql
            $statement->execute();
            $row = $statement->fetch();
            return ['code'=>'1','msg'=>'成功','data'=>$row === false ? false : $row];
        }catch (\PDOException $e) {
            $rdata=['sql'=>$sql,'error'=>$e->getMessage(),'sqlstate'=>$e->getCode(),'params'=>$fields];
            return ['code'=>'0','msg'=>'sql执行失败','data'=>$rdata];
        }
    }

    /**
     * 通过邮箱更新数据
     *
     * @param  \PDO   $pdo
     * @param  string $email  邮箱号，如'a@x.com'
     * @param  array  $data   数据类型为关联数组 字段名 => 值，如 ['captcha' => '6666', 'pwd' => 'xxx']
     * @return array  $rdata  
     */
    public static function update_data_users(\PDO $pdo,string $email,array $data = []): array{
        // 允许插入的字段白名单
        $allowed = ['email', 'pwd', 'captcha', 'captcha_update_time', 'token', 'token_update_time', 'is_activated', 'register_time'];
        try {
            // 过滤掉不在白名单里的字段
            $data = array_intersect_key($data, array_flip($allowed));
            if (empty($data)) {return ['code' => '0', 'msg' => '没有可更新的字段', 'data' => ''];}
            $set = [];
            $params = [];
            foreach ($data as $col => $val) {
                $ph = ":{$col}";
                $set[] = "{$col} = {$ph}";
                $params[$ph] = $val;
            }
            $sql="UPDATE users SET ". implode(',', $set) . " WHERE email = :email";
            $statement = $pdo->prepare($sql);
            foreach ($data as $col => $val) {
                $statement->bindValue(":{$col}", $val);
            }
            $statement->bindParam(":email", $email);
            $statement->execute();
            $rdata=['sql'=>$sql,'params'=>$data,'stmt'=>$statement->rowCount()];
            return ['code'=>'1','msg'=>'成功','data'=>$rdata];
        }catch (\PDOException $e) {
            $rdata=['sql'=>$sql,'error'=>$e->getMessage(),'sqlstate'=>$e->getCode(),'params'=>$fields];
            return ['code'=>'0','msg'=>'sql执行失败','data'=>$rdata];
        }
    }

    /**
     * 查询通过邮箱删除数据
     *
     * @param  \PDO   $pdo
     * @param  string $email  'a@x.comg'
     * @return array  $rdata  
     */
    public static function delete_data_by_meail_users(\PDO $pdo,string $email): array{
        try {
            $sql = "DELETE FROM users WHERE email = :email";
            //预处理
            $statement = $pdo->prepare($sql);
            //绑定值
            $statement->bindValue(':email', $email);
            //执行sql
            $statement->execute();
            $rows = $statement->rowCount();
            return ['code'=>'1','msg'=>'成功','data'=>$rows];
        }catch (\PDOException $e) {
            $rdata=['sql'=>$sql,'error'=>$e->getMessage(),'sqlstate'=>$e->getCode()];
            return ['code'=>'0','msg'=>'sql执行失败','data'=>$rdata];
        }
    }
  
    /**
     * 查询通过token查询数据
     *
     * @param  \PDO   $pdo
     * @param  string $token  'adfadfsadfsdffs1ddg1fd11'
     * @param  array  $fields ['id','email',......]
     * @return array  $rdata  
     */
    public static function select_data_by_token_users(\PDO $pdo,string $token, array $fields = []): array{
        // 白名单，防止 SQL 注入
        $allowed = ['id', 'email', 'pwd', 'captcha', 'captcha_update_time','token','token_update_time','is_activated','register_time'];
        try {
            //取两个数组的交集
            $fields = array_intersect($fields, $allowed);
            //如果传入的数组是空数组，则return
            if (empty($fields)) {return ['code'=>'0','msg'=>'没有合法字段','data'=>''];}
            //把$fields数组里的元素用逗号连接
            $cols = implode(',', $fields);
            $sql = "SELECT {$cols} FROM users WHERE token = :token";
            //预处理
            $statement = $pdo->prepare($sql);
            //绑定值
            $statement->bindValue(':token', $token);
            //执行sql
            $statement->execute();
            $row = $statement->fetch();
            return ['code'=>'1','msg'=>'成功','data'=>$row === false ? false : $row];
        }catch (\PDOException $e) {
            $rdata=['sql'=>$sql,'error'=>$e->getMessage(),'sqlstate'=>$e->getCode(),'params'=>$fields];
            return ['code'=>'0','msg'=>'sql执行失败','data'=>$rdata];
        }
    }


    //////////////////////////////////////////////////music表
    /**
     * 插入一条music数据
     *
     * @param  \PDO  $pdo
     * @param  array $data 数据类型为关联数组 字段名 => 值，如 ['artist' => '林俊杰', 'title' => '爱不会绝迹']
     * @return array $rdata
     */
    public static function insert_data_music(\PDO $pdo,array $data = []): array{
        // 允许插入的字段白名单
        $allowed = ['id','name', 'title', 'artist', 'status'];
        try {
            // 过滤掉不在白名单里的字段,array_flip()让数组变成关联数组，array_intersect_key(),取两个数组中的交集，以第一个为主
            $data = array_intersect_key($data, array_flip($allowed));
            if (empty($data)) {return ['code' => '0', 'msg' => '没有可插入的字段', 'data' => ''];}
            // ['name', 'title']，取$data里的key
            $cols = array_keys($data);                    
            // [':name', ':title']，把'key'变成':key'
            $placeholders = array_map(fn($c) => ":{$c}", $cols);  
            //开始拼接sql
            $sql = "INSERT INTO music (" . implode(',', $cols) . ") VALUES (" . implode(',', $placeholders) . ")";
            //预处理
            $statement = $pdo->prepare($sql);
            //循环给sql绑定值
            foreach ($data as $col => $val) {
                $statement->bindValue(":{$col}", $val);
            }
            //执行sql
            $statement->execute();
            $rdata=['sql'=>$sql,'params'=>$data];
            return ['code'=>'1','msg'=>'成功','data'=>$rdata];
        }catch (\PDOException $e) {
            $rdata=['sql'=>$sql,'error'=>$e->getMessage(),'sqlstate'=>$e->getCode(),'params'=>$fields];
            return ['code'=>'0','msg'=>'sql执行失败','data'=>$rdata];
        }
    }

    /**
     * 查询通过id查询数据
     *
     * @param  \PDO   $pdo
     * @param  string $id  
     * @param  array  $fields ['id','name',......]
     * @return array  $rdata  
     */
    public static function select_data_by_id_music(\PDO $pdo,string $id, array $fields = []): array{
        // 白名单，防止 SQL 注入
        $allowed = ['id','name', 'title', 'artist', 'status'];
        try {
            //取两个数组的交集
            $fields = array_intersect($fields, $allowed);
            //如果传入的数组是空数组，则return
            if (empty($fields)) {return ['code'=>'0','msg'=>'没有合法字段','data'=>''];}
            //把$fields数组里的元素用逗号连接
            $cols = implode(',', $fields);
            $sql = "SELECT {$cols} FROM music WHERE id = :id";
            //预处理
            $statement = $pdo->prepare($sql);
            //绑定值
            $statement->bindValue(':id', $id);
            //执行sql
            $statement->execute();
            $row = $statement->fetch();
            return ['code'=>'1','msg'=>'成功','data'=>$row === false ? false : $row];
        }catch (\PDOException $e) {
            $rdata=['sql'=>$sql,'error'=>$e->getMessage(),'sqlstate'=>$e->getCode(),'params'=>$fields];
            return ['code'=>'0','msg'=>'sql执行失败','data'=>$rdata];
        }
    }

    /**
     * 通过id更新数据
     *
     * @param  \PDO   $pdo
     * @param  string $id
     * @param  array  $data   数据类型为关联数组 字段名 => 值，如 ['id' => '6666', 'name' => 'xxx']
     * @return array  $rdata  
     */
    public static function update_data_music(\PDO $pdo,string $id,array $data = []): array{
        // 允许插入的字段白名单
        $allowed = ['id','name', 'title', 'artist', 'status'];
        try {
            // 过滤掉不在白名单里的字段
            $data = array_intersect_key($data, array_flip($allowed));
            if (empty($data)) {return ['code' => '0', 'msg' => '没有可更新的字段', 'data' => ''];}
            $set = [];
            $params = [];
            foreach ($data as $col => $val) {
                $ph = ":{$col}";
                $set[] = "{$col} = {$ph}";
                $params[$ph] = $val;
            }
            $sql="UPDATE music SET ". implode(',', $set) . " WHERE id = :id";
            $statement = $pdo->prepare($sql);
            foreach ($data as $col => $val) {
                $statement->bindValue(":{$col}", $val);
            }
            $statement->bindParam(":id", $id);
            $statement->execute();
            $rdata=['sql'=>$sql,'params'=>$data,'stmt'=>$statement->rowCount()];
            return ['code'=>'1','msg'=>'成功','data'=>$rdata];
        }catch (\PDOException $e) {
            $rdata=['sql'=>$sql,'error'=>$e->getMessage(),'sqlstate'=>$e->getCode(),'params'=>$fields];
            return ['code'=>'0','msg'=>'sql执行失败','data'=>$rdata];
        }
    }

    /**
     * 查询通过id删除数据
     *
     * @param  \PDO   $pdo
     * @param  string $id
     * @return array  $rdata
     */
    public static function delete_data_by_id_music(\PDO $pdo,string $id): array{
        try {
            $sql = "DELETE FROM music WHERE id = :id";
            //预处理
            $statement = $pdo->prepare($sql);
            //绑定值
            $statement->bindValue(':id', $id);
            //执行sql
            $statement->execute();
            $rows = $statement->rowCount();
            return ['code'=>'1','msg'=>'成功','data'=>$rows];
        }catch (\PDOException $e) {
            $rdata=['sql'=>$sql,'error'=>$e->getMessage(),'sqlstate'=>$e->getCode()];
            return ['code'=>'0','msg'=>'sql执行失败','data'=>$rdata];
        }
    }

    /**
     * 查询所有 music 数据
     *
     * @param  \PDO   $pdo
     * @param  array  $fields  需要查询的字段 ['id','title','url',...]
     * @return array  $rdata
     */
    public static function select_all_music(\PDO $pdo,$fields = []): array {
        // 白名单，防止 SQL 注入
        $allowed = ['id','name', 'title', 'artist', 'status'];
        try {
            // 取两个数组的交集
            $fields = array_intersect($fields, $allowed);
            // 如果传入的数组是空数组，则 return
            if (empty($fields)) {return ['code' => '0', 'msg' => '没有合法字段', 'data' => ''];}

            // 把 $fields 数组里的元素用逗号连接
            $cols = implode(',', $fields);
            $sql = "SELECT {$cols} FROM music";
            // 预处理
            $statement = $pdo->prepare($sql);
            // 执行 sql
            $statement->execute();
            $rows = $statement->fetchAll();

            $rdata = ['sql' => $sql, 'data' =>$rows];
            return ['code' => '1', 'msg' => 'sql执行成功', 'data' => $rdata];
        } catch (\PDOException $e) {
            $rdata = ['sql' => $sql, 'error' => $e->getMessage(), 'sqlstate' => $e->getCode()];
            return ['code' => '0', 'msg' => 'sql执行失败', 'data' => $rdata];
        }
    }

    /**
     * 查询通过id查询数据
     *
     * @param  \PDO   $pdo
     * @param  string $title  
     * @param  array  $fields ['id','name',......]
     * @return array  $rdata  
     */
    public static function select_data_by_title_music(\PDO $pdo,string $title, array $fields = []): array{
        // 白名单，防止 SQL 注入
        $allowed = ['id','name', 'title', 'artist', 'status'];
        try {
            //取两个数组的交集
            $fields = array_intersect($fields, $allowed);
            //如果传入的数组是空数组，则return
            if (empty($fields)) {return ['code'=>'0','msg'=>'没有合法字段','data'=>''];}
            //把$fields数组里的元素用逗号连接
            $cols = implode(',', $fields);
            $sql = "SELECT {$cols} FROM music WHERE title LIKE :title";
            //预处理
            $statement = $pdo->prepare($sql);
            //绑定值
            $statement->bindValue(':title', '%'.$title.'%');
            //执行sql
            $statement->execute();
            $rows = $statement->fetchAll();
            return ['code'=>'1','msg'=>'成功','data'=>$row === false ? false : $rows];
        }catch (\PDOException $e) {
            $rdata=['sql'=>$sql,'error'=>$e->getMessage(),'sqlstate'=>$e->getCode(),'params'=>$fields];
            return ['code'=>'0','msg'=>'sql执行失败','data'=>$rdata];
        }
    }


    ///////////////////////////////////////////////////////////user_music_favorites表

    /**
     * 判断用户是否收藏了某个音乐
     *
     * @param  \PDO   $pdo
     * @param  int    $user_id
     * @param  int    $music_id
     * @return array  $rdata
     */
    public static function select_data_by_userid_musicid_userMusicFavorites(\PDO $pdo,$user_id,$music_id): array {
        try {
            $sql = "SELECT 1 FROM user_music_favorites WHERE user_id = :user_id AND music_id = :music_id LIMIT 1";
            // 预处理
            $statement = $pdo->prepare($sql);
            // 绑定值
            $statement->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
            $statement->bindValue(':music_id', $music_id, \PDO::PARAM_INT);
            // 执行 sql
            $statement->execute();
            $rows = $statement->fetch() !== false;

            $rdata = ['sql' => $sql, 'data' =>$rows];
            return ['code' => '1', 'msg' => 'sql执行成功', 'data' => $rdata];
        } catch (\PDOException $e) {
            $rdata = ['sql' => $sql, 'error' => $e->getMessage(), 'sqlstate' => $e->getCode()];
            return ['code' => '0', 'msg' => 'sql执行失败', 'data' => $rdata];
        }
    }

    /**
     * 判断用户是否收藏了某个音乐
     *
     * @param  \PDO   $pdo
     * @param  int    $user_id
     * @param  int    $music_id
     * @return array  $rdata
     */
    public static function insert_data_userMusicFavorites(\PDO $pdo,$user_id,$music_id): array {
        try {
            $sql = "insert into user_music_favorites(user_id, music_id)VALUES(:user_id, :music_id)";
            // 预处理
            $statement = $pdo->prepare($sql);
            // 绑定值
            $statement->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
            $statement->bindValue(':music_id', $music_id, \PDO::PARAM_INT);
            // 执行 sql
            $statement->execute();

            $rdata = ['sql' => $sql];
            return ['code' => '1', 'msg' => 'sql执行成功', 'data' => $rdata];
        } catch (\PDOException $e) {
            $rdata = ['sql' => $sql, 'error' => $e->getMessage(), 'sqlstate' => $e->getCode()];
            return ['code' => '0', 'msg' => 'sql执行失败', 'data' => $rdata];
        }
    }

    /**
     * 判断用户是否收藏了某个音乐
     *
     * @param  \PDO   $pdo
     * @param  int    $user_id
     * @param  int    $music_id
     * @return array  $rdata
     */
    public static function delete_data_userMusicFavorites(\PDO $pdo,$user_id,$music_id): array {
        try {
            // $sql = "insert into user_music_favorites(user_id, music_id)VALUES(:user_id, :music_id)";
            $sql = "DELETE FROM user_music_favorites WHERE user_id = :user_id AND music_id = :music_id";
            // 预处理
            $statement = $pdo->prepare($sql);
            // 绑定值
            $statement->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
            $statement->bindValue(':music_id', $music_id, \PDO::PARAM_INT);
            // 执行 sql
            $statement->execute();
            $rows = $statement->rowCount();
            $rdata = ['sql' => $sql,'$rows'=>$rows];
            return ['code' => '1', 'msg' => 'sql执行成功', 'data' => $rdata];
        } catch (\PDOException $e) {
            $rdata = ['sql' => $sql, 'error' => $e->getMessage(), 'sqlstate' => $e->getCode()];
            return ['code' => '0', 'msg' => 'sql执行失败', 'data' => $rdata];
        }
    }

}

