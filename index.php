<?php
require_once("dbcon.php");

$result = $conn->query(
    "SELECT * FROM access_log
     ORDER BY scan_time DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="refresh" content="30">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>RFID Attendance Dashboard</title>


<style>

:root{
--bg:#07090f;
--surface:#0c1018;
--surface2:#111928;
--cyan:#00cfff;
--cyan-border:rgba(0,207,255,.18);
--cyan-glow:rgba(0,207,255,.12);
--green:#00ff66;
--red:#ff3366;
--text:#b8cce0;
--bright:#e4f0ff;
}

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Segoe UI',sans-serif;
}

body{
background:#07090f;
background-image:
radial-gradient(rgba(0,207,255,.03) 1px, transparent 1px);
background-size:28px 28px;
color:var(--text);
min-height:100vh;
overflow-x:hidden;
}

.header{
height:60px;
display:flex;
justify-content:space-between;
align-items:center;
padding:0 30px;
background:rgba(7,9,15,.9);
border-bottom:1px solid var(--cyan-border);
}

.logo{
font-size:14px;
font-weight:700;
letter-spacing:2px;
color:var(--cyan);
text-transform:uppercase;
}

.login-btn{
text-decoration:none;
padding:8px 18px;
border:1px solid var(--cyan);
border-radius:8px;
color:var(--cyan);
font-size:12px;
font-weight:700;
transition:.3s;
}

.login-btn:hover{
background:var(--cyan);
color:#000;
}

.container{
display:flex;
gap:20px;
padding:20px;
max-width:1600px;
margin:auto;
}

.sidebar{
width:320px;
flex-shrink:0;
}

.main-content{
flex:1;
min-width:0;
}

.card,
.table-card{
background:var(--surface);
border:1px solid var(--cyan-border);
border-radius:14px;
padding:20px;
}

.card h3,
.table-title{
color:var(--bright);
margin-bottom:15px;
}

.search-box{
width:100%;
padding:12px;
background:var(--surface2);
border:1px solid var(--cyan-border);
border-radius:8px;
color:white;
outline:none;
}

.search-btn{
width:100%;
padding:12px;
margin-top:10px;
background:var(--cyan);
border:none;
border-radius:8px;
font-weight:bold;
cursor:pointer;
transition:.3s;
}

.search-btn:hover{
opacity:.9;
}

.table-card{
overflow-x:auto;
}

table{
width:100%;
border-collapse:collapse;
min-width:700px;
}

th{
padding:12px;
text-align:left;
border-bottom:1px solid var(--cyan-border);
color:#7f9ab0;
font-size:11px;
text-transform:uppercase;
letter-spacing:1px;
}

td{
padding:12px;
border-bottom:1px solid rgba(255,255,255,.05);
white-space:nowrap;
}

tr:hover{
background:var(--cyan-glow);
}

.status-granted{
display:inline-block;
padding:8px 14px;
border-radius:8px;
background:rgba(0,255,102,.15);
color:#00ff66;
font-weight:700;
text-shadow:0 0 8px rgba(0,255,102,.6);
}

.status-denied{
display:inline-block;
padding:8px 14px;
border-radius:8px;
background:rgba(255,51,102,.15);
color:#ff3366;
font-weight:700;
text-shadow:0 0 8px rgba(255,51,102,.6);
}

.result-box{
margin-top:15px;
padding:15px;
background:var(--surface2);
border-radius:8px;
display:none;
}

/* ==========================
   TABLET
========================== */

@media (max-width:1024px){

.container{
flex-direction:column;
}

.sidebar{
width:100%;
}

.main-content{
width:100%;
}

}

/* ==========================
   MOBILE
========================== */

@media (max-width:768px){

.header{
height:auto;
flex-direction:column;
gap:12px;
padding:15px;
text-align:center;
}

.logo{
font-size:13px;
}

.login-btn{
width:100%;
max-width:250px;
text-align:center;
}

.container{
padding:12px;
gap:15px;
}

.sidebar{
width:100%;
}

.card,
.table-card{
padding:15px;
}

.search-box,
.search-btn{
font-size:15px;
}

.table-title{
font-size:16px;
}

th,
td{
padding:10px;
font-size:13px;
}

.status-granted,
.status-denied{
font-size:12px;
padding:6px 10px;
}

}

/* ==========================
   SMALL PHONES
========================== */

@media (max-width:480px){

.logo{
font-size:11px;
letter-spacing:1px;
}

.search-box{
padding:10px;
}

.search-btn{
padding:10px;
}

th,
td{
font-size:12px;
}

.status-granted,
.status-denied{
font-size:11px;
padding:5px 8px;
}

}

</style>
</head>

<body>

<div class="header">

<div class="logo">
RFID ATTENDANCE DASHBOARD
</div>

<a href="login.php" class="login-btn">
LOGIN
</a>

</div>

<div class="container">

<div class="sidebar">

<div class="card">

<h3>Search Student</h3>

<input
type="text"
id="searchName"
class="search-box"
placeholder="Enter Student Name">

<button
class="search-btn"
onclick="searchStudent()">
Search
</button>

<div
class="result-box"
id="resultBox">

<p>
<strong>Name:</strong>
<span id="studentName"></span>
</p>

<p>
<strong>Total Records:</strong>
<span id="attendanceCount"></span>
</p>

</div>

</div>

</div>

<div class="main-content">

<div class="table-card">

<div class="table-title">
RFID Attendance Logs
</div>

<table id="attendanceTable">

<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Status</th>
<th>Scan Time</th>
</tr>
</thead>

<tbody>

<?php while($row = $result->fetch_assoc()) { ?>

<tr>

<td><?= $row['id']; ?></td>

<td class="student-name">
<?= htmlspecialchars($row['name']); ?>
</td>

<td>

<?php
$status = strtoupper(trim($row['status']));

if($status == "ACCESS_GRANTED"){
    echo '<span class="status-granted">ACCESS_GRANTED</span>';
}
else{
    echo '<span class="status-denied">ACCESS_DENIED</span>';
}
?>

</td>

<td><?= $row['scan_time']; ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<script>

function searchStudent(){

let name = document
.getElementById("searchName")
.value
.trim()
.toLowerCase();

let rows = document.querySelectorAll(
"#attendanceTable tbody tr"
);

let count = 0;
let foundName = "";

rows.forEach(row => {

let student = row
.querySelector(".student-name")
.innerText
.trim();

if(student.toLowerCase() === name){
count++;
foundName = student;
}

});

document.getElementById("resultBox")
.style.display = "block";

if(count > 0){

document.getElementById("studentName")
.innerText = foundName;

document.getElementById("attendanceCount")
.innerText = count;

}
else{

document.getElementById("studentName")
.innerText = "Student Not Found";

document.getElementById("attendanceCount")
.innerText = "0";

}

}

</script>

</body>
</html>