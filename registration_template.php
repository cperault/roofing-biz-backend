<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <style>
        * {
            font-family: Cambria, Cochin, Georgia, Times, "Times New Roman", serif;
        }

        body {
            text-align: left;
        }

        h2 {
            font-weight: bold;
        }

        .container {
            width: 90%;
            margin: 0 auto;
            padding: 20px;
            border-radius: 5px;
            background: #253237;
            color: white;
            text-decoration: none;
        }

        .header {
            position: relative;
            font-weight: 300;
            font-size: 12px;
            color: white;
            border-bottom: 1px solid #9DB4C0;
            margin-bottom: 10px;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            margin-bottom: 10px;
        }

        p {
            line-height: 24px;
        }

        a {
            color: #253237;
            border-radius: 5px;
            margin-top: 10px;
            margin-bottom: 10px;
            background: #9DB4C0;
            padding: 10px;
            font-weight: bold;
            text-decoration: none
        }

        #unauthorized_notice_text {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Roofmasters Roofing, Siding, and Gutters</h1>
            <h2>Email confirmation</h2>
        </div>
        <p>
            Thank you for registering, %first_name%!
        </p>
        <p>
            In order to activate your account, we require you to confirm the email address provided. Please click the button below to activate your account.
        </p>
        <span id="unauthorized_notice_text">If you did not authorize this, please reply to this email and we will be in touch.</span>
        <br />
        <br />
        <a href="%activation_link%">Activate Your Account</a>
    </div>
</body>

</html>