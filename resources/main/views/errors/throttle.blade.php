<!DOCTYPE html>
<html>
    <head>
        <title>Too many requests</title>

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #000;
                display: table;
                font-weight: 100;
                font-family: Arial, Helvetica, sans-serif;
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 36px;
                color: #f00;
                margin-bottom: 40px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">Too many requests</div>
                <div class="content">
                    <span>We have encountered too many requests from IP address {{ request()->getClientIp() }}.</span>
                    <br><br>
                    <span>For safety measure, the said IP is blocked from accessing our site.</span>
                    <br>
                    <span>You are requested to check back after 1 hour.</span>
                </div>
            </div>
        </div>
    </body>
</html>
