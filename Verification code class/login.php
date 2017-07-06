    <?php  
        //开启Session  
        session_start();  
        //判断是否提交  
        if(isset($_POST['dosubmit'])){  
            //获取session中的验证码并转为小写  
            $sessionCode=strtolower($_SESSION['code']);  
            //获取输入的验证码  
            $code=strtolower($_POST['code']);  
            //判断是否相等  
            if($sessionCode==$code){  
                echo "<script type='text/javascript'>alert('验证码正确!');</script>";  
            }else{  
                echo "<script type='text/javascript'>alert('验证码错误!');</script>";  
            }  
        }  
    ?>  
    <!DOCTYPE html>  
    <html>  
        <head>  
            <title></title>  
            <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>  
            <style type="text/css">  
                *{margin:0px;padding:0px;}  
                ul{  
                    width:400px;  
                    list-style:none;  
                    margin:50px auto;  
                }  
                  
                li{  
                    padding:12px;  
                    position:relative;  
                }  
                  
                label{  
                    width:80px;  
                    display:inline-block;  
                    float:left;  
                    line-height:30px;  
                }  
                  
                input[type='text'],input[type='password']{  
                    height:30px;  
                }  
                  
                img{  
                    margin-left:10px;  
                }  
                  
                input[type="submit"]{  
                    margin-left:80px;  
                    padding:5px 10px;  
                }  
            </style>  
        </head>  
        <body>  
            <form action="login.php" method="post">  
                <ul>  
                    <li>  
                        <label>用户名：</label>  
                        <input type="text" name="username"/>  
                    </li>  
                    <li>  
                        <label>密码：</label>  
                        <input type="password" name="password"/>  
                    </li>  
                    <li>  
                        <label>验证码：</label>  
                        <input type="text" name="code" size="4" style="float:left"/>  
                        <img src="test.php" onclick="this.src='test.php?Math.random()'"/>  
                    </li>  
                    <li>  
                        <input type="submit" value="登录" name="dosubmit"/>  
                    </li>  
                </ul>  
            </form>  
        </body>  
    </html>  