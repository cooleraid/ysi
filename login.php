

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script language="javascript" type="text/javascript" src="ajax.js"></script>
</head>
<body>
<div class="form-group">
<form id="login" onsubmit='login();return false;'>
                        <div id="error_div" class="alert alert-danger" style="display : none;"></div>

                        <div class="form-group">
                            <label>Username</label>
                            <input id="email" type="text" name="email" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input id="password" type="password" name="password" class="form-control">
                        </div>

                        <input id="submit" type="submit" name="Login" value="Login" class="button" id="login"/>
                    </form>
</div>
</body>
</html>