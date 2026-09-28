<?php
try{
    $dsn="mysql:host=127.0.0.1;dbname=my_demo";      //配置信息
    $name="root";                      //mysql账号
    $pwd="root";                       //密码
    $pdo=new PDO($dsn,$name,$pwd);       //实例化pdo对象
    //设置错误处理模式，PDO::ERRMODE_EXCEPTION（异常模式）,PDO::ERRMODE_SILENT（沉默，不警告不报错），PDO::ERRMODE_WARNING（警告模式）
    $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);    //设置模式为异常
    $pdo -> setAttribute(PDO::ATTR_AUTOCOMMIT,1);     //事务是否自动提交，0是关闭，1是自动
    //设置结果集返回格式     PDO::FETCH_ASSOC（关联数组）  PDO::FETCH_NUM（索引数组）  PDO::FETCH_BOTH（两种数组都返回）  PDO::FETCH_OBJ（对象）
    $pdo -> setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);      //设置返回数组是关联数组
}catch(PDOException $e){
    echo $e -> getMessage().'<br />';    //错误信息
    echo $e -> getFile().'<br />';       //错误产生的文件
    echo $e -> getLine().'<br />';       //错误产生的行
    echo $e -> getCode().'<br />';       //错误码
}
?>