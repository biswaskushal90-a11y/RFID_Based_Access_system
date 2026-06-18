<?php
session_start();

if(!isset($_SESSION['admin']))
{
    header("Location: login.php");
    exit();
}

require_once("dbcon.php");

$message = "";

if(isset($_POST['add']))
{
    $uid  = trim($_POST['uid']);
    $name = trim($_POST['name']);

    $check = $conn->prepare(
        "SELECT id,name
         FROM authorized_cards
         WHERE uid=?"
    );

    $check->bind_param("s",$uid);
    $check->execute();

    $result = $check->get_result();

    if($result->num_rows > 0)
    {
        $row = $result->fetch_assoc();

        $message =
            "<script>
            alert('RFID Card Already Exists!\n\nUID: ".$uid."\nName: ".$row['name']."');
            </script>";
    }
    else
    {
        $stmt = $conn->prepare(
            "INSERT INTO authorized_cards(uid,name)
             VALUES(?,?)"
        );

        $stmt->bind_param("ss",$uid,$name);
        $stmt->execute();

        $message =
            "<script>
            alert('Card Added Successfully');
            </script>";
    }
}

if(isset($_POST['update']))
{
    $id   = $_POST['id'];
    $name = $_POST['name'];

    $stmt = $conn->prepare(
        "UPDATE authorized_cards
         SET name=?
         WHERE id=?"
    );

    $stmt->bind_param("si",$name,$id);
    $stmt->execute();

    $message =
        "<script>
        alert('Card Updated Successfully');
        </script>";
}

if(isset($_GET['delete']))
{
    $id = $_GET['delete'];

    $stmt = $conn->prepare(
        "DELETE FROM authorized_cards
         WHERE id=?"
    );

    $stmt->bind_param("i",$id);
    $stmt->execute();

    header("Location: manage_cards.php");
    exit();
}

$cards = $conn->query(
    "SELECT *
     FROM authorized_cards
     ORDER BY id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage RFID Cards</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>

:root {
    --bg:#07090f;
    --surface:#0c1018;
    --surface-2:#111928;
    --cyan:#00cfff;
    --cyan-glow:rgba(0,207,255,0.12);
    --cyan-border:rgba(0,207,255,0.18);
    --green:#00ff88;
    --green-dim:rgba(0,255,136,0.10);
    --red:#ff3366;
    --red-dim:rgba(255,51,102,0.10);
    --text:#b8cce0;
    --text-dim:#3a4e62;
    --text-bright:#e4f0ff;
    --ui:'Space Grotesk',sans-serif;
    --mono:'Space Mono',monospace;
    --r:14px;
    --r-sm:8px;
    --ease:0.2s ease;
}

*,
*::before,
*::after{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background-color:var(--bg);
    background-image:
        radial-gradient(rgba(0,207,255,.028) 1px, transparent 1px);
    background-size:28px 28px;
    color:var(--text);
    font-family:var(--ui);
    min-height:100vh;
    overflow-x:hidden;
    animation:fadeUp .4s ease both;
}

@keyframes fadeUp{
    from{
        opacity:0;
        transform:translateY(10px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

@keyframes scanPulse{
    0%,100%{
        box-shadow:0 0 8px rgba(0,207,255,.3);
    }
    50%{
        box-shadow:0 0 18px rgba(0,207,255,.6);
    }
}

/* NAVBAR */

.nav{
    position:sticky;
    top:0;
    z-index:100;
    height:60px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 32px;
    background:rgba(7,9,15,.88);
    backdrop-filter:blur(20px);
    border-bottom:1px solid var(--cyan-border);
}

.brand{
    font-family:var(--mono);
    font-size:13px;
    font-weight:700;
    color:var(--cyan);
    letter-spacing:2px;
    text-transform:uppercase;
}

/* BUTTONS */

.btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:8px 16px;
    border-radius:var(--r-sm);
    font-family:var(--mono);
    font-size:10px;
    font-weight:700;
    letter-spacing:1px;
    text-transform:uppercase;
    text-decoration:none;
    cursor:pointer;
    border:1px solid;
    transition:.2s;
}

.btn-logout{
    border-color:var(--red);
    color:var(--red);
    background:transparent;
}

.btn-logout:hover{
    background:var(--red-dim);
}

.btn-update{
    border-color:var(--green);
    color:var(--green);
    background:transparent;
}

.btn-update:hover{
    background:var(--green-dim);
}

/* MAIN */

.main{
    max-width:1200px;
    margin:auto;
    padding:24px;
    display:flex;
    flex-direction:column;
    gap:20px;
}

.card{
    background:var(--surface);
    border:1px solid var(--cyan-border);
    border-radius:var(--r);
    padding:24px;
}

.eyebrow{
    font-family:var(--mono);
    font-size:9px;
    letter-spacing:3px;
    text-transform:uppercase;
    color:var(--text-dim);
    margin-bottom:6px;
}

.card-title{
    color:var(--text-bright);
    font-size:18px;
    font-weight:700;
    margin-bottom:20px;
}

/* UID BOX */

.uid-display{
    display:flex;
    align-items:center;
    gap:14px;
    padding:14px 16px;
    background:var(--surface-2);
    border:1px solid var(--cyan-border);
    border-radius:var(--r-sm);
    margin-bottom:16px;
    animation:scanPulse 3s infinite;
}

.uid-label{
    font-family:var(--mono);
    font-size:9px;
    letter-spacing:2px;
    text-transform:uppercase;
    color:var(--text-dim);
    white-space:nowrap;
}

.uid-value{
    flex:1;
    border:none;
    background:transparent;
    outline:none;
    color:var(--cyan);
    font-family:var(--mono);
    font-size:15px;
    font-weight:700;
    letter-spacing:3px;
}

/* FORM */

.enroll-form{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    align-items:flex-end;
}

.field{
    flex:1;
    min-width:220px;
}

.field-label{
    display:block;
    font-family:var(--mono);
    font-size:9px;
    letter-spacing:2px;
    text-transform:uppercase;
    color:var(--text-dim);
    margin-bottom:7px;
}

input[type="text"]{
    width:100%;
    padding:11px 14px;
    border:1px solid var(--cyan-border);
    border-radius:var(--r-sm);
    background:var(--surface-2);
    color:var(--text-bright);
    outline:none;
}

input[type="text"]:focus{
    border-color:var(--cyan);
    box-shadow:0 0 0 3px var(--cyan-glow);
}

.add-btn{
    padding:11px 24px;
    border:none;
    border-radius:var(--r-sm);
    background:var(--cyan);
    color:#000;
    font-family:var(--mono);
    font-size:11px;
    font-weight:700;
    cursor:pointer;
    white-space:nowrap;
}

/* TABLE */

.scroll-wrap{
    overflow-x:auto;
    -webkit-overflow-scrolling:touch;
}

table{
    width:100%;
    min-width:700px;
    border-collapse:collapse;
    font-size:14px;
}

thead th{
    padding:10px 14px;
    text-align:left;
    border-bottom:1px solid var(--cyan-border);
    font-family:var(--mono);
    font-size:9px;
    letter-spacing:2px;
    color:var(--text-dim);
    white-space:nowrap;
}

tbody td{
    padding:11px 14px;
    border-bottom:1px solid rgba(255,255,255,.04);
}

tbody tr:hover{
    background:var(--cyan-glow);
}

.td-id{
    font-family:var(--mono);
    color:var(--text-dim);
}

.td-uid{
    font-family:var(--mono);
    color:rgba(0,207,255,.7);
}

.delete-link{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:7px 12px;
    border:1px solid rgba(255,51,102,.3);
    border-radius:var(--r-sm);
    text-decoration:none;
    color:var(--red);
    font-family:var(--mono);
    font-size:10px;
}

/* TABLET */

@media(max-width:1024px){

.main{
    padding:20px;
}

.card{
    padding:20px;
}

}

/* MOBILE */

@media(max-width:768px){

.nav{
    height:auto;
    flex-direction:column;
    gap:12px;
    padding:15px;
}

.brand{
    text-align:center;
}

.btn-logout{
    width:100%;
}

.main{
    padding:12px;
}

.card{
    padding:16px;
}

.uid-display{
    flex-direction:column;
    align-items:flex-start;
}

.uid-value{
    width:100%;
    letter-spacing:1px;
}

.enroll-form{
    flex-direction:column;
}

.field{
    width:100%;
    min-width:0;
}

.add-btn{
    width:100%;
}

table{
    min-width:650px;
}

}

/* SMALL MOBILE */

@media(max-width:480px){

.brand{
    font-size:10px;
}

.card-title{
    font-size:16px;
}

.card{
    padding:14px;
}

.uid-value{
    font-size:12px;
}

thead th{
    font-size:8px;
    padding:8px;
}

tbody td{
    padding:8px;
    font-size:12px;
}

.btn-update,
.delete-link{
    font-size:9px;
    padding:6px 10px;
}

}

</style>
</head>
<body>

<?php echo $message; ?>

<!-- NAV -->
<nav class="nav">
    <div class="brand">◈ Card Management</div>
    <a href="index.php" class="btn btn-logout">Logout</a>
</nav>

<!-- MAIN -->
<div class="main">

    <!-- ENROLL CARD -->
    <div class="card">
        <div class="eyebrow">Scanner</div>
        <div class="card-title">Enroll New RFID Card</div>

        <!-- UID display — outside form, populated by JS -->
        <div class="uid-display">
            <span class="uid-label">Scanned UID</span>
            <input
                type="text"
                id="uid"
                name="uid"
                class="uid-value"
                placeholder="Waiting for scan…"
                readonly
            >
        </div>

        <form method="POST" class="enroll-form">

            <input type="hidden" id="hidden_uid" name="uid">

            <div class="field">
                <label class="field-label">Student Name</label>
                <input
                    type="text"
                    name="name"
                    placeholder="Enter student name"
                    required
                >
            </div>

            <button type="submit" name="add" class="add-btn">
                Enroll Card
            </button>

        </form>
    </div>

    <!-- REGISTERED CARDS -->
    <div class="card">
        <div class="eyebrow">Registry</div>
        <div class="card-title">Registered Cards</div>

        <div class="scroll-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>UID</th>
                        <th>Name</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>

                <?php while($row = $cards->fetch_assoc()): ?>
                <tr>

                    <td class="td-id"><?= $row['id'] ?></td>

                    <td class="td-uid"><?= htmlspecialchars($row['uid']) ?></td>

                    <td>
                        <form method="POST">
                        <input
                            type="hidden"
                            name="id"
                            value="<?= $row['id'] ?>"
                        >
                        <input
                            type="text"
                            name="name"
                            value="<?= htmlspecialchars($row['name']) ?>"
                        >
                    </td>

                    <td>
                        <button
                            type="submit"
                            name="update"
                            class="btn btn-update"
                        >Update</button>
                        </form>
                    </td>

                    <td>
                        <a
                            href="?delete=<?= $row['id'] ?>"
                            class="delete-link"
                            onclick="return confirm('Delete this card?')"
                        >Delete</a>
                    </td>

                </tr>
                <?php endwhile; ?>

                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
let lastUID = "";

setInterval(function() {

    fetch("get_uid.php")
        .then(response => response.text())
        .then(uid => {

            uid = uid.trim();

            document.getElementById("uid").value       = uid;
            document.getElementById("hidden_uid").value = uid;

            if(uid !== "" && uid !== lastUID)
            {
                lastUID = uid;

                fetch("check_uid.php?uid=" + encodeURIComponent(uid))
                    .then(response => response.json())
                    .then(data => {

                        if(data.exists)
                        {
                            alert(
                                "RFID Card Already Exists\n\n" +
                                "UID : " + uid + "\n" +
                                "Name : " + data.name
                            );
                        }

                    });
            }

        });

}, 1000);
</script>

</body>
</html>