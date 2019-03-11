<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
</head>
<body style="color: #000000; font-family: 'Open Sans', sans-serif;">

<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
    <tr>
        <td align="left" valign="top">
            <table border="0" cellpadding="20" cellspacing="0" width="600" id="emailContainer">
                <tr>
                    <td align="left" valign="top">
                        <h3 style="padding-left:5px;height: 40px; line-height: 40px; background-color: #f56857; color: #ffffff;">There has been an exception thrown on {{ config('app.name') }}</h3>
                        <table class="emailExceptionTable" style="text-align: left;" border="0" cellspacing="0" cellpadding="3">
                            <tr>
                                <td>
                                    <strong>Environment:</strong>
                                </td>
                                <td>
                                    {{ config('app.env') }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Exception Url:</strong>
                                </td>
                                <td>
                                    {!! Request::fullUrl() !!}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Exception Class:</strong>
                                </td>
                                <td>
                                    {{ get_class($exception) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Exception Message:</strong>
                                </td>
                                <td>
                                    {{ $exception->getMessage() }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Exception Code:</strong>
                                </td>
                                <td>
                                    {{ $exception->getCode() }}
                                </td>
                            </tr>
                        </table>
                        <br>
                        <table align="left" style="text-align: left;" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    In File <b style="color:red;">{{ $exception->getFile() }} on line {{  $exception->getLine() }}</b>
                                </td>
                            </tr>
                        </table>
                        <br>
                        <table align="left" style="text-align: left;" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <strong>Stack Trace:</strong>
                                </td>
                            </tr>
                            <tr>
                                <td align="left" style="text-align: left;">
                                    <pre style="white-space: pre-wrap;">{{$exception->getTraceAsString()}}</pre>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>