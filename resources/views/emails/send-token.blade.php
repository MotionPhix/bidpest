<!DOCTYPE html>
<html>
<head>
  <title>Your OTP Token</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 20px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" border="0"
             style="background-color: #ffffff; border-radius: 5px; overflow: hidden;">
        <tr>
          <td align="center"
              style="padding: 20px 0; background-color: #007bff; color: #ffffff; font-size: 24px; font-weight: bold;">
            Your OTP Token
          </td>
        </tr>
        <tr>
          <td style="padding: 20px; color: #333333; font-size: 16px;">
            <p style="margin: 0 0 10px;">Hello, {{ $name }}</p>
            <p style="margin: 0 0 10px;">Your OTP code is:</p>
            <p style="margin: 0 0 20px; font-size: 24px; font-weight: bold; color: #007bff;">{{ $token }}</p>

            <p style="margin: 0;">
              Please use this token to complete your login. The code is valid for 10 minutes.
            </p>

            <p style="margin: 0;">
              This token will expire in {{ $expires }}
            </p>
          </td>
        </tr>
        <tr>
          <td align="center" style="padding: 20px; background-color: #f4f4f4; color: #999999; font-size: 12px;">
            <p style="margin: 0;">If you did not request this code, please ignore this email.</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
