<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">  
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Max Kouzel">
        <meta name="description" content="Music Mirror Login Page">  
        <title>Music Mirror Login</title>
        
    </head>
    <body>
        <div>
            <?php
                if (!empty($error_msg)) {
                    echo "<h3>$error_msg</h3>";
                }
            ?>
            <form action="?command=login" method="post">
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"/>
                </div>
                <div>
                    <label for="name">Name</label>
                    <input type="text"id="name" name="name"/>
                </div>
                <div>
                    <label for="password">Password</label>
                    <input type="password"id="password" name="password"/>
                </div>
                <div>                
                <button type="submit">LOG IN</button>
                </div>
            </form>
        </div>
    </body>


</html>