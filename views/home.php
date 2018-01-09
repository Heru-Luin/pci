<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PCI - PHP Continuous Integration Tool</title>
  <link rel="stylesheet" href="./../assets/style.css">
  <script src="./../assets/script.js"></script>
</head>
<body>  
  <?php include 'header.php';?>
  <input type="text" class=".input" placeholder="Search projects ..." autofocus/>
  
  <ul class="list-group">
    <li class="list-group-item"><a href="/projects/symfony/yaml/builds/current">symfony/yaml</a></li>
    <li class="list-group-item"><a href="/projects/psy/psysh/builds/current">psy/psysh</a></li>
    <li class="list-group-item"><a href="/projects/block8/phpci/builds/current">block8/phpci</a></li>
    <li class="list-group-item"><a href="/projects/phpstan/phpstan/builds/current">phpstan/phpstan</a></li>
    <li class="list-group-item"><a href="/projects/squizlabs/php_codesniffer/builds/current">squizlabs/php_codesniffer</a></li>
  </ul>
</body>
</html>
