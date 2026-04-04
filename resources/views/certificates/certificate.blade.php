<!DOCTYPE html>
<html>
<head>
<style>

body{
    text-align:center;
    font-family: DejaVu Sans, sans-serif;
}

.container{
    border:10px solid #2c3e50;
    padding:80px;
}

.title{
    font-size:50px;
    font-weight:bold;
}

.name{
    font-size:40px;
    margin-top:30px;
}

.text{
    font-size:20px;
    margin-top:20px;
}

.date{
    margin-top:40px;
}

</style>
</head>

<body>

<div class="container">

<div class="title">Certificate of Completion</div>

<div class="text">
This is to certify that
</div>

<div class="name">
{{ $intern->name }}
</div>

<div class="text">
has successfully completed the internship program
</div>

<div class="date">
Date: {{ date('F d, Y') }}
</div>

</div>

</body>
</html>