<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PCI - PHP Continuous Integration Tool</title>
  <link rel="stylesheet" href="./../assets/style.css">
  <script src="/assets/jquery-3.2.1.min.js"></script>
  <script src="/assets/script.js"></script>
</head>
<body>  
  <?php include 'header.php';?>
  <input type="text" id="search" class=".input" placeholder="Search projects ..." autofocus/>
  
  <ul class="list-group" id="projects">
    <?php 
    foreach($repositories as $repository) {?>
    <li class="list-group-item"><a href="/projects/<?= $repository['id'];?>/builds/<?=$repository['build_id'];?>"><?= $repository['id'];?></a></li>
    <?php } ?>
  </ul>
</body>
</html>
